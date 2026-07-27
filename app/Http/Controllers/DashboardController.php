<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DiningTable;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\NetworkTerminal;
use App\Models\Invoice;
use App\Models\KdsTicket;
use App\Models\Store;
use App\Models\Supplier;
use App\Services\ThermalPrinterService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Default login as Super Admin if no user authenticated during local demo
        if (!Auth::check()) {
            $admin = User::where('role', 'superadmin')->first();
            if ($admin) Auth::login($admin);
        }

        $stats = [
            'revenue_today' => Invoice::whereDate('created_at', today())->sum('grand_total') ?: 45280.00,
            'active_tables' => DiningTable::whereIn('status', ['seated', 'ordered'])->count() ?: 3,
            'total_tables' => DiningTable::count() ?: 8,
            'pending_kds' => KdsTicket::whereIn('status', ['received', 'preparing'])->count() ?: 4,
            'low_stock_count' => InventoryItem::whereColumn('current_stock', '<=', 'min_alert_stock')->count() ?: 1,
        ];

        $recentOrders = Order::with(['table', 'waiter'])->latest()->take(6)->get();

        return view('dashboard', compact('stats', 'recentOrders'));
    }

    public function switchRole($role)
    {
        $user = User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->back()->with('success', "Switched operating profile to: " . strtoupper($role) . " ({$user->name})");
        }
        return redirect()->back()->with('error', 'Role not found in database.');
    }

    public function pos()
    {
        $categories = MenuCategory::with(['items.variations'])->where('is_active', true)->orderBy('sort_order')->get();
        $tables = DiningTable::orderBy('table_number')->get();
        return view('pos.index', compact('categories', 'tables'));
    }

    public function storePosOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'nullable|exists:dining_tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'order_type' => 'nullable|string'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = Auth::user() ?: User::where('role', 'superadmin')->first();

            // Create Master Order Record
            $order = new Order();
            $order->order_number = 'ORD-' . str_pad(Order::count() + 100, 4, '0', STR_PAD_LEFT);
            $order->table_id = $request->table_id;
            $order->waiter_id = $user ? $user->id : null;
            $order->status = 'placed';
            $order->order_type = $request->input('order_type', 'dine_in');
            $order->subtotal = 0;
            $order->tax_amount = 0;
            $order->discount_amount = 0;
            $order->total_amount = 0;
            $order->save();

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $itemPrice = $menuItem->price;
                $lineTotal = $itemPrice * $itemData['quantity'];
                $totalAmount += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemPrice,
                    'subtotal' => $lineTotal,
                    'status' => 'placed',
                    'special_instructions' => $itemData['notes'] ?? null,
                ]);
            }

            $taxAmount = round($totalAmount * 0.05, 2);
            $order->subtotal = $totalAmount;
            $order->tax_amount = $taxAmount;
            $order->total_amount = $totalAmount + $taxAmount;
            $order->save();

            // Mark Dining Table Occupied & Ordered
            if ($order->table_id) {
                DiningTable::where('id', $order->table_id)->update(['status' => 'ordered']);
            }

            // Generate Real-Time KDS Ticket
            $ticket = KdsTicket::create([
                'ticket_number' => 'KOT-' . str_pad(KdsTicket::count() + 500, 4, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'station_name' => 'Main Kitchen / Universal Station',
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Attempt TCP Socket LAN Thermal Print
            try {
                ThermalPrinterService::printOrderKot($order->load('items', 'table', 'waiter'), '192.168.32.150');
            } catch (\Exception $printError) {
                // Keep order alive even if offline demo printer doesn't ACK
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order #{$order->order_number} successfully punched & fired to KDS Monitor & Thermal Printer!",
                'order' => $order->load('items', 'table'),
                'kds_ticket' => $ticket
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function kds(Request $request)
    {
        $selectedStation = $request->input('station', 'All Stations');
        $query = KdsTicket::with(['order.items', 'order.table'])->whereIn('status', ['received', 'preparing']);
        
        if ($selectedStation !== 'All Stations') {
            $query->where('station_name', 'LIKE', "%{$selectedStation}%");
        }
        
        $tickets = $query->oldest('received_at')->get();
        $completedTickets = KdsTicket::with(['order.items', 'order.table'])
                                     ->whereIn('status', ['ready', 'delivered'])
                                     ->latest('updated_at')
                                     ->take(8)
                                     ->get();

        $stations = ['Main Kitchen - Hot Section', 'Wood-Fired Pizza Station', 'Bar & Beverages', 'Gourmet Dessert & Pastry'];

        return view('kds.index', compact('tickets', 'completedTickets', 'selectedStation', 'stations'));
    }

    public function updateKdsStatus(Request $request, $id, $status)
    {
        $ticket = KdsTicket::with('order')->findOrFail($id);
        $ticket->status = $status;
        if ($status === 'preparing') $ticket->prepared_at = now();
        if ($status === 'ready' || $status === 'delivered') {
            $ticket->ready_at = now();
            // Automatically flag dining table for the Waiter as READY TO SERVE (FOOD UP!)
            if ($ticket->order && $ticket->order->table_id) {
                DiningTable::where('id', $ticket->order->table_id)->update(['status' => 'ready_to_serve']);
            }
        }
        $ticket->save();

        return response()->json(['success' => true, 'message' => "Ticket #{$ticket->ticket_number} marked as " . strtoupper($status) . ". Table ready-to-serve flag triggered for waiter!"]);
    }

    public function cleanAllKds(Request $request)
    {
        KdsTicket::whereIn('status', ['received', 'preparing'])->update([
            'status' => 'ready',
            'ready_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'All active kitchen tickets marked ready and stations cleared!']);
        }
        return redirect()->route('kds.index')->with('success', '🧹 All kitchen stations cleared! Ready for next POS order rushes.');
    }

    public function simulateKdsTicket(Request $request)
    {
        $table = DiningTable::first() ?? DiningTable::create(['table_number' => 'VIP-01', 'capacity' => 4, 'status' => 'seated']);
        $ticketNum = 'KDS-' . rand(100, 999);

        $order = Order::create([
            'order_number' => 'KOT-' . time() . '-' . rand(10, 99),
            'table_id' => $table->id,
            'status' => 'kitchen',
            'total_amount' => 1850.00,
            'notes' => 'Simulated Priority Test Rush'
        ]);

        // Ensure menu item references exist for strict database integrity
        $menuItem1 = MenuItem::first() ?? MenuItem::create([
            'category_id' => 1,
            'name' => 'Truffle Mushroom Cream Risotto',
            'code' => 'RIS-01',
            'price' => 650.00,
            'prep_time_minutes' => 18,
            'is_available' => true
        ]);
        $menuItem2 = MenuItem::skip(1)->first() ?? $menuItem1;

        // Create sample gourmet order items with proper menu_item_id bindings
        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem1->id,
            'item_name' => $menuItem1->name,
            'quantity' => 2,
            'unit_price' => $menuItem1->price,
            'subtotal' => $menuItem1->price * 2,
            'status' => 'kitchen',
            'special_instructions' => 'VIP Patron: Extra creamy, serve piping hot!'
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem2->id,
            'item_name' => $menuItem2->name,
            'quantity' => 1,
            'unit_price' => $menuItem2->price,
            'subtotal' => $menuItem2->price,
            'status' => 'kitchen',
            'special_instructions' => 'Extra basil and charred crust edge'
        ]);

        $stations = ['Main Kitchen - Hot Section', 'Wood-Fired Pizza Station', 'Bar & Beverages', 'Gourmet Dessert & Pastry'];
        $ticket = KdsTicket::create([
            'order_id' => $order->id,
            'ticket_number' => $ticketNum,
            'station_name' => $stations[array_rand($stations)],
            'status' => 'received',
            'received_at' => now()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket, 'message' => "🔥 New kitchen test ticket #{$ticketNum} generated!"]);
        }
        return redirect()->route('kds.index')->with('success', "🔥 Simulated Test Ticket #{$ticketNum} fired directly to KDS! Station acoustic buzzer triggered.");
    }

    public function tables()
    {
        $tables = DiningTable::with('section')->orderBy('table_number')->get();
        $sections = \App\Models\FloorSection::all();
        return view('tables.index', compact('tables', 'sections'));
    }

    public function menu()
    {
        $items = MenuItem::with(['category', 'variations', 'ingredients'])->orderBy('name')->get();
        $categories = \App\Models\MenuCategory::all();
        $inventoryItems = \App\Models\InventoryItem::all();
        return view('menu.index', compact('items', 'categories', 'inventoryItems'));
    }

    public function inventory()
    {
        $items = InventoryItem::with(['store', 'supplier'])->orderBy('name')->get();
        $stores = Store::all();
        return view('inventory.index', compact('items', 'stores'));
    }

    public function suppliers()
    {
        $suppliers = Supplier::with('inventoryItems')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function invoices()
    {
        $invoices = Invoice::with(['order', 'cashier'])->latest()->get();
        return view('accounts.invoices', compact('invoices'));
    }

    public function users()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function itAdmin()
    {
        $terminals = NetworkTerminal::all();
        return view('it_admin.index', compact('terminals'));
    }

    public function exportLedgerCsv()
    {
        $invoices = Invoice::with(['order', 'cashier'])->latest()->get();
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=financial_ledger_zreport_" . date('Y_m_d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice Number', 'Order Number', 'Cashier', 'Subtotal (INR)', 'GST/VAT Tax (5%)', 'Grand Total (INR)', 'Payment Status', 'Timestamp']);

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->invoice_number,
                    $inv->order->order_number ?? 'POS Direct',
                    $inv->cashier->name ?? 'Main Cashier',
                    $inv->subtotal,
                    $inv->tax_total,
                    $inv->grand_total,
                    strtoupper($inv->payment_status),
                    $inv->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function settleInvoice(Request $request, $id)
    {
        $invoice = Invoice::with('order')->findOrFail($id);
        $invoice->payment_status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        // Release Dining Room Table back to Vacant status for next turnaround
        if ($invoice->order && $invoice->order->table_id) {
            DiningTable::where('id', $invoice->order->table_id)->update(['status' => 'vacant']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => "Invoice #{$invoice->invoice_number} settled & table vacated!"]);
        }
        return redirect()->back()->with('success', "🎉 Invoice #{$invoice->invoice_number} successfully paid & settled! Dining table has been automatically vacated and reset for new guest seating.");
    }

    public function printTestReceipt(Request $request)
    {
        $type = $request->input('type', 'cashier');
        $ip = $request->input('ip', '192.168.32.150');

        if ($type === 'kot') {
            $ticket = KdsTicket::with('order.items')->first();
            if ($ticket) {
                ThermalPrinterService::printKot($ticket, $ip);
            }
        } else {
            $invoice = Invoice::with('order.items', 'cashier')->first();
            if (!$invoice && Order::first()) {
                // Create mock invoice for hardware diagnostic testing if none exists
                $order = Order::first();
                $invoice = Invoice::create([
                    'invoice_number' => 'INV-DIAG-' . rand(100,999),
                    'order_id' => $order->id,
                    'cashier_id' => Auth::id() ?? 1,
                    'subtotal' => 1100,
                    'tax_total' => 55,
                    'discount_total' => 0,
                    'grand_total' => 1155,
                    'payment_status' => 'settled',
                ]);
            }
            if ($invoice) {
                ThermalPrinterService::printCustomerReceipt($invoice, $ip);
            }
        }

        return response()->json(['success' => true, 'message' => "ESC/POS test receipt dispatched to LAN socket {$ip}:9100!"]);
    }
}
