<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Materias
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('color_hex', 20)->nullable();
            $table->string('color_badge', 50)->default('bg-brand-green/10 text-brand-green');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Jurisdicciones
        Schema::create('jurisdicciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('departamento', 100)->default('La Paz');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Roles de Partes Procesales
        Schema::create('roles_partes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 4. Estados del Proceso
        Schema::create('estados_proceso', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('tipo_agrupador', 50)->default('ACTIVO'); // ACTIVO, IMPUGNACION, CONCLUIDO, SUSPENDIDO
            $table->text('descripcion_estado')->nullable();
            $table->string('color_badge', 80)->default('bg-slate-100 text-slate-700');
            $table->integer('orden')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 5. Etapas Procesales
        Schema::create('etapas_procesales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->integer('orden')->default(1);
            $table->string('nombre', 150);
            $table->integer('dias_termino_sugerido')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas_procesales');
        Schema::dropIfExists('estados_proceso');
        Schema::dropIfExists('roles_partes');
        Schema::dropIfExists('jurisdicciones');
        Schema::dropIfExists('materias');
    }
};
