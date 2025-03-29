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
        Schema::table('quizz', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas déjà
            if (!Schema::hasColumn('quizz', 'idTuteur')) {
                $table->foreignId('idTuteur')
                    ->nullable()
                    ->constrained('tuteurs')
                    ->nullOnDelete();
            }
        });
    }
    
    public function down(): void
    {
        Schema::table('quizz', function (Blueprint $table) {
            // Supprime la contrainte seulement si elle existe
            if (Schema::hasColumn('quizz', 'idTuteur')) {
                $table->dropForeign(['idTuteur']);
                $table->dropColumn('idTuteur');
            }
        });
    }
};
