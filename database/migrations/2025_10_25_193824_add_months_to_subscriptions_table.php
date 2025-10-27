<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('subscriptions', 'months')) {
                $table->integer('months')->default(1)->after('monthly_fee');
            }
            
            if (!Schema::hasColumn('subscriptions', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('months');
            }
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['months', 'total_amount']);
            // Don't drop expires_at as it might be used elsewhere
        });
    }
};