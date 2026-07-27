<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\HotelSetting;
use App\Services\ThermalPrinterService;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display Cloud Kitchen & Delivery Aggregator Dispatch Hub
     */
    public function index()
    {
        $settings = HotelSetting::current();

        // Retrieve orders that originated from online delivery platforms
        $orders = Order::with('items')->whereIn('order_type', ['ZOMATO GOLD', 'SWIGGY ONE', 'UBER EATS', 'WHATSAPP DIRECT'])
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        // If demo delivery stream empty, generate realistic active cloud kitchen tickets
        if ($orders->isEmpty()) {
            $pizza = MenuItem::where('code', 'PZ-01')->first() ?? MenuItem::first();
            
            $demoPlatforms = [
                ['platform' => 'ZOMATO GOLD', 'id' => 'ZOM-99410', 'rider' => 'Vikram Singh (MH-02-EL-8831) | PIN: 4491 | Payment: Online Platform Paid (Razorpay/UPI)', 'status' => 'Out for Delivery'],
                ['platform' => 'SWIGGY ONE', 'id' => 'SWG-77215', 'rider' => 'Deepak V (DL-01-TC-9104) | PIN: 8102 | Payment: Online Platform Paid (Swiggy Wallet)', 'status' => 'Ready for Rider Pickup'],
                ['platform' => 'UBER EATS', 'id' => 'UBR-55102', 'rider' => 'Searching for Courier... | PIN: 1105 | Payment: Online Card Paid', 'status' => 'Preparing in Kitchen KDS'],
                ['platform' => 'WHATSAPP DIRECT', 'id' => 'WSP-10901', 'rider' => 'Hotel In-House Fleet (Staff #4) | PIN: 9002 | Payment: Cash on Delivery (COD)', 'status' => 'Received & Confirmed'],
            ];

            foreach ($demoPlatforms as $idx => $dp) {
                $o = Order::create([
                    'order_number' => $dp['id'],
                    'order_type' => $dp['platform'],
                    'notes' => $dp['rider'],
                    'subtotal' => 650.00 + ($idx * 120),
                    'tax_amount' => 32.50 + ($idx * 6),
                    'total_amount' => 682.50 + ($idx * 126),
                    'status' => $dp['status'],
                ]);
                
                if ($pizza) {
                    OrderItem::create([
                        'order_id' => $o->id,
                        'menu_item_id' => $pizza->id,
                        'item_name' => $pizza->name . ' [' . $dp['platform'] . ' Special Packaging]',
                        'unit_price' => $pizza->price,
                        'quantity' => 1 + ($idx % 2),
                        'subtotal' => $pizza->price * (1 + ($idx % 2)),
                    ]);
                }
            }
            
            $orders = Order::with('items')->whereIn('order_type', ['ZOMATO GOLD', 'SWIGGY ONE', 'UBER EATS', 'WHATSAPP DIRECT'])
                ->orderByDesc('created_at')
                ->take(15)
                ->get();
        }

        $dispatchStats = [
            'active_deliveries' => $orders->count(),
            'total_delivery_revenue' => $orders->sum('total_amount'),
            'avg_prep_sla' => '14.2 Mins',
            'on_time_pickup_pct' => 98.4,
            'zomato_share' => '42%',
            'swiggy_share' => '38%',
            'direct_share' => '20%',
        ];

        return view('delivery.index', compact('orders', 'dispatchStats', 'settings'));
    }

    /**
     * Simulate Incoming Live Webhook Order from Food Aggregators (Zomato/Swiggy)
     */
    public function simulateWebhook(Request $request)
    {
        $platform = $request->input('platform', 'ZOMATO GOLD');
        $orderNumber = substr(str_replace(' ', '', $platform), 0, 3) . '-' . rand(10000, 99999);
        
        $menuItem = MenuItem::with('ingredients')->inRandomOrder()->first();
        if (!$menuItem) {
            return redirect()->route('delivery.index')->with('error', 'No menu items found in database.');
        }

        $qty = rand(1, 3);
        $subtotal = $menuItem->price * $qty;
        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        $riders = [
            'Suresh M (KA-01-EQ-4421) | PIN: 4412 | Payment: Online Platform Paid (UPI / Wallets)',
            'Amit Patel (GJ-05-AB-9912) | PIN: 9811 | Payment: Online Platform Paid (Razorpay)',
            'Mohammed K (TN-09-XY-1209) | PIN: 1205 | Payment: Cash on Delivery (COD)',
            'Hotel Delivery Fleet Courier #2 | PIN: 5500 | Payment: Online Paid'
        ];

        $order = Order::create([
            'order_number' => $orderNumber,
            'order_type' => $platform,
            'notes' => $riders[array_rand($riders)],
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'Preparing in Kitchen KDS',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'item_name' => $menuItem->name . ' [Eco-friendly Tamper Proof Box]',
            'unit_price' => $menuItem->price,
            'quantity' => $qty,
            'subtotal' => $subtotal,
        ]);

        // Trigger Automated Inventory COGS Store Deduction
        if ($menuItem->ingredients && $menuItem->ingredients->count() > 0) {
            foreach ($menuItem->ingredients as $ing) {
                $qtyNeeded = $ing->pivot->quantity_needed ?? 0;
                $ing->decrement('current_stock', $qtyNeeded * $qty);
            }
        }

        // Trigger Optional KDS Direct Thermal Socket Printing over port 9100
        $printerMsg = "Dispatched to KDS Screen.";
        try {
            $printer = app(ThermalPrinterService::class);
            $printer->printOrderKot($order);
            $printerMsg = "ESC/POS receipt automatically broadcast via TCP Port 9100 to Kitchen LAN Thermal Printer and store raw material deducted!";
        } catch (\Exception $e) {
            $printerMsg = "Order logged in KDS stream (LAN Socket offline fallback).";
        }

        return redirect()->route('delivery.index')->with('success', "🚀 Simulated Live {$platform} Order ({$orderNumber}) received! {$printerMsg}");
    }
}
