<?php

namespace Database\Seeders;

use App\Models\ArticuloLey;
use App\Models\EstadoProceso;
use App\Models\EtapaProcesal;
use App\Models\GradoPolicial;
use App\Models\Jurisdiccion;
use App\Models\Materia;
use App\Models\RolParte;
use Illuminate\Database\Seeder;

class ParametricasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Materias Jurídicas
        $materias = [
            ['codigo' => 'PENAL', 'nombre' => 'Penal', 'color_hex' => '#0f2d1e', 'color_badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['codigo' => 'CIVIL', 'nombre' => 'Civil', 'color_hex' => '#1e3a8a', 'color_badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
            ['codigo' => 'FAMILIAR', 'nombre' => 'Familiar', 'color_hex' => '#9d174d', 'color_badge' => 'bg-rose-50 text-rose-700 border-rose-200'],
            ['codigo' => 'COMERCIAL', 'nombre' => 'Corporativo / Comercial', 'color_hex' => '#334155', 'color_badge' => 'bg-slate-100 text-slate-700 border-slate-200'],
            ['codigo' => 'LABORAL', 'nombre' => 'Laboral', 'color_hex' => '#d97706', 'color_badge' => 'bg-amber-50 text-brand-gold border-brand-gold/30'],
        ];

        $materiaModels = [];
        foreach ($materias as $m) {
            $materiaModels[$m['codigo']] = Materia::firstOrCreate(['codigo' => $m['codigo']], $m);
        }

        // 2. Jurisdicciones
        $jurisdicciones = [
            ['codigo' => 'CENTRO', 'nombre' => 'La Paz - Centro', 'departamento' => 'La Paz'],
            ['codigo' => 'ZONA_SUR', 'nombre' => 'La Paz - Zona Sur', 'departamento' => 'La Paz'],
            ['codigo' => 'EL_ALTO', 'nombre' => 'El Alto', 'departamento' => 'La Paz'],
            ['codigo' => 'SUCRE', 'nombre' => 'Sucre (Tribunal Supremo)', 'departamento' => 'Chuquisaca'],
        ];

        foreach ($jurisdicciones as $j) {
            Jurisdiccion::firstOrCreate(['codigo' => $j['codigo']], $j);
        }

        // 3. Roles de Partes Procesales
        $rolesPartes = [
            ['codigo' => 'DEMANDANTE_QUERELLANTE', 'nombre' => 'Demandante / Querellante'],
            ['codigo' => 'DEMANDADO_DENUNCIADO', 'nombre' => 'Demandado / Denunciado'],
            ['codigo' => 'TERCERO_INTERESADO', 'nombre' => 'Tercero Interesado'],
            ['codigo' => 'VICTIMA', 'nombre' => 'Víctima'],
        ];

        foreach ($rolesPartes as $rp) {
            RolParte::firstOrCreate(['codigo' => $rp['codigo']], $rp);
        }

        // 4. Grados Policiales (Ley Orgánica de la Policía Boliviana)
        $grados = [
            ['codigo' => 'POL', 'nombre' => 'Policía', 'abreviatura' => 'Pol.', 'jerarquia_orden' => 1],
            ['codigo' => 'CBO', 'nombre' => 'Cabo', 'abreviatura' => 'Cbo.', 'jerarquia_orden' => 2],
            ['codigo' => 'SGTO_2DO', 'nombre' => 'Sargento Segundo', 'abreviatura' => 'Sgto. 2do.', 'jerarquia_orden' => 3],
            ['codigo' => 'SGTO_1RO', 'nombre' => 'Sargento Primero', 'abreviatura' => 'Sgto. 1ro.', 'jerarquia_orden' => 4],
            ['codigo' => 'SOF_2DO', 'nombre' => 'Suboficial Segundo', 'abreviatura' => 'Sof. 2do.', 'jerarquia_orden' => 5],
            ['codigo' => 'SOF_1RO', 'nombre' => 'Suboficial Primero', 'abreviatura' => 'Sof. 1ro.', 'jerarquia_orden' => 6],
            ['codigo' => 'SOF_MY', 'nombre' => 'Suboficial Mayor', 'abreviatura' => 'Sof. My.', 'jerarquia_orden' => 7],
            ['codigo' => 'SOF_SUP', 'nombre' => 'Suboficial Superior', 'abreviatura' => 'Sof. Sup.', 'jerarquia_orden' => 8],
            ['codigo' => 'STTE', 'nombre' => 'Subteniente', 'abreviatura' => 'Stte.', 'jerarquia_orden' => 9],
            ['codigo' => 'TTE', 'nombre' => 'Teniente', 'abreviatura' => 'Tte.', 'jerarquia_orden' => 10],
            ['codigo' => 'CAP', 'nombre' => 'Capitán', 'abreviatura' => 'Cap.', 'jerarquia_orden' => 11],
            ['codigo' => 'MY', 'nombre' => 'Mayor', 'abreviatura' => 'My.', 'jerarquia_orden' => 12],
            ['codigo' => 'TCNL', 'nombre' => 'Teniente Coronel', 'abreviatura' => 'Tcnl.', 'jerarquia_orden' => 13],
            ['codigo' => 'CNL', 'nombre' => 'Coronel', 'abreviatura' => 'Cnl.', 'jerarquia_orden' => 14],
        ];

        foreach ($grados as $g) {
            GradoPolicial::firstOrCreate(['codigo' => $g['codigo']], $g);
        }

        // 5. Estados del Proceso
        $estados = [
            [
                'codigo' => 'EN_TRAMITE',
                'nombre' => 'En Trámite',
                'tipo_agrupador' => 'ACTIVO',
                'descripcion_estado' => 'Expediente activo con plazos abiertos y diligencias procesales en curso ordinario.',
                'color_badge' => 'bg-slate-100 text-slate-700',
                'orden' => 1,
            ],
            [
                'codigo' => 'SENTENCIA',
                'nombre' => 'Sentencia',
                'tipo_agrupador' => 'CONCLUIDO',
                'descripcion_estado' => 'Causa resuelta mediante resolución judicial de fondo de primera instancia (condenatoria o absolutoria).',
                'color_badge' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'orden' => 2,
            ],
            [
                'codigo' => 'CONCILIACION',
                'nombre' => 'Conciliación',
                'tipo_agrupador' => 'CONCLUIDO',
                'descripcion_estado' => 'Conflicto solucionado mediante salida alternativa o acuerdo reparatorio homologado.',
                'color_badge' => 'bg-teal-50 text-teal-700 border border-teal-200',
                'orden' => 3,
            ],
            [
                'codigo' => 'APELACION',
                'nombre' => 'Apelación',
                'tipo_agrupador' => 'IMPUGNACION',
                'descripcion_estado' => 'Expediente radicado en Sala Departamental en mérito a recurso de apelación restringida o incidental.',
                'color_badge' => 'bg-blue-50 text-blue-700 border border-blue-200',
                'orden' => 4,
            ],
            [
                'codigo' => 'CASACION',
                'nombre' => 'Casación',
                'tipo_agrupador' => 'IMPUGNACION',
                'descripcion_estado' => 'Causa remitida ante el Tribunal Supremo de Justicia en Sucre pendiente de Auto Supremo.',
                'color_badge' => 'bg-amber-50 text-brand-gold border border-brand-gold/30',
                'orden' => 5,
            ],
            [
                'codigo' => 'REBELDIA',
                'nombre' => 'Rebeldía',
                'tipo_agrupador' => 'SUSPENDIDO',
                'descripcion_estado' => 'Imputado o demandado formalmente declarado rebelde con mandamiento de aprehensión y arraigo.',
                'color_badge' => 'bg-rose-50 text-rose-700 border border-rose-200',
                'orden' => 6,
            ],
            [
                'codigo' => 'ARCHIVADO',
                'nombre' => 'Archivado',
                'tipo_agrupador' => 'CONCLUIDO',
                'descripcion_estado' => 'Causa concluida definitivamente con extinción de la acción o sobreseimiento en archivo judicial.',
                'color_badge' => 'bg-slate-200 text-slate-600',
                'orden' => 7,
            ],
        ];

        foreach ($estados as $est) {
            EstadoProceso::firstOrCreate(['codigo' => $est['codigo']], $est);
        }

        // 6. Etapas Procesales (Penal y Civil)
        $penalId = $materiaModels['PENAL']->id;
        $etapasPenal = [
            ['orden' => 1, 'nombre' => 'Investigación Preliminar', 'dias_termino_sugerido' => 60],
            ['orden' => 2, 'nombre' => 'Imputación Formal', 'dias_termino_sugerido' => 180],
            ['orden' => 3, 'nombre' => 'Acusación Formal', 'dias_termino_sugerido' => 30],
            ['orden' => 4, 'nombre' => 'Juicio Oral', 'dias_termino_sugerido' => 90],
            ['orden' => 5, 'nombre' => 'Sentencia y Ejecución', 'dias_termino_sugerido' => 15],
            ['orden' => 6, 'nombre' => 'Recurso de Apelación', 'dias_termino_sugerido' => 60],
            ['orden' => 7, 'nombre' => 'Recurso de Casación (TSJ)', 'dias_termino_sugerido' => 180],
        ];

        foreach ($etapasPenal as $ep) {
            EtapaProcesal::firstOrCreate(
                ['materia_id' => $penalId, 'nombre' => $ep['nombre']],
                array_merge($ep, ['materia_id' => $penalId])
            );
        }

        $civilId = $materiaModels['CIVIL']->id;
        $etapasCivil = [
            ['orden' => 1, 'nombre' => 'Demanda y Citación', 'dias_termino_sugerido' => 30],
            ['orden' => 2, 'nombre' => 'Contestación y Excepciones', 'dias_termino_sugerido' => 30],
            ['orden' => 3, 'nombre' => 'Audiencia Preliminar', 'dias_termino_sugerido' => 45],
            ['orden' => 4, 'nombre' => 'Audiencia Complementaria', 'dias_termino_sugerido' => 30],
            ['orden' => 5, 'nombre' => 'Sentencia Civil', 'dias_termino_sugerido' => 40],
            ['orden' => 6, 'nombre' => 'Apelación en el TDJ', 'dias_termino_sugerido' => 60],
            ['orden' => 7, 'nombre' => 'Casación en Sucre', 'dias_termino_sugerido' => 180],
        ];

        foreach ($etapasCivil as $ec) {
            EtapaProcesal::firstOrCreate(
                ['materia_id' => $civilId, 'nombre' => $ec['nombre']],
                array_merge($ec, ['materia_id' => $civilId])
            );
        }

        // 7. Catálogo de Artículos Normativos de Bolivia
        $articulos = [
            // Código Penal
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 335',
                'epigrafe_delito' => 'Estafa',
                'materia_id' => $penalId,
                'pena_minima_anos' => 1.0,
                'pena_maxima_anos' => 5.0,
                'texto_tipificacion' => 'El que con la intención de obtener para sí o un tercero un beneficio económico indebido, mediante engaños o artificios indujere a otro en error, o lo mantuviere en él, con perjuicio patrimonial ajeno.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 185 bis',
                'epigrafe_delito' => 'Legitimación de Ganancias Ilícitas',
                'materia_id' => $penalId,
                'pena_minima_anos' => 5.0,
                'pena_maxima_anos' => 10.0,
                'texto_tipificacion' => 'El que adquiera, convierta, transfiera bienes, recursos o derechos, conociendo que proceden de delitos vinculados al tráfico ilícito de sustancias, corrupción o actividades ilícitas.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 198',
                'epigrafe_delito' => 'Falsedad Material',
                'materia_id' => $penalId,
                'pena_minima_anos' => 1.0,
                'pena_maxima_anos' => 6.0,
                'texto_tipificacion' => 'El que forjare en todo o en parte un documento público falso o alterare uno verdadero, de modo que pueda resultar perjuicio.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 199',
                'epigrafe_delito' => 'Falsedad Ideológica',
                'materia_id' => $penalId,
                'pena_minima_anos' => 1.0,
                'pena_maxima_anos' => 6.0,
                'texto_tipificacion' => 'El que insertare o hiciere insertar en un instrumento público declaraciones falsas concernientes a un hecho que el documento deba probar.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 203',
                'epigrafe_delito' => 'Uso de Instrumento Falsificado',
                'materia_id' => $penalId,
                'pena_minima_anos' => 1.0,
                'pena_maxima_anos' => 6.0,
                'texto_tipificacion' => 'El que a sabiendas hiciere uso de un documento falso o alterado, será sancionado como si fuere autor de la falsedad.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 271',
                'epigrafe_delito' => 'Lesiones Graves y Leves',
                'materia_id' => $penalId,
                'pena_minima_anos' => 3.0,
                'pena_maxima_anos' => 6.0,
                'texto_tipificacion' => 'El que de cualquier modo ocasionare a otro un daño en el cuerpo o en la salud, que no estuviere previsto en el artículo anterior.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 326',
                'epigrafe_delito' => 'Hurto',
                'materia_id' => $penalId,
                'pena_minima_anos' => 0.5,
                'pena_maxima_anos' => 3.0,
                'texto_tipificacion' => 'El que se apoderare ilegítimamente de una cosa mueble ajena sin violencia en las personas ni fuerza en las cosas.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 327',
                'epigrafe_delito' => 'Hurto Agravado',
                'materia_id' => $penalId,
                'pena_minima_anos' => 1.0,
                'pena_maxima_anos' => 5.0,
                'texto_tipificacion' => 'La pena será de presidio de uno a cinco años si el hurto fuere cometido con escalamiento, sobre cosas afectas a un servicio público o de noche.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 343',
                'epigrafe_delito' => 'Quiebra Fraudulenta',
                'materia_id' => $penalId,
                'pena_minima_anos' => 2.0,
                'pena_maxima_anos' => 6.0,
                'texto_tipificacion' => 'El comerciante que se declare en quiebra y hubiere ocultado o mutilado sus libros, disminuido su activo o simulado deudas inexistentes.'
            ],
            [
                'codigo_normativo' => 'CODIGO_PENAL',
                'numero_articulo' => 'Art. 346 bis',
                'epigrafe_delito' => 'Avasallamiento',
                'materia_id' => $penalId,
                'pena_minima_anos' => 4.0,
                'pena_maxima_anos' => 8.0,
                'texto_tipificacion' => 'El que por sí o por terceros, mediante violencia, engaño o abuso de confianza invada o despoje la tenencia o posesión de predios urbanos o rurales ajenos.'
            ],
            [
                'codigo_normativo' => 'LEY_348',
                'numero_articulo' => 'Art. 84 / Ley 348',
                'epigrafe_delito' => 'Violencia Familiar o Doméstica',
                'materia_id' => $materiaModels['FAMILIAR']->id,
                'pena_minima_anos' => 2.0,
                'pena_maxima_anos' => 4.0,
                'texto_tipificacion' => 'Quien agreda física, psicológica o sexualmente a su cónyuge, conviviente o persona con quien mantenga o hubiere mantenido relación análoga.'
            ],
            [
                'codigo_normativo' => 'CODIGO_CIVIL',
                'numero_articulo' => 'Art. 621 / CPC 392',
                'epigrafe_delito' => 'Proceso de Desalojo y Restitución',
                'materia_id' => $civilId,
                'pena_minima_anos' => null,
                'pena_maxima_anos' => null,
                'texto_tipificacion' => 'Demanda civil de resolución de contrato de arrendamiento y entrega judicial del bien inmueble por vencimiento o falta de pago.'
            ],
            [
                'codigo_normativo' => 'CODIGO_FAMILIAS',
                'numero_articulo' => 'Art. 109 / Ley 603',
                'epigrafe_delito' => 'Asistencia Familiar',
                'materia_id' => $materiaModels['FAMILIAR']->id,
                'pena_minima_anos' => null,
                'pena_maxima_anos' => null,
                'texto_tipificacion' => 'Obligación civil preferente y de cumplimiento forzoso mediante apremio corporal para la manutención, educación y salud de los hijos menores de edad.'
            ],
        ];

        foreach ($articulos as $art) {
            ArticuloLey::firstOrCreate(
                ['numero_articulo' => $art['numero_articulo'], 'epigrafe_delito' => $art['epigrafe_delito']],
                $art
            );
        }
    }
}
