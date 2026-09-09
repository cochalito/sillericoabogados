@extends('layouts.admin')

@section('title', 'Dashboard - Sillerico & Abogados')
@section('header_title', 'Panel de Control - Sillerico & Abogados')

@section('content')
<div x-data="dashboardData()" class="space-y-8 animate-fade-in">
    <!-- Header Greeting and Date -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-brand-green">Hola, Dr. Alan</h2>
            <p class="text-sm text-slate-500">Bienvenido al sistema. Aquí está el control de procesos para hoy.</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-100 shadow-xs text-xs font-semibold text-slate-600">
            <i data-lucide="calendar" class="w-4 h-4 text-brand-gold"></i>
            <span x-text="currentDate"></span>
        </div>
    </div>

    <!-- KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- KPI 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between group hover:border-brand-green/20 transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Procesos Activos</span>
                <h3 class="text-3xl font-bold text-brand-green">{{ $totalProcesos }}</h3>
                <span class="inline-flex items-center text-[10px] font-semibold text-emerald-600 gap-0.5">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    <span>+12.4% este mes</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-green/5 text-brand-green flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-300">
                <i data-lucide="folder-open" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between group hover:border-brand-green/20 transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Audiencias esta Semana</span>
                <h3 class="text-3xl font-bold text-brand-green">{{ $proximosEventos->count() }}</h3>
                <span class="inline-flex items-center text-[10px] font-semibold text-rose-500 gap-0.5">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    <span>Próxima: Mañana 09:30</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-gold/10 text-brand-gold flex items-center justify-center group-hover:bg-brand-gold group-hover:text-white transition-all duration-300">
                <i data-lucide="gavel" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between group hover:border-brand-green/20 transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Clientes Registrados</span>
                <h3 class="text-3xl font-bold text-brand-green">{{ $totalClientes }}</h3>
                <span class="inline-flex items-center text-[10px] font-semibold text-emerald-600 gap-0.5">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    <span>+4 nuevos clientes</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-green/5 text-brand-green flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-300">
                <i data-lucide="users-round" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs flex items-center justify-between group hover:border-brand-green/20 transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tasa de Resolución</span>
                <h3 class="text-3xl font-bold text-brand-green">94.2%</h3>
                <span class="inline-flex items-center text-[10px] font-semibold text-brand-gold gap-0.5">
                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                    <span>Casos ganados/conciliados</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-gold/10 text-brand-gold flex items-center justify-center group-hover:bg-brand-gold group-hover:text-white transition-all duration-300">
                <i data-lucide="trophy" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Left (Distribution) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs lg:col-span-1 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 tracking-tight">Distribución de Procesos</h3>
                <p class="text-xs text-slate-400 mb-4">Clasificación por materia jurídica.</p>
            </div>
            <div class="relative flex items-center justify-center h-52">
                <canvas id="materiaChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 text-[10px] font-semibold text-slate-500">
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-950"></span> Penal ({{ $casosPenales }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Laboral ({{ $casosLaborales }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Familiar ({{ $casosFamiliares }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Civil ({{ $casosCiviles }})</div>
            </div>
        </div>

        <!-- Chart Right (Volume) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs lg:col-span-2 flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 tracking-tight">Evolución de Procesos en Curso</h3>
                    <p class="text-xs text-slate-400">Total de procesos activos por mes en 2026.</p>
                </div>
                <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-lg border border-slate-100 text-[10px] font-bold">
                    <button class="px-2.5 py-1 bg-white shadow-xs rounded text-brand-green">1S</button>
                    <button class="px-2.5 py-1 rounded text-slate-400 hover:text-slate-600">AÑO</button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Processes & Team Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Table Area (Takes 3 columns) -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden lg:col-span-3">
            <!-- Table Header -->
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 tracking-tight">Bandeja de Control de Procesos</h3>
                    <p class="text-xs text-slate-400">Procesos activos bajo seguimiento del bufete.</p>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                        </div>
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Buscar por caso, denunciado..." 
                               class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold text-slate-600">
                    </div>
                    <!-- Filter Dropdown -->
                    <select x-model="selectedMateria" 
                            class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold text-slate-600 font-medium font-sans">
                        <option value="Todas">Todas las materias</option>
                        <option value="Penal">Penal</option>
                        <option value="Laboral">Laboral</option>
                        <option value="Familiar">Familiar</option>
                    </select>
                </div>
            </div>

            <!-- Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                            <th class="py-3 px-4 w-[18%]">Nro. de Caso / CUD / NUREJ</th>
                            <th class="py-3 px-4 w-[13%]">Denunciante</th>
                            <th class="py-3 px-4 w-[15%]">Denunciado</th>
                            <th class="py-3 px-4 w-[20%]">Juzgado / Fiscalía</th>
                            <th class="py-3 px-4 w-[12%]">Delito / Acción</th>
                            <th class="py-3 px-4 w-[17%]">Estado del Proceso</th>
                            <th class="py-3 px-4 w-[5%] text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                        <template x-for="proceso in filteredProcesos" :key="proceso.codigo">
                            <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer" @click="openProcesoDetails(proceso)">
                                <!-- Case Identifiers -->
                                <td class="py-4 px-4 font-medium text-slate-800 space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                              :class="{
                                                'bg-emerald-100 text-emerald-800': proceso.tipo.includes('Portal Fis'),
                                                'bg-amber-100 text-brand-gold': proceso.tipo.includes('CUD'),
                                                'bg-blue-100 text-blue-800': proceso.tipo.includes('CASO')
                                              }" x-text="proceso.tipo"></span>
                                        <span class="text-[10px] font-bold text-slate-400" x-text="proceso.ubicacion"></span>
                                    </div>
                                    <div class="text-xs font-bold text-brand-green" x-text="proceso.codigo"></div>
                                    <div class="text-[10px] text-slate-400" x-show="proceso.nurej !== 'N/A'" x-text="`NUREJ/IANUS: ${proceso.nurej}`"></div>
                                </td>
                                
                                <!-- Denunciante -->
                                <td class="py-4 px-4 font-semibold text-slate-800 uppercase" x-text="proceso.denunciante"></td>
                                
                                <!-- Denunciado -->
                                <td class="py-4 px-4 font-bold text-slate-900 uppercase" x-text="proceso.denunciado"></td>
                                
                                <!-- Juzgado/Fiscalia -->
                                <td class="py-4 px-4 space-y-0.5">
                                    <div class="font-bold text-slate-800 leading-tight" x-text="proceso.juzgado"></div>
                                    <div class="text-[10px] text-slate-400" x-text="proceso.sala"></div>
                                    <div class="text-[10px] text-brand-gold font-medium" x-text="`Autoridad: ${proceso.fiscal}`"></div>
                                </td>
                                
                                <!-- Delito / Materia -->
                                <td class="py-4 px-4">
                                    <div class="mb-1">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                              :class="{
                                                'bg-red-50 text-red-700 border border-red-100': proceso.materia === 'Penal',
                                                'bg-blue-50 text-blue-700 border border-blue-100': proceso.materia === 'Civil',
                                                'bg-emerald-50 text-emerald-700 border border-emerald-100': proceso.materia === 'Laboral',
                                                'bg-purple-50 text-purple-700 border border-purple-100': proceso.materia === 'Corporativo',
                                                'bg-rose-50 text-rose-700 border border-rose-100': proceso.materia === 'Familiar'
                                              }" x-text="proceso.materia"></span>
                                    </div>
                                    <div class="font-semibold text-slate-700 uppercase text-[10px] leading-tight" x-text="proceso.delito"></div>
                                </td>
                                
                                <!-- Estado del Proceso (Paper Report Text) -->
                                <td class="py-4 px-4 space-y-1">
                                    <div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                                              :class="{
                                                'bg-amber-50 text-brand-gold border border-brand-gold/30': proceso.estado_badge === 'Casación',
                                                'bg-emerald-50 text-emerald-600 border border-emerald-100': proceso.estado_badge === 'Sentencia' || proceso.estado_badge === 'Conciliación',
                                                'bg-rose-50 text-rose-600 border border-rose-100': proceso.estado_badge === 'Apelación' || proceso.estado_badge === 'Rebeldía',
                                                'bg-slate-50 text-slate-500 border border-slate-200': proceso.estado_badge === 'Archivado'
                                              }" x-text="proceso.estado_badge"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 line-clamp-3 leading-relaxed" x-text="proceso.estado"></p>
                                </td>
                                
                                <!-- View Detail -->
                                <td class="py-4 px-4 text-center" @click.stop>
                                    <button @click="openProcesoDetails(proceso)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-brand-green transition-all" title="Ver Historial de Proceso">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between text-xs text-slate-400">
                <span x-text="`Mostrando ${filteredProcesos.length} de ${procesos.length} procesos`"></span>
                <div class="flex items-center gap-1">
                    <button class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50" disabled>
                        <i data-lucide="chevron-left" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <button class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50" disabled>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Team Sidebar Panel (Takes 1 column) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs lg:col-span-1 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 tracking-tight">Equipo del Bufete</h3>
                <p class="text-xs text-slate-400 mb-4">Profesionales activos en el sistema.</p>
                
                <!-- Members List -->
                <div class="space-y-3.5 overflow-y-auto max-h-[480px] pr-1">
                    <template x-for="miembro in equipo" :key="miembro.nombre">
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50/75 transition-colors">
                            <!-- Initials Circle -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 shadow-2xs border border-slate-100"
                                 :class="miembro.color"
                                 x-text="miembro.iniciales"></div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-slate-800 truncate" x-text="miembro.nombre"></h4>
                                <p class="text-[9px] text-slate-400 truncate font-medium" x-text="miembro.cargo"></p>
                            </div>
                            <span class="text-[8px] font-bold px-1.5 py-0.5 rounded-full shrink-0"
                                  :class="miembro.activos > 0 ? 'bg-brand-gold/10 text-brand-gold border border-brand-gold/25' : 'bg-slate-100 text-slate-400'"
                                  x-text="miembro.activos > 0 ? `${miembro.activos} proc.` : 'libre'"></span>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-wider">Alan Sillerico Segurondo</span>
                <span class="text-[8px] text-brand-gold font-bold uppercase tracking-widest">Director General</span>
            </div>
        </div>
    </div>

    <!-- DETALLES SLIDE-OVER (SLIDE FROM RIGHT) - WIDER max-w-2xl -->
    <div class="fixed inset-0 z-50 overflow-hidden" 
         x-show="slideOverOpen" 
         style="display: none;"
         x-cloak>
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity duration-300"
                 x-show="slideOverOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="slideOverOpen = false"></div>

            <!-- Panel Container -->
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                <!-- Slide-over Card -->
                <div class="pointer-events-auto w-screen max-w-2xl bg-white shadow-2xl flex flex-col justify-between transition-transform duration-300"
                     x-show="slideOverOpen"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-400 tracking-widest uppercase">Expediente de Proceso</span>
                            <h3 class="text-xl font-bold text-brand-green" x-text="activeProceso.codigo"></h3>
                        </div>
                        <button @click="slideOverOpen = false; formBitacoraOpen = false;" class="p-1 rounded-lg hover:bg-slate-200/50 text-slate-400 hover:text-slate-600 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Two-Column Case Details -->
                        <div class="bg-brand-green-light/20 p-5 rounded-2xl border border-brand-green/5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Materia Jurídica</span>
                                    <span class="text-sm font-bold text-brand-green" x-text="activeProceso.materia"></span>
                                </div>
                                <hr class="border-slate-200/50">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Denunciante / Cliente</span>
                                    <h4 class="text-base font-extrabold text-slate-800 uppercase" x-text="activeProceso.denunciante"></h4>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <span class="text-xs text-slate-400" x-text="activeProceso.correo"></span>
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <a :href="'tel:' + activeProceso.telefono" class="text-xs text-brand-gold font-bold hover:underline" x-text="activeProceso.telefono"></a>
                                    </div>
                                </div>
                                <hr class="border-slate-200/50">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Denunciado</span>
                                    <h4 class="text-base font-extrabold text-slate-800 uppercase" x-text="activeProceso.denunciado"></h4>
                                </div>
                            </div>
                            <div class="space-y-3 md:border-l md:border-slate-200/50 md:pl-4">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Juzgado / Organismo</span>
                                    <p class="text-sm font-bold text-slate-700 leading-tight" x-text="activeProceso.juzgado"></p>
                                    <span class="text-xs text-slate-400 block mt-0.5" x-text="activeProceso.sala"></span>
                                    <span class="text-xs text-brand-gold font-semibold block mt-0.5" x-text="`Autoridad: ${activeProceso.fiscal}`"></span>
                                </div>
                                <hr class="border-slate-200/50">
                                <div class="flex justify-between items-center gap-2">
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Abogado Responsable</span>
                                        <p class="text-sm font-extrabold text-slate-700" x-text="activeProceso.abogado"></p>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold shrink-0"
                                          :class="{
                                            'bg-amber-50 text-brand-gold border border-brand-gold/30': activeProceso.estado_badge === 'Casación',
                                            'bg-emerald-50 text-emerald-600 border border-emerald-100': activeProceso.estado_badge === 'Sentencia' || activeProceso.estado_badge === 'Conciliación',
                                            'bg-rose-50 text-rose-600 border border-rose-100': activeProceso.estado_badge === 'Apelación' || activeProceso.estado_badge === 'Rebeldía',
                                            'bg-slate-50 text-slate-500 border border-slate-200': activeProceso.estado_badge === 'Archivado'
                                          }" x-text="activeProceso.estado_badge"></span>
                                </div>
                                <hr class="border-slate-200/50">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Delito / Acción</span>
                                    <p class="text-sm font-bold text-brand-green uppercase" x-text="activeProceso.delito"></p>
                                </div>
                            </div>
                            
                            <!-- Detailed legal status text spans full width -->
                            <div class="col-span-1 md:col-span-2 pt-2 border-t border-slate-200/50">
                                <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Estado Detallado del Proceso</span>
                                <p class="text-sm text-slate-600 leading-relaxed bg-white/60 p-3 rounded-xl border border-slate-100/50 font-medium" x-text="activeProceso.estado"></p>
                            </div>
                        </div>

                        <!-- Timeline Bitacora (Enriched with Comments & Documents) -->
                        <div class="space-y-4">
                            <!-- Title and Collapse Button -->
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <h4 class="text-sm font-extrabold text-brand-green uppercase tracking-wide flex items-center gap-1.5">
                                    <i data-lucide="history" class="w-4.5 h-4.5 text-brand-gold"></i>
                                    Bitácora de actividades
                                </h4>
                                <button type="button" @click="formBitacoraOpen = !formBitacoraOpen"
                                        class="flex items-center gap-1 px-3 py-1 bg-brand-gold/10 hover:bg-brand-gold/25 text-brand-gold text-xs font-bold rounded-lg border border-brand-gold/20 transition-all shadow-2xs">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                    Nueva actividad
                                </button>
                            </div>

                            <!-- Collapsible Form to Add Milestone -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3"
                                 x-show="formBitacoraOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 style="display: none;"
                                 x-cloak>
                                <h5 class="text-[11px] font-bold text-brand-green uppercase tracking-wider">Registrar Nueva Actividad</h5>
                                <div class="space-y-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase">Descripción de la actividad</label>
                                        <input type="text" x-model="newHito.accion" placeholder="Descripción de la actividad realizada..." required
                                               class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold bg-white text-slate-700 font-sans">
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase">Comentario de la actividad</label>
                                        <textarea x-model="newHito.comentarios" placeholder="Notas internas sobre esta actuación..." rows="2"
                                                  class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold bg-white text-slate-700 font-sans"></textarea>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase">Archivos adjuntos</label>
                                        <div class="relative flex items-center justify-center w-full">
                                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-white hover:bg-slate-50 hover:border-brand-gold transition-colors">
                                                <div class="flex flex-col items-center justify-center pt-3 pb-3">
                                                    <i data-lucide="upload-cloud" class="w-6 h-6 text-slate-400 mb-1"></i>
                                                    <p class="text-[10px] text-slate-500 font-bold" x-text="newHito.fileName ? `Archivo seleccionado: ${newHito.fileName}` : 'Hacer clic para adjuntar documento PDF'"></p>
                                                </div>
                                                <input type="file" accept=".pdf" class="hidden" 
                                                       @change="newHito.fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase">
                                        <div>
                                            <span>Abogado Ejecutante:</span>
                                            <span class="text-brand-green">Alan Sillerico Segurondo (Usuario Actual)</span>
                                        </div>
                                    </div>

                                    <!-- Telegram Integration Block -->
                                    <div class="border-t border-slate-200 pt-3 space-y-2">
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox" x-model="telegramNotify"
                                                   class="rounded text-brand-gold focus:ring-brand-gold border-slate-300 w-4 h-4">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1">
                                                <i data-lucide="send" class="w-3.5 h-3.5 text-blue-500"></i>
                                                Notificar vía Telegram (Demo)
                                            </span>
                                        </label>

                                        <!-- Collapsible Config Fields -->
                                        <div x-show="telegramNotify" class="bg-white p-3 rounded-lg border border-slate-200 space-y-2"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 transform -translate-y-1"
                                             style="display: none;" x-cloak>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <div class="space-y-1">
                                                    <label class="text-[9px] font-bold text-slate-400 uppercase">Token del Bot</label>
                                                    <input type="text" x-model="telegramToken" placeholder="Token de BotFather"
                                                           class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded focus:outline-none focus:border-brand-gold bg-slate-50/50">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[9px] font-bold text-slate-400 uppercase">Chat ID</label>
                                                    <input type="text" x-model="telegramChatId" placeholder="ID del chat de destino"
                                                           class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded focus:outline-none focus:border-brand-gold bg-slate-50/50">
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center pt-1.5">
                                                <span class="text-[8px] text-slate-400 font-medium">Los datos se guardan en el navegador.</span>
                                                <button type="button" @click="testTelegram()"
                                                        class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-[10px] text-slate-600 rounded font-semibold transition-colors flex items-center gap-1">
                                                    <i data-lucide="bell" class="w-3 h-3 text-brand-gold"></i>
                                                    Probar Envío
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" @click="addHitoToProceso()"
                                            class="w-full py-2.5 bg-brand-green hover:bg-brand-green-hover text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        Añadir actividad
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Timeline container -->
                            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                                <template x-for="(hito, index) in activeProceso.hitos" :key="index">
                                    <div class="relative pb-1">
                                        <!-- Bullet dot -->
                                        <div class="absolute -left-6 top-1.5 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center shadow-xs"
                                             :class="index === 0 ? 'bg-brand-gold' : 'bg-slate-300'">
                                            <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                        </div>
                                        
                                        <div class="space-y-2 bg-slate-50/20 p-3 rounded-xl border border-slate-100">
                                            <!-- Date and Lawyer -->
                                            <div class="flex justify-between items-center text-xs font-bold">
                                                <span class="text-slate-400" x-text="hito.fecha"></span>
                                                <span class="px-2 py-0.5 rounded bg-brand-green-light text-brand-green font-bold" x-text="hito.abogado || activeProceso.abogado"></span>
                                            </div>
                                            <!-- Action -->
                                            <h5 class="text-sm font-extrabold text-slate-800 leading-snug" x-text="hito.accion"></h5>
                                            
                                            <!-- Comments (Comentarios de Bitácora) -->
                                            <div x-show="hito.comentarios" class="bg-white p-2.5 rounded-lg border border-slate-200/50 text-xs text-slate-600 leading-relaxed font-sans shadow-2xs">
                                                <div class="font-bold text-[10px] text-brand-gold uppercase tracking-wider mb-1 flex items-center gap-1 shrink-0">
                                                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                                    Notas &amp; Comentarios Internos
                                                </div>
                                                <p x-text="hito.comentarios"></p>
                                            </div>

                                            <!-- Documents associated with this milestone -->
                                            <div x-show="hito.documentos && hito.documentos.length" class="flex flex-wrap gap-1.5 pt-1">
                                                <template x-for="doc in hito.documentos" :key="doc">
                                                    <div @click.stop="openPreview(doc)" class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg px-2.5 py-1 text-xs font-bold hover:bg-rose-100 transition-colors shadow-2xs cursor-pointer text-ellipsis">
                                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                                                        <span class="truncate max-w-[180px]" x-text="doc"></span>
                                                        <button class="p-0.5 hover:bg-rose-200/50 rounded shrink-0" title="Ver Vista Previa">
                                                            <i data-lucide="eye" class="w-2.5 h-2.5"></i>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Attachments Documents (General folder) -->
                        <div class="space-y-3">
                            <h4 class="text-sm font-extrabold text-brand-green uppercase tracking-wide flex items-center gap-1.5">
                                <i data-lucide="folder-archive" class="w-4.5 h-4.5 text-brand-gold"></i>
                                Archivos Generales del Expediente
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="(doc, idx) in activeProceso.documentos" :key="idx">
                                    <div @click="openPreview(doc)" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors cursor-pointer">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="p-1.5 rounded bg-rose-100 text-rose-600 shrink-0">
                                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700 truncate" x-text="doc"></span>
                                        </div>
                                        <button class="p-1 text-slate-400 hover:text-brand-green" title="Previsualizar PDF">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions inside panel -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50/70 flex gap-3 shrink-0">
                        <button @click="slideOverOpen = false; formBitacoraOpen = false;" 
                                class="w-full px-4 py-2 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REGISTRAR NUEVO PROCESO -->
    <div class="fixed inset-0 z-50 overflow-y-auto" 
         x-show="newCaseModalOpen" 
         style="display: none;"
         @open-new-case-modal.window="newCaseModalOpen = true"
         x-cloak>
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"
                 x-show="newCaseModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="newCaseModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle"
                 x-show="newCaseModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand-green uppercase tracking-wider">Nuevo Proceso Jurídico</h3>
                    <button @click="newCaseModalOpen = false" class="p-1 rounded-lg hover:bg-slate-200/50 text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Nro. de Caso / CUD</label>
                            <input type="text" x-model="newProceso.codigo" required placeholder="e.g. LPZ1910962"
                                   class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Materia</label>
                            <select x-model="newProceso.materia" required
                                    class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-600">
                                <option value="Penal">Penal</option>
                                <option value="Civil">Civil</option>
                                <option value="Laboral">Laboral</option>
                                <option value="Familiar">Familiar</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Denunciante</label>
                            <input type="text" x-model="newProceso.denunciante" required placeholder="Denunciante / Querellante"
                                   class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Denunciado</label>
                            <input type="text" x-model="newProceso.denunciado" required placeholder="Denunciado / Imputado"
                                   class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Celular / WhatsApp</label>
                            <input type="text" x-model="newProceso.telefono" required placeholder="e.g. 59177234317"
                                   class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Tipo Reg.</label>
                            <select x-model="newProceso.tipo" required
                                    class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-600">
                                <option value="Portal Fis">Portal Fis</option>
                                <option value="CUD">CUD</option>
                                <option value="CASO">CASO</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Juzgado u Organismo</label>
                        <input type="text" x-model="newProceso.juzgado" required placeholder="e.g. JUZGADO 14° DE INSTRUCCIÓN ANTICORRUPCION"
                               class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Abogado Asignado</label>
                            <select x-model="newProceso.abogado" required
                                    class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-600">
                                <template x-for="m in equipo" :key="m.nombre">
                                    <option :value="m.nombre" x-text="m.nombre"></option>
                                </template>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Estado Inicial</label>
                            <select x-model="newProceso.estado_badge" required
                                    class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-600">
                                <option value="Casación">Casación</option>
                                <option value="Sentencia">Sentencia</option>
                                <option value="Apelación">Apelación</option>
                                <option value="Rebeldía">Rebeldía</option>
                                <option value="Conciliación">Conciliación</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Resumen de Estado del Proceso</label>
                        <textarea x-model="newProceso.estado" required rows="2" placeholder="Describa la situación actual del caso..."
                                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="newCaseModalOpen = false" 
                                class="flex-1 px-4 py-2.5 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-brand-green hover:bg-brand-green-hover text-white rounded-xl text-xs font-bold shadow-md shadow-brand-green/10 transition-colors">
                            Guardar Proceso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PREVISUALIZACION DE DOCUMENTO MODAL (HTML-BASED REALISTIC PDF VIEWER) -->
    <div class="fixed inset-0 z-55 overflow-y-auto" 
         x-show="previewModalOpen" 
         style="display: none;"
         x-cloak>
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs transition-opacity z-10"
                 x-show="previewModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closePreview()"></div>

            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="relative z-20 inline-block transform overflow-hidden rounded-2xl bg-slate-800 text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:align-middle border border-slate-700"
                 x-show="previewModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- PDF Toolbar -->
                <div class="bg-slate-900 px-6 py-3 border-b border-slate-700 flex items-center justify-between text-white shrink-0 select-none">
                    <div class="flex items-center gap-3">
                        <div class="p-1.5 rounded bg-rose-600 text-white shrink-0">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold truncate max-w-[280px] sm:max-w-[400px] text-slate-200" x-text="previewDocName"></h4>
                            <p class="text-[9px] text-slate-400 font-medium">Visor de Documentos Digitales v1.4</p>
                        </div>
                    </div>

                    <!-- PDF Viewer Controls (Always Visible, Responsive Labels) -->
                    <div class="flex items-center gap-2 sm:gap-4 text-[10px] sm:text-xs font-semibold text-slate-300">
                        <button class="hover:text-white p-0.5 sm:p-1 hover:bg-slate-800 rounded transition-colors"><i data-lucide="zoom-out" class="w-3.5 h-3.5"></i></button>
                        <span class="text-slate-400 bg-slate-950 px-1.5 py-0.5 rounded text-[9px]">120%</span>
                        <button class="hover:text-white p-0.5 sm:p-1 hover:bg-slate-800 rounded transition-colors"><i data-lucide="zoom-in" class="w-3.5 h-3.5"></i></button>
                        <span class="text-slate-700">|</span>
                        <span class="whitespace-nowrap">Pág. 1 de 2</span>
                        <span class="text-slate-700">|</span>
                        <button class="hover:text-white flex items-center gap-1 hover:bg-slate-800 px-1.5 py-0.5 sm:py-1 rounded transition-colors" title="Descargar PDF">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Descargar</span>
                        </button>
                        <button class="hover:text-white flex items-center gap-1 hover:bg-slate-800 px-1.5 py-0.5 sm:py-1 rounded transition-colors" title="Imprimir">
                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Imprimir</span>
                        </button>
                    </div>

                    <button @click="closePreview()" class="p-1 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Viewer Body -->
                <div class="p-6 bg-slate-700 flex justify-center overflow-y-auto max-h-[70vh]">
                    <!-- Simulated Paper Page -->
                    <div class="w-full max-w-2xl bg-white shadow-2xl p-10 font-serif text-slate-800 border border-slate-200 relative min-h-[800px] select-none text-[11px] leading-relaxed">
                        
                        <!-- Watermark -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-3 pointer-events-none select-none">
                            <div class="border-8 border-brand-green/10 text-brand-green/5 text-6xl font-extrabold uppercase tracking-widest rotate-45 select-none py-10 px-6 border-double">
                                SILLERICO
                            </div>
                        </div>

                        <!-- Legal Stamps Margin (Left) -->
                        <div class="absolute left-3 top-32 w-16 border border-slate-300 rounded p-1 text-[8px] font-sans text-slate-400 space-y-1 bg-slate-50/50">
                            <div class="font-extrabold text-[7px] text-center border-b border-slate-200 pb-0.5 text-brand-green uppercase">Órgano Judicial</div>
                            <div>Fojas: <span class="font-bold text-slate-700">04</span></div>
                            <div>Cargo: <span class="font-bold text-slate-700">SR-12</span></div>
                            <div>Hora: <span class="font-bold text-slate-700">14:22</span></div>
                            <div class="h-8 border border-dashed border-slate-300 flex items-center justify-center text-[7px] text-slate-300">Firma Sec.</div>
                        </div>

                        <!-- Top Official Header -->
                        <div class="text-center space-y-1 font-sans mb-8">
                            <h3 class="text-xs font-bold tracking-widest text-slate-900 uppercase">Estado Plurinacional de Bolivia</h3>
                            <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Órgano Judicial del Distrito de La Paz</h4>
                            <p class="text-[9px] text-brand-gold font-bold tracking-widest">SISTEMA INTEGRADO DE CONTROL JURÍDICO</p>
                            <hr class="border-slate-300 w-1/3 mx-auto mt-2">
                        </div>

                        <!-- Case Info Block -->
                        <div class="font-sans border border-slate-200 p-3 rounded-lg bg-slate-50/70 mb-6 flex justify-between gap-4 text-[10px]">
                            <div class="space-y-0.5">
                                <div><span class="font-bold text-slate-500">PROCESO:</span> <span class="font-extrabold text-slate-800" x-text="activeProceso.codigo"></span></div>
                                <div><span class="font-bold text-slate-500">CLIENTE:</span> <span class="font-bold text-brand-green" x-text="activeProceso.denunciante"></span></div>
                                <div><span class="font-bold text-slate-500">MATERIA:</span> <span class="font-bold" x-text="activeProceso.materia"></span></div>
                            </div>
                            <div class="text-right space-y-0.5">
                                <div><span class="font-bold text-slate-500">DOCUMENTO:</span> <span class="font-extrabold text-brand-gold uppercase" x-text="previewDocName"></span></div>
                                <div><span class="font-bold text-slate-500">JUZGADO:</span> <span class="font-bold text-slate-700 truncate max-w-[200px]" x-text="activeProceso.juzgado"></span></div>
                                <div><span class="font-bold text-slate-500">FECHA PREV:</span> <span class="font-bold text-slate-700" x-text="new Date().toLocaleDateString('es-ES')"></span></div>
                            </div>
                        </div>

                        <!-- Legal Body Text Placeholder (Serif) -->
                        <div class="space-y-4 text-justify pr-4 pl-12 text-slate-800 leading-relaxed">
                            <p class="indent-8 font-bold">VISTOS:</p>
                            <p class="indent-8">
                                Con relación al estado actual de las actuaciones procesales y habiéndose verificado las notificaciones de ley, corresponde a esta autoridad judicial pronunciarse de acuerdo con la norma adjetiva vigente en el Estado Plurinacional de Bolivia.
                            </p>
                            <p class="indent-8 font-bold">CONSIDERANDO:</p>
                            <p class="indent-8">
                                Que, en mérito a los antecedentes del cuaderno de investigaciones y los argumentos expuestos por la defensa técnica del bufete **Sillerico & Abogados** a cargo del patrocinio legal, se evidencia que se han cumplido a cabalidad los plazos establecidos por el procedimiento penal.
                            </p>
                            <p class="indent-8 text-slate-400 select-none">
                                [Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.]
                            </p>
                            <p class="indent-8">
                                Que, valorando las pruebas presentadas por la parte querellante contra el acusado en el marco de la materia de <span class="font-bold" x-text="activeProceso.materia"></span>, este tribunal determina la pertinencia del recurso deducido conforme a derecho.
                            </p>
                            <p class="indent-8 font-bold">POR TANTO:</p>
                            <p class="indent-8">
                                La autoridad judicial del Tribunal de Sentencia de La Paz, administrando justicia a nombre del Estado Plurinacional, resuelve disponer la radicatoria del trámite y tener por admitido el correspondiente recurso de apelación incidental de acuerdo a norma.
                            </p>
                            <p class="indent-8 font-bold text-right text-[10px] font-sans text-slate-400 mt-6 uppercase">
                                Regístrese, notifíquese and archívese.
                            </p>
                        </div>

                        <!-- Bottom Signatures and Stamp -->
                        <div class="mt-16 flex justify-around items-center border-t border-slate-100 pt-8 pl-12">
                            <!-- Signature 1 -->
                            <div class="text-center font-sans space-y-1">
                                <div class="w-24 h-0.5 bg-slate-300 mx-auto mb-1"></div>
                                <p class="text-[9px] font-bold text-slate-700">Dr. Vocal Relator</p>
                                <p class="text-[8px] text-slate-400">Tribunal Departamental</p>
                            </div>
                            
                            <!-- Seal / Stamp -->
                            <div class="relative w-20 h-20 rounded-full border-2 border-brand-green/20 flex flex-col items-center justify-center text-[7px] font-bold font-sans text-brand-green/60 p-1 text-center rotate-6 select-none bg-emerald-50/10">
                                <div class="absolute inset-1 rounded-full border border-dashed border-brand-green/20"></div>
                                <div class="font-extrabold uppercase text-[8px] leading-tight text-brand-green/75">ÓRGANO JUDICIAL</div>
                                <div class="text-[6px]">La Paz - Bolivia</div>
                                <div class="font-extrabold text-[8px] mt-0.5">JUZGADO 1°</div>
                            </div>

                            <!-- Gold seal on bottom right -->
                            <div class="absolute bottom-6 right-6 w-12 h-12 rounded-full bg-brand-gold/15 border-2 border-brand-gold/45 flex items-center justify-center shadow-xs rotate-12">
                                <i data-lucide="shield-check" class="w-6 h-6 text-brand-gold"></i>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Viewer Footer -->
                <div class="bg-slate-900 px-6 py-3 border-t border-slate-700 flex justify-end gap-3 shrink-0">
                    <button @click="closePreview()" 
                            class="px-4 py-1.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-xs font-semibold transition-colors">
                        Cerrar Vista Previa
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js for graphs via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function dashboardData() {
        return {
            currentDate: '',
            searchQuery: '',
            selectedMateria: 'Todas',
            slideOverOpen: false,
            newCaseModalOpen: false,
            toastVisible: false,
            toastMessage: '',
            activeProceso: {},
            formBitacoraOpen: false, // Collapse/Expand state for Bitacora Form
            previewModalOpen: false, // Document Preview state
            previewDocName: '',
            telegramNotify: localStorage.getItem('telegramNotify') === 'true',
            telegramToken: localStorage.getItem('telegramToken') || '',
            telegramChatId: localStorage.getItem('telegramChatId') || '',
            newProceso: {
                codigo: '',
                tipo: 'Portal Fis',
                nurej: 'N/A',
                ubicacion: 'CENTRO',
                denunciante: '',
                denunciado: '',
                juzgado: '',
                sala: 'SALA PENAL',
                fiscal: 'Bismarck Molina',
                delito: '',
                materia: 'Penal',
                estado_badge: 'Casación',
                estado: '',
                fecha: '2026-07-07',
                correo: 'legal@bufete-sillerico.com',
                telefono: '59177234317',
                abogado: 'Bismarck Molina',
                hitos: [
                    { fecha: '07/07/2026 17:15', accion: 'Creación del proceso de forma exitosa.' }
                ],
                documentos: ['Auto_Inicial.pdf']
            },
            newHito: {
                accion: '',
                comentarios: '',
                fileName: '',
                abogado: 'Alan Sillerico Segurondo'
            },
            equipo: @json($equipo),
            procesos: @json($procesos),

            get filteredProcesos() {
                return this.procesos.filter(p => {
                    const matchesSearch = !this.searchQuery || 
                        (p.codigo && p.codigo.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                        (p.denunciante && p.denunciante.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                        (p.denunciado && p.denunciado.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                        (p.delito && p.delito.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    const matchesMateria = this.selectedMateria === 'Todas' || p.materia === this.selectedMateria;
                    return matchesSearch && matchesMateria;
                });
            },

            openProcesoDetails(p) {
                window.location.href = '{{ url('/procesos') }}?search=' + encodeURIComponent(p.codigo);
            },

            showToast(msg) {
                this.toastMessage = msg;
                this.toastVisible = true;
                setTimeout(() => { this.toastVisible = false; }, 3500);
            },

            submitForm() {
                window.location.href = '{{ url('/procesos') }}';
            },

            addHitoToProceso() {},
            openPreview() {},
            closePreview() {},

            initCharts() {
                // Doughnut Chart (Distribution)
                const ctxMateria = document.getElementById('materiaChart').getContext('2d');
                
                const countPenal = this.procesos.filter(p => p.materia === 'Penal').length;
                const countLaboral = this.procesos.filter(p => p.materia === 'Laboral').length;
                const countFamiliar = this.procesos.filter(p => p.materia === 'Familiar').length;
                
                this.materiaChart = new Chart(ctxMateria, {
                    type: 'doughnut',
                    data: {
                        labels: ['Penal', 'Laboral', 'Familiar', 'Civil'],
                        datasets: [{
                            data: [countPenal, countLaboral, countFamiliar, 0],
                            backgroundColor: ['#082a20', '#0d3d2f', '#c5a059', '#e6ecea'],
                            borderColor: ['#ffffff'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        cutout: '70%'
                    }
                });

                // Line Chart (Evolution)
                const ctxEvolution = document.getElementById('evolutionChart').getContext('2d');
                this.evolutionChart = new Chart(ctxEvolution, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Procesos Activos',
                            data: [28, 30, 31, 35, 34, 37, 38],
                            borderColor: '#082a20',
                            backgroundColor: 'rgba(8, 42, 32, 0.05)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#c5a059',
                            pointBorderColor: '#ffffff',
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            },
            
            updateChartsData() {
                const countPenal = this.procesos.filter(p => p.materia === 'Penal').length;
                const countLaboral = this.procesos.filter(p => p.materia === 'Laboral').length;
                const countFamiliar = this.procesos.filter(p => p.materia === 'Familiar').length;
                const countCivil = this.procesos.filter(p => p.materia === 'Civil').length;
                
                this.materiaChart.data.datasets[0].data = [countPenal, countLaboral, countFamiliar, countCivil];
                this.materiaChart.update();
            }
        }
    }
</script>
@endsection
