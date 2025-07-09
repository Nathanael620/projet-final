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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // ID unique de la transaction
            $table->string('type'); // deposit, withdrawal, payment, refund
            $table->decimal('amount', 10, 2); // Montant en euros
            $table->decimal('balance_before', 10, 2); // Solde avant
            $table->decimal('balance_after', 10, 2); // Solde après
            $table->string('currency', 3)->default('EUR');
            $table->text('description')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();
            
            $table->index(['wallet_id', 'type']);
            $table->index(['user_id', 'type']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
}; 