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
        Schema::create('registro_artisticos', function (Blueprint $table) {
            $table->id();
            // Campo obligatorio: disciplina artística, máximo 80 caracteres
            $table->string('disciplina_artistica', 80);
            // Campo opcional: nivel artístico, máximo 40 caracteres
            $table->string('nivel_artistico', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_artisticos');
    }
};
