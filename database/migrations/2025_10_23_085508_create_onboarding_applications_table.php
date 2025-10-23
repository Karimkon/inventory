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
        Schema::create('onboarding_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('business_type', ['retail', 'wholesale', 'hardware', 'supermarket']);
            $table->string('location');
            $table->decimal('activation_fee', 12, 2);
            $table->decimal('monthly_fee', 12, 2);
            $table->enum('status', ['pending', 'processing_payment', 'paid', 'payment_failed', 'approved', 'rejected'])->default('pending');
            $table->string('pesapal_tracking_id')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index('reference');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_applications');
    }
};