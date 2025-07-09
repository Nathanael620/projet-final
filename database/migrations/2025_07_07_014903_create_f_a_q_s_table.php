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
        Schema::create('f_a_q_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('question');
            $table->text('answer')->nullable();
            $table->enum('category', ['general', 'technical', 'payment', 'sessions', 'account', 'other']);
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending');
            $table->integer('votes')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_a_q_s');
    }
};
