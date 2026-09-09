<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles del Bufete
        $roles = [
            [
                'codigo' => 'DIRECTOR',
                'nombre' => 'Director General',
                'descripcion' => 'Socio Fundador con control total sobre todos los módulos, auditoría y configuraciones.',
                'color_badge' => 'bg-brand-green text-brand-gold',
            ],
            [
                'codigo' => 'SOCIO',
                'nombre' => 'Socio del Bufete',
                'descripcion' => 'Socio directivo con acceso amplio a casos, equipo y finanzas.',
                'color_badge' => 'bg-emerald-800 text-white',
            ],
            [
                'codigo' => 'JEFE_PENAL',
                'nombre' => 'Jefe de Área Penal',
                'descripcion' => 'Asociado responsable de la dirección técnica y asignación de causas penales.',
                'color_badge' => 'bg-brand-green text-white',
            ],
            [
                'codigo' => 'JEFE_CIVIL',
                'nombre' => 'Jefe de Área Civil',
                'descripcion' => 'Asociado responsable de procesos civiles, comerciales y ejecutivos.',
                'color_badge' => 'bg-blue-900 text-white',
            ],
            [
                'codigo' => 'JEFE_COMERCIAL',
                'nombre' => 'Jefe de Área Comercial',
                'descripcion' => 'Asociado responsable del asesoramiento corporativo y empresarial.',
                'color_badge' => 'bg-slate-700 text-white',
            ],
            [
                'codigo' => 'ASOCIADO',
                'nombre' => 'Abogado Asociado',
                'descripcion' => 'Abogado litigante con facultades plenas para tramitar y patrocinar causas asignadas.',
                'color_badge' => 'bg-indigo-700 text-white',
            ],
            [
                'codigo' => 'JUNIOR',
                'nombre' => 'Abogado Junior',
                'descripcion' => 'Abogado auxiliar para redacción de memoriales, seguimiento y diligencias.',
                'color_badge' => 'bg-amber-600 text-white',
            ],
            [
                'codigo' => 'PROCURADOR',
                'nombre' => 'Procurador / Pasante',
                'descripcion' => 'Personal operativo para notificaciones de juzgado, presentación de memoriales y recopilación.',
                'color_badge' => 'bg-slate-500 text-white',
            ],
        ];

        $createdRoles = [];
        foreach ($roles as $r) {
            $createdRoles[$r['codigo']] = Rol::firstOrCreate(['codigo' => $r['codigo']], $r);
        }

        // 2. Permisos del Sistema por Módulos
        $permisos = [
            // Procesos
            ['codigo' => 'procesos.ver', 'modulo' => 'PROCESOS', 'nombre' => 'Ver Expedientes', 'descripcion' => 'Consultar listado y ficha detallada de procesos.'],
            ['codigo' => 'procesos.crear', 'modulo' => 'PROCESOS', 'nombre' => 'Crear Expedientes', 'descripcion' => 'Dar de alta nuevas causas en el sistema.'],
            ['codigo' => 'procesos.editar', 'modulo' => 'PROCESOS', 'nombre' => 'Editar Expedientes', 'descripcion' => 'Modificar datos procesales y carátula.'],
            ['codigo' => 'procesos.eliminar', 'modulo' => 'PROCESOS', 'nombre' => 'Eliminar / Archivar', 'descripcion' => 'Archivar o eliminar expedientes del sistema.'],
            
            // Actuaciones y Documentos
            ['codigo' => 'actuaciones.crear', 'modulo' => 'ACTUACIONES', 'nombre' => 'Registrar Actuaciones', 'descripcion' => 'Agregar diligencias y notas a la bitácora.'],
            ['codigo' => 'documentos.subir', 'modulo' => 'DOCUMENTOS', 'nombre' => 'Subir Documentos', 'descripcion' => 'Adjuntar memoriales y resoluciones en PDF.'],
            
            // Calendario
            ['codigo' => 'calendario.gestionar', 'modulo' => 'AGENDA', 'nombre' => 'Gestionar Audiencias', 'descripcion' => 'Crear y reprogramar audiencias y plazos fatales.'],
            
            // Directorio
            ['codigo' => 'clientes.gestionar', 'modulo' => 'CLIENTES', 'nombre' => 'Gestionar Directorio', 'descripcion' => 'Crear y editar clientes y sujetos procesales.'],
            
            // Catálogo Normativo
            ['codigo' => 'articulos.gestionar', 'modulo' => 'NORMATIVA', 'nombre' => 'Catálogo de Artículos', 'descripcion' => 'Crear y editar artículos de ley y tipologías.'],
            
            // Auditoría y Control
            ['codigo' => 'auditoria.ver', 'modulo' => 'AUDITORIA', 'nombre' => 'Ver Auditoría Forense', 'descripcion' => 'Inspeccionar bitácora global de trazabilidad.'],
            
            // Equipo y Usuarios
            ['codigo' => 'usuarios.gestionar', 'modulo' => 'EQUIPO', 'nombre' => 'Gestionar Usuarios', 'descripcion' => 'Crear cuentas y asignar roles y permisos.'],
        ];

        $createdPermisos = [];
        foreach ($permisos as $p) {
            $createdPermisos[$p['codigo']] = Permiso::firstOrCreate(['codigo' => $p['codigo']], $p);
        }

        // 3. Asignación de Permisos (Llaveros)
        // Director y Socio tienen todos
        $allPermisoIds = array_column($createdPermisos, 'id');
        $createdRoles['DIRECTOR']->permisos()->sync($allPermisoIds);
        $createdRoles['SOCIO']->permisos()->sync($allPermisoIds);

        // Jefes de Área
        $jefesPermisos = Permiso::whereIn('codigo', [
            'procesos.ver', 'procesos.crear', 'procesos.editar', 'procesos.eliminar',
            'actuaciones.crear', 'documentos.subir', 'calendario.gestionar',
            'clientes.gestionar', 'articulos.gestionar', 'auditoria.ver'
        ])->pluck('id');
        $createdRoles['JEFE_PENAL']->permisos()->sync($jefesPermisos);
        $createdRoles['JEFE_CIVIL']->permisos()->sync($jefesPermisos);
        $createdRoles['JEFE_COMERCIAL']->permisos()->sync($jefesPermisos);

        // Asociados
        $asociadosPermisos = Permiso::whereIn('codigo', [
            'procesos.ver', 'procesos.crear', 'procesos.editar',
            'actuaciones.crear', 'documentos.subir', 'calendario.gestionar',
            'clientes.gestionar'
        ])->pluck('id');
        $createdRoles['ASOCIADO']->permisos()->sync($asociadosPermisos);

        // Juniors
        $juniorsPermisos = Permiso::whereIn('codigo', [
            'procesos.ver', 'actuaciones.crear', 'documentos.subir', 'calendario.gestionar'
        ])->pluck('id');
        $createdRoles['JUNIOR']->permisos()->sync($juniorsPermisos);

        // Procuradores
        $procuradorPermisos = Permiso::whereIn('codigo', [
            'procesos.ver', 'actuaciones.crear', 'documentos.subir'
        ])->pluck('id');
        $createdRoles['PROCURADOR']->permisos()->sync($procuradorPermisos);
    }
}
