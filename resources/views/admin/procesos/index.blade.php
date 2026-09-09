@extends('layouts.admin')

@section('title', 'Procesos - Sillerico & Abogados')
@section('header_title', 'Control de Procesos')

@section('content')
<div x-data="procesosData()" class="flex-1 min-h-0 flex flex-col w-full animate-fade-in overflow-hidden">
    <!-- Table and Filter Area -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs flex-1 min-h-0 flex flex-col w-full overflow-hidden">
        <!-- Advanced Filters & Action Header -->
        <div class="border-b border-slate-100 space-y-4 shrink-0" style="padding: 18px !important;">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Action Button in Header -->
                <button @click="$dispatch('open-new-case-modal')" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-all hover:scale-[1.01] shrink-0">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    Registrar Nuevo Proceso
                </button>

                <!-- Tab Filters by Estado -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-[10px] font-bold text-slate-500 overflow-x-auto">
                    <button class="px-3.5 py-1.5 rounded-lg transition-all whitespace-nowrap"
                            :class="selectedEstado === 'Todos' ? 'bg-white text-brand-green shadow-xs' : 'hover:text-slate-700'"
                            @click="selectedEstado = 'Todos'">Todos</button>
                    <template x-for="est in estadosList" :key="est.id">
                        <button class="px-3.5 py-1.5 rounded-lg transition-all whitespace-nowrap"
                                :class="selectedEstado === est.nombre ? 'bg-white text-brand-green shadow-xs' : 'hover:text-slate-700'"
                                @click="selectedEstado = est.nombre"
                                x-text="est.nombre"></button>
                    </template>
                </div>
            </div>

            <!-- Parametric Filters: Materia, Abogado, Search -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- 1. Materia Filter -->
                <div>
                    <select x-model="selectedMateria" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-medium">
                        <option value="Todas">Todas las materias</option>
                        <template x-for="mat in materiasList" :key="mat.id">
                            <option :value="mat.nombre" x-text="mat.nombre"></option>
                        </template>
                    </select>
                </div>

                <!-- 2. Abogado Filter -->
                <div>
                    <select x-model="selectedAbogado" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-medium">
                        <option value="Todos">Todos los abogados</option>
                        <template x-for="m in equipo" :key="m.nombre">
                            <option :value="m.nombre" x-text="m.nombre"></option>
                        </template>
                    </select>
                </div>

                <!-- 3. Search Query Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Buscar por caso, CUD, NUREJ, partes, juzgado..." 
                           class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                </div>
            </div>
        </div>

        <!-- Table View with Scrollable Rows -->
        <div class="flex-1 min-h-0 overflow-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="sticky top-0 z-10 bg-slate-50 shadow-2xs">
                    <tr class="border-b border-slate-200 text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                        <th class="py-3 px-4 w-[18%] bg-slate-50">Nro. de Caso / CUD / NUREJ</th>
                        <th class="py-3 px-4 w-[13%] bg-slate-50">Denunciante</th>
                        <th class="py-3 px-4 w-[15%] bg-slate-50">Denunciado</th>
                        <th class="py-3 px-4 w-[20%] bg-slate-50">Juzgado / Fiscalía</th>
                        <th class="py-3 px-4 w-[12%] bg-slate-50">Delito / Acción</th>
                        <th class="py-3 px-4 w-[17%] bg-slate-50">Estado del Proceso</th>
                        <th class="py-3 px-4 w-[5%] bg-slate-50 text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    <template x-for="proceso in paginatedCasos" :key="proceso.id || proceso.codigo">
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
                                <div class="text-[10px] text-slate-400" x-show="proceso.nurej && proceso.nurej !== 'N/A'" x-text="`NUREJ/IANUS: ${proceso.nurej}`"></div>
                            </td>
                            
                            <!-- Denunciante -->
                            <td class="py-4 px-4 font-semibold text-slate-800 uppercase" x-text="proceso.denunciante"></td>
                            
                            <!-- Denunciado -->
                            <td class="py-4 px-4 font-bold text-slate-900 uppercase" x-text="proceso.denunciado"></td>
                            
                            <!-- Juzgado/Fiscalia -->
                            <td class="py-4 px-4 space-y-0.5">
                                <div class="font-bold text-slate-800 leading-tight" x-text="proceso.juzgado"></div>
                                <div class="text-[10px] text-slate-400" x-show="proceso.sala" x-text="proceso.sala"></div>
                                <div class="text-[10px] text-brand-gold font-medium" x-show="proceso.fiscal" x-text="`Autoridad: ${proceso.fiscal}`"></div>
                            </td>
                            
                            <!-- Delito / Materia -->
                            <td class="py-4 px-4">
                                <div class="mb-1">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                          :class="proceso.materia_badge || 'bg-slate-100 text-slate-700'"
                                          x-text="proceso.materia"></span>
                                </div>
                                <div class="font-semibold text-slate-700 uppercase text-[10px] leading-tight" x-text="proceso.delito"></div>
                            </td>
                            
                            <!-- Estado del Proceso -->
                            <td class="py-4 px-4 space-y-1">
                                <div>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                                          :class="proceso.estado_color || 'bg-slate-100 text-slate-700 border border-slate-200'"
                                          x-text="proceso.estado_badge"></span>
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
                    <tr x-show="filteredCasos.length === 0">
                        <td colspan="7" class="py-16 text-center text-slate-400 font-medium bg-slate-50/20">
                            <i data-lucide="folder-search" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                            Ningún proceso coincide con los criterios de filtrado seleccionados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Table Footer Pagination -->
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 shrink-0">
            <div class="flex items-center gap-3">
                <span x-text="paginationSummary"></span>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                    <span>Filas:</span>
                    <select x-model.number="perPage" 
                            class="bg-white border border-slate-200 rounded-lg px-2 py-0.5 text-xs font-semibold text-slate-600 focus:outline-none focus:border-brand-gold">
                        <option :value="10">10</option>
                        <option :value="20">20</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <!-- Anterior -->
                <button @click="prevPage()" 
                        :disabled="currentPage === 1"
                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed transition-colors shadow-2xs">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Anterior</span>
                </button>

                <!-- Páginas Numeradas -->
                <div class="flex items-center gap-1">
                    <template x-for="(page, idx) in pageNumbers" :key="idx">
                        <div>
                            <template x-if="page === '...'">
                                <span class="px-2 py-1 text-slate-400 text-xs font-bold select-none">...</span>
                            </template>
                            <template x-if="page !== '...'">
                                <button @click="goToPage(page)" 
                                        class="min-w-8 h-8 px-2 rounded-lg text-xs font-bold transition-all"
                                        :class="currentPage === page ? 'bg-brand-green text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                                        x-text="page"></button>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Siguiente -->
                <button @click="nextPage()" 
                        :disabled="currentPage >= totalPages"
                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed transition-colors shadow-2xs">
                    <span class="hidden sm:inline">Siguiente</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </button>
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
                     x-transition:enter="transform transition ease-in-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300"
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

                    <!-- Content -->
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
                            
                            <div class="col-span-1 md:col-span-2 pt-2 border-t border-slate-200/50">
                                <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Estado Detallado del Proceso</span>
                                <p class="text-sm text-slate-600 leading-relaxed bg-white/70 p-3 rounded-lg border border-slate-100" x-text="activeProceso.estado"></p>
                            </div>
                        </div>

                        <!-- Navigation Tabs inside Slide-Over -->
                        <div class="flex items-center gap-2 border-b border-slate-200/80 pb-2">
                            <button type="button" 
                                    @click="slideOverTab = 'actuaciones'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="slideOverTab === 'actuaciones' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                                <i data-lucide="history" class="w-3.5 h-3.5" :class="slideOverTab === 'actuaciones' ? 'text-brand-gold' : ''"></i>
                                Actuaciones &amp; Documentos
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" 
                                      :class="slideOverTab === 'actuaciones' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                                      x-text="(activeProceso.hitos ? activeProceso.hitos.length : 0)"></span>
                            </button>

                            <button type="button" 
                                    @click="slideOverTab = 'auditoria'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="slideOverTab === 'auditoria' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5" :class="slideOverTab === 'auditoria' ? 'text-brand-gold' : ''"></i>
                                Trazabilidad &amp; Auditoría
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" 
                                      :class="slideOverTab === 'auditoria' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                                      x-text="(activeProceso.auditorias ? activeProceso.auditorias.length : 0)"></span>
                            </button>
                        </div>

                        <!-- Tab 1: Actuaciones & Documentos -->
                        <div x-show="slideOverTab === 'actuaciones'" class="space-y-6">
                        <!-- Timeline -->
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
                                                <input type="file" accept=".pdf" class="hidden" id="bitacoraFileInput" 
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
                                            
                                            <!-- Comments -->
                                            <div x-show="hito.comentarios" class="bg-white p-2.5 rounded-lg border border-slate-200/50 text-xs text-slate-600 leading-relaxed font-sans shadow-2xs">
                                                <div class="font-bold text-[10px] text-brand-gold uppercase tracking-wider mb-1 flex items-center gap-1 shrink-0">
                                                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                                    Notas &amp; Comentarios Internos
                                                </div>
                                                <p x-text="hito.comentarios"></p>
                                            </div>

                                            <!-- Documents -->
                                            <div x-show="hito.documentos && hito.documentos.length" class="flex flex-wrap gap-1.5 pt-1">
                                                <template x-for="doc in hito.documentos" :key="doc">
                                                    <div @click.stop="openPreview(doc)" class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg px-2.5 py-1 text-xs font-bold hover:bg-rose-100 transition-colors shadow-2xs cursor-pointer">
                                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                                                        <span class="truncate max-w-[180px]" x-text="doc"></span>
                                                        <button class="p-0.5 hover:bg-rose-200/50 rounded shrink-0">
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

                        <!-- Documents -->
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
                        </div> <!-- End Tab 1: Actuaciones & Documentos -->

                        <!-- Tab 2: Trazabilidad & Auditoría -->
                        <div x-show="slideOverTab === 'auditoria'" class="space-y-4" style="display: none;" x-cloak>
                            <div class="bg-amber-50/80 border border-brand-gold/30 p-3.5 rounded-xl flex items-start gap-2.5">
                                <i data-lucide="shield-alert" class="w-4 h-4 text-brand-gold shrink-0 mt-0.5"></i>
                                <div class="text-xs text-amber-950 leading-snug">
                                    <span class="font-bold text-brand-gold">Auditoría Inmutable:</span> Toda acción procesal, cambio de estado y alta de diligencias queda rubricada con la identidad del operador y estampa de tiempo.
                                </div>
                            </div>

                            <!-- Audit entries stream for this case -->
                            <div class="space-y-3">
                                <template x-for="aud in activeProceso.auditorias" :key="aud.id">
                                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-3 hover:bg-slate-100/60 transition-colors">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs"
                                             :class="aud.color || 'bg-brand-green text-white'">
                                            <span x-text="aud.iniciales || 'OP'"></span>
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <span class="text-xs font-bold text-slate-800 truncate" x-text="aud.usuario"></span>
                                                    <span class="text-[10px] text-slate-400 shrink-0" x-text="'(' + aud.cargo + ')'"></span>
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-medium shrink-0" x-text="aud.fecha"></span>
                                            </div>
                                            <p class="text-xs text-slate-700 leading-snug" x-text="aud.descripcion"></p>
                                            <div class="pt-0.5">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider"
                                                      :class="{
                                                        'bg-emerald-100 text-emerald-800': aud.accion === 'CREACION_PROCESO',
                                                        'bg-amber-100 text-amber-800': aud.accion === 'NUEVA_ACTUACION',
                                                        'bg-indigo-100 text-indigo-800': aud.accion === 'CAMBIO_ESTADO',
                                                        'bg-blue-100 text-blue-800': aud.accion === 'EDICION_PROCESO',
                                                        'bg-rose-100 text-rose-800': aud.accion === 'ELIMINACION_PROCESO'
                                                      }"
                                                      x-text="aud.accion"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="!activeProceso.auditorias || activeProceso.auditorias.length === 0" class="py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i data-lucide="shield" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-xs text-slate-500 font-bold">Sin registros de auditoría aún</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Las acciones que realice sobre este expediente se listarán aquí.</p>
                                </div>
                            </div>
                        </div> <!-- End Tab 2 -->

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

    <!-- REUSED MODAL FOR REGISTRAR NEW PROCESO -->
    <div class="fixed inset-0 z-50 overflow-y-auto" 
         x-show="newCaseModalOpen" 
         style="display: none;"
         @open-new-case-modal.window="newCaseModalOpen = true"
         x-cloak>
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
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
                                <template x-for="mat in materiasList" :key="mat.id">
                                    <option :value="mat.nombre" x-text="mat.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Denunciante / Cliente</label>
                            <input type="text" x-model="newProceso.denunciante" required placeholder="Nombre del Denunciante / Cliente"
                                   class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Denunciado / Contraparte</label>
                            <input type="text" x-model="newProceso.denunciado" required placeholder="Nombre del Denunciado"
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
                                <template x-for="est in estadosList" :key="est.id">
                                    <option :value="est.nombre" x-text="est.nombre"></option>
                                </template>
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

    <!-- Toast Success Notification -->
    <div class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-xl transition-all transform duration-300"
         x-show="toastVisible" 
         x-transition:enter="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="translate-y-10 opacity-0"
         style="display: none;"
         x-cloak>
        <div class="p-1 rounded bg-brand-gold text-slate-900">
            <i data-lucide="check" class="w-4 h-4"></i>
        </div>
        <div class="text-xs">
            <p class="font-bold text-brand-gold">¡Operación Exitosa!</p>
            <p class="text-slate-400" x-text="toastMessage"></p>
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
                                Regístrese, notifíquese y archívese.
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

<script>
    function procesosData() {
        return {
            searchQuery: '',
            selectedMateria: 'Todas',
            selectedEstado: 'Todos',
            selectedAbogado: 'Todos',
            slideOverOpen: false,
            slideOverTab: 'actuaciones',
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
            
            // Statistics derived dynamically
            get stats() {
                const countMateria = (m) => this.casos.filter(c => c.materia === m).length;
                return [
                    { label: 'Total Activos', count: this.casos.length, value: 'Todas', icon: 'folder-open' },
                    { label: 'Derecho Penal', count: countMateria('Penal'), value: 'Penal', icon: 'shield-alert' },
                    { label: 'Derecho Civil', count: countMateria('Civil'), value: 'Civil', icon: 'scale' },
                    { label: 'Derecho Laboral', count: countMateria('Laboral'), value: 'Laboral', icon: 'briefcase' },
                    { label: 'Otros (Fam.)', count: countMateria('Familiar'), value: 'Familiar', icon: 'folder-plus' }
                ];
            },

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
            casos: @json($procesosFormatted),
            materiasList: @json($materias),
            estadosList: @json($estados),
            juzgadosList: @json($juzgados),
            articulosList: @json($articulos),
            jurisdiccionesList: @json($jurisdicciones),

            get filteredCasos() {
                return this.casos.filter(caso => {
                    const q = this.searchQuery ? this.searchQuery.toLowerCase().trim() : '';
                    const matchesSearch = !q || 
                        (caso.codigo && String(caso.codigo).toLowerCase().includes(q)) ||
                        (caso.nurej && String(caso.nurej).toLowerCase().includes(q)) ||
                        (caso.cud && String(caso.cud).toLowerCase().includes(q)) ||
                        (caso.denunciante && String(caso.denunciante).toLowerCase().includes(q)) ||
                        (caso.denunciado && String(caso.denunciado).toLowerCase().includes(q)) ||
                        (caso.delito && String(caso.delito).toLowerCase().includes(q)) ||
                        (caso.juzgado && String(caso.juzgado).toLowerCase().includes(q));

                    const matchesMateria = this.selectedMateria === 'Todas' || caso.materia === this.selectedMateria;
                    const matchesEstado = this.selectedEstado === 'Todos' || caso.estado_badge === this.selectedEstado;
                    const matchesAbogado = this.selectedAbogado === 'Todos' || caso.abogado === this.selectedAbogado;

                    return matchesSearch && matchesMateria && matchesEstado && matchesAbogado;
                });
            },

            // Paginación
            currentPage: 1,
            perPage: 10,

            get totalPages() {
                return Math.ceil(this.filteredCasos.length / this.perPage) || 1;
            },

            get paginatedCasos() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredCasos.slice(start, start + this.perPage);
            },

            get paginationSummary() {
                if (this.filteredCasos.length === 0) return 'Sin causas registradas';
                const start = (this.currentPage - 1) * this.perPage + 1;
                const end = Math.min(this.currentPage * this.perPage, this.filteredCasos.length);
                return `Mostrando ${start} - ${end} de ${this.filteredCasos.length} procesos (${this.casos.length} en total)`;
            },

            get pageNumbers() {
                const total = this.totalPages;
                const current = this.currentPage;
                if (total <= 7) {
                    return Array.from({ length: total }, (_, i) => i + 1);
                }
                if (current <= 4) {
                    return [1, 2, 3, 4, 5, '...', total];
                }
                if (current >= total - 3) {
                    return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                }
                return [1, '...', current - 1, current, current + 1, '...', total];
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.$nextTick(() => window.lucide && window.lucide.createIcons());
                }
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.$nextTick(() => window.lucide && window.lucide.createIcons());
                }
            },

            goToPage(p) {
                if (typeof p === 'number' && p >= 1 && p <= this.totalPages) {
                    this.currentPage = p;
                    this.$nextTick(() => window.lucide && window.lucide.createIcons());
                }
            },

            init() {
                this.$watch('searchQuery', () => { this.currentPage = 1; });
                this.$watch('selectedMateria', () => { this.currentPage = 1; });
                this.$watch('selectedEstado', () => { this.currentPage = 1; });
                this.$watch('selectedAbogado', () => { this.currentPage = 1; });
                this.$watch('perPage', () => { this.currentPage = 1; });
            },

            openProcesoDetails(proceso) {
                this.activeProceso = proceso;
                if (!this.activeProceso.hitos) this.activeProceso.hitos = [];
                if (!this.activeProceso.documentos) this.activeProceso.documentos = [];
                if (!this.activeProceso.auditorias) this.activeProceso.auditorias = [];
                this.slideOverTab = 'actuaciones';
                this.slideOverOpen = true;
                this.formBitacoraOpen = false;
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            showToast(msg) {
                this.toastMessage = msg;
                this.toastVisible = true;
                setTimeout(() => {
                    this.toastVisible = false;
                }, 3500);
            },
            
            async submitForm() {
                try {
                    const matObj = this.materiasList ? this.materiasList.find(m => m.nombre === this.newProceso.materia) : null;
                    const estObj = this.estadosList ? this.estadosList.find(e => e.nombre === this.newProceso.estado_badge) : null;
                    const abgObj = this.equipo ? this.equipo.find(e => e.nombre === this.newProceso.abogado) : null;

                    const payload = {
                        codigo_interno: this.newProceso.codigo,
                        materia_id: matObj ? matObj.id : null,
                        materia: this.newProceso.materia,
                        estado_id: estObj ? estObj.id : null,
                        estado: this.newProceso.estado_badge,
                        delito_accion: this.newProceso.delito || 'Acción Jurídica',
                        demandante_denunciante: this.newProceso.denunciante,
                        demandado_denunciado: this.newProceso.denunciado,
                        telefono: this.newProceso.telefono,
                        cud: this.newProceso.tipo === 'CUD' ? this.newProceso.codigo : null,
                        codigo_caso: this.newProceso.tipo === 'CASO' ? this.newProceso.codigo : null,
                        nurej: (this.newProceso.nurej && this.newProceso.nurej !== 'N/A') ? this.newProceso.nurej : null,
                        portal_fiscalia: this.newProceso.tipo === 'Portal Fis',
                        jurisdiccion: this.newProceso.ubicacion || 'CENTRO',
                        juzgado_tribunal: this.newProceso.juzgado,
                        estado_detalle: this.newProceso.estado,
                        nuevo_cliente_nombre: this.newProceso.denunciante,
                        abogado_id: abgObj ? abgObj.id : null
                    };

                    const response = await fetch('{{ route('procesos.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.casos.unshift(data.proceso);
                        if (abgObj) {
                            abgObj.activos++;
                        }
                        this.newCaseModalOpen = false;
                        this.showToast('Proceso registrado exitosamente en la base de datos.');
                        
                        this.newProceso = {
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
                            fecha: '{{ date('Y-m-d') }}',
                            correo: 'legal@bufete-sillerico.com',
                            telefono: '59177234317',
                            abogado: 'Alan Sillerico Segurondo',
                            hitos: [],
                            documentos: []
                        };
                    } else {
                        this.showToast('Error al registrar: ' + (data.message || 'Verifique los campos'));
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Error de conexión con el servidor.');
                }
            },

            async addHitoToProceso() {
                if (!this.newHito.accion) return;
                
                try {
                    const formData = new FormData();
                    formData.append('titulo_actuacion', this.newHito.accion);
                    formData.append('descripcion', this.newHito.comentarios || '');
                    formData.append('tipo_actuacion', 'Diligencia');
                    
                    const abgObj = this.equipo.find(e => e.nombre === this.newHito.abogado);
                    if (abgObj && abgObj.id) {
                        formData.append('abogado_id', abgObj.id);
                    }

                    const fileInput = document.getElementById('bitacoraFileInput');
                    if (fileInput && fileInput.files && fileInput.files[0]) {
                        formData.append('archivo', fileInput.files[0]);
                    }

                    const response = await fetch(`{{ url('/procesos') }}/${this.activeProceso.id}/actuaciones`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        const hitoObj = {
                            id: data.actuacion.id,
                            fecha: new Date(data.actuacion.fecha_hora).toLocaleDateString('es-ES') + ' ' + new Date(data.actuacion.fecha_hora).toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'}),
                            accion: data.actuacion.titulo_actuacion,
                            comentarios: data.actuacion.descripcion,
                            abogado: this.newHito.abogado,
                            documentos: data.documento ? [data.documento.nombre_original] : (this.newHito.fileName ? [this.newHito.fileName] : [])
                        };

                        this.activeProceso.hitos.unshift(hitoObj);
                        if (data.auditoria) {
                            if (!this.activeProceso.auditorias) this.activeProceso.auditorias = [];
                            this.activeProceso.auditorias.unshift(data.auditoria);
                        }
                        if (data.documento && !this.activeProceso.documentos.includes(data.documento.nombre_original)) {
                            this.activeProceso.documentos.unshift(data.documento.nombre_original);
                        } else if (this.newHito.fileName && !this.activeProceso.documentos.includes(this.newHito.fileName)) {
                            this.activeProceso.documentos.unshift(this.newHito.fileName);
                        }

                        // Telegram notification
                        if (this.telegramNotify && this.telegramToken && this.telegramChatId) {
                            const telegramText = `🔔 *Nueva Actividad Registrada*
` +
                                                 `*Expediente:* ${this.activeProceso.codigo}
` +
                                                 `*Cliente:* ${this.activeProceso.denunciante}
` +
                                                 `*Actuación:* ${hitoObj.accion}
` +
                                                 `*Comentario:* ${hitoObj.comentarios || 'Sin comentarios.'}
` +
                                                 `*Abogado:* ${this.newHito.abogado}`;
                                                 
                            fetch(`https://api.telegram.org/bot${this.telegramToken}/sendMessage`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    chat_id: this.telegramChatId,
                                    text: telegramText,
                                    parse_mode: 'Markdown'
                                })
                            }).catch(err => console.error('Error de Telegram:', err));
                        }

                        this.newHito = {
                            accion: '',
                            comentarios: '',
                            fileName: '',
                            abogado: 'Alan Sillerico Segurondo'
                        };
                        if (fileInput) fileInput.value = '';
                        this.formBitacoraOpen = false;
                        this.showToast('Nueva actividad registrada en la bitácora de la DB.');
                    } else {
                        this.showToast('Error al registrar en la bitácora: ' + (data.message || 'Intente de nuevo'));
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Error de conexión al guardar actividad.');
                }
            },

            testTelegram() {
                if (!this.telegramToken || !this.telegramChatId) {
                    this.showToast('Por favor configure el Token y Chat ID.');
                    return;
                }
                
                // Save Telegram configuration to browser storage
                localStorage.setItem('telegramToken', this.telegramToken);
                localStorage.setItem('telegramChatId', this.telegramChatId);
                
                const text = `🔔 *Mensaje de Prueba*\n` +
                             `Conexión exitosa desde el sistema Sillerico & Abogados.\n` +
                             `*Usuario:* Alan Sillerico Segurondo`;
                             
                fetch(`https://api.telegram.org/bot${this.telegramToken}/sendMessage`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        chat_id: this.telegramChatId,
                        text: text,
                        parse_mode: 'Markdown'
                    })
                }).then(res => {
                    if (res.ok) {
                        this.showToast('Mensaje de prueba enviado a Telegram.');
                    } else {
                        this.showToast('Error de Telegram. Verifique Token y Chat ID.');
                    }
                }).catch(err => {
                    this.showToast('Error de red al conectar con Telegram.');
                });
            },

            openPreview(docName) {
                this.previewDocName = docName;
                this.previewModalOpen = true;
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            closePreview() {
                this.previewModalOpen = false;
                this.previewDocName = '';
            }
        }
    }
</script>
@endsection
