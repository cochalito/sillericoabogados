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
        if (! Schema::hasTable('articulos_ley')) {
            Schema::create('articulos_ley', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 50)->unique();
                $table->string('tipo', 20)->default('ARTICULO');
                $table->string('autor', 250)->nullable();
                $table->text('titulo');
                $table->longText('descripcion')->nullable();
                $table->string('archivo_pdf', 255)->nullable();
                $table->dateTime('fecha_publicacion');
                $table->string('estado', 20)->default('Activo');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos_ley');
    }
};
