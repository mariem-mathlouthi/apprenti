<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reponses_etudiants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quizz_id')->constrained('quizz')->onDelete('cascade');
            $table->json('reponses');
            $table->boolean('est_correcte');
            $table->integer('score_obtenu');
            $table->timestamps();
            
            $table->index(['etudiant_id', 'quizz_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reponses_etudiants');
    }
};