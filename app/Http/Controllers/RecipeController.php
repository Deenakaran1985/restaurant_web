<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\InventoryItem;
use App\Models\RecipeMapping;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * Store Brand New Dish Recipe & Explicitly Create MySQL Recipe Mapping Rows
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:menu_categories,id',
        ]);

        // Auto-generate Dish Code if blank
        $code = $request->input('code');
        if (empty($code)) {
            $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->input('name')), 0, 4)) . '-' . rand(100, 999);
        }

        $dish = MenuItem::create([
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'code' => strtoupper($code),
            'description' => $request->input('description', 'Artisanal chef gourmet preparation with zero-leakage recipe COGS tracking.'),
            'price' => floatval($request->input('price')),
            'prep_time_minutes' => intval($request->input('prep_time_minutes', 15)),
            'is_available' => true,
        ]);

        // Explicitly create database rows in recipe_mappings table
        $ingredientIds = $request->input('ingredient_id', []);
        $quantities = $request->input('quantity_needed', []);
        $mappedCount = 0;

        if (is_array($ingredientIds) && count($ingredientIds) > 0) {
            foreach ($ingredientIds as $index => $ingId) {
                if (!empty($ingId)) {
                    $qty = isset($quantities[$index]) && !empty($quantities[$index]) ? floatval($quantities[$index]) : 0.150;
                    RecipeMapping::create([
                        'menu_item_id' => $dish->id,
                        'inventory_item_id' => intval($ingId),
                        'quantity_needed' => $qty,
                    ]);
                    $mappedCount++;
                }
            }
        }

        // If user submitted without picking specific ingredients, auto-link first available ingredient as proof-of-concept
        if ($mappedCount == 0) {
            $defaultInv = InventoryItem::first();
            if ($defaultInv) {
                RecipeMapping::create([
                    'menu_item_id' => $dish->id,
                    'inventory_item_id' => $defaultInv->id,
                    'quantity_needed' => 0.200,
                ]);
                $mappedCount = 1;
            }
        }

        return redirect()->route('menu.index')->with('success', "👨‍🍳 New Gourmet Dish Recipe '{$dish->name}' ({$dish->code}) created in database! Linked to {$mappedCount} raw materials in central warehouse for automated COGS deduction.");
    }

    /**
     * Adjust Existing Dish Recipe Ingredients & Portion Weights
     */
    public function adjust(Request $request, $id)
    {
        $dish = MenuItem::findOrFail($id);

        // Remove old recipe mapping records for clean overwrite
        RecipeMapping::where('menu_item_id', $dish->id)->delete();

        $ingredientIds = $request->input('ingredient_id', []);
        $quantities = $request->input('quantity_needed', []);
        $mappedCount = 0;

        if (is_array($ingredientIds)) {
            foreach ($ingredientIds as $index => $ingId) {
                if (!empty($ingId)) {
                    $qty = isset($quantities[$index]) && !empty($quantities[$index]) ? floatval($quantities[$index]) : 0.150;
                    RecipeMapping::create([
                        'menu_item_id' => $dish->id,
                        'inventory_item_id' => intval($ingId),
                        'quantity_needed' => $qty,
                    ]);
                    $mappedCount++;
                }
            }
        }

        if ($request->has('price') && !empty($request->input('price'))) {
            $dish->price = floatval($request->input('price'));
        }
        if ($request->has('prep_time_minutes') && !empty($request->input('prep_time_minutes'))) {
            $dish->prep_time_minutes = intval($request->input('prep_time_minutes'));
        }
        $dish->save();

        return redirect()->route('menu.index')->with('success', "⚙️ Recipe formula for '{$dish->name}' updated! Now mapped to {$mappedCount} raw ingredients with updated pricing & kitchen prep SLA timer.");
    }

    /**
     * Register New Gourmet Menu Category in Database
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:menu_categories,name|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $category = MenuCategory::create([
            'name' => trim($request->input('name')),
            'icon' => $request->input('icon', 'bi-cup-hot'),
            'sort_order' => intval($request->input('sort_order', MenuCategory::count() + 1)),
            'is_active' => true,
        ]);

        return redirect()->route('menu.index')->with('success', "📑 Menu Category '{$category->name}' added to your master database! You can now link dishes and recipes directly to this category.");
    }
}
