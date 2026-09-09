@extends('layouts.admin')

@section('title', 'Equipo Legal y Operadores - Sillerico & Abogados')
@section('header_title', 'Gestión de Equipo y Usuarios')

@section('content')
<div x-data="usuariosData()" class="space-y-6 animate-fade-in">
    <!-- Breadcrumbs & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                <a href="{{ url('/') }}" class="hover:text-brand-green">Inicio</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-slate-600">Equipo Legal</span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-brand-green mt-1">Operadores y Abogados del Bufete</h2>
            <p class="text-xs text-slate-500">Administración de las 6 cuentas operativas para control de autoría y auditoría de procesos.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="newModalOpen = true" 
                    class="flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-colors">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Nuevo Operador
            </button>
        </div>
    </div>

    <!-- Active User Banner / Fast Switch Notice -->
    <div class="bg-gradient-to-r from-brand-green/90 to-brand-green text-white p-5 rounded-2xl shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center font-black text-sm text-brand-gold shrink-0">
                <span x-text="currentUser ? currentUser.iniciales : 'SB'"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-brand-gold/20 text-brand-gold px-2 py-0.5 rounded-md border border-brand-gold/30">
                        Sesión Actual Activa
                    </span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <h3 class="text-lg font-bold mt-0.5" x-text="currentUser ? currentUser.name : 'Alan Sillerico Segurondo'"></h3>
                <p class="text-xs text-slate-300" x-text="currentUser ? currentUser.cargo : 'Director General'"></p>
            </div>
        </div>
        <div class="text-xs text-slate-200/90 max-w-md bg-white/5 p-3 rounded-xl border border-white/10">
            <div class="font-bold text-brand-gold flex items-center gap-1 mb-1">
                <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                Trazabilidad Garantizada
            </div>
            Todas las creaciones de procesos, registros de diligencias y cambios de estado quedarán sellados a nombre del usuario activo seleccionado.
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Operadores</span>
                <h4 class="text-xl font-bold text-brand-green" x-text="usuarios.length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-green/5 text-brand-green flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Cuerpo Jurídico Activo</span>
                <h4 class="text-xl font-bold text-brand-gold" x-text="usuarios.filter(u => u.es_abogado).length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-gold/10 text-brand-gold flex items-center justify-center">
                <i data-lucide="scale" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Expedientes en Atención</span>
                <h4 class="text-xl font-bold text-emerald-600" x-text="totalProcesos"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Users Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="user in usuarios" :key="user.id">
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
                 :class="{ 'ring-2 ring-brand-green/40 border-brand-green/30': currentUser && currentUser.id === user.id }">
                
                <div>
                    <!-- Card Top -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs"
                                 :class="user.color || 'bg-brand-green text-white'">
                                <span x-text="user.iniciales || 'AB'"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800" x-text="user.name"></h4>
                                <span class="text-[11px] font-semibold text-brand-gold block" x-text="user.cargo"></span>
                            </div>
                        </div>

                        <!-- Active Indicator -->
                        <span x-show="currentUser && currentUser.id === user.id" 
                              class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Activo
                        </span>
                    </div>

                    <!-- Contact details -->
                    <div class="space-y-2 py-3 border-y border-slate-50 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="truncate" x-text="user.email"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span x-text="user.telefono || '+591 77234317'"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] font-semibold text-slate-400">Expedientes asignados:</span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded-md text-[11px]" x-text="(user.procesos_count || 0) + ' procesos'"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Switch Button -->
                <div class="pt-4 mt-2">
                    <button x-show="!currentUser || currentUser.id !== user.id"
                            @click="switchUser(user)"
                            class="w-full py-2 px-3 rounded-xl bg-slate-50 hover:bg-brand-green hover:text-white text-slate-700 text-xs font-bold border border-slate-200 transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                        <i data-lucide="arrow-right-left" class="w-3.5 h-3.5"></i>
                        Iniciar como este operador
                    </button>

                    <div x-show="currentUser && currentUser.id === user.id"
                         class="w-full py-2 px-3 rounded-xl bg-brand-green/10 text-brand-green text-xs font-bold text-center flex items-center justify-center gap-1.5 border border-brand-green/20">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-brand-green"></i>
                        Sesión en curso
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- New User Modal -->
    <div x-show="newModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-100 overflow-hidden"
             @click.away="newModalOpen = false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-green/10 text-brand-green flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Registrar Nuevo Operador</h3>
                        <p class="text-xs text-slate-400">Añada un miembro al equipo legal con acceso y bitácora.</p>
                    </div>
                </div>
                <button @click="newModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="saveUser()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre Completo</label>
                    <input type="text" x-model="newUser.name" required placeholder="Ej: Dr. Roberto Gómez" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-gold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cargo / Especialidad</label>
                        <input type="text" x-model="newUser.cargo" required placeholder="Ej: Especialista Penal" 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono / WhatsApp</label>
                        <input type="text" x-model="newUser.telefono" placeholder="+591 7XXXXXXX" 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" x-model="newUser.email" required placeholder="nombre@sillericoabogados.com" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-brand-gold">
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="newModalOpen = false"
                            class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-brand-green hover:bg-brand-green-hover text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Guardar Operador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function usuariosData() {
        return {
            newModalOpen: false,
            usuarios: @json($usuarios),
            currentUser: @json($currentUser),
            newUser: {
                name: '',
                email: '',
                cargo: '',
                telefono: '',
                color: 'bg-brand-green text-white'
            },

            init() {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },

            get totalProcesos() {
                return this.usuarios.reduce((acc, u) => acc + (u.procesos_count || 0), 0);
            },

            async switchUser(user) {
                try {
                    const res = await fetch(`{{ url('/switch-user') }}/${user.id}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.currentUser = data.user;
                        window.location.reload();
                    }
                } catch (e) {
                    console.error(e);
                    // Fallback to direct navigation
                    window.location.href = `{{ url('/switch-user') }}/${user.id}`;
                }
            },

            async saveUser() {
                try {
                    const res = await fetch('{{ route('usuarios.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newUser)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.usuarios.push(data.user);
                        this.newModalOpen = false;
                        this.newUser = { name: '', email: '', cargo: '', telefono: '', color: 'bg-brand-green text-white' };
                        alert('Operador registrado exitosamente.');
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    } else {
                        alert(data.message || 'Error al guardar.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error en la comunicación con el servidor.');
                }
            }
        }
    }
</script>
@endsection
