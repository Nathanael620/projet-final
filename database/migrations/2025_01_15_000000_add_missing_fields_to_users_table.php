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
            // Champs pour la gestion des comptes
            $table->boolean('is_active')->default(true)->after('email_verified_at');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            
            // Champs pour le suivi des connexions
            $table->timestamp('last_login_at')->nullable()->after('deactivation_reason');
            $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            $table->string('last_ip')->nullable()->after('last_activity_at');
            $table->text('last_user_agent')->nullable()->after('last_ip');
            
            // Champs pour la gestion des profils (avatar existe déjà)
            // $table->string('avatar')->nullable()->after('last_user_agent');
            $table->boolean('is_public_profile')->default(true)->after('last_user_agent');
            
            // Champs pour les statistiques
            $table->decimal('rating', 3, 2)->default(0)->after('is_public_profile');
            $table->integer('total_sessions')->default(0)->after('rating');
            
            // Soft delete
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'deactivated_at',
                'deactivation_reason',
                'last_login_at',
                'last_activity_at',
                'last_ip',
                'last_user_agent',
                'is_public_profile',
                'rating',
                'total_sessions',
                'deleted_at'
            ]);
        });
    }
}; 