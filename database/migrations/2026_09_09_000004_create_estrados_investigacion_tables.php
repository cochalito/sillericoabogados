<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Juzgados
        Schema::create('juzgados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurisdiccion_id')->constrained('jurisdicciones')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->string('nombre', 255)->index();
            $table->string('edificio_direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Salas Departamentales (Tribunales de Alzada)
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurisdiccion_id')->constrained('jurisdicciones')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->string('nombre', 255)->index();
            $table->string('edificio_direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Jueces y Fiscales
        Schema::create('jueces', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_autoridad', ['JUEZ_INSTRUCCION', 'JUEZ_SENTENCIA', 'VOCAL_SALA', 'FISCAL_MATERIA'])->default('JUEZ_INSTRUCCION');
            $table->string('nombre_completo', 255)->index();
            $table->foreignId('juzgado_id')->nullable()->constrained('juzgados')->nullOnDelete(); // Despacho habitual
            $table->string('telefono', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 4. Grados Policiales (Escalafón Policía Boliviana)
        Schema::create('grados_policiales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('abreviatura', 50);
            $table->integer('jerarquia_orden')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 5. Investigadores Policiales (Asignados al Caso FELCC/FELCV)
        Schema::create('investigadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grado_id')->constrained('grados_policiales')->cascadeOnDelete();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('division', 150)->nullable(); // Ej: FELCC - Delitos Patrimoniales
            $table->string('celular_contacto', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investigadores');
        Schema::dropIfExists('grados_policiales');
        Schema::dropIfExists('jueces');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('juzgados');
    }
};
