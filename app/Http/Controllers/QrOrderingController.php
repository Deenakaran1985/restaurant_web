<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\KdsTicket;
use App\Models\HotelSetting;
use App\Services\ThermalPrinterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QrOrderingController extends Controller
{
    /**
     * Display Guest Mobile Touch Self-Ordering Portal for a Specific Table
     */
    public function showTableMenu($table_id)
    {
        $table = DiningTable::with('section')->findOrFail($table_id);
        $categories = MenuCategory::with(['items' => function($q) {
            $q->where('is_available', true);
        }])->where('is_active', true)->orderBy('sort_order')->get();
        
        $settings = HotelSetting::current();

        return view('qr.menu', compact('table', 'categories', 'settings'));
    }

    /**
     * Process Guest Table-side Order & Route via KDS / Thermal Printer
     */
    public function placeGuestOrder(Request $request, $table_id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'guest_name' => 'nullable|string|max:100',
        ]);

        $table = DiningTable::findOrFail($table_id);
        $settings = HotelSetting::current();

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->order_number = 'ORD-' . str_pad(Order::count() + 100, 4, '0', STR_PAD_LEFT);
            $order->table_id = $table->id;
            $order->waiter_id = null; // Self-ordered by customer via QR scan
            $order->status = 'placed';
            $order->order_type = 'dine_in';
            $order->notes = "Guest QR Self-Order | Patron Name: " . ($request->guest_name ?? 'Guest');
            $order->subtotal = 0;
            $order->tax_amount = 0;
            $order->discount_amount = 0;
            $order->total_amount = 0;
            $order->save();

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                if (intval($itemData['quantity']) <= 0) continue;

                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $itemPrice = $menuItem->price;
                $lineTotal = $itemPrice * intval($itemData['quantity']);
                $totalAmount += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'quantity' => intval($itemData['quantity']),
                    'unit_price' => $itemPrice,
                    'subtotal' => $lineTotal,
                    'status' => 'placed',
                    'special_instructions' => $itemData['notes'] ?? null,
                ]);
            }

            if ($totalAmount == 0) {
                throw new \Exception("Cart is empty. Please select at least one item quantity.");
            }

            $taxRate = $settings->default_tax_rate / 100;
            $taxAmount = round($totalAmount * $taxRate, 2);
            $order->subtotal = $totalAmount;
            $order->tax_amount = $taxAmount;
            $order->total_amount = $totalAmount + $taxAmount;
            $order->save();

            // Mark Table Status as Ordered
            $table->update(['status' => 'ordered']);

            // Intelligent KDS Routing based on Hotel Settings Toggle
            if ($settings->kds_routing_mode === 'thermal_printer_only' || !$settings->kds_enabled) {
                // Direct Kitchen Thermal LAN Printer Order Receipt (KDS Optional/Disabled)
                ThermalPrinterService::printOrderKot($order->load('items', 'table', 'waiter'), $settings->kitchen_printer_ip);
                
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
                // Interactive Digital KDS Monitor Ticket Creation
                KdsTicket::create([
                    'ticket_number' => 'KOT-' . str_pad(KdsTicket::count() + 500, 4, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'station_name' => 'Main Kitchen / Universal Station (QR Guest)',
                    'status' => 'received',
                    'received_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your dining order has been transmitted directly to our executive chef team.',
                'order' => $order->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
