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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuteur_id')->constrained('tuteurs')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            // $table->foreignId('cours_id')->constrained('cours')->onDelete('cascade');
            $table->enum('sender_type', ['tuteur', 'etudiant']);
            $table->text('message')->nullable(); // Message can be null if a file is sent
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // e.g., image/jpeg, application/pdf
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};