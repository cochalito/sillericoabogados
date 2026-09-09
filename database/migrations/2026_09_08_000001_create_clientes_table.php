<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_cliente', ['NATURAL', 'JURIDICO'])->default('NATURAL');
            $table->string('nombre_razon_social', 255)->index();
            $table->string('documento_identidad', 50)->nullable()->index();
            $table->string('telefono', 50)->nullable();
            $table->string('celular_whatsapp', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('persona_contacto', 255)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};