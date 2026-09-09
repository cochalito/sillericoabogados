<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sujetos_procesales', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_persona', ['NATURAL', 'JURIDICA'])->default('NATURAL');
            $table->string('nombre_razon_social', 255)->index();
            $table->string('documento_identidad', 50)->nullable()->index(); // CI o NIT
            $table->string('persona_contacto', 255)->nullable();
            $table->string('celular_whatsapp', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->boolean('es_cliente')->default(false)->index();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sujetos_procesales');
    }
};
