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
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idEntreprise'); // foreign
            $table->enum('status',['en attente','accepté','refusé']); // modif to apply later
            $table->string('titre');
            $table->string('description',255);
            $table->foreignId('domaine_id')->constrained('domaines')->onDelete('cascade');
            $table->date('dateDebut');
            $table->date('dateFin');
            $table->foreignId('type_stage_id')->constrained('type_stages')->cascadeOnDelete();
            $table->string('cahierCharge'); // file path?
            $table->timestamps();
            $table->foreign('idEntreprise')->references('id')->on('entreprises')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
