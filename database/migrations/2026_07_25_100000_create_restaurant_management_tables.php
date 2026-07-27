<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Stores & Inventory Management
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('manager_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('gst_vat_number')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('unit'); // kg, liter, g, ml, pcs
            $table->decimal('current_stock', 10, 3)->default(0);
            $table->decimal('min_alert_stock', 10, 3)->default(5);
            $table->decimal('unit_cost', 10, 2)->default(0); // For COGS calculation
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // purchase, deduction, transfer, manual_adjustment
            $table->decimal('quantity_changed', 10, 3);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->string('reference_number')->nullable(); // Order number or Supplier invoice
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wastage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('quantity', 10, 3);
            $table->string('reason'); // expired, spillage, quality, burnt
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Menu, Variations & Recipe Mapping (COGS)
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(5.00); // GST / VAT default 5%
            $table->string('image_url')->nullable();
            $table->integer('prep_time_minutes')->default(15); // SLA Target Timer for KDS
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_item_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->string('name'); // e.g. Half / Full, Small / Large
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('recipe_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity_needed', 10, 4); // Exact quantity deducted per order served
            $table->timestamps();
        });

        // 3. Floor & Dining Table Management
        Schema::create('floor_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Main Hall, VIP, Rooftop, Garden
            $table->timestamps();
        });

        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_section_id')->nullable()->constrained('floor_sections')->onDelete('set null');
            $table->string('table_number');
            $table->integer('capacity')->default(4);
            $table->string('status')->default('vacant'); // vacant, seated, ordered, billed, reserved, cleaning
            $table->unsignedBigInteger('current_order_id')->nullable();
            $table->timestamps();
        });

        // 4. POS Orders & Order Items
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. ORD-2026-0001
            $table->foreignId('table_id')->nullable()->constrained('dining_tables')->onDelete('set null');
            $table->foreignId('waiter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_type')->default('dine_in'); // dine_in, takeaway, delivery, qr_self_order
            $table->string('status')->default('placed'); // placed, kitchen_preparing, kitchen_ready, served, billed, closed, cancelled
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('variation_id')->nullable()->constrained('menu_item_variations')->onDelete('set null');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('status')->default('queued'); // queued, cooking, ready, served, cancelled
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });

        // 5. Invoicing & Accounting Suite
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g. INV-2026-0001
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_total', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('payment_status')->default('unpaid'); // unpaid, part_paid, paid, refunded
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_method'); // cash, card, upi_qr, split
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference')->nullable();
            $table->timestamps();
        });

        // 6. KDS (Kitchen Display System) Tickets & Logs
        Schema::create('kds_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('ticket_number'); // e.g. KOT-101
            $table->string('station_name')->default('Main Kitchen'); // Main Kitchen, Grill, Dessert, Bar
            $table->string('status')->default('received'); // received, preparing, ready, delivered
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamps();
        });

        // 7. IT Admin Hardware Security & Printer Configuration
        Schema::create('network_terminals', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_name'); // POS Terminal 1, KDS Kitchen iPad, Waiter Tablet 3
            $table->string('ip_address')->unique();
            $table->string('terminal_type'); // pos, kds, waiter, admin
            $table->boolean('is_authorized')->default(true);
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });

        Schema::create('printer_configs', function (Blueprint $table) {
            $table->id();
            $table->string('printer_name'); // Main Cashier Thermal Printer, Kitchen KOT Printer
            $table->string('ip_address');
            $table->integer('port')->default(9100);
            $table->string('paper_width')->default('80mm'); // 80mm or 58mm
            $table->string('target_section')->default('invoice'); // invoice, kitchen_kot, bar_kot
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_configs');
        Schema::dropIfExists('network_terminals');
        Schema::dropIfExists('kds_tickets');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('dining_tables');
        Schema::dropIfExists('floor_sections');
        Schema::dropIfExists('recipe_mappings');
        Schema::dropIfExists('menu_item_variations');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('wastage_logs');
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('stores');
    }
};
