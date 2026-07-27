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
        Schema::create('patrons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->enum('tier', ['silver', 'gold', 'platinum_vip'])->default('silver');
            $table->integer('loyalty_points')->default(100); // 100 welcome points
            $table->decimal('lifetime_spend', 10, 2)->default(0.00);
            $table->string('favorite_dish_category')->nullable();
            $table->text('dietary_notes')->nullable(); // e.g., Nut allergy, Halal, Gluten-Free
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrons');
    }
};
