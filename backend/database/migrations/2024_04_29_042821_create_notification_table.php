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
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idEtudiant');
            $table->unsignedBigInteger('idEntreprise');
            $table->unsignedBigInteger('idTuteur');
            $table->String('message');
            $table->enum('destination',['Etudiant','Entreprise','Tuteur']);
            $table->enum('type',['offre','demande','cours']);
            $table->enum('visibility',['shown','hidden']);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
