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
        Schema::create('banquet_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('event_title');
            $table->string('client_name');
            $table->date('event_date');
            $table->string('venue_hall');
            $table->integer('guest_count');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('advance_paid', 12, 2)->default(0.00);
            $table->string('status')->default('Confirmed & Active');
            $table->text('catering_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banquet_contracts');
    }
};
