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
        // Add subscription status to shops table
        Schema::table('shops', function (Blueprint $table) {
            $table->enum('subscription_status', ['pending', 'active', 'expired'])->default('pending');
            $table->string('business_type')->nullable(); // retail, wholesale, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
