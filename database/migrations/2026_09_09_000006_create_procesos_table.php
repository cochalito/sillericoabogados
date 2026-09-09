<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Central de Procesos
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno', 50)->unique();
            $table->string('cud', 100)->nullable()->index();
            $table->string('nurej', 100)->nullable()->index();
            $table->string('ianus', 100)->nullable()->index();
            $table->string('codigo_caso', 100)->nullable()->index();
            $table->boolean('portal_fiscalia')->default(false);

            // Paramétricas relacionales
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('jurisdiccion_id')->constrained('jurisdicciones');

            // Sujetos procesales (Directorio Unificado)
            $table->foreignId('demandante_id')->constrained('sujetos_procesales');
            $table->foreignId('demandado_id')->constrained('sujetos_procesales');
            $table->foreignId('cliente_id')->constrained('sujetos_procesales');
            $table->foreignId('rol_cliente_id')->constrained('roles_partes');

            // Tipificación y normativa principal
            $table->foreignId('articulo_principal_id')->nullable()->constrained('articulos_ley')->nullOnDelete();

            // Autoridades y radicatoria (Preservación de histórico juez-juzgado)
            $table->foreignId('juez_id')->nullable()->constrained('jueces')->nullOnDelete();
            $table->foreignId('juzgado_id')->nullable()->constrained('juzgados')->nullOnDelete(); // Snapshot histórico
            $table->foreignId('sala_id')->nullable()->constrained('salas')->nullOnDelete();
            $table->foreignId('investigador_id')->nullable()->constrained('investigadores')->nullOnDelete();

            // Etapa y estado operativo
            $table->foreignId('etapa_procesal_id')->constrained('etapas_procesales');
            $table->foreignId('estado_id')->constrained('estados_proceso');

            // Bitácora viva del caso
            $table->text('situacion_actual')->nullable();

            // Asignación interna
            $table->foreignId('abogado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices compuestos para alto rendimiento
            $table->index(['materia_id', 'estado_id']);
            $table->index(['cliente_id', 'estado_id']);
            $table->index(['abogado_id', 'estado_id']);
        });

        // 2. Concurso de Delitos / Artículos conexos (Pivote)
        Schema::create('proceso_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('procesos')->cascadeOnDelete();
            $table->foreignId('articulo_id')->constrained('articulos_ley')->cascadeOnDelete();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->unique(['proceso_id', 'articulo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proceso_articulos');
        Schema::dropIfExists('procesos');
    }
};
