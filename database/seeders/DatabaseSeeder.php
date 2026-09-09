<?php

namespace Database\Seeders;

use App\Models\Actuacion;
use App\Models\ArticuloLey;
use App\Models\AuditoriaProceso;
use App\Models\EstadoProceso;
use App\Models\EtapaProcesal;
use App\Models\EventoCalendario;
use App\Models\GradoPolicial;
use App\Models\Investigador;
use App\Models\Juez;
use App\Models\Juzgado;
use App\Models\Jurisdiccion;
use App\Models\Materia;
use App\Models\Proceso;
use App\Models\Rol;
use App\Models\RolParte;
use App\Models\Sala;
use App\Models\SujetoProcesal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ejecutar Seeders de RBAC y Paramétricas Base
        $this->call(RbacSeeder::class);
        $this->call(ParametricasSeeder::class);

        // 2. Crear Usuarios con sus Roles RBAC
        $roles = Rol::pluck('id', 'codigo');

        $abogadosData = [
            [
                'name' => 'Alan Sillerico Segurondo',
                'email' => 'alan@sillericoabogados.com',
                'rol_id' => $roles['DIRECTOR'] ?? null,
                'cargo' => 'Socio Fundador & Director General',
                'iniciales' => 'AS',
                'color' => 'bg-brand-green text-brand-gold',
            ],
            [
                'name' => 'Anghela Soliz de Sillerico',
                'email' => 'anghela@sillericoabogados.com',
                'rol_id' => $roles['SOCIO'] ?? null,
                'cargo' => 'Socia & Subdirectora',
                'iniciales' => 'AS',
                'color' => 'bg-emerald-800 text-brand-gold',
            ],
            [
                'name' => 'Bismarck Molina',
                'email' => 'bismarck@sillericoabogados.com',
                'rol_id' => $roles['JEFE_PENAL'] ?? null,
                'cargo' => 'Asociado Jefe Área Penal',
                'iniciales' => 'BM',
                'color' => 'bg-brand-green text-white',
            ],
            [
                'name' => 'Mauricio Mercado Foronda',
                'email' => 'mauricio@sillericoabogados.com',
                'rol_id' => $roles['JEFE_CIVIL'] ?? null,
                'cargo' => 'Asociado Jefe Área Civil',
                'iniciales' => 'MM',
                'color' => 'bg-blue-900 text-white',
            ],
            [
                'name' => 'Álvaro Arias Antequera',
                'email' => 'alvaro@sillericoabogados.com',
                'rol_id' => $roles['JEFE_COMERCIAL'] ?? null,
                'cargo' => 'Asociado Jefe Área Comercial',
                'iniciales' => 'AA',
                'color' => 'bg-slate-700 text-white',
            ],
            [
                'name' => 'Eduardo Yupanqui Quispe',
                'email' => 'eduardo@sillericoabogados.com',
                'rol_id' => $roles['ASOCIADO'] ?? null,
                'cargo' => 'Abogado Asociado',
                'iniciales' => 'EY',
                'color' => 'bg-slate-600 text-white',
            ],
            [
                'name' => 'Amalia Paucara Mamani',
                'email' => 'amalia@sillericoabogados.com',
                'rol_id' => $roles['JUNIOR'] ?? null,
                'cargo' => 'Abogada Junior',
                'iniciales' => 'AP',
                'color' => 'bg-amber-600 text-white',
            ],
            [
                'name' => 'Massiel Rullier Loza',
                'email' => 'massiel@sillericoabogados.com',
                'rol_id' => $roles['JUNIOR'] ?? null,
                'cargo' => 'Abogada Junior',
                'iniciales' => 'MR',
                'color' => 'bg-rose-700 text-white',
            ],
        ];

        $userModels = [];
        foreach ($abogadosData as $abg) {
            $userModels[$abg['name']] = User::firstOrCreate(
                ['email' => $abg['email']],
                [
                    'name' => $abg['name'],
                    'password' => Hash::make('password123'),
                    'rol_id' => $abg['rol_id'],
                    'cargo' => $abg['cargo'],
                    'iniciales' => $abg['iniciales'],
                    'color' => $abg['color'],
                    'telefono' => '+591 77234317',
                    'es_abogado' => true,
                    'activo' => true,
                ]
            );
        }

        // 3. Cargar Dataset Real (82 casos de procesos.docx)
        $jsonPath = database_path('data/procesos_seed.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("No se encontró el archivo $jsonPath");
            return;
        }

        $casos = json_decode(file_get_contents($jsonPath), true);
        $this->command->info("Cargando y estructurando " . count($casos) . " casos reales desde procesos.docx...");

        // Mapas de búsqueda rápida para paramétricas
        $materiasMap = Materia::pluck('id', 'codigo')->toArray();
        $jurisdiccionesMap = Jurisdiccion::pluck('id', 'codigo')->toArray();
        $rolesPartesMap = RolParte::pluck('id', 'codigo')->toArray();
        $estadosMap = EstadoProceso::pluck('id', 'nombre')->toArray();
        $gradosMap = GradoPolicial::pluck('id', 'codigo')->toArray();
        $gradoDefaultId = $gradosMap['SGTO_1RO'] ?? GradoPolicial::first()->id;

        $userList = array_values($userModels);

        foreach ($casos as $index => $item) {
            // A. Sujetos Procesales: Cliente, Demandante y Demandado
            $nombreCliente = trim($item['cliente_nombre'] ?? $item['demandante_denunciante']);
            $clienteSujeto = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => $nombreCliente],
                [
                    'tipo_persona' => ($item['tipo_cliente'] ?? 'NATURAL') === 'JURIDICO' ? 'JURIDICA' : 'NATURAL',
                    'persona_contacto' => $nombreCliente,
                    'celular_whatsapp' => '700' . rand(10000, 99999),
                    'email' => strtolower(Str::slug(substr($nombreCliente, 0, 10))) . '@correo.com',
                    'es_cliente' => true,
                    'activo' => true,
                ]
            );

            // Demandante
            $nombreDemandante = trim($item['demandante_denunciante'] ?: $nombreCliente);
            if ($nombreDemandante === $nombreCliente) {
                $demandanteSujeto = $clienteSujeto;
            } else {
                $demandanteSujeto = SujetoProcesal::firstOrCreate(
                    ['nombre_razon_social' => $nombreDemandante],
                    [
                        'tipo_persona' => Str::contains(strtoupper($nombreDemandante), ['S.A.', 'SRL', 'BANCO', 'MINISTERIO', 'EMPRESA', 'ENTEL']) ? 'JURIDICA' : 'NATURAL',
                        'persona_contacto' => $nombreDemandante,
                        'es_cliente' => false,
                        'activo' => true,
                    ]
                );
            }

            // Demandado
            $nombreDemandado = trim($item['demandado_denunciado'] ?: 'PERSONAS INDETERMINADAS');
            $demandadoSujeto = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => $nombreDemandado],
                [
                    'tipo_persona' => Str::contains(strtoupper($nombreDemandado), ['S.A.', 'SRL', 'BANCO', 'MINISTERIO', 'EMPRESA']) ? 'JURIDICA' : 'NATURAL',
                    'persona_contacto' => $nombreDemandado,
                    'es_cliente' => false,
                    'activo' => true,
                ]
            );

            // B. Materia y Jurisdicción
            $materiaCode = strtoupper($item['materia'] ?? 'PENAL');
            if ($materiaCode === 'CORPORATIVO') $materiaCode = 'COMERCIAL';
            $materiaId = $materiasMap[$materiaCode] ?? $materiasMap['PENAL'];

            $jurisCode = $item['jurisdiccion'] ?? 'CENTRO';
            if ($jurisCode === 'ZONA SUR') $jurisCode = 'ZONA_SUR';
            if ($jurisCode === 'EL ALTO') $jurisCode = 'EL_ALTO';
            $jurisdiccionId = $jurisdiccionesMap[$jurisCode] ?? $jurisdiccionesMap['CENTRO'];

            // C. Juzgado y Sala
            $rawJuzgado = trim($item['juzgado_tribunal'] ?? '');
            $juzgadoId = null;
            $salaId = null;

            if (!empty($rawJuzgado)) {
                // Si contiene Sala
                if (preg_match('/(SALA PENAL\s+[A-ZÁÉÍÓÚ]+|SALA CIVIL\s+[A-ZÁÉÍÓÚ]+)/i', $rawJuzgado, $sm)) {
                    $salaNombre = trim($sm[1]);
                    $sala = Sala::firstOrCreate(
                        ['nombre' => $salaNombre, 'jurisdiccion_id' => $jurisdiccionId],
                        ['materia_id' => $materiaId, 'activo' => true]
                    );
                    $salaId = $sala->id;
                }

                // Juzgado
                $juzgadoNombre = $rawJuzgado;
                if (preg_match('/(JUZGADO\s+[^,;Dra|Dr|Inv]+|TRIBUNAL\s+[^,;Dra|Dr|Inv]+)/i', $rawJuzgado, $jm)) {
                    $juzgadoNombre = trim(preg_replace('/\s+/', ' ', $jm[1]));
                }
                $juzgado = Juzgado::firstOrCreate(
                    ['nombre' => substr($juzgadoNombre, 0, 250), 'jurisdiccion_id' => $jurisdiccionId],
                    ['materia_id' => $materiaId, 'activo' => true]
                );
                $juzgadoId = $juzgado->id;
            }

            // D. Juez o Fiscal
            $rawJuez = trim($item['autoridad_juez_fiscal'] ?? '');
            $juezId = null;
            if (!empty($rawJuez) && strlen($rawJuez) > 2) {
                $juezNombre = trim(preg_replace('/\s+/', ' ', $rawJuez));
                $juez = Juez::firstOrCreate(
                    ['nombre_completo' => substr($juezNombre, 0, 250)],
                    [
                        'tipo_autoridad' => Str::contains(strtoupper($rawJuzgado), 'TRIBUNAL') ? 'JUEZ_SENTENCIA' : 'JUEZ_INSTRUCCION',
                        'juzgado_id' => $juzgadoId,
                        'activo' => true,
                    ]
                );
                $juezId = $juez->id;
            }

            // E. Investigador y Grado
            $rawInv = trim($item['investigador_asignado'] ?? '');
            $investigadorId = null;
            if (!empty($rawInv) && $rawInv !== '.' && strlen($rawInv) > 2) {
                $gradoId = $gradoDefaultId;
                if (preg_match('/(Sof|Suboficial|Sgto|Sargento|Tte|Teniente|Cbo|Cabo|Cap|Capitan)/i', $rawInv, $gm)) {
                    $gmatch = strtolower($gm[1]);
                    if (str_starts_with($gmatch, 'sof')) $gradoId = $gradosMap['SOF_1RO'] ?? $gradoDefaultId;
                    elseif (str_starts_with($gmatch, 'sgt')) $gradoId = $gradosMap['SGTO_1RO'] ?? $gradoDefaultId;
                    elseif (str_starts_with($gmatch, 'tte')) $gradoId = $gradosMap['TTE'] ?? $gradoDefaultId;
                    elseif (str_starts_with($gmatch, 'cbo')) $gradoId = $gradosMap['CBO'] ?? $gradoDefaultId;
                    elseif (str_starts_with($gmatch, 'cap')) $gradoId = $gradosMap['CAP'] ?? $gradoDefaultId;
                }

                $cleanInv = trim(preg_replace('/^(Sof|Suboficial|Sgto|Sargento|Tte|Teniente|Cbo|Cabo|Cap|Capitán|\.|\s)+/i', '', $rawInv));
                $investigador = Investigador::firstOrCreate(
                    ['nombres' => $cleanInv ?: 'Asignado', 'grado_id' => $gradoId],
                    ['apellidos' => 'al Caso', 'division' => 'FELCC - Delitos Patrimoniales', 'activo' => true]
                );
                $investigadorId = $investigador->id;
            }

            // F. Tipificación / Artículo Legal
            $delitoNombre = trim($item['delito_accion'] ?? 'ACCIÓN JURÍDICA');
            $articulo = ArticuloLey::where('epigrafe_delito', 'like', "%{$delitoNombre}%")->first();
            if (!$articulo) {
                $articulo = ArticuloLey::firstOrCreate(
                    ['epigrafe_delito' => $delitoNombre, 'materia_id' => $materiaId],
                    [
                        'codigo_normativo' => $materiaCode === 'PENAL' ? 'CODIGO_PENAL' : 'CODIGO_CIVIL',
                        'numero_articulo' => 'Art. Especial',
                        'texto_tipificacion' => "Tipificación y pretensión jurídica de {$delitoNombre}.",
                        'activo' => true,
                    ]
                );
            }

            // G. Etapa Procesal y Estado Operativo
            $etapaNombre = trim($item['etapa_procesal'] ?? 'Investigación Preliminar');
            $etapa = EtapaProcesal::firstOrCreate(
                ['materia_id' => $materiaId, 'nombre' => $etapaNombre],
                ['orden' => 1, 'activo' => true]
            );

            $estadoNombre = trim($item['estado'] ?? 'En Trámite');
            $estadoId = $estadosMap[$estadoNombre] ?? ($estadosMap['En Trámite'] ?? EstadoProceso::first()->id);

            // H. Asignar Abogado del Bufete
            if ($materiaCode === 'PENAL') {
                $abogado = $userModels['Bismarck Molina'] ?? $userList[0];
            } elseif ($materiaCode === 'CIVIL') {
                $abogado = $userModels['Mauricio Mercado Foronda'] ?? $userList[0];
            } elseif ($materiaCode === 'COMERCIAL') {
                $abogado = $userModels['Álvaro Arias Antequera'] ?? $userList[0];
            } else {
                $abogado = $userList[$index % count($userList)];
            }

            // I. Crear Proceso 100% Parametrizado
            $proceso = Proceso::firstOrCreate(
                ['codigo_interno' => $item['codigo_interno']],
                [
                    'cud' => $item['cud'],
                    'nurej' => $item['nurej'],
                    'ianus' => $item['ianus'],
                    'codigo_caso' => $item['codigo_caso'],
                    'portal_fiscalia' => $item['portal_fiscalia'] ?? false,
                    'materia_id' => $materiaId,
                    'jurisdiccion_id' => $jurisdiccionId,
                    'demandante_id' => $demandanteSujeto->id,
                    'demandado_id' => $demandadoSujeto->id,
                    'cliente_id' => $clienteSujeto->id,
                    'rol_cliente_id' => $rolesPartesMap['DEMANDANTE_QUERELLANTE'] ?? 1,
                    'articulo_principal_id' => $articulo->id,
                    'juez_id' => $juezId,
                    'juzgado_id' => $juzgadoId, // Snapshot histórico
                    'sala_id' => $salaId,
                    'investigador_id' => $investigadorId,
                    'etapa_procesal_id' => $etapa->id,
                    'estado_id' => $estadoId,
                    'situacion_actual' => $item['estado_detalle'] ?? null, // Bitácora viva del caso
                    'abogado_id' => $abogado->id,
                    'fecha_inicio' => now()->subDays(rand(10, 300)),
                ]
            );

            // Vincular artículo principal en la tabla pivote de concurso de delitos
            $proceso->articulos()->syncWithoutDetaching([$articulo->id => ['es_principal' => true]]);

            // J. Auditoría de creación de causa
            AuditoriaProceso::create([
                'proceso_id' => $proceso->id,
                'user_id' => $abogado->id,
                'accion' => 'CREACION_PROCESO',
                'descripcion' => "Apertura e ingreso de expediente {$proceso->codigo_interno} ({$articulo->epigrafe_delito}) para patrocinio de {$clienteSujeto->nombre_razon_social}",
                'detalles' => [
                    'codigo' => $proceso->codigo_interno,
                    'delito' => $articulo->epigrafe_delito,
                    'materia' => $materiaCode,
                    'juzgado' => $juzgadoNombre ?? 'Sin radicatoria inicial',
                ],
                'ip_address' => '192.168.1.' . rand(20, 99),
                'created_at' => $proceso->fecha_inicio ?: now()->subDays(rand(10, 60)),
            ]);

            // K. Actuación inicial basada en la situación viva del documento
            if (!empty($item['estado_detalle'])) {
                $actuacion = Actuacion::create([
                    'proceso_id' => $proceso->id,
                    'user_id' => $abogado->id,
                    'fecha_hora' => now()->subDays(rand(1, 20))->setTime(rand(9, 17), rand(0, 59)),
                    'titulo_actuacion' => 'Última actuación procesal registrada',
                    'tipo_actuacion' => 'Diligencia',
                    'descripcion' => $item['estado_detalle'],
                    'es_hito_relevante' => true,
                ]);

                AuditoriaProceso::create([
                    'proceso_id' => $proceso->id,
                    'user_id' => $abogado->id,
                    'accion' => 'NUEVA_ACTUACION',
                    'descripcion' => "Registro de diligencia procesal: {$actuacion->titulo_actuacion}",
                    'detalles' => [
                        'actuacion_id' => $actuacion->id,
                        'tipo' => $actuacion->tipo_actuacion,
                    ],
                    'ip_address' => '192.168.1.' . rand(20, 99),
                    'created_at' => $actuacion->fecha_hora,
                ]);
            }

            // L. Audiencias y Términos Fatales (12 primeros expedientes para agenda activa)
            if ($index < 12) {
                $esPlazo = ($index % 2 == 0);
                EventoCalendario::create([
                    'proceso_id' => $proceso->id,
                    'user_id' => $abogado->id,
                    'titulo' => $esPlazo 
                        ? 'Vencimiento de Término / Diligencia: ' . $proceso->codigo_interno 
                        : 'Audiencia Jurisdiccional: ' . $articulo->epigrafe_delito,
                    'tipo_evento' => $esPlazo ? 'Plazo Fatal' : 'Audiencia',
                    'fecha_hora_inicio' => now()->addDays(rand(1, 14))->setTime(rand(9, 16), 0),
                    'fecha_hora_fin' => now()->addDays(rand(1, 14))->setTime(rand(16, 18), 0),
                    'lugar_enlace' => $juzgadoNombre ?: 'Tribunal Departamental de Justicia',
                    'es_plazo_fatal' => $esPlazo,
                    'prioridad' => $esPlazo ? 'Alta' : 'Media',
                    'estado' => 'Pendiente',
                    'observaciones' => $item['estado_detalle'] ?? 'Control de plazos judiciales de ley.',
                ]);
            }
        }

        $this->command->info("¡Base de datos migrada y poblada exitosamente con arquitectura paramétrica completa, RBAC y 82 causas reales!");
    }
}