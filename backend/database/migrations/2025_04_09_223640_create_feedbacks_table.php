<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('cours_id')->constrained('cours')->onDelete('cascade');
            $table->integer('note')->unsigned()->between(1, 5);
            $table->text('commentaire');
            $table->timestamps();
            
            $table->unique(['etudiant_id', 'cours_id']); // Un étudiant ne peut donner qu'un feedback par cours
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};