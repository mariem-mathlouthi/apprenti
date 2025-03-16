<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('prix', 10, 2);
            $table->foreignId('idTuteur')->nullable()->constrained('tuteurs')->onDelete('cascade');
            $table->foreignId('idApprenant')->nullable()->constrained('etudiants')->onDelete('set null');
            $table->integer('duration'); // en minutes ou heures
            $table->string('file')->nullable();
            $table->foreignId('createdBy')->nullable()->constrained('tuteurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
