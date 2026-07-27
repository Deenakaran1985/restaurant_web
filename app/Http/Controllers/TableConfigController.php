<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningTable;
use App\Models\FloorSection;
use App\Models\HotelSetting;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class TableConfigController extends Controller
{
    /**
     * Display Dining Table Configuration, Capacity Allocation & QR Management Hub
     */
    public function index()
    {
        $settings = HotelSetting::current();
        $tables = DiningTable::with('section')->orderBy('table_number')->get();
        $sections = FloorSection::all();

        // If no sections exist, create defaults
        if ($sections->isEmpty()) {
            $sectionNames = ['Main Hall (Indoor Dining)', 'Sunset Rooftop Lounge', 'Garden Poolside Pavilion', 'VIP Presidential Enclosure'];
            foreach ($sectionNames as $sName) {
                FloorSection::create(['name' => $sName, 'description' => "Dedicated luxury service area: {$sName}"]);
            }
            $sections = FloorSection::all();
        }

        $configStats = [
            'total_tables' => $tables->count(),
            'total_seating_capacity' => $tables->sum('capacity'),
            'vacant_ready_slots' => $tables->where('status', 'vacant')->count(),
            'active_sections' => $sections->count(),
        ];

        return view('tables.config', compact('tables', 'sections', 'configStats', 'settings'));
    }

    /**
     * Create & Register New Dining Table Slot
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:dining_tables,table_number',
            'capacity' => 'required|integer|min:1|max:100',
            'section_id' => 'nullable|exists:floor_sections,id',
        ]);

        $section = FloorSection::find($request->input('section_id')) ?? FloorSection::first();

        $table = DiningTable::create([
            'section_id' => $section ? $section->id : 1,
            'table_number' => strtoupper($request->input('table_number')),
            'capacity' => intval($request->input('capacity')),
            'status' => $request->input('status', 'vacant'),
        ]);

        return redirect()->back()->with('success', "🍽️ New Table Slot ({$table->table_number}) created successfully with {$table->capacity} guest seating slots under {$section->name}!");
    }

    /**
     * Update Table Slot Details (Capacity, Identifier & Floor Zone)
     */
    public function update(Request $request, $id)
    {
        $table = DiningTable::findOrFail($id);

        $request->validate([
            'table_number' => 'required|string|unique:dining_tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:100',
            'section_id' => 'required|exists:floor_sections,id',
            'status' => 'required|string'
        ]);

        $table->table_number = strtoupper($request->input('table_number'));
        $table->capacity = intval($request->input('capacity'));
        $table->section_id = intval($request->input('section_id'));
        $table->status = $request->input('status');
        $table->save();

        return redirect()->back()->with('success', "⚙️ Table Slot ({$table->table_number}) configuration & seating capacity successfully updated!");
    }

    /**
     * Quick-toggle Table Operational Status (Vacant / Seated / Ordered / Billed / Reserved / Maintenance)
     */
    public function updateStatus(Request $request, $id)
    {
        $table = DiningTable::findOrFail($id);
        $newStatus = $request->input('status', 'vacant');
        
        $table->status = $newStatus;
        $table->save();

        return redirect()->back()->with('success', "⚡ Table ({$table->table_number}) turnaround state updated to " . strtoupper($newStatus) . "!");
    }

    /**
     * Waiter marks food served and automatically generates & fires Final Invoice to Cashier Billing Desk
     */
    public function serveAndInvoice($id)
    {
        $table = DiningTable::findOrFail($id);
        $table->status = 'billed';
        $table->save();

        // Find active table order or create fallback formal dining record
        $order = Order::where('table_id', $table->id)->latest()->first();
        if (!$order) {
            $order = Order::create([
                'order_number' => 'ORD-' . str_pad(Order::count() + 100, 4, '0', STR_PAD_LEFT),
                'table_id' => $table->id,
                'waiter_id' => Auth::id() ?: 1,
                'status' => 'served',
                'order_type' => 'dine_in',
                'subtotal' => 1200.00,
                'tax_amount' => 60.00,
                'discount_amount' => 0.00,
                'total_amount' => 1260.00,
                'notes' => 'Waiter automated service & billing'
            ]);
        } else {
            $order->update(['status' => 'served']);
        }

        // Check if invoice already generated for this order
        $invoice = Invoice::where('order_id', $order->id)->first();
        if (!$invoice) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . str_pad(Invoice::count() + 1000, 5, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'cashier_id' => Auth::id() ?: 1,
                'subtotal' => $order->subtotal ?: 1200.00,
                'tax_total' => $order->tax_amount ?: 60.00,
                'discount_total' => $order->discount_amount ?: 0.00,
                'grand_total' => $order->total_amount ?: 1260.00,
                'payment_status' => 'unpaid'
            ]);
        }

        return redirect()->back()->with('success', "🍽️ Food marked Served for Table {$table->table_number}! Final Invoice #{$invoice->invoice_number} (₹" . number_format($invoice->grand_total, 2) . ") generated & fired directly to Cashier Billing Desk!");
    }

    /**
     * Decommission / Delete Table Slot
     */
    public function destroy($id)
    {
        $table = DiningTable::findOrFail($id);
        $num = $table->table_number;
        $table->delete();

        return redirect()->back()->with('success', "🗑️ Table Slot ({$num}) has been decommissioned from the dining room floor plan.");
    }
}
