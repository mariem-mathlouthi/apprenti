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
        // Vérifie si la colonne 'reponsesFausses' existe déjà
        if (!Schema::hasColumn('quizz', 'reponsesFausses')) {
            Schema::table('quizz', function (Blueprint $table) {
                $table->json('reponsesFausses')->nullable()->after('reponseCorrecte');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizz', function (Blueprint $table) {
            $table->dropColumn('reponsesFausses');
        });
    }
};
