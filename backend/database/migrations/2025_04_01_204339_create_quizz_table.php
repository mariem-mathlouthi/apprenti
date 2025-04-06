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
        Schema::create('quizz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idCours')->constrained('cours')->onDelete('cascade');
            $table->foreignId('idTuteur')->nullable()->constrained('tuteurs')->nullOnDelete();
            $table->string('titre');
            $table->text('question');
            $table->json('reponseCorrecte')->nullable();  // Correct answer(s) (JSON array)
            $table->json('reponsesFausses')->nullable();  // Incorrect answers (JSON array)
            $table->integer('score')->default(1);         // Default score per question
            $table->timestamps();                         // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizz');
    }
};