@extends('layouts.admin')

@section('title', 'Clientes - Sillerico & Abogados')
@section('header_title', 'Gestión de Clientes y Patrocinados')

@section('content')
<div x-data="clientesData()" class="space-y-6 animate-fade-in">
    <!-- Header & New Client Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-brand-green">Directorio de Clientes</h2>
        </div>
        <button @click="newClientModalOpen = true" 
                class="flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold rounded-xl shadow-md shadow-brand-green/10 transition-colors">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Registrar Nuevo Cliente
        </button>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Clientes</span>
                <h4 class="text-xl font-bold text-brand-green" x-text="clientes.length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-green/5 text-brand-green flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Empresas / Instituciones</span>
                <h4 class="text-xl font-bold text-brand-gold" x-text="clientes.filter(c => c.tipo_cliente === 'JURIDICO').length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-brand-gold/10 text-brand-gold flex items-center justify-center">
                <i data-lucide="building-2" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Personas Naturales</span>
                <h4 class="text-xl font-bold text-emerald-600" x-text="clientes.filter(c => c.tipo_cliente === 'NATURAL').length"></h4>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Table and Filter Area -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Registro Completo de Clientes y Empresas</h3>
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Buscar por nombre, CI/NIT o contacto..." 
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold text-slate-600">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
                        <th class="py-3.5 px-4">Cliente / Razón Social</th>
                        <th class="py-3.5 px-4">Tipo</th>
                        <th class="py-3.5 px-4">Documento (CI/NIT)</th>
                        <th class="py-3.5 px-4">Teléfono / WhatsApp</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Causas Vinculadas</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    <template x-for="c in filteredClientes" :key="c.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-4 font-bold text-slate-800 uppercase">
                                <div x-text="c.nombre_razon_social"></div>
                                <div class="text-[10px] text-slate-400 font-normal" x-show="c.persona_contacto" x-text="`Contacto: ${c.persona_contacto}`"></div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase"
                                      :class="c.tipo_cliente === 'JURIDICO' ? 'bg-amber-50 text-brand-gold border border-brand-gold/30' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'"
                                      x-text="c.tipo_cliente === 'JURIDICO' ? 'Empresa' : 'Persona'"></span>
                            </td>
                            <td class="py-4 px-4 font-mono text-[11px]" x-text="c.documento_identidad || 'Sin registro'"></td>
                            <td class="py-4 px-4">
                                <template x-if="c.celular_whatsapp">
                                    <a :href="`https://wa.me/${c.celular_whatsapp}`" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-medium transition-colors text-[11px]">
                                        <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                                        <span x-text="c.celular_whatsapp"></span>
                                    </a>
                                </template>
                                <template x-if="!c.celular_whatsapp">
                                    <span class="text-slate-400">N/D</span>
                                </template>
                            </td>
                            <td class="py-4 px-4 text-slate-500 font-sans text-[11px]" x-text="c.email || 'Sin correo'"></td>
                            <td class="py-4 px-4 text-center font-bold">
                                <span class="px-2 py-0.5 rounded-full bg-brand-green/10 text-brand-green text-xs" x-text="c.procesos_count || 1"></span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a :href="`{{ url('/procesos') }}?search=${encodeURIComponent(c.nombre_razon_social)}`" 
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-100 hover:bg-brand-green hover:text-white text-slate-600 text-[10px] font-semibold transition-colors"
                                   title="Ver Expedientes de este cliente">
                                    <i data-lucide="folder" class="w-3.5 h-3.5"></i>
                                    <span>Ver Casos</span>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between text-xs text-slate-400">
            <span x-text="`Mostrando ${filteredClientes.length} de ${clientes.length} clientes`"></span>
        </div>
    </div>

    <!-- MODAL NUEVO CLIENTE -->
    <div class="fixed inset-0 z-50 overflow-y-auto" x-show="newClientModalOpen" style="display: none;" x-cloak>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" @click="newClientModalOpen = false"></div>

            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand-green uppercase tracking-wider">Nuevo Cliente / Patrocinado</h3>
                    <button @click="newClientModalOpen = false" class="p-1 rounded-lg hover:bg-slate-200/50 text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="saveCliente" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Tipo</label>
                            <select x-model="newClient.tipo_cliente" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                                <option value="NATURAL">Persona Natural</option>
                                <option value="JURIDICO">Empresa / Institución</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">CI / NIT</label>
                            <input type="text" x-model="newClient.documento_identidad" placeholder="e.g. 4839201 LP" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Nombre Completo o Razón Social</label>
                        <input type="text" x-model="newClient.nombre_razon_social" required placeholder="Nombre del cliente o empresa" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Celular / WhatsApp</label>
                            <input type="text" x-model="newClient.celular_whatsapp" placeholder="e.g. 77234317" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Correo Electrónico</label>
                            <input type="email" x-model="newClient.email" placeholder="cliente@correo.com" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Persona de Contacto o Representante</label>
                        <input type="text" x-model="newClient.persona_contacto" placeholder="Nombre de contacto interno" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="newClientModalOpen = false" class="flex-1 px-4 py-2.5 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-brand-green hover:bg-brand-green-hover text-white rounded-xl text-xs font-bold shadow-md">
                            Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function clientesData() {
        return {
            searchQuery: '',
            newClientModalOpen: false,
            clientes: @json($clientes),
            newClient: {
                tipo_cliente: 'NATURAL',
                nombre_razon_social: '',
                documento_identidad: '',
                celular_whatsapp: '',
                email: '',
                persona_contacto: ''
            },

            get filteredClientes() {
                if (!this.searchQuery) return this.clientes;
                const q = this.searchQuery.toLowerCase();
                return this.clientes.filter(c => 
                    (c.nombre_razon_social && c.nombre_razon_social.toLowerCase().includes(q)) ||
                    (c.documento_identidad && c.documento_identidad.toLowerCase().includes(q)) ||
                    (c.persona_contacto && c.persona_contacto.toLowerCase().includes(q)) ||
                    (c.email && c.email.toLowerCase().includes(q))
                );
            },

            async saveCliente() {
                try {
                    const response = await fetch('{{ route('clientes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newClient)
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.clientes.unshift(data.cliente);
                        this.newClientModalOpen = false;
                        this.newClient = {
                            tipo_cliente: 'NATURAL',
                            nombre_razon_social: '',
                            documento_identidad: '',
                            celular_whatsapp: '',
                            email: '',
                            persona_contacto: ''
                        };
                        alert('Cliente registrado con éxito.');
                    } else {
                        alert('Error al guardar cliente.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión.');
                }
            }
        }
    }
</script>
@endsection