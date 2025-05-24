<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pour la table etudiants
        Schema::table('etudiants', function (Blueprint $table) {
            $table->string('password_token')->nullable()->after('password');
            $table->timestamp('password_token_send_at')->nullable()->after('password_token');
        });

        // Pour la table entreprises
        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('password_token')->nullable()->after('password');
            $table->timestamp('password_token_send_at')->nullable()->after('password_token');
        });

        // Pour la table tuteurs
        Schema::table('tuteurs', function (Blueprint $table) {
            $table->string('password_token')->nullable()->after('password');
            $table->timestamp('password_token_send_at')->nullable()->after('password_token');
        });

        // Pour la table admins
        Schema::table('admins', function (Blueprint $table) {
            $table->string('password_token')->nullable()->after('password');
            $table->timestamp('password_token_send_at')->nullable()->after('password_token');
        });
    }

    public function down()
    {
        // Pour la suppression en cas de rollback
        Schema::table('etudiants', function (Blueprint $table) {
            $table->dropColumn(['password_token', 'password_token_send_at']);
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn(['password_token', 'password_token_send_at']);
        });

        Schema::table('tuteurs', function (Blueprint $table) {
            $table->dropColumn(['password_token', 'password_token_send_at']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['password_token', 'password_token_send_at']);
        });
    }
};