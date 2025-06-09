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
        Schema::create('tuteurs', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('specialite_id')->constrained()->onDelete('cascade');
            $table->integer('experience');
            $table->string('phone')->unique();
            $table->enum('status', ['en attente', 'accepté', 'refusé'])->default('en attente');
            $table->string('image')->nullable();
            $table->string('cv')->nullable(); // ajout intégré ici
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuteurs');
    }
};
