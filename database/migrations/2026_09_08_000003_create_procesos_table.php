<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno', 50)->unique();
            $table->string('cud', 100)->nullable()->index();
            $table->string('nurej', 100)->nullable()->index();
            $table->string('ianus', 100)->nullable();
            $table->string('codigo_caso', 100)->nullable();
            $table->boolean('portal_fiscalia')->default(false);
            $table->string('jurisdiccion', 100)->default('CENTRO');
            $table->string('materia', 50)->default('Penal')->index();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('rol_cliente', 50)->default('DEMANDANTE_QUERELLANTE');
            $table->string('demandante_denunciante', 255)->index();
            $table->string('demandado_denunciado', 255)->index();
            $table->string('juzgado_tribunal', 255)->nullable();
            $table->string('sala', 255)->nullable();
            $table->string('autoridad_juez_fiscal', 255)->nullable();
            $table->string('investigador_asignado', 255)->nullable();
            $table->string('delito_accion', 255)->index();
            $table->string('etapa_procesal', 100)->nullable()->default('Investigación Preliminar');
            $table->string('estado', 50)->default('En Trámite')->index();
            $table->text('estado_detalle')->nullable();
            $table->foreignId('abogado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['materia', 'estado']);
            $table->index(['cliente_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};