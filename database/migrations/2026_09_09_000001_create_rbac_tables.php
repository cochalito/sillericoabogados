<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('color_badge', 50)->default('bg-brand-green/10 text-brand-green');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Permisos
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100)->unique();
            $table->string('modulo', 50)->index();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 3. Rol - Permisos (Pivote RBAC)
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->unique(['rol_id', 'permiso_id']);
            $table->timestamps();
        });

        // 4. Agregar rol_id y campos de perfil a users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('cargo', 100)->nullable()->after('email');
            $table->string('iniciales', 10)->nullable()->after('cargo');
            $table->string('color', 50)->default('bg-brand-green text-white')->after('iniciales');
            $table->string('telefono', 50)->nullable()->after('color');
            $table->boolean('es_abogado')->default(true)->after('telefono');
            $table->boolean('activo')->default(true)->after('es_abogado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn(['rol_id', 'cargo', 'iniciales', 'color', 'telefono', 'es_abogado', 'activo']);
        });

        Schema::dropIfExists('rol_permisos');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};
