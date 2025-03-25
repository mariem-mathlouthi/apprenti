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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('idCours')->constrained('cours')->onDelete('cascade'); 
            $table->foreignId('idApprenant')->constrained('etudiants')->onDelete('cascade'); 
            $table->dateTime('dateInscription')->default(now());
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};