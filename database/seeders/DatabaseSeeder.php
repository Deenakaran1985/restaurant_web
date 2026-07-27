<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\RecipeMapping;
use App\Models\FloorSection;
use App\Models\DiningTable;
use App\Models\NetworkTerminal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create All User Roles
        $password = Hash::make('password123');

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@restaurant.com', 'role' => 'superadmin', 'pin' => '1111'],
            ['name' => 'General Manager', 'email' => 'admin@restaurant.com', 'role' => 'admin', 'pin' => '2222'],
            ['name' => 'Financial Controller', 'email' => 'accounts@restaurant.com', 'role' => 'accounts', 'pin' => '3333'],
            ['name' => 'Kitchen Manager', 'email' => 'kitchen@restaurant.com', 'role' => 'kitchenmanager', 'pin' => '4444'],
            ['name' => 'Executive Chef', 'email' => 'chef@restaurant.com', 'role' => 'chef', 'pin' => '5555'],
            ['name' => 'Network & IT Admin', 'email' => 'it@restaurant.com', 'role' => 'itadmin', 'pin' => '6666'],
            ['name' => 'Lead Waiter', 'email' => 'waiter@restaurant.com', 'role' => 'waiter', 'pin' => '7777'],
            ['name' => 'Main Cashier', 'email' => 'cashier@restaurant.com', 'role' => 'cashier', 'pin' => '8888'],
            ['name' => 'Store & Inventory Keeper', 'email' => 'store@restaurant.com', 'role' => 'storekeeper', 'pin' => '9999'],
            ['name' => 'Guest Customer', 'email' => 'guest@restaurant.com', 'role' => 'customer', 'pin' => '0000'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => $password,
                    'role' => $u['role'],
                    'pin_code' => $u['pin'],
                    'phone' => '+91-98765432' . rand(10, 99),
                    'is_active' => true,
                ]
            );
        }

        // 2. Create Store & Supplier
        $store = Store::firstOrCreate(['name' => 'Main Central Kitchen Store'], [
            'location' => 'Block A, Ground Floor',
            'manager_name' => 'Store & Inventory Keeper',
            'is_active' => true
        ]);

        $supplier = Supplier::firstOrCreate(['name' => 'Gourmet Dairy & Farm Harvests'], [
            'contact_person' => 'Rajesh Sharma',
            'phone' => '9845012345',
            'email' => 'supplies@gourmetfarm.in',
            'address' => 'Industrial Sector 4, Bangalore',
            'gst_vat_number' => '29AAACN1234N1Z2'
        ]);

        // 3. Create Inventory Items (For Automated COGS)
        $flour = InventoryItem::firstOrCreate(['sku' => 'INV-DOUGH-01'], [
            'store_id' => $store->id, 'name' => 'Artisanal Pizza Dough Flour', 'unit' => 'kg',
            'current_stock' => 50.000, 'min_alert_stock' => 10.000, 'unit_cost' => 120.00,
            'preferred_supplier_id' => $supplier->id
        ]);
        $mozzarella = InventoryItem::firstOrCreate(['sku' => 'INV-MOZZ-01'], [
            'store_id' => $store->id, 'name' => 'Fresh Mozzarella Cheese', 'unit' => 'kg',
            'current_stock' => 35.000, 'min_alert_stock' => 5.000, 'unit_cost' => 450.00,
            'preferred_supplier_id' => $supplier->id
        ]);
        $coffee = InventoryItem::firstOrCreate(['sku' => 'INV-BEANS-01'], [
            'store_id' => $store->id, 'name' => 'Arabica Coffee Beans', 'unit' => 'kg',
            'current_stock' => 20.000, 'min_alert_stock' => 4.000, 'unit_cost' => 850.00,
            'preferred_supplier_id' => $supplier->id
        ]);

        // 4. Create Menu Categories
        $pizzaCat = MenuCategory::firstOrCreate(['name' => 'Artisanal Pizzas'], ['icon' => 'bi-pie-chart-fill', 'sort_order' => 1]);
        $burgerCat = MenuCategory::firstOrCreate(['name' => 'Gourmet Burgers'], ['icon' => 'bi-bullseye', 'sort_order' => 2]);
        $drinkCat = MenuCategory::firstOrCreate(['name' => 'Signature Beverages'], ['icon' => 'bi-cup-hot-fill', 'sort_order' => 3]);

        // 5. Create Menu Items & Variations
        $trufflePizza = MenuItem::firstOrCreate(['code' => 'PIZ-01'], [
            'category_id' => $pizzaCat->id,
            'name' => 'Truffle & Forest Mushroom Pizza',
            'description' => 'Wood-fired sourdough base topped with rich white truffle cream, wild forest mushrooms, and aged mozzarella.',
            'price' => 550.00,
            'tax_percentage' => 5.00,
            'prep_time_minutes' => 14,
            'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80'
        ]);

        MenuItemVariation::firstOrCreate(['menu_item_id' => $trufflePizza->id, 'name' => 'Regular 10"'], ['price_adjustment' => 0.00]);
        MenuItemVariation::firstOrCreate(['menu_item_id' => $trufflePizza->id, 'name' => 'Large 14" (+₹150)'], ['price_adjustment' => 150.00]);

        // Map recipe: 1 Pizza consumes 0.2 kg Dough and 0.12 kg Mozzarella
        RecipeMapping::firstOrCreate(['menu_item_id' => $trufflePizza->id, 'inventory_item_id' => $flour->id], ['quantity_needed' => 0.2000]);
        RecipeMapping::firstOrCreate(['menu_item_id' => $trufflePizza->id, 'inventory_item_id' => $mozzarella->id], ['quantity_needed' => 0.1200]);

        $burger = MenuItem::firstOrCreate(['code' => 'BUR-01'], [
            'category_id' => $burgerCat->id,
            'name' => 'Smoked Hickory Chicken Burger',
            'description' => 'Crispy fried chicken breast smothered in smoky hickory BBQ sauce with melted cheddar in a toasted brioche bun.',
            'price' => 380.00,
            'tax_percentage' => 5.00,
            'prep_time_minutes' => 12,
            'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80'
        ]);

        $latte = MenuItem::firstOrCreate(['code' => 'BEV-01'], [
            'category_id' => $drinkCat->id,
            'name' => 'Iced Hazelnut Caramel Latte',
            'description' => 'Double shot espresso infused with roasted hazelnut syrup and chilled whole milk over crystal ice.',
            'price' => 220.00,
            'tax_percentage' => 5.00,
            'prep_time_minutes' => 4,
            'image_url' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80'
        ]);

        RecipeMapping::firstOrCreate(['menu_item_id' => $latte->id, 'inventory_item_id' => $coffee->id], ['quantity_needed' => 0.0180]); // 18g coffee beans

        // 6. Create Floor Sections & Dining Tables
        $mainHall = FloorSection::firstOrCreate(['name' => 'Main Dining Lounge']);
        $rooftop = FloorSection::firstOrCreate(['name' => 'Sunset Rooftop Garden']);

        foreach (['T-01', 'T-02', 'T-03', 'T-04', 'T-05'] as $index => $tNum) {
            DiningTable::firstOrCreate(['table_number' => $tNum], [
                'floor_section_id' => $mainHall->id,
                'capacity' => ($index % 2 == 0) ? 4 : 2,
                'status' => 'vacant'
            ]);
        }

        foreach (['R-01', 'R-02', 'R-03'] as $rNum) {
            DiningTable::firstOrCreate(['table_number' => $rNum], [
                'floor_section_id' => $rooftop->id,
                'capacity' => 6,
                'status' => 'vacant'
            ]);
        }

        // 7. Authorize Local Terminal & KDS Server
        NetworkTerminal::firstOrCreate(['ip_address' => '192.168.32.249'], [
            'terminal_name' => 'Central Master POS & KDS Gateway',
            'terminal_type' => 'admin_pc',
            'is_authorized' => true,
            'last_ping_at' => now()
        ]);
    }
}
