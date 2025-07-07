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
        Schema::create('support_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('subject', ['mathematics', 'physics', 'chemistry', 'biology', 'computer_science', 'languages', 'literature', 'history', 'geography', 'economics', 'other']);
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('type', ['online', 'in_person']);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes');
            $table->decimal('price', 8, 2);
            $table->string('meeting_link')->nullable(); // Pour les séances en ligne
            $table->string('location')->nullable(); // Pour les séances en présentiel
            $table->text('notes')->nullable(); // Notes du tuteur
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_sessions');
    }
};
