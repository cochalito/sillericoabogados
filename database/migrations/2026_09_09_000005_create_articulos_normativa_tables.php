<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de Artículos y Tipologías Legales
        Schema::create('articulos_ley', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_normativo', 50)->index(); // CODIGO_PENAL, CODIGO_CIVIL, LEY_348, LEY_004, etc.
            $table->string('numero_articulo', 50)->index();  // Ej: Art. 335, Art. 185 bis
            $table->string('epigrafe_delito', 255)->index(); // Ej: Estafa, Legitimación de Ganancias Ilícitas
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->decimal('pena_minima_anos', 4, 1)->nullable();
            $table->decimal('pena_maxima_anos', 4, 1)->nullable();
            $table->text('texto_tipificacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos_ley');
    }
};
