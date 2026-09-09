<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_calendario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo', 255);
            $table->string('tipo_evento', 50)->default('Audiencia');
            $table->dateTime('fecha_hora_inicio')->index();
            $table->dateTime('fecha_hora_fin')->nullable();
            $table->string('lugar_enlace', 255)->nullable();
            $table->boolean('es_plazo_fatal')->default(false);
            $table->string('prioridad', 20)->default('Media');
            $table->string('estado', 30)->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->boolean('notificado_telegram')->default(false);
            $table->timestamps();

            $table->index(['fecha_hora_inicio', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_calendario');
    }
};