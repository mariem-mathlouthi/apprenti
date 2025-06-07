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
        Schema::table('tuteurs', function (Blueprint $table) {
            $table->string('cv')->nullable()->after('image');
            $table->string('image')->nullable()->change(); // Optionnel : si tu veux juste t'assurer qu’il est nullable
        });
    }

    public function down(): void
    {
        Schema::table('tuteurs', function (Blueprint $table) {
            $table->dropColumn('cv');
            // Pas besoin de retirer `image` si tu ne fais que `nullable()->change()`
        });
    }
};
