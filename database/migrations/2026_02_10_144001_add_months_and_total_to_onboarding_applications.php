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
        Schema::table('onboarding_applications', function (Blueprint $table) {
            $table->integer('months_paid')->default(1)->after('monthly_fee');
            $table->decimal('total_amount', 12, 2)->nullable()->after('months_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_applications', function (Blueprint $table) {
            $table->dropColumn(['months_paid', 'total_amount']);
        });
    }
};
