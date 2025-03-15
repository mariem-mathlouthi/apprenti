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
<<<<<<< HEAD
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('prix', 10, 2);
            $table->foreignId('idTuteur')->constrained('tuteurs')->onDelete('cascade');
            $table->foreignId('idApprenant')->nullable()->constrained('etudiants')->onDelete('set null');
            $table->integer('duration'); // en minutes ou heures
            $table->string('file')->nullable();
            $table->foreignId('createdBy')->constrained('tuteurs')->onDelete('cascade');
            $table->timestamps();
        });
=======
      Schema::create('cours', function (Blueprint $table) {
        $table->id();
        $table->string('titre');
        $table->text('description');
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->decimal('prix', 10, 2);
        $table->foreignId('idTuteur')->nullable()->constrained('tuteurs')->onDelete('cascade'); // Ajout de nullable()
        $table->foreignId('idApprenant')->nullable()->constrained('etudiants')->onDelete('set null');
        $table->integer('duration');
        $table->string('file')->nullable();
        $table->foreignId('createdBy')->nullable()->constrained('tuteurs')->onDelete('cascade'); // Ajout de nullable()
        $table->timestamps();
    });
    
>>>>>>> 673f7af5d820339a7a5e76d843fda127b93ee883
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
<<<<<<< HEAD
};
=======
};
>>>>>>> 673f7af5d820339a7a5e76d843fda127b93ee883
