<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\DiningTable;
use App\Models\KdsTicket;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\NetworkTerminal;
use App\Models\HotelSetting;
use App\Services\ThermalPrinterService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantApiController extends Controller
{
    /**
     * Touchscreen Fast Switch PIN Login for Flutter App
     */
    public function loginWithPin(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string',
            'terminal_ip' => 'nullable|string',
        ]);

        // Security check against IT Admin Whitelisted IP Terminals (if enforced)
        if ($request->terminal_ip) {
            $authorized = NetworkTerminal::where('ip_address', $request->terminal_ip)->exists();
            
            // Log security ping if not yet registered in IT Admin portal
            if (!$authorized && $request->terminal_ip !== '192.168.32.249' && $request->terminal_ip !== '127.0.0.1') {
                Log::warning("Unauthorized terminal API ping from IP: {$request->terminal_ip}");
            }
        }

        $user = User::where('pin_code', $request->pin_code)->where('is_active', true)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN Code. Please verify with IT Admin or Super Admin.'
            ], 401);
        }

        $token = $user->createToken('flutter-tablet-terminal')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'branch_id' => $user->branch_id,
                'email' => $user->email,
            ]
        ], 200);
    }

    /**
     * Fetch Operational Menu & Categories for POS Waiter Tablets
     */
    public function getMenu()
    {
        $categories = MenuCategory::with(['items' => function($q) {
            $q->where('is_available', true);
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Fetch Dining Room Tables & Turnaround Status
     */
    public function getTables()
    {
        $tables = DiningTable::with('section')->orderBy('table_number')->get();
        return response()->json(['success' => true, 'tables' => $tables]);
    }

    /**
     * Update Dining Table Status (Vacant / Seated / Ordered / Billed)
     */
    public function updateTableStatus(Request $request, $id)
    {
        $table = DiningTable::findOrFail($id);
        $table->status = $request->input('status', 'seated');
        $table->save();

        return response()->json(['success' => true, 'table' => $table]);
    }

    /**
     * Punch POS Order from Waiter Handheld Tablet or Main Cashier
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'nullable|exists:dining_tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
        ]);

        DB::beginTransaction();
        try {
            $user = $request->user() ?: User::where('role', 'superadmin')->first();

            // Create Master Order Record
            $order = new Order();
            $order->order_number = 'ORD-' . str_pad(Order::count() + 100, 4, '0', STR_PAD_LEFT);
            $order->table_id = $request->table_id;
            $order->waiter_id = $user ? $user->id : null;
            $order->status = 'placed';
            $order->order_type = $request->order_type;
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

            // Mark Table Occupied if Dine-In
            if ($order->table_id) {
                DiningTable::where('id', $order->table_id)->update(['status' => 'ordered']);
            }

            // Check Hotel Settings for KDS Workflow (Thermal Printer Only vs Interactive Screen Monitor)
            $settings = HotelSetting::current();
            $ticket = null;
            $workflowMessage = 'Order successfully placed!';

            if ($settings->kds_routing_mode === 'thermal_printer_only' || !$settings->kds_enabled) {
                // KDS Display is optional/disabled: Immediately print thermal order receipt via TCP socket
                ThermalPrinterService::printOrderKot($order->load('items', 'table', 'waiter'), $settings->kitchen_printer_ip);
                $workflowMessage = "Order #{$order->order_number} fired directly to Kitchen Thermal Printer [{$settings->kitchen_printer_ip}:{$settings->kitchen_printer_port}] via LAN Socket!";

                // Automatically execute COGS store ingredient deduction upon printing if enabled
                if ($settings->auto_deduct_inventory_on_print) {
                    foreach ($order->items as $orderItem) {
                        $menuItem = $orderItem->menuItem;
                        if ($menuItem && $menuItem->ingredients) {
                            foreach ($menuItem->ingredients as $ing) {
                                $qtyToSubtract = $ing->pivot->quantity_needed * $orderItem->quantity;
                                $ing->decrement('current_stock', $qtyToSubtract);
                            }
                        }
                    }
                }
            } else {
                // Generate Real-time Interactive KDS Ticket for Kitchen Screen Monitors
                $ticket = KdsTicket::create([
                    'ticket_number' => 'KOT-' . str_pad(KdsTicket::count() + 500, 4, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'station_name' => 'Main Kitchen / Universal Station',
                    'status' => 'received',
                    'received_at' => now(),
                ]);
                $workflowMessage = 'Order successfully punched & fired to Kitchen KDS Display Monitor!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $workflowMessage,
                'kds_mode' => $settings->kds_routing_mode,
                'order' => $order->load('items'),
                'kds_ticket' => $ticket,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch Live KDS Stream for Kitchen Touch Displays (Flutter App / iPad)
     */
    public function getKdsTickets()
    {
        $tickets = KdsTicket::with(['order.items', 'order.table', 'order.waiter'])
            ->whereIn('status', ['received', 'preparing'])
            ->oldest('received_at')
            ->get();

        return response()->json(['success' => true, 'tickets' => $tickets]);
    }

    /**
     * Advance KDS Prep Stage & Automate Recipe Ingredient Deductions
     */
    public function updateKdsTicketStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:received,preparing,ready,delivered']);
        $ticket = KdsTicket::with('order.items.menuItem.ingredients')->findOrFail($id);

        $previousStatus = $ticket->status;
        $ticket->status = $request->status;

        if ($request->status === 'preparing') {
            $ticket->prepared_at = now();
            if ($ticket->order) {
                $ticket->order->update(['status' => 'kitchen_preparing']);
            }
        }

        // Trigger automated recipe ingredient subtraction upon marking Ready to Serve
        if (($request->status === 'ready' || $request->status === 'delivered') && $previousStatus !== 'ready' && $previousStatus !== 'delivered') {
            $ticket->ready_at = now();
            if ($ticket->order) {
                $ticket->order->update(['status' => 'served']);
                
                // Perform automated COGS store deduction
                foreach ($ticket->order->items as $orderItem) {
                    $menuItem = $orderItem->menuItem;
                    if ($menuItem && $menuItem->ingredients) {
                        foreach ($menuItem->ingredients as $ing) {
                            $qtyToSubtract = $ing->pivot->quantity_needed * $orderItem->quantity;
                            $ing->decrement('current_stock', $qtyToSubtract);
                        }
                    }
                }
            }
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => "Ticket #{$ticket->ticket_number} status updated to " . strtoupper($request->status),
            'ticket' => $ticket
        ]);
    }

    /**
     * Storekeeper Raw Material Auditing Stream
     */
    public function getInventory()
    {
        $items = InventoryItem::with(['store', 'supplier'])->orderBy('name')->get();
        return response()->json(['success' => true, 'inventory_items' => $items]);
    }

    /**
     * Adjust Raw Ingredient Stock (GRN intake from barcode scanner or wastage log)
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|in:addition,wastage,audit_override',
            'quantity' => 'required|numeric',
            'reason' => 'nullable|string'
        ]);

        $item = InventoryItem::findOrFail($id);

        if ($request->adjustment_type === 'addition') {
            $item->increment('current_stock', $request->quantity);
        } elseif ($request->adjustment_type === 'wastage') {
            $item->decrement('current_stock', $request->quantity);
        } else {
            $item->current_stock = $request->quantity;
            $item->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Stock updated for {$item->name}. Current total: {$item->current_stock} {$item->unit}",
            'item' => $item
        ]);
    }
}
