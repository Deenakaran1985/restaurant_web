<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waste_logs', function (Blueprint $table) {
            $table->id();
            $table->string('incident_reference')->unique();
            $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->decimal('quantity', 10, 3);
            $table->string('unit')->default('kg');
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->decimal('total_loss', 10, 2)->default(0.00);
            $table->string('reason');
            $table->string('station');
            $table->string('logged_by')->default('Executive Audit Manager');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_logs');
    }
};
