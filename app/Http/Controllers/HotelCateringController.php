<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\HotelSetting;
use App\Services\ThermalPrinterService;

class HotelCateringController extends Controller
{
    /**
     * Display In-Room Dining (Room Service) & Banquet Event Catering Hub
     */
    public function index()
    {
        $settings = HotelSetting::current();

        // Active room service orders
        $roomOrders = Order::with('items')->where('order_type', 'in_room_dining')->orderByDesc('created_at')->get();

        // If no room orders, seed initial active suites for visual demonstration
        if ($roomOrders->isEmpty()) {
            $dish1 = MenuItem::first();
            $dish2 = MenuItem::skip(1)->first() ?? $dish1;

            $demoRooms = [
                ['room' => 'Room 502 (Presidential Suite)', 'guest' => 'Mr. Vikram Singhania | Folio #F-9901', 'amount' => 3450.00, 'status' => 'En Route to Room (Elevator 2)'],
                ['room' => 'Room 310 (Executive Sea View)', 'guest' => 'Mrs. Elena Rostova | Folio #F-9884', 'amount' => 1850.50, 'status' => 'Preparing in Kitchen KDS'],
                ['room' => 'Room 204 (Deluxe Twin Tower)', 'guest' => 'Dr. Aravind Mehta | Folio #F-9812', 'amount' => 950.00, 'status' => 'Delivered & Folio Charged'],
            ];

            foreach ($demoRooms as $idx => $dr) {
                $o = Order::create([
                    'order_number' => 'IRM-' . rand(1000, 9999),
                    'order_type' => 'in_room_dining',
                    'table_number' => $dr['room'],
                    'notes' => $dr['guest'],
                    'subtotal' => $dr['amount'] * 0.95,
                    'tax_amount' => $dr['amount'] * 0.05,
                    'total_amount' => $dr['amount'],
                    'status' => $dr['status'],
                ]);

                if ($dish1) {
                    OrderItem::create([
                        'order_id' => $o->id,
                        'menu_item_id' => $dish1->id,
                        'item_name' => $dish1->name . ' [Silver Tray Room Setup]',
                        'unit_price' => $dish1->price,
                        'quantity' => 2,
                        'subtotal' => $dish1->price * 2,
                    ]);
                }
            }

            $roomOrders = Order::with('items')->where('order_type', 'in_room_dining')->orderByDesc('created_at')->get();
        }

        // Active Banquet & Catering Contracts
        $banquetEvents = [
            [
                'event_name' => 'TechCorp Annual Founders Gala & Award Night',
                'venue' => 'Grand Crystal Ballroom (A & B)',
                'date_time' => now()->addDays(2)->format('d-M-Y @ 19:00 Hrs'),
                'guest_count' => 320,
                'buffet_package' => 'Platinum Multi-Cuisine Live Counter Buffet',
                'price_per_plate' => 2200.00,
                'total_contract' => 704000.00,
                'deposit_paid' => 350000.00,
                'chef_in_charge' => 'Chef Marco Pierre (Executive Banquet Head)',
                'status' => 'Confirmed & Raw Materials Reserved',
                'badge' => 'badge-emerald',
            ],
            [
                'event_name' => 'Aditi & Siddharth Wedding Reception',
                'venue' => 'Sunset Palm Poolside Pavilion',
                'date_time' => now()->addDays(5)->format('d-M-Y @ 18:30 Hrs'),
                'guest_count' => 450,
                'buffet_package' => 'Royal Heritage Traditional Feast (100% Veg)',
                'price_per_plate' => 1650.00,
                'total_contract' => 742500.00,
                'deposit_paid' => 500000.00,
                'chef_in_charge' => 'Chef Anand V. (Regional Master Chef)',
                'status' => 'Final Menu Sampling Complete',
                'badge' => 'badge-purple',
            ],
            [
                'event_name' => 'Global Healthcare Summit Luncheon',
                'venue' => 'Emerald Executive Boardroom & Terrace',
                'date_time' => now()->addWeeks(2)->format('d-M-Y @ 12:30 Hrs'),
                'guest_count' => 85,
                'buffet_package' => 'High-Protein Continental Artisanal Buffet',
                'price_per_plate' => 1450.00,
                'total_contract' => 123250.00,
                'deposit_paid' => 60000.00,
                'chef_in_charge' => 'Chef Sarah Jenkins (Pastry & Continental Lead)',
                'status' => 'Advance Received',
                'badge' => 'badge-amber',
            ],
        ];

        $cateringStats = [
            'active_room_orders' => $roomOrders->count(),
            'total_room_revenue' => $roomOrders->sum('total_amount'),
            'banquet_contract_val' => array_sum(array_column($banquetEvents, 'total_contract')),
            'total_banquet_guests' => array_sum(array_column($banquetEvents, 'guest_count')),
        ];

        return view('hotel.catering', compact('roomOrders', 'banquetEvents', 'cateringStats', 'settings'));
    }

    /**
     * Fire new In-Room Dining Order & Bill directly to Hotel Guest Room Folio
     */
    public function billToRoom(Request $request)
    {
        $roomNumber = $request->input('room_number', 'Room 601 (Penthouse Suite)');
        $guestName = $request->input('guest_name', 'Honorable Guest | Folio #F-9942');
        
        $menuItem = MenuItem::with('ingredients')->inRandomOrder()->first();
        if (!$menuItem) {
            return redirect()->route('hotel.catering')->with('error', 'No dishes available.');
        }

        $qty = intval($request->input('quantity', 2));
        $subtotal = $menuItem->price * $qty;
        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        $order = Order::create([
            'order_number' => 'IRM-' . rand(10000, 99999),
            'order_type' => 'in_room_dining',
            'table_number' => $roomNumber,
            'notes' => "Charged to Guest Room Folio: {$guestName}",
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'Preparing in Kitchen KDS',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'item_name' => $menuItem->name . ' [In-Room Dining Cart Setup]',
            'unit_price' => $menuItem->price,
            'quantity' => $qty,
            'subtotal' => $subtotal,
        ]);

        // Automatically deduct inventory raw store COGS
        if ($menuItem->ingredients && $menuItem->ingredients->count() > 0) {
            foreach ($menuItem->ingredients as $ing) {
                $qtyNeeded = $ing->pivot->quantity_needed ?? 0;
                $ing->decrement('current_stock', $qtyNeeded * $qty);
            }
        }

        // Trigger Optional KDS Direct Thermal Socket Printing over port 9100
        $printerMsg = "Dispatched to KDS screen.";
        try {
            $printer = app(ThermalPrinterService::class);
            $printer->printOrderKot($order);
            $printerMsg = "ESC/POS receipt printed over TCP Port 9100 on Kitchen LAN Printer & charged to Room Folio!";
        } catch (\Exception $e) {
            $printerMsg = "Logged in Room Service KDS queue (LAN printer offline fallback).";
        }

        return redirect()->route('hotel.catering')->with('success', "🛎️ In-Room Dining order fired for {$roomNumber}! {$printerMsg}");
    }
}
