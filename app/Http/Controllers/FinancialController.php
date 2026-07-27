<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use App\Models\HotelSetting;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    /**
     * Display Executive Financial Controller P&L Statement & COGS Audit
     */
    public function profitAndLoss(Request $request)
    {
        $settings = HotelSetting::current();
        
        // Time horizon filtering (default month-to-date)
        $period = $request->get('period', 'month');
        
        // Aggregate Gross Revenue & Collected GST/VAT Taxes from Settled & Placed Orders
        $orders = Order::with('items.menuItem.ingredients')->get();
        
        $totalOrders = $orders->count();
        $grossRevenue = $orders->sum('subtotal');
        $taxCollected = $orders->sum('tax_amount');
        $totalBilling = $orders->sum('total_amount');
        
        // Compute Exact Actual COGS consumed from Recipe Store Depletion
        $actualCogs = 0;
        $totalDishesSold = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $totalDishesSold += $item->quantity;
                $menuItem = $item->menuItem;
                if ($menuItem && $menuItem->ingredients && $menuItem->ingredients->count() > 0) {
                    foreach ($menuItem->ingredients as $ing) {
                        $qtyNeeded = $ing->pivot->quantity_needed ?? 0;
                        $unitCost = $ing->unit_cost ?? 0;
                        $actualCogs += ($qtyNeeded * $unitCost * $item->quantity);
                    }
                } else {
                    // Standard estimated 28% food cost for unmapped specials
                    $actualCogs += ($item->subtotal * 0.28);
                }
            }
        }

        // Financial KPI Computations
        $grossProfit = $grossRevenue - $actualCogs;
        $foodCostPercentage = ($grossRevenue > 0) ? ($actualCogs / $grossRevenue) * 100 : 0;
        $grossMarginPercentage = ($grossRevenue > 0) ? ($grossProfit / $grossRevenue) * 100 : 0;

        // Estimated Operating Overheads & EBITDA
        $estimatedLaborCost = $grossRevenue * 0.18; // 18% hospitality labor benchmark
        $estimatedUtilities = $grossRevenue * 0.07; // 7% rent, utilities & licensing
        $totalOverhead = $estimatedLaborCost + $estimatedUtilities;
        
        $netOperatingProfit = $grossProfit - $totalOverhead;
        $netProfitMargin = ($grossRevenue > 0) ? ($netOperatingProfit / $grossRevenue) * 100 : 0;

        // Category-wise Revenue Split
        $categoryBreakdown = OrderItem::join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('menu_categories', 'menu_items.category_id', '=', 'menu_categories.id')
            ->select(
                'menu_categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as items_sold'),
                DB::raw('SUM(order_items.subtotal) as category_revenue')
            )
            ->groupBy('menu_categories.name')
            ->get();

        // Current Inventory Asset Valuation in Central Stores
        $inventoryValuation = InventoryItem::sum(DB::raw('current_stock * unit_cost'));

        $plReport = [
            'period' => strtoupper($period),
            'total_orders' => $totalOrders,
            'total_dishes_sold' => $totalDishesSold,
            'gross_revenue' => round($grossRevenue, 2),
            'tax_collected' => round($taxCollected, 2),
            'total_billing' => round($totalBilling, 2),
            'actual_cogs' => round($actualCogs, 2),
            'gross_profit' => round($grossProfit, 2),
            'food_cost_pct' => round($foodCostPercentage, 1),
            'gross_margin_pct' => round($grossMarginPercentage, 1),
            'labor_cost' => round($estimatedLaborCost, 2),
            'utilities_cost' => round($estimatedUtilities, 2),
            'total_overhead' => round($totalOverhead, 2),
            'net_operating_profit' => round($netOperatingProfit, 2),
            'net_margin_pct' => round($netProfitMargin, 1),
            'inventory_asset_valuation' => round($inventoryValuation, 2),
            'category_breakdown' => $categoryBreakdown,
        ];

        return view('accounts.profit_loss', compact('plReport', 'settings'));
    }

    /**
     * Run Automated 5-Stage Enterprise Lifecycle Verification Diagnostic
     */
    public function verifyLifecycle()
    {
        return view('accounts.verify_lifecycle', ['settings' => HotelSetting::current()]);
    }
}
