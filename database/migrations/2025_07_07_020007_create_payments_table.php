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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('support_sessions')->onDelete('cascade');
            $table->string('payment_id')->unique(); // ID unique du paiement
            $table->string('payment_method'); // stripe, paypal, wallet
            $table->string('status'); // pending, completed, failed, refunded
            $table->decimal('amount', 10, 2); // Montant en euros
            $table->string('currency', 3)->default('EUR');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 