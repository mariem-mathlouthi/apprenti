<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idCours')->constrained('cours')->onDelete('cascade');
            $table->string('titre');
            $table->text('question');
            $table->string('reponseCorrecte');
            $table->json('reponsesFausses');
            $table->integer('score')->default(1);
            $table->timestamps();
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
