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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idEtudiant')->nullable();
            $table->unsignedBigInteger('idEntreprise')->nullable();
            $table->unsignedBigInteger('idTuteur')->nullable();
            $table->String('message');
            $table->enum('destination',['Etudiant','Entreprise','Tuteur']);
            $table->enum('type',['offre','demande','cours', 'ressource', 'appointment']);
            $table->enum('visibility',['shown','hidden']);
            $table->unsignedBigInteger('appointmentId')->nullable();
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
