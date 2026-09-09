<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('procesos')->cascadeOnDelete();
            $table->foreignId('actuacion_id')->nullable()->constrained('actuaciones')->nullOnDelete();
            $table->string('nombre_original', 255);
            $table->string('ruta_archivo', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('peso_bytes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
