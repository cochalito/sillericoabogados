<?php

namespace Database\Seeders;

use App\Models\Actuacion;
use App\Models\AuditoriaProceso;
use App\Models\Cliente;
use App\Models\EventoCalendario;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Lawyers / Users
        $abogados = [
            [
                'name' => 'Alan Sillerico Segurondo',
                'email' => 'alan@sillericoabogados.com',
                'cargo' => 'Socio Fundador & Director General',
                'iniciales' => 'AS',
                'color' => 'bg-brand-green text-brand-gold',
            ],
            [
                'name' => 'Anghela Soliz de Sillerico',
                'email' => 'anghela@sillericoabogados.com',
                'cargo' => 'Socia & Subdirectora',
                'iniciales' => 'AS',
                'color' => 'bg-emerald-800 text-brand-gold',
            ],
            [
                'name' => 'Bismarck Molina',
                'email' => 'bismarck@sillericoabogados.com',
                'cargo' => 'Asociado Jefe Área Penal',
                'iniciales' => 'BM',
                'color' => 'bg-brand-green text-white',
            ],
            [
                'name' => 'Mauricio Mercado Foronda',
                'email' => 'mauricio@sillericoabogados.com',
                'cargo' => 'Asociado Jefe Área Civil',
                'iniciales' => 'MM',
                'color' => 'bg-blue-900 text-white',
            ],
            [
                'name' => 'Álvaro Arias Antequera',
                'email' => 'alvaro@sillericoabogados.com',
                'cargo' => 'Asociado Jefe Área Comercial',
                'iniciales' => 'AA',
                'color' => 'bg-slate-700 text-white',
            ],
            [
                'name' => 'Eduardo Yupanqui Quispe',
                'email' => 'eduardo@sillericoabogados.com',
                'cargo' => 'Abogado Asociado',
                'iniciales' => 'EY',
                'color' => 'bg-slate-600 text-white',
            ],
            [
                'name' => 'Amalia Paucara Mamani',
                'email' => 'amalia@sillericoabogados.com',
                'cargo' => 'Abogada Junior',
                'iniciales' => 'AP',
                'color' => 'bg-amber-600 text-white',
            ],
            [
                'name' => 'Massiel Rullier Loza',
                'email' => 'massiel@sillericoabogados.com',
                'cargo' => 'Abogada Junior',
                'iniciales' => 'MR',
                'color' => 'bg-rose-700 text-white',
            ],
        ];

        $userModels = [];
        foreach ($abogados as $abg) {
            $userModels[$abg['name']] = User::firstOrCreate(
                ['email' => $abg['email']],
                [
                    'name' => $abg['name'],
                    'password' => Hash::make('password123'),
                    'cargo' => $abg['cargo'],
                    'iniciales' => $abg['iniciales'],
                    'color' => $abg['color'],
                    'es_abogado' => true,
                    'activo' => true,
                ]
            );
        }

        // 2. Load JSON dataset with the 82 real cases from docx
        $jsonPath = database_path('data/procesos_seed.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("No se encontró el archivo $jsonPath");
            return;
        }

        $casos = json_decode(file_get_contents($jsonPath), true);
        $this->command->info("Cargando " . count($casos) . " casos reales desde procesos.docx...");

        $userList = array_values($userModels);

        foreach ($casos as $index => $item) {
            // Find or create Cliente
            $clienteNombre = $item['cliente_nombre'];
            $cliente = Cliente::firstOrCreate(
                ['nombre_razon_social' => $clienteNombre],
                [
                    'tipo_cliente' => $item['tipo_cliente'],
                    'persona_contacto' => $clienteNombre,
                    'celular_whatsapp' => '700' . rand(10000, 99999),
                    'email' => strtolower(str_replace(' ', '', substr($clienteNombre, 0, 10))) . '@correo.com',
                    'activo' => true,
                ]
            );

            // Assign lawyer
            if ($item['materia'] === 'Penal') {
                $abogado = $userModels['Bismarck Molina'] ?? $userList[0];
            } elseif ($item['materia'] === 'Civil') {
                $abogado = $userModels['Mauricio Mercado Foronda'] ?? $userList[0];
            } elseif ($item['materia'] === 'Corporativo') {
                $abogado = $userModels['Álvaro Arias Antequera'] ?? $userList[0];
            } else {
                $abogado = $userList[$index % count($userList)];
            }

            // Create Proceso
            $proceso = Proceso::firstOrCreate(
                ['codigo_interno' => $item['codigo_interno']],
                [
                    'cud' => $item['cud'],
                    'nurej' => $item['nurej'],
                    'ianus' => $item['ianus'],
                    'codigo_caso' => $item['codigo_caso'],
                    'portal_fiscalia' => $item['portal_fiscalia'],
                    'jurisdiccion' => $item['jurisdiccion'],
                    'materia' => $item['materia'],
                    'cliente_id' => $cliente->id,
                    'rol_cliente' => $item['rol_cliente'],
                    'demandante_denunciante' => $item['demandante_denunciante'],
                    'demandado_denunciado' => $item['demandado_denunciado'],
                    'juzgado_tribunal' => $item['juzgado_tribunal'],
                    'sala' => $item['sala'],
                    'autoridad_juez_fiscal' => $item['autoridad_juez_fiscal'],
                    'investigador_asignado' => $item['investigador_asignado'],
                    'delito_accion' => $item['delito_accion'],
                    'etapa_procesal' => $item['etapa_procesal'],
                    'estado' => $item['estado'],
                    'estado_detalle' => $item['estado_detalle'],
                    'abogado_id' => $abogado->id,
                    'fecha_inicio' => now()->subDays(rand(10, 300)),
                ]
            );

            // Audit log for process creation
            AuditoriaProceso::create([
                'proceso_id' => $proceso->id,
                'user_id' => $abogado->id,
                'accion' => 'CREACION_PROCESO',
                'descripcion' => "Apertura e ingreso de expediente judicial {$proceso->codigo_interno} para patrocinio de {$proceso->demandante_denunciante}",
                'detalles' => [
                    'codigo' => $proceso->codigo_interno,
                    'materia' => $proceso->materia,
                    'delito' => $proceso->delito_accion,
                    'juzgado' => $proceso->juzgado_tribunal,
                ],
                'ip_address' => '192.168.1.' . rand(20, 99),
                'created_at' => $proceso->fecha_inicio ?: now()->subDays(rand(10, 60)),
            ]);

            // Seed initial actuation
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

            // For cases with urgent pending items, seed Calendar events (audiences / deadlines)
            if ($index < 12) {
                $esPlazo = ($index % 2 == 0);
                EventoCalendario::create([
                    'proceso_id' => $proceso->id,
                    'user_id' => $abogado->id,
                    'titulo' => $esPlazo 
                        ? 'Vencimiento de Término / Diligencia: ' . $proceso->codigo_interno 
                        : 'Audiencia Jurisdiccional: ' . $proceso->delito_accion,
                    'tipo_evento' => $esPlazo ? 'Plazo Fatal' : 'Audiencia',
                    'fecha_hora_inicio' => now()->addDays(rand(1, 14))->setTime(rand(9, 16), 0),
                    'fecha_hora_fin' => now()->addDays(rand(1, 14))->setTime(rand(16, 18), 0),
                    'lugar_enlace' => $proceso->juzgado_tribunal ?: 'Tribunal Departamental de Justicia',
                    'es_plazo_fatal' => $esPlazo,
                    'prioridad' => $esPlazo ? 'Alta' : 'Media',
                    'estado' => 'Pendiente',
                    'observaciones' => $item['estado_detalle'],
                ]);
            }
        }

        $this->command->info("¡Base de datos poblada exitosamente con 82 causas reales, clientes y eventos!");
    }
}