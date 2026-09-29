@extends('layouts.admin')

@section('title', 'Procesos - Sillerico & Abogados')
@section('header_title', 'Control de Procesos')

@section('content')
<div x-data="procesosData()" @open-new-case-modal.window="openCreateProceso()" class="flex-1 min-h-0 flex flex-col w-full h-full animate-fade-in overflow-hidden" style="height: 100%; max-height: 100%; min-height: 0; overflow: hidden !important;">
    <!-- Table and Filter Area -->
    <div x-show="currentView === 'list'" class="bg-white rounded-2xl border border-slate-100 shadow-xs flex-1 min-h-0 flex flex-col w-full h-full overflow-hidden" style="height: 100%; max-height: 100%; min-height: 0; overflow: hidden !important;">
        <!-- Encabezado de Acciones y Herramientas -->
        <div class="border-b border-slate-100 shrink-0" style="padding: 12px 18px !important; flex-shrink: 0;">
            <div class="flex items-center justify-between gap-3">
                <!-- Alineado a la izquierda: Botón Nuevo proceso + Botón de filtro -->
                <div class="flex items-center gap-2.5">
                    <!-- Botón Nuevo proceso -->
                    <button @click="openCreateProceso()" 
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-all hover:scale-[1.01] shrink-0 cursor-pointer">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Nuevo proceso
                    </button>

                    <!-- Botón filtro (solo icono) -->
                    <button type="button" 
                            @click="openFilterModal()"
                            title="Filtrar por columna"
                            class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Alineado a la derecha: Botón de configuración de columnas (icono de ecualizador) -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="columnsConfigModalOpen = true"
                            title="Configuración de columnas"
                            class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View with Scrollable Rows -->
        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-auto" style="flex: 1 1 0px !important; min-height: 0 !important; overflow-y: auto !important;">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="sticky top-0 z-10 bg-slate-50 shadow-2xs">
                    <tr class="border-b border-slate-200 text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                        <th class="py-3 px-4 w-[16%] bg-slate-50">Nro. de Caso / CUD / NUREJ</th>
                        <th class="py-3 px-4 w-[12%] bg-slate-50">Denunciante</th>
                        <th class="py-3 px-4 w-[12%] bg-slate-50">Denunciado</th>
                        <th class="py-3 px-4 w-[16%] bg-slate-50">Juzgado / Fiscalía</th>
                        <th class="py-3 px-4 w-[14%] bg-slate-50">Delito / Acción</th>
                        <th class="py-3 px-4 w-[24%] bg-slate-50">Estado del Proceso</th>
                        <th class="py-3 px-4 w-[6%] bg-slate-50 text-right">Acción</th>
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
                            <td class="py-4 px-4 space-y-1">
                                <div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                          :class="proceso.materia_badge || 'bg-slate-100 text-slate-700'"
                                          x-text="proceso.materia"></span>
                                </div>
                                <div class="font-bold text-slate-800 uppercase text-xs leading-snug" x-text="proceso.delito"></div>
                            </td>
                            
                            <!-- Estado del Proceso -->
                            <td class="py-4 px-4 space-y-1.5">
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                          :class="proceso.estado_color || 'bg-slate-100 text-slate-700 border border-slate-200'"
                                          x-text="proceso.estado_badge"></span>
                                </div>
                                <p class="text-xs text-slate-700 leading-relaxed font-medium line-clamp-3" x-text="proceso.estado"></p>
                            </td>

                            <!-- Acción Directa: Editar -->
                            <td class="py-4 px-4 text-right" @click.stop>
                                <button type="button" 
                                        @click="openEditProcesoModal(proceso)" 
                                        title="Editar Expediente"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-brand-green hover:text-white text-slate-600 transition-all shadow-2xs cursor-pointer">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
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
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 shrink-0" style="flex-shrink: 0;">
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

    <!-- ========================================================================= -->
    <!-- DETALLE COMPLETO DEL PROCESO (PANTALLA COMPLETA)                         -->
    <!-- ========================================================================= -->
    <div x-show="currentView === 'detail'" 
         class="bg-white rounded-2xl border border-slate-100 shadow-xs flex-1 min-h-0 flex flex-col w-full h-full overflow-hidden" 
         style="height: 100%; max-height: 100%; min-height: 0; overflow: hidden !important;"
         x-cloak>
        
        <!-- Header Fijo: Volver, Código, Badges y Botón "+ Nueva Acción" -->
        <div class="border-b border-slate-100 bg-white shrink-0" style="padding: 16px 20px !important; flex-shrink: 0;">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <!-- Izquierda: Botón Volver + Código y Badges -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <button @click="closeProcesoDetails()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-brand-green text-xs font-bold transition-all shadow-2xs cursor-pointer">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Volver a Procesos</span>
                    </button>

                    <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                              :class="{
                                'bg-emerald-100 text-emerald-800': activeProceso.tipo && activeProceso.tipo.includes('Portal Fis'),
                                'bg-amber-100 text-brand-gold': activeProceso.tipo && activeProceso.tipo.includes('CUD'),
                                'bg-blue-100 text-blue-800': activeProceso.tipo && activeProceso.tipo.includes('CASO')
                              }" x-text="activeProceso.tipo || 'EXPEDIENTE'"></span>
                        <h2 class="text-lg sm:text-xl font-black text-brand-green tracking-tight" x-text="activeProceso.codigo"></h2>
                    </div>

                    <!-- Badges del Proceso -->
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                          :class="activeProceso.materia_badge || 'bg-slate-100 text-slate-700'"
                          x-text="activeProceso.materia"></span>

                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="activeProceso.estado_color || 'bg-slate-100 text-slate-700 border border-slate-200'"
                          x-text="activeProceso.estado_badge"></span>

                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100"
                          x-show="activeProceso.etapa"
                          x-text="'Etapa: ' + activeProceso.etapa"></span>

                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-brand-gold border border-brand-gold/30"
                          x-show="activeProceso.cud"
                          x-text="'CUD: ' + activeProceso.cud"></span>
                    
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200"
                          x-show="activeProceso.nurej && activeProceso.nurej !== 'N/A'"
                          x-text="'NUREJ: ' + activeProceso.nurej"></span>
                </div>

                <!-- Derecha: Botones de Acción (Editar Expediente + Nueva Acción) -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="openEditProcesoModal(activeProceso)" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-brand-green hover:text-white text-slate-700 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer">
                        <i data-lucide="edit-3" class="w-4 h-4 text-brand-gold"></i>
                        <span>Editar Expediente</span>
                    </button>

                    <button @click="openNewHitoModal()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green hover:bg-brand-green-hover text-white text-xs font-bold rounded-xl shadow-sm shadow-brand-green/20 transition-all hover:scale-[1.02] cursor-pointer">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-brand-gold"></i>
                        <span>Nueva Acción</span>
                    </button>
                </div>
            </div>

            <!-- Menú Horizontal de Navegación del Proceso con Contadores -->
            <div class="flex items-center gap-1.5 overflow-x-auto pt-3 mt-3 border-t border-slate-100">
                <!-- Tab: Ficha General / Datos -->
                <button type="button" 
                        @click="detailTab = 'general'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="detailTab === 'general' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                    <i data-lucide="file-text" class="w-3.5 h-3.5" :class="detailTab === 'general' ? 'text-brand-gold' : ''"></i>
                    <span>Datos del Proceso</span>
                </button>

                <!-- Tab: Acciones / Bitácora con Contador -->
                <button type="button" 
                        @click="detailTab = 'acciones'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="detailTab === 'acciones' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                    <i data-lucide="activity" class="w-3.5 h-3.5" :class="detailTab === 'acciones' ? 'text-brand-gold' : ''"></i>
                    <span>Acciones</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold"
                          :class="detailTab === 'acciones' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                          x-text="(activeProceso.hitos ? activeProceso.hitos.length : 0)"></span>
                </button>

                <!-- Tab: Documentos con Contador -->
                <button type="button" 
                        @click="detailTab = 'documentos'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="detailTab === 'documentos' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                    <i data-lucide="folder-archive" class="w-3.5 h-3.5" :class="detailTab === 'documentos' ? 'text-brand-gold' : ''"></i>
                    <span>Documentos</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold"
                          :class="detailTab === 'documentos' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                          x-text="activeProceso.documentos_list ? activeProceso.documentos_list.length : (activeProceso.documentos ? activeProceso.documentos.length : 0)"></span>
                </button>

                <!-- Tab: Audiencias y Plazos con Contador -->
                <button type="button" 
                        @click="detailTab = 'eventos'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="detailTab === 'eventos' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                    <i data-lucide="calendar" class="w-3.5 h-3.5" :class="detailTab === 'eventos' ? 'text-brand-gold' : ''"></i>
                    <span>Audiencias &amp; Plazos</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold"
                          :class="detailTab === 'eventos' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                          x-text="(activeProceso.eventos ? activeProceso.eventos.length : 0)"></span>
                </button>

                <!-- Tab: Trazabilidad & Auditoría con Contador -->
                <button type="button" 
                        @click="detailTab = 'auditoria'; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="detailTab === 'auditoria' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100'">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5" :class="detailTab === 'auditoria' ? 'text-brand-gold' : ''"></i>
                    <span>Trazabilidad</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold"
                          :class="detailTab === 'auditoria' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                          x-text="(activeProceso.auditorias ? activeProceso.auditorias.length : 0)"></span>
                </button>
            </div>
        </div>

        <!-- Área de Contenido Scrollable Interno -->
        <div class="flex-1 min-h-0 overflow-y-auto bg-slate-50/40 p-4 sm:p-6">
            
            <!-- ========================================== -->
            <!-- TAB 1: DATOS COMPLETOS DEL PROCESO         -->
            <!-- ========================================== -->
            <div x-show="detailTab === 'general'" class="space-y-6">
                <!-- Situación Actual Destacada -->
                <div class="rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4" 
                     style="background-color: #082a20 !important; background-image: linear-gradient(135deg, #051d16 0%, #082a20 55%, #0e3d30 100%) !important; border: 1px solid #14493a; color: #ffffff;">
                    <div class="space-y-2 max-w-3xl">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-lg shadow-xs"
                                  style="background-color: #c5a059 !important; color: #082a20 !important;">
                                Situación Procesal Actual
                            </span>
                            <span class="text-xs font-semibold" style="color: #cbd5e1;" x-text="'Radicatoria: ' + (activeProceso.fecha || 'N/A')"></span>
                        </div>
                        <p class="text-base font-bold text-white leading-relaxed pt-0.5" style="color: #ffffff;" x-text="activeProceso.estado || 'En trámite ordinario'"></p>
                    </div>
                    <div class="rounded-xl shrink-0 flex items-center p-4 gap-4 shadow-sm" 
                         style="padding: 16px 20px !important; gap: 16px !important; background-color: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold shrink-0 shadow-xs" 
                             style="background-color: #c5a059; color: #082a20; margin-right: 4px;">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <span class="text-[10px] uppercase font-bold tracking-wider block" style="color: #94a3b8;">Abogado Responsable</span>
                            <span class="text-sm font-black text-white block mt-1" style="color: #ffffff;" x-text="activeProceso.abogado"></span>
                            <span class="text-xs font-bold block mt-0.5" style="color: #f3d496;" x-text="activeProceso.abogado_cargo || 'Bufete Jurídico'"></span>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas Grid de Información Estructurada -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Tarjeta 1: Partes Procesales y Cliente -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2 text-brand-green font-bold text-sm">
                                <i data-lucide="users" class="w-4 h-4 text-brand-gold"></i>
                                <span>Partes Procesales &amp; Cliente</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-green/10 text-brand-green" x-text="activeProceso.rol_cliente || 'Cliente'"></span>
                        </div>

                        <!-- Cliente Patrocinado -->
                        <div class="space-y-2 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Cliente Patrocinado</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded" x-text="activeProceso.cliente_tipo || 'NATURAL'"></span>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-800 uppercase" x-text="activeProceso.cliente"></h4>
                            
                            <div class="grid grid-cols-2 gap-2 pt-1 text-xs text-slate-600">
                                <div x-show="activeProceso.cliente_ci">
                                    <span class="text-[10px] text-slate-400 block font-semibold">C.I. / NIT:</span>
                                    <span class="font-bold text-slate-700" x-text="activeProceso.cliente_ci"></span>
                                </div>
                                <div x-show="activeProceso.cliente_contacto">
                                    <span class="text-[10px] text-slate-400 block font-semibold">Contacto:</span>
                                    <span class="font-medium text-slate-700 truncate block" x-text="activeProceso.cliente_contacto"></span>
                                </div>
                            </div>

                            <!-- Vías de Comunicación Directa -->
                            <div class="pt-2 border-t border-slate-200/50 flex flex-wrap items-center gap-2">
                                <a :href="'https://wa.me/' + (activeProceso.telefono ? activeProceso.telefono.replace(/\D/g, '') : '')" 
                                   target="_blank"
                                   x-show="activeProceso.telefono"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold hover:bg-emerald-100 transition-colors">
                                    <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                                    <span x-text="activeProceso.telefono"></span>
                                </a>
                                <a :href="'mailto:' + activeProceso.correo" 
                                   x-show="activeProceso.correo"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-medium hover:bg-slate-200 transition-colors">
                                    <i data-lucide="mail" class="w-3 h-3 text-slate-400"></i>
                                    <span class="truncate max-w-[150px]" x-text="activeProceso.correo"></span>
                                </a>
                            </div>

                            <div x-show="activeProceso.cliente_direccion" class="pt-1 text-[11px] text-slate-500">
                                <span class="font-semibold text-slate-400">Domicilio:</span> <span x-text="activeProceso.cliente_direccion"></span>
                            </div>
                        </div>

                        <!-- Sujetos en Contienda -->
                        <div class="space-y-3 pt-1">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Denunciante / Demandante:</span>
                                <p class="text-xs font-bold text-slate-800 uppercase mt-0.5" x-text="activeProceso.denunciante"></p>
                                <span class="text-[10px] text-slate-400" x-show="activeProceso.demandante_ci" x-text="'C.I.: ' + activeProceso.demandante_ci"></span>
                            </div>
                            <hr class="border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Denunciado / Demandado:</span>
                                <p class="text-xs font-bold text-slate-900 uppercase mt-0.5" x-text="activeProceso.denunciado"></p>
                                <span class="text-[10px] text-slate-400" x-show="activeProceso.demandado_ci" x-text="'C.I.: ' + activeProceso.demandado_ci"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Identificación Judicial y Estrados -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2 text-brand-green font-bold text-sm">
                                <i data-lucide="landmark" class="w-4 h-4 text-brand-gold"></i>
                                <span>Radicatoria &amp; Autoridades</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600" x-text="activeProceso.ubicacion || 'La Paz'"></span>
                        </div>

                        <!-- Estrados Judiciales -->
                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Juzgado / Tribunal:</span>
                                <p class="text-sm font-bold text-slate-800 leading-snug mt-0.5" x-text="activeProceso.juzgado"></p>
                                <span class="text-[11px] text-slate-500 font-medium block mt-0.5" x-show="activeProceso.sala" x-text="activeProceso.sala"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Juez Titular:</span>
                                    <span class="font-bold text-slate-700 block mt-0.5" x-text="activeProceso.juez || 'Por sorteo / No asignado'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Fiscal Asignado:</span>
                                    <span class="font-bold text-brand-gold block mt-0.5" x-text="activeProceso.fiscal || 'En asignación'"></span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100" x-show="activeProceso.investigador">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Investigador Policial (FELCC):</span>
                                <span class="font-semibold text-slate-700 block mt-0.5" x-text="activeProceso.investigador"></span>
                            </div>
                        </div>

                        <!-- Identificadores y Códigos Judiciales -->
                        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100 space-y-2 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Registros del Sistema Judicial</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block">CUD Fiscalía:</span>
                                    <span class="font-bold text-slate-800" x-text="activeProceso.cud || 'Sin CUD'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block">NUREJ / IANUS:</span>
                                    <span class="font-bold text-slate-800" x-text="activeProceso.nurej || 'N/A'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block">Código de Caso:</span>
                                    <span class="font-bold text-slate-800" x-text="activeProceso.codigo_caso || 'N/A'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block">Portal Fiscalía:</span>
                                    <span class="font-bold" :class="activeProceso.tipo && activeProceso.tipo.includes('Portal') ? 'text-emerald-700' : 'text-slate-600'" x-text="activeProceso.tipo && activeProceso.tipo.includes('Portal') ? 'Digitalizado' : 'Físico'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Tipificación Jurídica y Normativa -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2 text-brand-green font-bold text-sm">
                                <i data-lucide="scale" class="w-4 h-4 text-brand-gold"></i>
                                <span>Marco Jurídico &amp; Delito</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase" :class="activeProceso.materia_badge || 'bg-slate-100 text-slate-700'" x-text="activeProceso.materia"></span>
                        </div>

                        <!-- Delito Principal -->
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Delito / Acción Principal:</span>
                            <h3 class="text-sm font-black text-brand-green uppercase leading-snug" x-text="activeProceso.delito"></h3>
                            
                            <div class="bg-brand-green/5 p-3 rounded-xl border border-brand-green/10 space-y-1">
                                <span class="text-[10px] font-bold text-brand-gold uppercase tracking-wider block">Artículo de Ley Imputado</span>
                                <p class="text-xs font-bold text-slate-800" x-text="activeProceso.articulo || 'Sin artículo específico'"></p>
                                <span class="text-[10px] text-slate-500 block" x-text="activeProceso.articulo_ley || 'Código Penal Boliviano'"></span>
                            </div>
                        </div>

                        <!-- Concurso de Delitos / Artículos Conexos -->
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Artículos y Tipificaciones Conexas:</span>
                            <template x-if="activeProceso.articulos_conexos && activeProceso.articulos_conexos.length > 0">
                                <div class="space-y-1.5">
                                    <template x-for="art in activeProceso.articulos_conexos" :key="art.id">
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                                            <span class="font-bold text-slate-700" x-text="art.numero + ' - ' + art.delito"></span>
                                            <span class="text-[9px] font-bold uppercase px-1.5 py-0.2 rounded" :class="art.es_principal ? 'bg-brand-gold/20 text-brand-gold' : 'bg-slate-200 text-slate-600'" x-text="art.es_principal ? 'Principal' : 'Conexo'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!activeProceso.articulos_conexos || activeProceso.articulos_conexos.length === 0">
                                <p class="text-xs text-slate-400 italic">No registra concurso con otros tipos penales.</p>
                            </template>
                        </div>

                        <!-- Estado y Etapa Procesal -->
                        <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Etapa Procesal:</span>
                                <span class="font-bold text-indigo-700 mt-0.5 block" x-text="activeProceso.etapa || 'Inicial'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Estado Operativo:</span>
                                <span class="font-bold mt-0.5 block" :class="activeProceso.estado_color || 'text-slate-700'" x-text="activeProceso.estado_badge"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: ACCIONES / BITÁCORA PROCESAL        -->
            <!-- ========================================== -->
            <div x-show="detailTab === 'acciones'" class="space-y-4">
                <!-- Encabezado con Botón Nueva Acción -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-brand-green">Historial de Actuaciones y Diligencias</h3>
                        <p class="text-xs text-slate-400">Bitácora cronológica completa de actos procesales, memoriales y resoluciones.</p>
                    </div>
                    <button @click="openNewHitoModal()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green hover:bg-brand-green-hover text-white text-xs font-bold rounded-xl shadow-sm shadow-brand-green/20 transition-all cursor-pointer">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-brand-gold"></i>
                        <span>Nueva Acción</span>
                    </button>
                </div>

                <!-- Timeline de Actuaciones -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <template x-if="activeProceso.hitos && activeProceso.hitos.length > 0">
                        <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            <template x-for="(hito, index) in activeProceso.hitos" :key="index">
                                <div class="relative">
                                    <!-- Bullet Dot -->
                                    <div class="absolute -left-[27px] top-1.5 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center shadow-xs"
                                         :class="index === 0 ? 'bg-brand-gold text-white' : 'bg-brand-green text-white'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>

                                    <!-- Tarjeta de la Actuación -->
                                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors space-y-2.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700" x-text="hito.tipo || 'Diligencia'"></span>
                                                <h4 class="text-sm font-extrabold text-slate-800" x-text="hito.accion"></h4>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="text-slate-400 font-medium" x-text="hito.fecha"></span>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-green/10 text-brand-green" x-text="hito.abogado || activeProceso.abogado"></span>
                                            </div>
                                        </div>

                                        <!-- Contenido / Comentarios -->
                                        <div x-show="hito.comentarios" class="text-xs text-slate-600 bg-white p-3 rounded-lg border border-slate-200/60 leading-relaxed">
                                            <p x-text="hito.comentarios"></p>
                                        </div>

                                        <!-- Documentos Anexos -->
                                        <div x-show="hito.documentos && hito.documentos.length" class="flex flex-wrap gap-2 pt-1">
                                            <template x-for="doc in hito.documentos" :key="doc">
                                                <div @click.stop="openPreview(doc)" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 rounded-lg px-2.5 py-1 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-500"></i>
                                                    <span class="truncate max-w-[200px]" x-text="doc"></span>
                                                    <i data-lucide="eye" class="w-3 h-3 text-rose-400"></i>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!activeProceso.hitos || activeProceso.hitos.length === 0">
                        <div class="py-16 text-center text-slate-400 space-y-3">
                            <i data-lucide="clipboard-list" class="w-12 h-12 mx-auto text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-600">Sin actuaciones registradas todavía</p>
                            <p class="text-xs text-slate-400">Utilice el botón "Nueva Acción" para asentar el primer hito en el expediente.</p>
                            <button @click="openNewHitoModal()" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green text-white text-xs font-bold rounded-xl shadow-xs hover:bg-brand-green-hover transition-colors">
                                <i data-lucide="plus-circle" class="w-4 h-4 text-brand-gold"></i>
                                Registrar Primera Acción
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 3: DOCUMENTOS DEL EXPEDIENTE           -->
            <!-- ========================================== -->
            <div x-show="detailTab === 'documentos'" class="space-y-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-brand-green">Archivos y Documentos del Proceso</h3>
                        <p class="text-xs text-slate-400">Expedientes digitalizados, memoriales en PDF, notificaciones y pruebas aportadas.</p>
                    </div>
                    <button @click="openNewHitoModal()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green hover:bg-brand-green-hover text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                        <i data-lucide="upload" class="w-4 h-4 text-brand-gold"></i>
                        <span>Adjuntar Documento</span>
                    </button>
                </div>

                <!-- Grilla de Documentos -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <template x-if="activeProceso.documentos_list && activeProceso.documentos_list.length > 0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="doc in activeProceso.documentos_list" :key="doc.id || doc.nombre">
                                <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/40 hover:bg-slate-50 transition-all flex flex-col justify-between space-y-3 group hover:border-brand-green/30 hover:shadow-xs">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2.5 rounded-xl bg-rose-100 text-rose-600 shrink-0 group-hover:scale-105 transition-transform">
                                            <i data-lucide="file-text" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-extrabold text-slate-800 truncate" x-text="doc.nombre"></h4>
                                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="'Origen: ' + (doc.origen || 'Expediente')"></span>
                                            <div class="flex items-center gap-2 text-[10px] text-slate-400 font-semibold mt-1">
                                                <span x-text="doc.peso"></span>
                                                <span>•</span>
                                                <span x-text="doc.fecha"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <button @click="openPreview(doc.nombre)" 
                                                class="flex-1 py-1.5 px-2.5 rounded-lg bg-white hover:bg-brand-green hover:text-white text-slate-700 text-xs font-bold border border-slate-200 transition-all flex items-center justify-center gap-1 shadow-2xs cursor-pointer">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Previsualizar</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!activeProceso.documentos_list || activeProceso.documentos_list.length === 0">
                        <div class="py-16 text-center text-slate-400 space-y-3">
                            <i data-lucide="folder-x" class="w-12 h-12 mx-auto text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-600">No hay documentos anexados en este proceso</p>
                            <p class="text-xs text-slate-400">Puede adjuntar memoriales, providencias o contratos en cualquier momento.</p>
                            <button @click="openNewHitoModal()" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green text-white text-xs font-bold rounded-xl shadow-xs hover:bg-brand-green-hover transition-colors">
                                <i data-lucide="upload" class="w-4 h-4 text-brand-gold"></i>
                                Adjuntar Primer Archivo
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: AUDIENCIAS Y PLAZOS                 -->
            <!-- ========================================== -->
            <div x-show="detailTab === 'eventos'" class="space-y-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-brand-green">Audiencias y Vencimiento de Plazos</h3>
                        <p class="text-xs text-slate-400">Compromisos procesales, fechas de audiencia y plazos fatales con juzgados.</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <template x-if="activeProceso.eventos && activeProceso.eventos.length > 0">
                        <div class="space-y-3">
                            <template x-for="ev in activeProceso.eventos" :key="ev.id">
                                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2.5 rounded-xl text-white shrink-0" :class="ev.es_fatal ? 'bg-rose-500' : 'bg-brand-green'">
                                            <i data-lucide="calendar" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-bold text-slate-800" x-text="ev.titulo"></h4>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase" :class="ev.es_fatal ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800'" x-text="ev.tipo"></span>
                                            </div>
                                            <span class="text-[11px] text-slate-500 block mt-0.5" x-text="'Lugar / Enlace: ' + ev.lugar"></span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-bold text-slate-700 block" x-text="ev.fecha"></span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block" :class="ev.estado === 'Pendiente' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700'" x-text="ev.estado"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!activeProceso.eventos || activeProceso.eventos.length === 0">
                        <div class="py-16 text-center text-slate-400 space-y-2">
                            <i data-lucide="calendar-check" class="w-12 h-12 mx-auto text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-600">Sin audiencias o plazos pendientes para este expediente</p>
                            <p class="text-xs text-slate-400">Los plazos programados en el módulo de Calendario se reflejarán automáticamente aquí.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 5: TRAZABILIDAD & AUDITORÍA            -->
            <!-- ========================================== -->
            <div x-show="detailTab === 'auditoria'" class="space-y-4">
                <div class="bg-amber-50/80 border border-brand-gold/30 p-3.5 rounded-xl flex items-start gap-2.5">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-brand-gold shrink-0 mt-0.5"></i>
                    <div class="text-xs text-amber-950 leading-snug">
                        <span class="font-bold text-brand-gold">Auditoría Inmutable:</span> Cada creación de proceso, modificación de datos, cambio de estado y registro de diligencia queda sellado con el usuario responsable y la fecha/hora exacta.
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
                    <template x-if="activeProceso.auditorias && activeProceso.auditorias.length > 0">
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
                        </div>
                    </template>

                    <template x-if="!activeProceso.auditorias || activeProceso.auditorias.length === 0">
                        <div class="py-16 text-center text-slate-400 space-y-2">
                            <i data-lucide="shield" class="w-12 h-12 mx-auto text-slate-300"></i>
                            <p class="text-xs font-bold text-slate-500">Sin registros de auditoría para este expediente</p>
                            <p class="text-[11px] text-slate-400">Las acciones que realice sobre este expediente se listarán aquí.</p>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL REGISTRAR NUEVA ACCIÓN / ACTUACIÓN EN EL PROCESO                     -->
    <!-- ========================================================================= -->
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         x-show="newHitoModalOpen" 
         @keydown.escape.window="newHitoModalOpen = false"
         @click.self="newHitoModalOpen = false"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         x-cloak>

        <!-- Tarjeta del Modal Por Encima -->
        <div class="relative w-full max-w-lg rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 border border-slate-100 overflow-hidden"
             @click.stop>
            
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-brand-gold"></i>
                    <h3 class="text-sm font-bold text-brand-green uppercase tracking-wider">Nueva Acción en Expediente</h3>
                </div>
                <button @click="newHitoModalOpen = false" class="p-1.5 rounded-xl hover:bg-slate-200/60 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="addHitoToProceso()" class="p-6 space-y-4">
                <!-- Fila 1: Tipo de Acción / Abogado Implicado -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1">
                            <span>Tipo de Acción</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="newHito.tipo" required
                                class="w-full px-3 py-2.5 text-xs font-semibold border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 bg-white transition-all cursor-pointer">
                            <option value="Diligencia">Diligencia Judicial</option>
                            <option value="Audiencia">Audiencia Judicial</option>
                            <option value="Memorial">Presentación de Memorial</option>
                            <option value="Notificación">Notificación Legal</option>
                            <option value="Resolución">Resolución / Auto</option>
                            <option value="Requerimiento">Requerimiento Fiscal</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Abogado Implicado</span>
                            <span class="text-[9px] font-bold text-brand-gold bg-brand-gold/10 px-1.5 py-0.5 rounded">En Sesión</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user-check" class="w-4 h-4 text-brand-gold"></i>
                            </div>
                            <input type="text" 
                                   :value="currentUser ? currentUser.name : ''"
                                   readonly 
                                   class="w-full pl-9 pr-3 py-2.5 text-xs font-bold border border-slate-200 rounded-xl bg-slate-50 text-slate-700 select-none cursor-default">
                        </div>
                    </div>
                </div>

                <!-- Fila 2: Título de la Actuación / Diligencia * -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-1">
                        <span>Título de la Actuación / Diligencia</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="newHito.accion" 
                           required 
                           placeholder="Ej: Notificación con Auto de Admisión, Audiencia Preliminar, etc."
                           class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-semibold placeholder:text-slate-400 placeholder:font-normal transition-all">
                </div>

                <!-- Fila 3: Fecha de Audiencia (Mostrar este campo en caso de que se seleccione Audiencia Judicial) -->
                <div x-show="newHito.tipo === 'Audiencia'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="p-3 bg-amber-50/70 border border-amber-200/90 rounded-xl space-y-1.5"
                     style="display: none;">
                    <label class="text-[10px] font-black text-amber-950 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Fecha de Audiencia</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           x-model="newHito.fecha_audiencia" 
                           :required="newHito.tipo === 'Audiencia'"
                           class="w-full px-3.5 py-2 text-xs border border-amber-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-slate-800 font-semibold bg-white transition-all shadow-xs">
                    <p class="text-[10px] text-amber-800/90 font-medium">Esta audiencia se agendará automáticamente en el calendario del sistema.</p>
                </div>

                <!-- Fila 4: Descripción / Comentarios Detallados -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Descripción / Comentarios Detallados</label>
                    <textarea x-model="newHito.comentarios" 
                              rows="3" 
                              placeholder="Detalle lo acontecido, acuerdos o decretos emitidos por el juzgado..."
                              class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 resize-none transition-all placeholder:text-slate-400"></textarea>
                </div>

                <!-- Fila 5: Campo para subir documentos (todo tipo de extension) -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider flex items-center justify-between">
                        <span>Adjuntar Documento o Prueba</span>
                        <span class="text-[9px] font-semibold text-slate-400">Todo tipo de extensión</span>
                    </label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-brand-gold/60 rounded-xl p-3 bg-slate-50/50 hover:bg-brand-gold/5 transition-all text-center">
                        <input type="file" 
                               id="bitacoraModalFileInput"
                               @change="newHito.fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex items-center justify-center gap-2">
                            <i data-lucide="upload-cloud" class="w-5 h-5 text-brand-gold"></i>
                            <div class="text-left">
                                <p class="text-xs font-bold text-slate-700" x-text="newHito.fileName ? newHito.fileName : 'Seleccionar o arrastrar archivo'"></p>
                                <p class="text-[10px] text-slate-400" x-show="!newHito.fileName">Permite PDF, Word, Excel, imágenes, ZIP o cualquier formato (Máx. 20MB)</p>
                            </div>
                        </div>
                        <template x-if="newHito.fileName">
                            <button type="button" 
                                    @click.stop="document.getElementById('bitacoraModalFileInput').value = ''; newHito.fileName = ''"
                                    class="relative z-20 mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 hover:text-rose-700 cursor-pointer">
                                <i data-lucide="trash-2" class="w-3 h-3"></i> Quitar archivo
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Footer Botones -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="newHitoModalOpen = false"
                            class="px-4 py-2 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="submittingHito"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer disabled:opacity-60"
                            style="background-color: #082a20; color: #c5a059;">
                        <span x-show="submittingHito" class="w-3.5 h-3.5 border-2 border-brand-gold border-t-transparent rounded-full animate-spin"></span>
                        <i x-show="!submittingHito" data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Registrar Acción</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- REGISTRO DE NUEVO PROCESO (PANTALLA COMPLETA)                             -->
    <!-- ========================================================================= -->
    <div x-show="currentView === 'create'" 
         class="bg-white rounded-2xl border border-slate-100 shadow-xs flex-1 min-h-0 flex flex-col w-full h-full overflow-hidden" 
         style="height: 100%; max-height: 100%; min-height: 0; overflow: hidden !important;"
         x-cloak>
        
        <!-- Header Fijo Superior -->
        <div class="border-b border-slate-100 bg-white shrink-0" style="padding: 16px 24px !important; flex-shrink: 0;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Izquierda: Botón Volver + Título -->
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" @click="closeCreateProceso()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-brand-green text-xs font-bold transition-all shadow-2xs cursor-pointer">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Volver a Procesos</span>
                    </button>

                    <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-green/10 flex items-center justify-center text-brand-green">
                            <i data-lucide="folder-plus" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-brand-green tracking-tight">Registrar Nuevo Proceso Jurídico</h2>
                            <p class="text-[11px] text-slate-500 font-medium">Apertura y radicatoria de causa en el sistema judicial / fiscal</p>
                        </div>
                    </div>
                </div>

                <!-- Derecha: Acciones rápidas (Cancelar y Guardar) -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="closeCreateProceso()" 
                            class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button" @click="submitForm()" 
                            :disabled="submittingNew"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-brand-green hover:bg-brand-green-hover disabled:opacity-50 rounded-xl shadow-md shadow-brand-green/15 transition-all cursor-pointer">
                        <span x-show="submittingNew" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <i x-show="!submittingNew" data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span x-text="submittingNew ? 'Guardando...' : 'Guardar Proceso'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Formulario con Scroll Interno -->
        <div class="flex-1 min-h-0 overflow-y-auto p-6 md:p-8 space-y-6" style="flex: 1 1 0px !important; min-height: 0 !important; overflow-y: auto !important;">
            <form @submit.prevent="submitForm()" class="max-w-6xl mx-auto space-y-6">

                <!-- 1. IDENTIFICACIÓN Y TIPO DE REGISTRO -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 md:p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-black">1</span>
                            <h3 class="text-xs font-bold text-brand-green uppercase tracking-wider">Identificación y Clasificación de la Causa</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600">Datos judiciales y fiscales</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tipo de Registro -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Tipo de Identificador <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newProceso.tipo" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-semibold shadow-2xs">
                                <option value="CUD">CUD (Fiscalía / Caso Penal)</option>
                                <option value="Portal Fis">Portal Fiscalía (IANUS)</option>
                                <option value="NUREJ">NUREJ (Tribunales / Juzgados)</option>
                                <option value="CASO">Caso Interno / Extrajudicial</option>
                            </select>
                        </div>

                        <!-- Número de Caso / CUD / Código Principal -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Nro. de Caso / CUD / Código <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="newProceso.codigo" required 
                                   placeholder="Ej: LPZ1910962 o 2011020120015"
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-bold shadow-2xs">
                        </div>

                        <!-- NUREJ / IANUS (Opcional) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                NUREJ / IANUS Complementario
                            </label>
                            <input type="text" x-model="newProceso.nurej" 
                                   placeholder="Ej: 20184712 o N/A"
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 shadow-2xs">
                        </div>

                        <!-- Materia -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Materia Jurídica <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newProceso.materia_id" 
                                    @change="onMateriaChange()"
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-semibold shadow-2xs">
                                <template x-for="mat in materiasList" :key="mat.id">
                                    <option :value="mat.id" x-text="mat.nombre"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Delito / Acción Jurídica Principal -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Delito / Acción Principal <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="newProceso.delito" required 
                                   list="articulos-datalist"
                                   placeholder="Ej: Estafa, Violencia Familiar, Cobro de Dinero..."
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 shadow-2xs">
                            <datalist id="articulos-datalist">
                                <template x-for="art in articulosList" :key="art.id">
                                    <option :value="art.epigrafe_delito" x-text="art.numero_articulo + ' - ' + art.epigrafe_delito"></option>
                                </template>
                            </datalist>
                        </div>

                        <!-- Jurisdicción / Asiento Judicial -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Jurisdicción / Asiento <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newProceso.jurisdiccion_id" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-semibold shadow-2xs">
                                <template x-for="jur in jurisdiccionesList" :key="jur.id">
                                    <option :value="jur.id" x-text="jur.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. SUJETOS PROCESALES (PARTES) -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 md:p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-black">2</span>
                            <h3 class="text-xs font-bold text-brand-green uppercase tracking-wider">Sujetos Procesales / Partes del Caso</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600">Representación y contraparte</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Lado A: Nuestro Cliente / Demandante -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200/80 space-y-3 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-brand-green uppercase flex items-center gap-1.5">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-brand-gold"></i>
                                    Nuestro Cliente / Demandante / Denunciante
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    Parte Asistida
                                </span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                    Nombre o Razón Social <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" x-model="newProceso.denunciante" required 
                                       @input="onClienteSelect()"
                                       list="clientes-datalist"
                                       placeholder="Nombre completo del cliente o empresa"
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-semibold">
                                <datalist id="clientes-datalist">
                                    <template x-for="cli in clientesList" :key="cli.id">
                                        <option :value="cli.nombre_razon_social" x-text="cli.nombre_razon_social + ' (' + (cli.documento_identidad || 'Sin CI') + ')'"></option>
                                    </template>
                                </datalist>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        Rol del Cliente
                                    </label>
                                    <select x-model="newProceso.rol_cliente_id" 
                                            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                        <template x-for="rol in rolesPartesList" :key="rol.id">
                                            <option :value="rol.id" x-text="rol.nombre"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        Tipo Persona
                                    </label>
                                    <select x-model="newProceso.cliente_tipo" 
                                            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                        <option value="NATURAL">Persona Natural</option>
                                        <option value="JURIDICA">Persona Jurídica (Empresa)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        Teléfono / WhatsApp
                                    </label>
                                    <input type="text" x-model="newProceso.telefono" 
                                           placeholder="Ej: 59177234317"
                                           class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        Correo Electrónico
                                    </label>
                                    <input type="email" x-model="newProceso.correo" 
                                           placeholder="cliente@ejemplo.com"
                                           class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                </div>
                            </div>
                        </div>

                        <!-- Lado B: Contraparte / Denunciado -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200/80 space-y-3 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-700 uppercase flex items-center gap-1.5">
                                    <i data-lucide="user-x" class="w-3.5 h-3.5 text-rose-500"></i>
                                    Contraparte / Demandado / Denunciado
                                </span>
                                <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                    Parte Contraria
                                </span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                    Nombre o Razón Social <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" x-model="newProceso.denunciado" required 
                                       placeholder="Nombre de la contraparte o demandado"
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        C.I. / NIT Contraparte
                                    </label>
                                    <input type="text" x-model="newProceso.denunciado_ci" 
                                           placeholder="Ej: 4892104 LP"
                                           class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                        Teléfono de Referencia
                                    </label>
                                    <input type="text" x-model="newProceso.denunciado_telefono" 
                                           placeholder="Opcional"
                                           class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">
                                    Abogado Patrocinante Contraparte
                                </label>
                                <input type="text" x-model="newProceso.abogado_contraparte" 
                                       placeholder="Nombre del abogado contrario (si se conoce)"
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. RADICATORIA Y AUTORIDADES -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 md:p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-black">3</span>
                            <h3 class="text-xs font-bold text-brand-green uppercase tracking-wider">Radicatoria, Juzgado y Autoridades</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600">Tribunal o Fiscalía</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Juzgado / Fiscalía / Organismo -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Juzgado, Tribunal o Fiscalía <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="newProceso.juzgado" required 
                                   list="juzgados-datalist"
                                   placeholder="Ej: JUZGADO 14° DE INSTRUCCIÓN ANTICORRUPCION Y CONTRA LA VIOLENCIA"
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-semibold shadow-2xs">
                            <datalist id="juzgados-datalist">
                                <template x-for="juz in juzgadosList" :key="juz.id">
                                    <option :value="juz.nombre" x-text="juz.nombre"></option>
                                </template>
                            </datalist>
                        </div>

                        <!-- Sala Judicial -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Sala de Turno / Apelación
                            </label>
                            <select x-model="newProceso.sala_id" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 shadow-2xs">
                                <option value="">Sin sala asignada</option>
                                <template x-for="s in salasList" :key="s.id">
                                    <option :value="s.id" x-text="s.nombre"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Juez / Fiscal a Cargo -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Juez o Fiscal a Cargo
                            </label>
                            <select x-model="newProceso.juez_id" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 shadow-2xs">
                                <option value="">Por designar / No asignado</option>
                                <template x-for="jz in juecesList" :key="jz.id">
                                    <option :value="jz.id" x-text="jz.nombre_completo + ' (' + (jz.tipo_autoridad || 'Juez') + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Investigador Policial / Asignado -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Investigador Asignado (FELCC / FELCV)
                            </label>
                            <select x-model="newProceso.investigador_id" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 shadow-2xs">
                                <option value="">Sin investigador asignado</option>
                                <template x-for="inv in investigadoresList" :key="inv.id">
                                    <option :value="inv.id" x-text="(inv.grado?.sigla || '') + ' ' + inv.nombres + ' ' + inv.apellidos"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Fecha de Inicio / Radicatoria -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Fecha de Inicio / Radicatoria <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" x-model="newProceso.fecha" required 
                                   class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-semibold shadow-2xs">
                        </div>
                    </div>
                </div>

                <!-- 4. GESTIÓN INTERNA Y ESTADO -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 md:p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-black">4</span>
                            <h3 class="text-xs font-bold text-brand-green uppercase tracking-wider">Gestión Interna y Estado del Proceso</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600">Asignación en el bufete</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Abogado Asignado -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Abogado Responsable del Caso <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newProceso.abogado_id" required 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 font-bold shadow-2xs">
                                <template x-for="m in equipo" :key="m.id">
                                    <option :value="m.id" x-text="m.nombre + ' (' + m.cargo + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Estado Inicial -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Estado Procesal Inicial <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newProceso.estado_id" required 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 font-semibold shadow-2xs">
                                <template x-for="est in estadosList" :key="est.id">
                                    <option :value="est.id" x-text="est.nombre"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Etapa Procesal -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Etapa Procesal Inicial
                            </label>
                            <select x-model="newProceso.etapa_procesal_id" 
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-700 shadow-2xs">
                                <option value="">Predeterminada por materia</option>
                                <template x-for="et in (etapasList || []).filter(e => !e.materia_id || e.materia_id == newProceso.materia_id)" :key="et.id">
                                    <option :value="et.id" x-text="et.nombre"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Situación Actual / Resumen de Hechos -->
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Situación Actual / Resumen del Caso y Diligencias Iniciales <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="newProceso.estado" required rows="3" 
                                      placeholder="Describa los antecedentes principales, memoriales presentados o diligencias inmediatas..."
                                      class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-800 shadow-2xs"></textarea>
                            <p class="text-[10px] text-slate-600 mt-1">Este resumen se registrará automáticamente como la primera actuación relevante en la bitácora del expediente.</p>
                        </div>
                    </div>
                </div>

                <!-- Barra de Acciones Final -->
                <div class="flex items-center justify-end gap-3 pt-4 pb-8 border-t border-slate-100">
                    <button type="button" @click="closeCreateProceso()" 
                            class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="submittingNew"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-brand-green hover:bg-brand-green-hover disabled:opacity-50 rounded-xl shadow-lg shadow-brand-green/20 transition-all cursor-pointer">
                        <span x-show="submittingNew" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <i x-show="!submittingNew" data-lucide="check-circle" class="w-4 h-4 text-brand-gold"></i>
                        <span x-text="submittingNew ? 'Guardando Expediente...' : 'Guardar y Abrir Expediente'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL PARA EDITAR PROCESO / EXPEDIENTE                                    -->
    <!-- ========================================================================= -->
    <div class="fixed inset-0 z-50 overflow-y-auto"
         x-show="editCaseModalOpen" 
         style="display: none;"
         @keydown.escape.window="editCaseModalOpen = false"
         x-cloak>

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-10"
             @click="editCaseModalOpen = false"
             x-show="editCaseModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Centering Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center relative z-20 pointer-events-none">
            <!-- Modal Card -->
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all pointer-events-auto z-30 border border-slate-100 flex flex-col max-h-[90vh] text-left"
                 @click.stop
                 x-show="editCaseModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Header Modal -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0"
                     style="background-color: #082a20; color: #ffffff;">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-xs"
                             style="background-color: #c5a059; color: #082a20;">
                            <i data-lucide="edit-3" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black text-white tracking-tight">Editar Expediente</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold"
                                      style="background-color: rgba(197, 160, 89, 0.2); color: #f3d496;"
                                      x-text="editProceso.codigo"></span>
                            </div>
                            <p class="text-[10px] text-slate-300 font-medium">Actualice la información procesal, autoridades, tipificación y situación actual</p>
                        </div>
                    </div>
                    <button type="button" @click="editCaseModalOpen = false" class="text-slate-400 hover:text-white transition-colors cursor-pointer p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Form Body con Scroll Interno -->
                <form @submit.prevent="submitEditProceso()" class="flex-1 overflow-y-auto p-6 space-y-5">
                    
                    <!-- SECCIÓN 1: Identificación Judicial -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-brand-green">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-brand-gold"></i>
                            <span>Identificación del Expediente</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nro. / Código *</label>
                                <input type="text" x-model="editProceso.codigo" required
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold text-brand-green focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tipo de Registro</label>
                                <select x-model="editProceso.tipo"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <option value="Portal Fis">Portal Fis</option>
                                    <option value="CUD">CUD</option>
                                    <option value="CASO">CASO</option>
                                    <option value="EXPEDIENTE">EXPEDIENTE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Jurisdicción</label>
                                <select x-model="editProceso.jurisdiccion_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="jur in jurisdiccionesList" :key="jur.id">
                                        <option :value="jur.id" x-text="`${jur.nombre} (${jur.departamento})`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">CUD / IANUS</label>
                                <input type="text" x-model="editProceso.cud" placeholder="Código Único de Causa..."
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">NUREJ</label>
                                <input type="text" x-model="editProceso.nurej" placeholder="NUREJ del juzgado..."
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Partes Procesales & Sujetos -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-brand-green">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-brand-gold"></i>
                            <span>Partes Procesales &amp; Contacto</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Denunciante / Demandante *</label>
                                <input type="text" x-model="editProceso.denunciante" required placeholder="Nombre completo o razón social..."
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold uppercase text-slate-800 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Denunciado / Demandado *</label>
                                <input type="text" x-model="editProceso.denunciado" required placeholder="Nombre de la contraparte..."
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold uppercase text-slate-800 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Celular / WhatsApp de Contacto</label>
                                <input type="text" x-model="editProceso.telefono" placeholder="Ej: 59177234317"
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 font-mono focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Rol de Nuestro Patrocinado</label>
                                <select x-model="editProceso.rol_cliente_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="rc in rolesPartesList" :key="rc.id">
                                        <option :value="rc.id" x-text="rc.nombre"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Marco Jurídico & Tipificación -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-brand-green">
                            <i data-lucide="scale" class="w-3.5 h-3.5 text-brand-gold"></i>
                            <span>Tipificación Jurídica &amp; Materia</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Materia Jurídica *</label>
                                <select x-model="editProceso.materia_id" required
                                        @change="const found = materiasList.find(m => m.id == editProceso.materia_id); if (found) editProceso.materia = found.nombre;"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="mat in materiasList" :key="mat.id">
                                        <option :value="mat.id" x-text="mat.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Etapa Procesal</label>
                                <select x-model="editProceso.etapa_procesal_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="et in (etapasList.filter(e => !editProceso.materia_id || e.materia_id == editProceso.materia_id).length > 0 ? etapasList.filter(e => !editProceso.materia_id || e.materia_id == editProceso.materia_id) : etapasList)" :key="et.id">
                                        <option :value="et.id" x-text="et.nombre"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Delito / Acción Principal *</label>
                            <div class="space-y-1">
                                <select x-model="editProceso.articulo_principal_id" 
                                        @change="const found = articulosList.find(a => a.id == editProceso.articulo_principal_id); if (found) editProceso.delito = found.epigrafe_delito;"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <option :value="null">-- Seleccionar tipificación del catálogo o escribir abajo --</option>
                                    <template x-for="a in articulosList" :key="a.id">
                                        <option :value="a.id" x-text="`${a.numero_articulo} - ${a.epigrafe_delito}`"></option>
                                    </template>
                                </select>
                                <input type="text" x-model="editProceso.delito" required placeholder="Ej: Falsedad Material, Estafa, Asistencia Familiar..."
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 font-bold uppercase focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Estrados & Autoridades -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-brand-green">
                            <i data-lucide="landmark" class="w-3.5 h-3.5 text-brand-gold"></i>
                            <span>Estrados Judiciales &amp; Autoridades</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Juzgado / Tribunal *</label>
                                <div class="space-y-1">
                                    <select x-model="editProceso.juzgado_id" 
                                            @change="const found = juzgadosList.find(j => j.id == editProceso.juzgado_id); if (found) editProceso.juzgado = found.nombre;"
                                            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                        <option :value="null">-- Seleccionar juzgado o escribir abajo --</option>
                                        <template x-for="j in juzgadosList" :key="j.id">
                                            <option :value="j.id" x-text="j.nombre"></option>
                                        </template>
                                    </select>
                                    <input type="text" x-model="editProceso.juzgado" required placeholder="Nombre del juzgado o tribunal..."
                                           class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sala Departamental (Alzada)</label>
                                <select x-model="editProceso.sala_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <option :value="null">-- Sin sala asignada --</option>
                                    <template x-for="s in salasList" :key="s.id">
                                        <option :value="s.id" x-text="s.nombre"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Juez Titular / Fiscal</label>
                                <select x-model="editProceso.juez_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <option :value="null">-- En asignación / Por sorteo --</option>
                                    <template x-for="j in juecesList" :key="j.id">
                                        <option :value="j.id" x-text="`${j.nombre_completo} (${j.tipo_autoridad})`"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Investigador Policial (FELCC)</label>
                                <select x-model="editProceso.investigador_id"
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <option :value="null">-- Sin investigador policial --</option>
                                    <template x-for="inv in investigadoresList" :key="inv.id">
                                        <option :value="inv.id" x-text="`${inv.grado ? inv.grado.abreviatura + ' ' : ''}${inv.nombres} ${inv.apellidos}`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 5: Asignación, Estado & Situación Actual -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-brand-green">
                            <i data-lucide="briefcase" class="w-3.5 h-3.5 text-brand-gold"></i>
                            <span>Gestión Operativa &amp; Estado</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Abogado Asignado *</label>
                                <select x-model="editProceso.abogado_id" required
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="m in equipo" :key="m.id">
                                        <option :value="m.id" x-text="m.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Estado Procesal *</label>
                                <select x-model="editProceso.estado_id" required
                                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-bold text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                                    <template x-for="est in estadosList" :key="est.id">
                                        <option :value="est.id" x-text="est.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Fecha de Radicatoria</label>
                                <input type="date" x-model="editProceso.fecha_inicio"
                                       class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Situación Procesal Actual *</label>
                            <textarea x-model="editProceso.situacion_actual" required rows="3" placeholder="Detalle la situación procesal actual del caso..."
                                      class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-700 focus:border-brand-gold focus:ring-1 focus:ring-brand-gold leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- Footer Botones -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" 
                                @click="editCaseModalOpen = false"
                                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" 
                                :disabled="submittingEdit"
                                class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-black rounded-xl transition-all shadow-xs cursor-pointer disabled:opacity-50"
                                style="background-color: #082a20; color: #c5a059;">
                            <span x-show="submittingEdit" class="w-3.5 h-3.5 border-2 border-brand-gold border-t-transparent rounded-full animate-spin"></span>
                            <i x-show="!submittingEdit" data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="submittingEdit ? 'Guardando Cambios...' : 'Guardar Cambios'"></span>
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
                    <div class="w-full max-w-2xl bg-white shadow-2xl p-10 text-slate-800 border border-slate-200 relative min-h-[800px] select-none text-[11px] leading-relaxed">
                        
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
    <!-- ========================================================================= -->
    <!-- MODAL POPUP: FILTROS POR COLUMNA                                          -->
    <!-- ========================================================================= -->
    <div class="fixed inset-0 z-50 overflow-y-auto"
         x-show="filterModalOpen" 
         style="display: none;"
         @keydown.escape.window="filterModalOpen = false"
         x-cloak>

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="filterModalOpen = false"
             x-show="filterModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Modal Dialog -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all"
                 x-show="filterModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-green/10 text-brand-green flex items-center justify-center">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filtro de Procesos</h3>
                            <p class="text-[11px] text-slate-500 font-normal">Filtre las causas por cada columna</p>
                        </div>
                    </div>
                    <button type="button" @click="filterModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Body Placeholder -->
                <div class="p-6 space-y-4">
                    <div class="text-center py-6 text-slate-400 space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                            <i data-lucide="filter" class="w-6 h-6 text-brand-gold"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase">Filtros por Columna</h4>
                        <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Selección y filtrado específico por cada columna de la tabla.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-2 px-5 py-3.5 bg-slate-50 border-t border-slate-100">
                    <button type="button" @click="filterModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                        Cerrar
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
            currentView: 'list', // 'list' | 'detail' | 'create'
            detailTab: 'general', // 'general' | 'acciones' | 'documentos' | 'eventos' | 'auditoria'
            newHitoModalOpen: false,
            newCaseModalOpen: false,
            filterModalOpen: false,
            columnsConfigModalOpen: false,
            submittingNew: false,
            toastVisible: false,
            toastMessage: '',
            activeProceso: {},
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
                tipo: 'CUD',
                codigo: '',
                nurej: '',
                materia_id: {{ count($materias) ? $materias[0]->id : 1 }},
                delito: '',
                jurisdiccion_id: {{ count($jurisdicciones) ? $jurisdicciones[0]->id : 1 }},
                
                // Sujetos procesales
                denunciante: '',
                demandante_id: null,
                cliente_id: null,
                rol_cliente_id: {{ count($rolesPartes) ? $rolesPartes[0]->id : 1 }},
                cliente_tipo: 'NATURAL',
                telefono: '',
                correo: '',

                denunciado: '',
                demandado_id: null,
                denunciado_ci: '',
                denunciado_telefono: '',
                abogado_contraparte: '',

                // Radicatoria
                juzgado: '',
                sala_id: '',
                juez_id: '',
                investigador_id: '',
                fecha: '{{ date('Y-m-d') }}',

                // Gestión
                abogado_id: {{ $currentUser ? $currentUser->id : (count($equipo) ? $equipo[0]->id : 1) }},
                estado_id: {{ count($estados) ? $estados[0]->id : 1 }},
                etapa_procesal_id: '',
                estado: ''
            },
            currentUser: {!! json_encode($currentUser ? ['id' => $currentUser->id, 'name' => $currentUser->name, 'cargo' => $currentUser->cargo] : ['id' => 1, 'name' => 'Alan Sillerico Segurondo', 'cargo' => 'Director General']) !!},
            newHito: {
                tipo: 'Diligencia',
                abogado_id: {{ $currentUser ? $currentUser->id : 1 }},
                abogado: {!! json_encode($currentUser ? $currentUser->name : 'Alan Sillerico Segurondo') !!},
                accion: '',
                fecha_audiencia: '',
                comentarios: '',
                fileName: ''
            },
            submittingHito: false,
            equipo: @json($equipo),
            casos: @json($procesosFormatted),
            materiasList: @json($materias),
            estadosList: @json($estados),
            etapasList: @json($etapas),
            juzgadosList: @json($juzgados),
            salasList: @json($salas),
            juecesList: @json($jueces),
            investigadoresList: @json($investigadores),
            articulosList: @json($articulos),
            rolesPartesList: @json($rolesPartes),
            clientesList: @json($clientes),
            jurisdiccionesList: @json($jurisdicciones),
            editCaseModalOpen: false,
            submittingEdit: false,
            editProceso: {},

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
                if (!this.activeProceso.documentos_list) this.activeProceso.documentos_list = [];
                if (!this.activeProceso.auditorias) this.activeProceso.auditorias = [];
                if (!this.activeProceso.eventos) this.activeProceso.eventos = [];
                this.detailTab = 'general';
                this.currentView = 'detail';
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            closeProcesoDetails() {
                this.currentView = 'list';
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            openCreateProceso() {
                this.resetNewProcesoForm();
                this.currentView = 'create';
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            closeCreateProceso() {
                this.currentView = 'list';
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            openFilterModal() {
                this.filterModalOpen = true;
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            closeFilterModal() {
                this.filterModalOpen = false;
            },

            resetNewProcesoForm() {
                this.newProceso = {
                    tipo: 'CUD',
                    codigo: '',
                    nurej: '',
                    materia_id: this.materiasList && this.materiasList.length ? this.materiasList[0].id : 1,
                    delito: '',
                    jurisdiccion_id: this.jurisdiccionesList && this.jurisdiccionesList.length ? this.jurisdiccionesList[0].id : 1,
                    
                    // Sujetos procesales
                    denunciante: '',
                    demandante_id: null,
                    cliente_id: null,
                    rol_cliente_id: this.rolesPartesList && this.rolesPartesList.length ? this.rolesPartesList[0].id : 1,
                    cliente_tipo: 'NATURAL',
                    telefono: '',
                    correo: '',

                    denunciado: '',
                    demandado_id: null,
                    denunciado_ci: '',
                    denunciado_telefono: '',
                    abogado_contraparte: '',

                    // Radicatoria
                    juzgado: '',
                    sala_id: '',
                    juez_id: '',
                    investigador_id: '',
                    fecha: '{{ date('Y-m-d') }}',

                    // Gestión
                    abogado_id: this.currentUser ? this.currentUser.id : (this.equipo && this.equipo.length ? this.equipo[0].id : 1),
                    estado_id: this.estadosList && this.estadosList.length ? this.estadosList[0].id : 1,
                    etapa_procesal_id: '',
                    estado: ''
                };
            },

            onClienteSelect() {
                if (!this.newProceso.denunciante || !this.clientesList) return;
                const search = this.newProceso.denunciante.trim().toLowerCase();
                const matched = this.clientesList.find(c => c.nombre_razon_social && c.nombre_razon_social.trim().toLowerCase() === search);
                if (matched) {
                    if (matched.celular_whatsapp) this.newProceso.telefono = matched.celular_whatsapp;
                    if (matched.email) this.newProceso.correo = matched.email;
                    if (matched.tipo_persona) this.newProceso.cliente_tipo = matched.tipo_persona;
                    this.newProceso.cliente_id = matched.id;
                    this.newProceso.demandante_id = matched.id;
                }
            },

            onMateriaChange() {
                this.newProceso.etapa_procesal_id = '';
            },

            openEditProcesoModal(proceso) {
                const p = proceso || this.activeProceso;
                if (!p) return;
                this.editProceso = {
                    id: p.id,
                    codigo: p.codigo || '',
                    codigo_interno: p.codigo || '',
                    tipo: p.tipo || 'Portal Fis',
                    cud: p.cud || '',
                    nurej: (p.nurej && p.nurej !== 'N/A') ? p.nurej : '',
                    codigo_caso: p.codigo_caso || '',
                    portal_fiscalia: p.tipo ? p.tipo.includes('Portal') : false,
                    materia_id: p.materia_id || (this.materiasList[0] ? this.materiasList[0].id : null),
                    materia: p.materia || '',
                    jurisdiccion_id: p.jurisdiccion_id || (this.jurisdiccionesList[0] ? this.jurisdiccionesList[0].id : null),
                    ubicacion: p.ubicacion || '',
                    denunciante: p.denunciante || '',
                    demandante_id: p.denunciante_id || null,
                    denunciado: p.denunciado || '',
                    demandado_id: p.demandado_id || null,
                    cliente_id: p.cliente_id || null,
                    rol_cliente_id: p.rol_cliente_id || (this.rolesPartesList[0] ? this.rolesPartesList[0].id : 1),
                    telefono: p.telefono || '',
                    delito: p.delito || '',
                    articulo_principal_id: p.articulo_id || null,
                    juzgado: p.juzgado || '',
                    juzgado_id: p.juzgado_id || null,
                    sala_id: p.sala_id || null,
                    juez_id: p.juez_id || null,
                    investigador_id: p.investigador_id || null,
                    etapa_procesal_id: p.etapa_id || (this.etapasList[0] ? this.etapasList[0].id : null),
                    etapa: p.etapa || '',
                    abogado_id: p.abogado_id || (this.equipo[0] ? this.equipo[0].id : null),
                    abogado: p.abogado || '',
                    estado_id: p.estado_id || (this.estadosList[0] ? this.estadosList[0].id : null),
                    estado_badge: p.estado_badge || '',
                    situacion_actual: p.situacion_actual || p.estado || '',
                    fecha_inicio: p.fecha_inicio_raw || '{{ date("Y-m-d") }}'
                };
                this.editCaseModalOpen = true;
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },

            async submitEditProceso() {
                if (!this.editProceso.codigo || !this.editProceso.id) {
                    this.showToast('Identificador de expediente obligatorio.');
                    return;
                }
                this.submittingEdit = true;

                try {
                    const response = await fetch(`/procesos/${this.editProceso.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.editProceso)
                    });

                    const data = await response.json();
                    if (data.success && data.proceso) {
                        const idx = this.casos.findIndex(c => c.id === data.proceso.id);
                        if (idx !== -1) {
                            this.casos[idx] = data.proceso;
                        }
                        if (this.activeProceso && this.activeProceso.id === data.proceso.id) {
                            this.activeProceso = data.proceso;
                            if (!this.activeProceso.hitos) this.activeProceso.hitos = [];
                            if (!this.activeProceso.documentos) this.activeProceso.documentos = [];
                            if (!this.activeProceso.documentos_list) this.activeProceso.documentos_list = [];
                            if (!this.activeProceso.auditorias) this.activeProceso.auditorias = [];
                            if (!this.activeProceso.eventos) this.activeProceso.eventos = [];
                        }
                        this.editCaseModalOpen = false;
                        this.showToast('Expediente actualizado exitosamente.');
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    } else {
                        this.showToast('Error al actualizar: ' + (data.message || 'Verifique los campos.'));
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Error de conexión al actualizar expediente.');
                } finally {
                    this.submittingEdit = false;
                }
            },

            openNewHitoModal() {
                this.newHito = {
                    tipo: 'Diligencia',
                    abogado_id: this.currentUser ? this.currentUser.id : {{ $currentUser ? $currentUser->id : 1 }},
                    abogado: this.currentUser ? this.currentUser.name : {!! json_encode($currentUser ? $currentUser->name : 'Alan Sillerico Segurondo') !!},
                    accion: '',
                    fecha_audiencia: '',
                    comentarios: '',
                    fileName: ''
                };
                const fileInput = document.getElementById('bitacoraModalFileInput');
                if (fileInput) fileInput.value = '';
                this.newHitoModalOpen = true;
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
                if (!this.newProceso.codigo || !this.newProceso.denunciante || !this.newProceso.denunciado || !this.newProceso.juzgado || !this.newProceso.delito) {
                    this.showToast('Por favor complete los campos obligatorios marcados con (*)');
                    return;
                }

                this.submittingNew = true;
                try {
                    const matObj = this.materiasList ? this.materiasList.find(m => m.id == this.newProceso.materia_id) : null;
                    const estObj = this.estadosList ? this.estadosList.find(e => e.id == this.newProceso.estado_id) : null;
                    const abgObj = this.equipo ? this.equipo.find(e => e.id == this.newProceso.abogado_id) : null;

                    const payload = {
                        codigo_interno: this.newProceso.codigo,
                        codigo: this.newProceso.codigo,
                        tipo: this.newProceso.tipo,
                        cud: this.newProceso.tipo === 'CUD' ? this.newProceso.codigo : null,
                        codigo_caso: this.newProceso.tipo === 'CASO' ? this.newProceso.codigo : null,
                        nurej: (this.newProceso.nurej && this.newProceso.nurej !== 'N/A') ? this.newProceso.nurej : null,
                        portal_fiscalia: this.newProceso.tipo === 'Portal Fis',
                        
                        materia_id: this.newProceso.materia_id,
                        materia: matObj ? matObj.nombre : null,
                        delito_accion: this.newProceso.delito || 'Acción Jurídica',
                        jurisdiccion_id: this.newProceso.jurisdiccion_id,

                        // Sujetos procesales
                        demandante_id: this.newProceso.demandante_id || null,
                        demandante_denunciante: this.newProceso.denunciante,
                        cliente_id: this.newProceso.cliente_id || null,
                        nuevo_cliente_nombre: this.newProceso.denunciante,
                        rol_cliente_id: this.newProceso.rol_cliente_id || 1,
                        cliente_tipo: this.newProceso.cliente_tipo || 'NATURAL',
                        telefono: this.newProceso.telefono,
                        correo: this.newProceso.correo,

                        demandado_id: this.newProceso.demandado_id || null,
                        demandado_denunciado: this.newProceso.denunciado,
                        demandado_ci: this.newProceso.denunciado_ci,
                        demandado_telefono: this.newProceso.denunciado_telefono,
                        abogado_contraparte: this.newProceso.abogado_contraparte,

                        // Radicatoria
                        juzgado_tribunal: this.newProceso.juzgado,
                        sala_id: this.newProceso.sala_id || null,
                        juez_id: this.newProceso.juez_id || null,
                        investigador_id: this.newProceso.investigador_id || null,
                        fecha_inicio: this.newProceso.fecha,

                        // Gestión interna
                        abogado_id: this.newProceso.abogado_id,
                        estado_id: this.newProceso.estado_id,
                        estado: estObj ? estObj.nombre : null,
                        estado_badge: estObj ? estObj.nombre : null,
                        estado_detalle: this.newProceso.estado,
                        situacion_actual: this.newProceso.estado,
                        etapa_procesal_id: this.newProceso.etapa_procesal_id || null
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
                    if (data.success && data.proceso) {
                        this.casos.unshift(data.proceso);
                        if (abgObj) {
                            abgObj.activos = (abgObj.activos || 0) + 1;
                        }
                        this.resetNewProcesoForm();
                        this.showToast('Proceso registrado exitosamente en la base de datos.');
                        
                        // Abrir directamente la vista detalle a pantalla completa del nuevo caso
                        this.openProcesoDetails(data.proceso);
                    } else {
                        this.showToast('Error al registrar: ' + (data.message || 'Verifique los campos requeridos'));
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Error de conexión con el servidor.');
                } finally {
                    this.submittingNew = false;
                }
            },

            async addHitoToProceso() {
                if (!this.newHito.accion) return;
                if (this.newHito.tipo === 'Audiencia' && !this.newHito.fecha_audiencia) {
                    this.showToast('Debe ingresar la fecha y hora de la audiencia.');
                    return;
                }
                
                this.submittingHito = true;
                try {
                    const formData = new FormData();
                    formData.append('titulo_actuacion', this.newHito.accion);
                    formData.append('descripcion', this.newHito.comentarios || '');
                    formData.append('tipo_actuacion', this.newHito.tipo || 'Diligencia');
                    formData.append('abogado_id', this.newHito.abogado_id || (this.currentUser ? this.currentUser.id : 1));
                    
                    if (this.newHito.tipo === 'Audiencia' && this.newHito.fecha_audiencia) {
                        formData.append('fecha_hora', this.newHito.fecha_audiencia);
                    }

                    const fileInput = document.getElementById('bitacoraModalFileInput') || document.getElementById('bitacoraFileInput');
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
                        const hitoDate = data.actuacion && data.actuacion.fecha_hora ? new Date(data.actuacion.fecha_hora) : new Date();
                        const hitoObj = {
                            id: data.actuacion.id,
                            fecha: hitoDate.toLocaleDateString('es-ES') + ' ' + hitoDate.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'}),
                            tipo: data.actuacion.tipo_actuacion || this.newHito.tipo || 'Diligencia',
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
                        if (data.documento) {
                            if (!this.activeProceso.documentos) this.activeProceso.documentos = [];
                            if (!this.activeProceso.documentos.includes(data.documento.nombre_original)) {
                                this.activeProceso.documentos.unshift(data.documento.nombre_original);
                            }
                            if (!this.activeProceso.documentos_list) this.activeProceso.documentos_list = [];
                            this.activeProceso.documentos_list.unshift({
                                id: data.documento.id,
                                nombre: data.documento.nombre_original,
                                peso: data.documento.peso_bytes ? Math.round(data.documento.peso_bytes / 1024) + ' KB' : 'Documento adjunto',
                                fecha: new Date().toLocaleDateString('es-ES') + ' ' + new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'}),
                                origen: data.actuacion.titulo_actuacion,
                                mime: data.documento.mime_type
                            });
                        } else if (this.newHito.fileName && !this.activeProceso.documentos.includes(this.newHito.fileName)) {
                            this.activeProceso.documentos.unshift(this.newHito.fileName);
                        }

                        // Telegram notification
                        if (this.telegramNotify && this.telegramToken && this.telegramChatId) {
                            const telegramText = `🔔 *Nueva Actividad Registrada*\n` +
                                                 `*Expediente:* ${this.activeProceso.codigo}\n` +
                                                 `*Cliente:* ${this.activeProceso.cliente || this.activeProceso.denunciante}\n` +
                                                 `*Tipo:* ${hitoObj.tipo}\n` +
                                                 `*Actuación:* ${hitoObj.accion}\n` +
                                                 (this.newHito.tipo === 'Audiencia' && this.newHito.fecha_audiencia ? `*Fecha Audiencia:* ${this.newHito.fecha_audiencia}\n` : '') +
                                                 `*Comentario:* ${hitoObj.comentarios || 'Sin comentarios.'}\n` +
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
                            tipo: 'Diligencia',
                            abogado_id: this.currentUser ? this.currentUser.id : {{ $currentUser ? $currentUser->id : 1 }},
                            abogado: this.currentUser ? this.currentUser.name : {!! json_encode($currentUser ? $currentUser->name : 'Alan Sillerico Segurondo') !!},
                            accion: '',
                            fecha_audiencia: '',
                            comentarios: '',
                            fileName: ''
                        };
                        if (fileInput) fileInput.value = '';
                        this.newHitoModalOpen = false;
                        this.showToast('Nueva acción registrada exitosamente en la bitácora.');
                        this.$nextTick(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        });
                    } else {
                        this.showToast('Error al registrar en la bitácora: ' + (data.message || 'Intente de nuevo'));
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Error de conexión al guardar actividad.');
                } finally {
                    this.submittingHito = false;
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
