@extends('layouts.admin')

@section('title', 'Calendario y Audiencias - Sillerico & Abogados')
@section('header_title', 'Agenda Jurídica y Control de Plazos')

@section('content')
<div x-data="calendarioData()" class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                <a href="{{ url('/') }}" class="hover:text-brand-green">Inicio</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-slate-600">Calendario</span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-brand-green mt-1">Audiencias y Plazos Fatales</h2>
        </div>
        <button @click="newEventModalOpen = true" 
                class="flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-colors">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
            Agendar Audiencia o Plazo
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Programados</span>
                <h4 class="text-xl font-bold text-brand-green" x-text="eventos.length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-green/5 text-brand-green flex items-center justify-center">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Audiencias Judiciales</span>
                <h4 class="text-xl font-bold text-blue-800" x-text="eventos.filter(e => e.tipo_evento === 'Audiencia').length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                <i data-lucide="gavel" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Plazos Fatales</span>
                <h4 class="text-xl font-bold text-rose-600" x-text="eventos.filter(e => e.es_plazo_fatal || e.tipo_evento === 'Plazo Fatal').length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Pendientes de Realización</span>
                <h4 class="text-xl font-bold text-brand-gold" x-text="eventos.filter(e => e.estado === 'Pendiente').length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-gold/10 text-brand-gold flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold">
            <button @click="selectedTipo = 'Todos'" 
                    class="px-3.5 py-1.5 rounded-lg transition-colors"
                    :class="selectedTipo === 'Todos' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'">
                Todos
            </button>
            <button @click="selectedTipo = 'Audiencia'" 
                    class="px-3.5 py-1.5 rounded-lg transition-colors"
                    :class="selectedTipo === 'Audiencia' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'">
                Audiencias
            </button>
            <button @click="selectedTipo = 'Plazo Fatal'" 
                    class="px-3.5 py-1.5 rounded-lg transition-colors"
                    :class="selectedTipo === 'Plazo Fatal' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'">
                Plazos Fatales
            </button>
            <button @click="selectedTipo = 'Diligencia'" 
                    class="px-3.5 py-1.5 rounded-lg transition-colors"
                    :class="selectedTipo === 'Diligencia' ? 'bg-brand-green text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'">
                Diligencias
            </button>
        </div>

        <div class="flex items-center gap-2">
            <select x-model="selectedEstado" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-600 font-medium">
                <option value="Todos">Todos los Estados</option>
                <option value="Pendiente">Solo Pendientes</option>
                <option value="Realizado">Realizados</option>
            </select>
        </div>
    </div>

    <!-- Events List -->
    <div class="space-y-3">
        <template x-for="e in filteredEventos" :key="e.id">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs hover:border-brand-gold/30 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <!-- Date Box -->
                    <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center shrink-0 border"
                         :class="{
                             'bg-rose-50 border-rose-200 text-rose-700': e.tipo_evento === 'Plazo Fatal' || e.es_plazo_fatal,
                             'bg-blue-50 border-blue-200 text-blue-800': e.tipo_evento === 'Audiencia',
                             'bg-amber-50 border-amber-200 text-amber-800': e.tipo_evento !== 'Plazo Fatal' && e.tipo_evento !== 'Audiencia'
                         }">
                        <span class="text-xs font-bold uppercase tracking-wider" x-text="formatDateShort(e.fecha_hora_inicio)"></span>
                        <span class="text-lg font-black" x-text="formatDay(e.fecha_hora_inicio)"></span>
                    </div>

                    <!-- Event Info -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase"
                                  :class="{
                                      'bg-rose-100 text-rose-800': e.tipo_evento === 'Plazo Fatal' || e.es_plazo_fatal,
                                      'bg-blue-100 text-blue-800': e.tipo_evento === 'Audiencia',
                                      'bg-amber-100 text-amber-800': e.tipo_evento !== 'Plazo Fatal' && e.tipo_evento !== 'Audiencia'
                                  }" x-text="e.tipo_evento"></span>
                            
                            <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <span x-text="formatTime(e.fecha_hora_inicio)"></span>
                            </span>

                            <span class="px-2 py-0.5 rounded text-[9px] font-bold"
                                  :class="e.estado === 'Realizado' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'"
                                  x-text="e.estado"></span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900" x-text="e.titulo"></h4>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-0.5">
                            <template x-if="e.proceso">
                                <div class="flex items-center gap-1 font-medium text-brand-green">
                                    <i data-lucide="folder" class="w-3.5 h-3.5"></i>
                                    <a :href="`{{ url('/procesos') }}?search=${e.proceso.codigo_interno}`" class="hover:underline" x-text="`${e.proceso.codigo_interno}: ${e.proceso.demandante_denunciante}`"></a>
                                </div>
                            </template>
                            <template x-if="e.lugar_enlace">
                                <div class="flex items-center gap-1 text-slate-400">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span x-text="e.lugar_enlace"></span>
                                </div>
                            </template>
                            <template x-if="e.user">
                                <div class="flex items-center gap-1 text-slate-500">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span x-text="e.user.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                    <button @click="toggleEstado(e)" 
                            class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors flex items-center gap-1.5"
                            :class="e.estado === 'Realizado' ? 'border-emerald-200 text-emerald-700 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span x-text="e.estado === 'Realizado' ? 'Completado' : 'Marcar Realizado'"></span>
                    </button>
                    <button @click="deleteEvento(e)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Eliminar">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </template>

        <div x-show="filteredEventos.length === 0" class="bg-white p-12 rounded-2xl border border-slate-100 text-center text-slate-400">
            <i data-lucide="calendar-check" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
            No se encontraron actividades con los filtros actuales.
        </div>
    </div>

    <!-- MODAL AGENDAR EVENTO -->
    <div class="fixed inset-0 z-50 overflow-y-auto" x-show="newEventModalOpen" style="display: none;" x-cloak>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" @click="newEventModalOpen = false"></div>

            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand-green uppercase tracking-wider">Agendar Audiencia o Plazo Fatal</h3>
                    <button @click="newEventModalOpen = false" class="p-1 rounded-lg hover:bg-slate-200/50 text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="saveEvento" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Título de la Actuación / Audiencia</label>
                        <input type="text" x-model="newEvent.titulo" required placeholder="e.g. Audiencia de Medidas Cautelares" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Tipo</label>
                            <select x-model="newEvent.tipo_evento" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                                <option value="Audiencia">Audiencia Judicial</option>
                                <option value="Plazo Fatal">Plazo Fatal / Término</option>
                                <option value="Diligencia">Diligencia / Notificación</option>
                                <option value="Reunion">Reunión con Cliente</option>
                                <option value="Inspeccion">Inspección Ocular</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Prioridad</label>
                            <select x-model="newEvent.prioridad" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                                <option value="Alta">Alta (Urgente)</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Proceso Vinculado</label>
                        <select x-model="newEvent.proceso_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                            <option value="">-- Sin proceso vinculado --</option>
                            <template x-for="p in procesos" :key="p.id">
                                <option :value="p.id" x-text="`${p.codigo_interno} - ${p.demandante_denunciante} (${p.delito_accion})`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Fecha y Hora Inicio</label>
                            <input type="datetime-local" x-model="newEvent.fecha_hora_inicio" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Abogado Asignado</label>
                            <select x-model="newEvent.user_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                                <template x-for="abg in abogados" :key="abg.id">
                                    <option :value="abg.id" x-text="abg.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Lugar o Juzgado</label>
                        <input type="text" x-model="newEvent.lugar_enlace" placeholder="e.g. Tribunal Departamental de Justicia, Salón 3" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Observaciones o Notas</label>
                        <textarea x-model="newEvent.observaciones" rows="2" placeholder="Requisitos para la audiencia..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="newEventModalOpen = false" class="flex-1 px-4 py-2.5 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-brand-green hover:bg-brand-green-hover text-white rounded-xl text-xs font-bold shadow-md">
                            Guardar en Calendario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function calendarioData() {
        return {
            selectedTipo: 'Todos',
            selectedEstado: 'Todos',
            newEventModalOpen: false,
            eventos: @json($eventos),
            procesos: @json($procesos),
            abogados: @json($abogados),
            newEvent: {
                titulo: '',
                tipo_evento: 'Audiencia',
                prioridad: 'Media',
                proceso_id: '',
                user_id: @json($abogados->first()->id ?? null),
                fecha_hora_inicio: '',
                lugar_enlace: '',
                observaciones: ''
            },

            get filteredEventos() {
                return this.eventos.filter(e => {
                    const matchesTipo = this.selectedTipo === 'Todos' || e.tipo_evento === this.selectedTipo;
                    const matchesEstado = this.selectedEstado === 'Todos' || e.estado === this.selectedEstado;
                    return matchesTipo && matchesEstado;
                });
            },

            formatDateShort(d) {
                if (!d) return '';
                const date = new Date(d);
                return date.toLocaleDateString('es-ES', { month: 'short' });
            },

            formatDay(d) {
                if (!d) return '';
                const date = new Date(d);
                return date.getDate();
            },

            formatTime(d) {
                if (!d) return '';
                const date = new Date(d);
                return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            },

            async toggleEstado(evento) {
                const nuevoEstado = evento.estado === 'Realizado' ? 'Pendiente' : 'Realizado';
                try {
                    const res = await fetch(`{{ url('/api/calendario/eventos') }}/${evento.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ estado: nuevoEstado })
                    });
                    const data = await res.json();
                    if (data.success) {
                        evento.estado = nuevoEstado;
                    }
                } catch (err) {
                    console.error(err);
                }
            },

            async deleteEvento(evento) {
                if (!confirm('¿Desea eliminar este evento de la agenda?')) return;
                try {
                    const res = await fetch(`{{ url('/api/calendario/eventos') }}/${evento.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.eventos = this.eventos.filter(e => e.id !== evento.id);
                    }
                } catch (err) {
                    console.error(err);
                }
            },

            async saveEvento() {
                try {
                    const res = await fetch('{{ route('calendario.api.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newEvent)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.eventos.unshift(data.evento);
                        this.newEventModalOpen = false;
                        this.newEvent = {
                            titulo: '',
                            tipo_evento: 'Audiencia',
                            prioridad: 'Media',
                            proceso_id: '',
                            user_id: @json($abogados->first()->id ?? null),
                            fecha_hora_inicio: '',
                            lugar_enlace: '',
                            observaciones: ''
                        };
                        alert('Audiencia / Evento agendado con éxito.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error al guardar el evento.');
                }
            }
        }
    }
</script>
@endsection