<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cargo', 100)->nullable()->after('email');
            $table->string('iniciales', 10)->nullable()->after('cargo');
            $table->string('color', 100)->nullable()->default('bg-brand-green text-white')->after('iniciales');
            $table->string('telefono', 50)->nullable()->after('color');
            $table->boolean('es_abogado')->default(true)->after('telefono');
            $table->boolean('activo')->default(true)->after('es_abogado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cargo', 'iniciales', 'color', 'telefono', 'es_abogado', 'activo']);
        });
    }
};