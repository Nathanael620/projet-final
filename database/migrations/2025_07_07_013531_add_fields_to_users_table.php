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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->enum('role', ['student', 'tutor', 'admin'])->default('student')->after('bio');
            $table->json('skills')->nullable()->after('role');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('skills');
            $table->boolean('is_available')->default(true)->after('level');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'bio',
                'role',
                'skills',
                'level',
                'is_available',
                'hourly_rate'
            ]);
        });
    }
};
