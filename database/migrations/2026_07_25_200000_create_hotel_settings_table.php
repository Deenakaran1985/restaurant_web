<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name')->default('Antigravity Grand Hotel & Gourmet Suite');
            $table->string('branch_code')->default('BR-MAIN-01');
            $table->string('contact_phone')->default('+91 98765 43210');
            $table->string('contact_email')->default('management@antigravityhotel.com');
            $table->string('gst_vat_number')->default('29AAFGA1234F1Z9');
            $table->string('currency_symbol')->default('₹');
            $table->decimal('default_tax_rate', 5, 2)->default(5.00);
            
            // KDS & Thermal Kitchen Printing Toggles
            $table->boolean('kds_enabled')->default(false); // Optional KDS default false
            $table->string('kds_routing_mode')->default('thermal_printer_only'); // 'thermal_printer_only' or 'screen_interactive'
            $table->string('kitchen_printer_ip')->default('192.168.32.151');
            $table->integer('kitchen_printer_port')->default(9100);
            $table->string('cashier_printer_ip')->default('192.168.32.150');
            $table->boolean('auto_deduct_inventory_on_print')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_settings');
    }
};
