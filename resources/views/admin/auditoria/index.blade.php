@extends('layouts.admin')

@section('title', 'Auditoría y Trazabilidad - Sillerico & Abogados')
@section('header_title', 'Registro de Actividades y Trazabilidad')

@section('content')
<div x-data="auditoriaData()" class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-brand-green">Bitácora Global de Operaciones</h2>
            <p class="text-xs text-slate-500">Registro inmutable de todas las acciones ejecutadas por el equipo sobre los expedientes.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="refreshAuditorias()" 
                    :disabled="loading"
                    class="flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-colors shadow-2xs">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-brand-green" :class="{ 'animate-spin': loading }"></i>
                Actualizar Bitácora
            </button>
            <a href="{{ route('usuarios.index') }}" 
               class="flex items-center gap-1.5 px-3.5 py-2 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-colors">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                Ver Operadores
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Actividades Registradas</span>
                <h4 class="text-2xl font-black text-brand-green" x-text="auditorias.length"></h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brand-green/5 text-brand-green flex items-center justify-center">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Causas Creadas</span>
                <h4 class="text-2xl font-black text-emerald-600" x-text="countAccion('CREACION_PROCESO')"></h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Actuaciones Procesales</span>
                <h4 class="text-2xl font-black text-brand-gold" x-text="countAccion('NUEVA_ACTUACION')"></h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brand-gold/10 text-brand-gold flex items-center justify-center">
                <i data-lucide="file-check" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Modificaciones de Estado</span>
                <h4 class="text-2xl font-black text-indigo-600" x-text="countAccion('CAMBIO_ESTADO') + countAccion('EDICION_PROCESO')"></h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="git-commit" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Main Stream and Filters -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <!-- Filter Header -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
            <!-- Search bar -->
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Buscar por código (EXP-...), cliente o descripción..." 
                       class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold shadow-2xs transition-all">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- User Filter -->
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                    <select x-model="selectedUser" class="text-xs bg-transparent border-none focus:ring-0 text-slate-700 font-medium cursor-pointer pr-6">
                        <option value="Todos">Todos los Operadores</option>
                        <template x-for="u in abogados" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Action Type Filter -->
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-slate-400"></i>
                    <select x-model="selectedAccion" class="text-xs bg-transparent border-none focus:ring-0 text-slate-700 font-medium cursor-pointer pr-6">
                        <option value="Todas">Todas las Acciones</option>
                        <option value="CREACION_PROCESO">Creación de Expediente</option>
                        <option value="NUEVA_ACTUACION">Actuación / Diligencia</option>
                        <option value="CAMBIO_ESTADO">Cambio de Estado</option>
                        <option value="EDICION_PROCESO">Edición de Expediente</option>
                        <option value="ELIMINACION_PROCESO">Eliminación</option>
                    </select>
                </div>

                <!-- Counter Badge -->
                <div class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl">
                    <span x-text="filteredAuditorias.length"></span> eventos
                </div>
            </div>
        </div>

        <!-- Activity Timeline / List -->
        <div class="divide-y divide-slate-100">
            <template x-for="item in filteredAuditorias" :key="item.id">
                <div class="p-5 hover:bg-slate-50/80 transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <!-- Left: Operator + Action + Process -->
                    <div class="flex items-start gap-3.5 min-w-0">
                        <!-- User Avatar -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs"
                             :class="item.user_color || 'bg-brand-green text-white'">
                            <span x-text="item.user_iniciales || 'OP'"></span>
                        </div>

                        <!-- Main Info -->
                        <div class="space-y-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold text-slate-800" x-text="item.user_nombre"></span>
                                <span class="text-[10px] text-slate-400 font-medium" x-text="item.user_cargo"></span>
                                
                                <!-- Action Badge -->
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wide uppercase"
                                      :class="{
                                        'bg-emerald-50 text-emerald-700 border border-emerald-200': item.accion === 'CREACION_PROCESO',
                                        'bg-amber-50 text-brand-gold border border-brand-gold/30': item.accion === 'NUEVA_ACTUACION',
                                        'bg-indigo-50 text-indigo-700 border border-indigo-200': item.accion === 'CAMBIO_ESTADO',
                                        'bg-blue-50 text-blue-700 border border-blue-200': item.accion === 'EDICION_PROCESO',
                                        'bg-rose-50 text-rose-700 border border-rose-200': item.accion === 'ELIMINACION_PROCESO'
                                      }"
                                      x-text="actionLabel(item.accion)"></span>
                            </div>

                            <!-- Description -->
                            <p class="text-xs text-slate-700 font-medium leading-snug" x-text="item.descripcion"></p>

                            <!-- Process Association Chip -->
                            <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                <a :href="'{{ url('/procesos') }}?search=' + item.proceso_codigo" 
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-green bg-brand-green/5 hover:bg-brand-green/10 px-2 py-0.5 rounded-md border border-brand-green/10 transition-colors">
                                    <i data-lucide="folder" class="w-3 h-3 text-brand-gold"></i>
                                    <span x-text="item.proceso_codigo"></span>
                                </a>

                                <span x-show="item.proceso_cliente" class="text-[11px] text-slate-500 truncate max-w-[200px]" x-text="'Cliente: ' + item.proceso_cliente"></span>
                                
                                <span x-show="item.proceso_materia" class="px-1.5 py-0.2 bg-slate-100 text-slate-600 text-[10px] rounded font-semibold" x-text="item.proceso_materia"></span>

                                <span x-show="item.ip_address" class="text-[10px] text-slate-400 font-mono" x-text="'IP: ' + item.ip_address"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Timestamp -->
                    <div class="text-right shrink-0 self-end sm:self-center pl-13 sm:pl-0">
                        <div class="text-xs font-bold text-slate-700" x-text="item.fecha_hora"></div>
                        <div class="text-[10px] text-slate-400 font-medium" x-text="item.hace_tiempo"></div>
                    </div>

                </div>
            </template>

            <!-- Empty state -->
            <div x-show="filteredAuditorias.length === 0" class="py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <i data-lucide="shield-alert" class="w-7 h-7"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700">No se encontraron eventos registrados</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No hay acciones registradas en la bitácora que coincidan con los filtros seleccionados.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function auditoriaData() {
        return {
            loading: false,
            searchQuery: '',
            selectedUser: 'Todos',
            selectedAccion: 'Todas',
            auditorias: @json($auditorias),
            abogados: @json($abogados),

            init() {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            countAccion(act) {
                return this.auditorias.filter(a => a.accion === act).length;
            },

            actionLabel(acc) {
                switch (acc) {
                    case 'CREACION_PROCESO': return 'Creación de Expediente';
                    case 'NUEVA_ACTUACION': return 'Actuación Procesal';
                    case 'CAMBIO_ESTADO': return 'Cambio de Estado';
                    case 'EDICION_PROCESO': return 'Edición de Expediente';
                    case 'ELIMINACION_PROCESO': return 'Eliminación';
                    default: return acc;
                }
            },

            get filteredAuditorias() {
                return this.auditorias.filter(item => {
                    // Filter User
                    const matchesUser = this.selectedUser === 'Todos' || item.user_id == this.selectedUser;
                    
                    // Filter Action
                    const matchesAccion = this.selectedAccion === 'Todas' || item.accion === this.selectedAccion;

                    // Filter Search
                    let matchesSearch = true;
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        matchesSearch = (item.proceso_codigo && item.proceso_codigo.toLowerCase().includes(q)) ||
                                       (item.descripcion && item.descripcion.toLowerCase().includes(q)) ||
                                       (item.user_nombre && item.user_nombre.toLowerCase().includes(q)) ||
                                       (item.proceso_cliente && item.proceso_cliente.toLowerCase().includes(q));
                    }

                    return matchesUser && matchesAccion && matchesSearch;
                });
            },

            async refreshAuditorias() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route('auditoria.index') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.auditorias = data.auditorias;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            }
        }
    }
</script>
@endsection
