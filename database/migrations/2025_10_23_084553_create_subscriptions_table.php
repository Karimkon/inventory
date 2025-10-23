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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->enum('plan_type', ['retail', 'wholesale', 'hardware', 'supermarket']);
            $table->decimal('activation_fee', 12, 2);
            $table->decimal('monthly_fee', 12, 2);
            $table->boolean('is_active')->default(false);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('pesapal_tracking_id')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index('shop_id');
            $table->index('pesapal_tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};