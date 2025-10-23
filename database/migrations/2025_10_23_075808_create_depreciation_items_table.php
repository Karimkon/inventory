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
        // database/migrations/xxxx_xx_xx_create_depreciation_items_table.php
        Schema::create('depreciation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('asset_name'); // Equipment, Vehicle, Furniture, etc.
            $table->decimal('purchase_cost', 12, 2);
            $table->decimal('current_value', 12, 2);
            $table->decimal('depreciation_rate', 5, 2); // Annual depreciation percentage
            $table->date('purchase_date');
            $table->integer('useful_life_years');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciation_items');
    }
};
