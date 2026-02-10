<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE shops MODIFY COLUMN subscription_status ENUM('pending', 'pending_payment', 'pending_approval', 'active', 'expired', 'rejected', 'inactive') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shops MODIFY COLUMN subscription_status ENUM('pending', 'active', 'expired') DEFAULT 'pending'");
    }
};
