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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('support_sessions')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // Celui qui laisse le feedback
            $table->foreignId('reviewed_id')->constrained('users')->onDelete('cascade'); // Celui qui reçoit le feedback
            $table->integer('rating'); // 1-5 étoiles
            $table->text('comment')->nullable();
            $table->enum('type', ['student_to_tutor', 'tutor_to_student']);
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
