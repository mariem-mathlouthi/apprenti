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
        // Vérifier si la colonne 'reponseCorrecte' existe
        if (Schema::hasColumn('quizz', 'reponseCorrecte')) {
            // Convertir toutes les valeurs existantes en JSON avant de modifier la colonne
            DB::statement("UPDATE quizz SET reponseCorrecte = JSON_ARRAY(reponseCorrecte) WHERE reponseCorrecte IS NOT NULL AND reponseCorrecte NOT LIKE '[%'");

            // Modifier le type de la colonne en JSON
            Schema::table('quizz', function (Blueprint $table) {
                $table->json('reponseCorrecte')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizz', function (Blueprint $table) {
            $table->string('reponseCorrecte')->nullable(false)->change();
        });

        // Convertir les valeurs JSON en texte simple (prendre le premier élément du tableau)
        DB::statement("UPDATE quizz SET reponseCorrecte = JSON_UNQUOTE(JSON_EXTRACT(reponseCorrecte, '$[0]')) WHERE JSON_VALID(reponseCorrecte)");
    }
};
