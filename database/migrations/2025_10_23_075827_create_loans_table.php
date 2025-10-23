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
        // database/migrations/xxxx_xx_xx_create_loans_table.php
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('loan_name');
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2); // Annual interest rate
            $table->integer('term_months');
            $table->decimal('monthly_payment', 10, 2);
            $table->date('start_date');
            $table->decimal('remaining_balance', 12, 2);
            $table->string('lender_name');
            $table->text('purpose')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
