<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sillerico & Abogados - Sistema')</title>
    <!-- Tailwind v4 via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN for interactive prototype -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Custom scrollbar for premium feel */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800" 
      x-data="{ 
          sidebarOpen: false, 
          desktopSidebarCollapsed: false,
          menus: {
              procesos: {{ Request::is('procesos*', 'audiencias*', 'clientes*', 'documentos*', 'parametros*') ? 'true' : 'true' }},
              herramientas: {{ Request::is('articulos*', 'calendario*', 'auditoria*') ? 'true' : 'false' }},
              administracion: {{ Request::is('usuarios*', 'roles*') ? 'true' : 'false' }}
          },
          toggle(group) {
              if (this.desktopSidebarCollapsed) {
                  this.desktopSidebarCollapsed = false;
                  this.menus[group] = true;
              } else {
                  this.menus[group] = !this.menus[group];
              }
          }
      }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" 
         @click="sidebarOpen = false"
         style="display: none;"></div>

    <!-- SIDEBAR -->
    <div :class="{
            'w-64': !desktopSidebarCollapsed,
            'w-20': desktopSidebarCollapsed,
            'translate-x-0': sidebarOpen,
            '-translate-x-full': !sidebarOpen
         }"
         class="fixed inset-y-0 left-0 z-50 flex flex-col bg-brand-green border-r border-brand-gold/15 transition-all duration-300 ease-in-out lg:translate-x-0 shadow-lg shadow-brand-green/10"
         x-cloak>
        
        <!-- Sidebar Brand Header -->
        <div class="flex items-center justify-between h-16 px-5 border-b border-brand-gold/15 shrink-0">
            <a href="{{ url('/') }}" class="flex items-center gap-3 overflow-hidden group">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white/10 border border-brand-gold/30 shrink-0">
                    <img src="{{ asset('images/logo-splash.png') }}" alt="Sillerico & Abogados" class="w-8 h-8 object-contain">
                </div>
                <div class="flex flex-col transition-opacity duration-300" 
                     :class="{ 'opacity-0 w-0': desktopSidebarCollapsed, 'opacity-100': !desktopSidebarCollapsed }">
                    <span class="text-xs font-semibold tracking-[0.2em] text-white uppercase leading-tight font-sans">Sillerico</span>
                    <span class="text-[9px] font-bold tracking-[0.15em] text-brand-gold uppercase leading-tight">&amp; Abogados</span>
                </div>
            </a>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="p-1 rounded-lg lg:hidden hover:bg-white/10 text-slate-300 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Sidebar Navigation (Multinivel) -->
        <div class="flex-1 overflow-y-auto py-5 px-3 space-y-3">
            
            <!-- 1. DASHBOARD -->
            <div>
                <a href="{{ url('/') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ Request::is('/') ? 'bg-brand-gold/15 text-brand-gold font-semibold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 transition-colors {{ Request::is('/') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                    <span class="transition-opacity duration-300" :class="{ 'opacity-0 w-0 hidden': desktopSidebarCollapsed }">Dashboard</span>
                </a>
            </div>

            <!-- 2. CONTROL DE PROCESOS (Multinivel) -->
            <div class="space-y-1">
                <!-- Header / Toggle -->
                <button type="button" 
                        @click="toggle('procesos')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all group {{ Request::is('procesos*', 'audiencias*', 'clientes*', 'documentos*', 'parametros*') ? 'text-brand-gold bg-white/5' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <i data-lucide="folder-kanban" class="w-4 h-4 shrink-0 {{ Request::is('procesos*', 'audiencias*', 'clientes*', 'documentos*', 'parametros*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                        <span class="truncate transition-opacity duration-300 text-xs font-semibold uppercase tracking-wider" 
                              :class="{ 'opacity-0 w-0 hidden': desktopSidebarCollapsed }">
                            Control de Procesos
                        </span>
                    </div>
                    <i data-lucide="chevron-down" 
                       class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                       :class="{ 'rotate-180': menus.procesos, 'opacity-0 w-0 hidden': desktopSidebarCollapsed }"></i>
                </button>

                <!-- Submenu Items -->
                <div x-show="menus.procesos" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="ml-4 pl-3 border-l-2 border-brand-gold/20 space-y-1 pt-1 pb-1"
                     :class="{ 'hidden': desktopSidebarCollapsed }">
                    
                    <!-- Procesos -->
                    <a href="{{ url('/procesos') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('procesos*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="file-text" class="w-4 h-4 shrink-0 {{ Request::is('procesos*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Procesos</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-gold/15 text-brand-gold">
                            {{ \App\Models\Proceso::count() }}
                        </span>
                    </a>

                    <!-- Audiencias -->
                    <a href="{{ url('/audiencias') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('audiencias*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="gavel" class="w-4 h-4 shrink-0 {{ Request::is('audiencias*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Audiencias</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-gold/10 text-brand-gold">
                            {{ \App\Models\EventoCalendario::where('tipo_evento', 'like', '%Audiencia%')->count() }}
                        </span>
                    </a>

                    <!-- Clientes -->
                    <a href="{{ url('/clientes') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('clientes*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="users" class="w-4 h-4 shrink-0 {{ Request::is('clientes*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Clientes</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-slate-300">
                            {{ \App\Models\Cliente::count() }}
                        </span>
                    </a>

                    <!-- Documentos -->
                    <a href="{{ url('/documentos') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('documentos*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="files" class="w-4 h-4 shrink-0 {{ Request::is('documentos*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Documentos</span>
                        </div>
                    </a>

                    <!-- Parametros -->
                    <a href="{{ url('/parametros') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('parametros*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4 shrink-0 {{ Request::is('parametros*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Parámetros</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- 3. HERRAMIENTAS (Multinivel) -->
            <div class="space-y-1">
                <!-- Header / Toggle -->
                <button type="button" 
                        @click="toggle('herramientas')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all group {{ Request::is('articulos*', 'calendario*', 'auditoria*') ? 'text-brand-gold bg-white/5' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <i data-lucide="wrench" class="w-4 h-4 shrink-0 {{ Request::is('articulos*', 'calendario*', 'auditoria*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                        <span class="truncate transition-opacity duration-300 text-xs font-semibold uppercase tracking-wider" 
                              :class="{ 'opacity-0 w-0 hidden': desktopSidebarCollapsed }">
                            Herramientas
                        </span>
                    </div>
                    <i data-lucide="chevron-down" 
                       class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                       :class="{ 'rotate-180': menus.herramientas, 'opacity-0 w-0 hidden': desktopSidebarCollapsed }"></i>
                </button>

                <!-- Submenu Items -->
                <div x-show="menus.herramientas" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="ml-4 pl-3 border-l-2 border-brand-gold/20 space-y-1 pt-1 pb-1"
                     :class="{ 'hidden': desktopSidebarCollapsed }">
                    
                    <!-- Articulos y Leyes -->
                    <a href="{{ url('/articulos') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('articulos*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="book-open" class="w-4 h-4 shrink-0 {{ Request::is('articulos*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Artículos y Leyes</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-slate-300">
                            {{ \App\Models\ArticuloLey::count() }}
                        </span>
                    </a>

                    <!-- Calendarios -->
                    <a href="{{ url('/calendario') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('calendario*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="calendar" class="w-4 h-4 shrink-0 {{ Request::is('calendario*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Calendarios</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-brand-gold"></span>
                    </a>

                    <!-- Trasabilidad -->
                    <a href="{{ url('/auditoria') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('auditoria*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="history" class="w-4 h-4 shrink-0 {{ Request::is('auditoria*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Trasabilidad</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- 4. ADMINISTRACION (Multinivel) -->
            <div class="space-y-1">
                <!-- Header / Toggle -->
                <button type="button" 
                        @click="toggle('administracion')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all group {{ Request::is('usuarios*', 'roles*') ? 'text-brand-gold bg-white/5' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0 {{ Request::is('usuarios*', 'roles*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                        <span class="truncate transition-opacity duration-300 text-xs font-semibold uppercase tracking-wider" 
                              :class="{ 'opacity-0 w-0 hidden': desktopSidebarCollapsed }">
                            Administración
                        </span>
                    </div>
                    <i data-lucide="chevron-down" 
                       class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                       :class="{ 'rotate-180': menus.administracion, 'opacity-0 w-0 hidden': desktopSidebarCollapsed }"></i>
                </button>

                <!-- Submenu Items -->
                <div x-show="menus.administracion" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="ml-4 pl-3 border-l-2 border-brand-gold/20 space-y-1 pt-1 pb-1"
                     :class="{ 'hidden': desktopSidebarCollapsed }">
                    
                    <!-- Equipo Legal -->
                    <a href="{{ url('/usuarios') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('usuarios*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="users-round" class="w-4 h-4 shrink-0 {{ Request::is('usuarios*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Equipo Legal</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-slate-300">
                            {{ \App\Models\User::where('es_abogado', true)->count() }}
                        </span>
                    </a>

                    <!-- Roles -->
                    <a href="{{ url('/roles') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ Request::is('roles*') ? 'bg-brand-gold/15 text-brand-gold font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i data-lucide="key-round" class="w-4 h-4 shrink-0 {{ Request::is('roles*') ? 'text-brand-gold' : 'text-slate-400 group-hover:text-white' }}"></i>
                            <span class="truncate">Roles</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-gold/15 text-brand-gold">
                            {{ \App\Models\Rol::count() }}
                        </span>
                    </a>
                </div>
            </div>

        </div>

        @php
            $currentActiveUser = \App\Services\AuditService::getCurrentUser();
            $allTeamUsers = \App\Models\User::where('es_abogado', true)->get();
        @endphp

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-brand-gold/10 bg-brand-green-hover/20 shrink-0">
            <div class="flex items-center gap-3" :class="{ 'justify-center': desktopSidebarCollapsed }">
                <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm shadow-sm shrink-0 {{ $currentActiveUser ? $currentActiveUser->color : 'bg-brand-gold text-brand-green' }}">
                    {{ $currentActiveUser ? $currentActiveUser->iniciales : 'AS' }}
                </div>
                <div class="flex flex-col min-w-0 transition-opacity duration-300"
                     :class="{ 'opacity-0 w-0 hidden': desktopSidebarCollapsed }">
                    <span class="text-xs font-semibold text-white truncate">{{ $currentActiveUser ? $currentActiveUser->name : 'Alan Sillerico' }}</span>
                    <span class="text-[10px] text-brand-gold truncate">{{ $currentActiveUser ? $currentActiveUser->cargo : 'Director General' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN APP CONTAINER -->
    <div class="flex flex-col min-h-full transition-all duration-300"
         :class="{
            'lg:pl-64': !desktopSidebarCollapsed,
            'lg:pl-20': desktopSidebarCollapsed
         }">
        
        <!-- TOPBAR -->
        <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white/95 backdrop-blur-md border-b-2 border-brand-gold/10 shrink-0 shadow-sm shadow-slate-100/5">
            <!-- Left Header Section -->
            <div class="flex items-center gap-4">
                <!-- Hamburger Mobile Toggle -->
                <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-lg lg:hidden hover:bg-slate-50 text-slate-400 hover:text-slate-600">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <!-- Desktop Sidebar Toggle -->
                <button @click="desktopSidebarCollapsed = !desktopSidebarCollapsed" class="hidden p-2 rounded-lg lg:flex hover:bg-slate-50 text-slate-400 hover:text-slate-600">
                    <i data-lucide="align-justify" class="w-5 h-5"></i>
                </button>
                <!-- Toolbar Title -->
                <h1 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">
                    @yield('header_title', 'Panel de Administración')
                </h1>
            </div>

            <!-- Right Header Section (Search, Notifications, Quick Actions) -->
            <div class="flex items-center gap-3">
                
                <!-- Search Mockup -->
                <div class="relative hidden sm:block w-64 md:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" 
                           placeholder="Buscar procesos, clientes, juzgados..." 
                           class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-all">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <kbd class="text-[9px] font-bold text-slate-400 border border-slate-200 bg-white px-1.5 py-0.5 rounded shadow-2xs">Ctrl K</kbd>
                    </div>
                </div>

                <!-- Notifications Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="p-2 rounded-xl hover:bg-slate-50 text-slate-400 hover:text-slate-600 relative transition-all">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                    </button>
                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-800">Notificaciones</span>
                            <span class="text-[10px] text-brand-gold font-semibold cursor-pointer hover:underline">Marcar leídas</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto text-xs divide-y divide-slate-50">
                            <!-- Notification Item -->
                            <a href="#" class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-slate-600"><span class="font-semibold text-slate-800">Audiencia Urgente</span> programada para el caso LPZ091264 mañana a las 09:30.</p>
                                    <span class="text-[9px] text-slate-400">Hace 10 min</span>
                                </div>
                            </a>
                            <!-- Notification Item -->
                            <a href="#" class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-slate-600">Nuevo documento adjuntado al proceso <span class="font-semibold text-slate-800">LPZ1903613</span>.</p>
                                    <span class="text-[9px] text-slate-400">Hace 2 horas</span>
                                </div>
                            </a>
                            <!-- Notification Item -->
                            <a href="#" class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-slate-600">El proceso <span class="font-semibold text-slate-800">CUD: 21010106211015</span> ha sido marcado como Concluido.</p>
                                    <span class="text-[9px] text-slate-400">Ayer</span>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-1.5 text-center border-t border-slate-100">
                            <a href="#" class="text-[10px] font-semibold text-brand-green hover:text-brand-green-hover">Ver todas las notificaciones</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Button -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-green text-white hover:bg-brand-green-hover text-xs font-semibold shadow-md shadow-brand-green/10 transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Acción</span>
                    </button>
                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2 divide-y divide-slate-100"
                         style="display: none;">
                        <div class="py-1">
                            <!-- Trigger event for opening parent components modal -->
                            <button @click="open = false; $dispatch('open-new-case-modal')" 
                                    class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors">
                                <i data-lucide="folder-plus" class="w-4 h-4 text-slate-400"></i>
                                Nuevo Proceso
                            </button>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors">
                                <i data-lucide="user-plus" class="w-4 h-4 text-slate-400"></i>
                                Registrar Cliente
                            </a>
                        </div>
                        <div class="py-1">
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors">
                                <i data-lucide="calendar-plus" class="w-4 h-4 text-slate-400"></i>
                <!-- User Switcher Dropdown (Fast Team Switching for Audit Trail) -->
                <div x-data="{ userMenuOpen: false }" class="relative ml-2 pl-3 border-l border-slate-200">
                    <button @click="userMenuOpen = !userMenuOpen" 
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-50 transition-colors text-left group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $currentActiveUser ? $currentActiveUser->color : 'bg-brand-green text-brand-gold' }}">
                            {{ $currentActiveUser ? $currentActiveUser->iniciales : 'AS' }}
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-xs font-bold text-slate-800 leading-tight group-hover:text-brand-green transition-colors">{{ $currentActiveUser ? $currentActiveUser->name : 'Alan Sillerico' }}</span>
                            <span class="text-[9px] text-brand-gold font-bold">{{ $currentActiveUser ? $currentActiveUser->cargo : 'Director General' }}</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                    </button>

                    <div x-show="userMenuOpen" 
                         @click.outside="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2 divide-y divide-slate-100"
                         style="display: none;">
                        <div class="px-4 py-2.5 bg-slate-50/50">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Operador en Sesión</span>
                            <span class="text-xs font-bold text-brand-green">{{ $currentActiveUser ? $currentActiveUser->name : 'Alan Sillerico' }}</span>
                            <p class="text-[10px] text-slate-500 mt-0.5">Toda acción sobre los expedientes se firmará con este usuario.</p>
                        </div>
                        
                        <div class="py-1 max-h-64 overflow-y-auto">
                            <span class="px-4 py-1 text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Cambiar a otro Abogado</span>
                            @foreach($allTeamUsers as $member)
                                <a href="{{ url('/switch-user/' . $member->id) }}" 
                                   class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 transition-colors {{ $currentActiveUser && $currentActiveUser->id === $member->id ? 'bg-brand-gold/10 font-bold' : '' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 {{ $member->color }}">
                                        {{ $member->iniciales }}
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1">
                                        <span class="text-xs text-slate-800 truncate">{{ $member->name }}</span>
                                        <span class="text-[9px] text-slate-400 truncate">{{ $member->cargo }}</span>
                                    </div>
                                    @if($currentActiveUser && $currentActiveUser->id === $member->id)
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-gold shrink-0"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="px-4 py-2 bg-slate-50/60 flex justify-between items-center text-[10px] font-bold">
                            <a href="{{ url('/usuarios') }}" class="text-brand-green hover:underline flex items-center gap-1">
                                <i data-lucide="users" class="w-3 h-3"></i>
                                Gestión Equipo
                            </a>
                            <a href="{{ url('/auditoria') }}" class="text-brand-gold hover:underline flex items-center gap-1">
                                <i data-lucide="history" class="w-3 h-3"></i>
                                Ver Trazabilidad
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- MAIN BODY CONTENT -->
        <main class="flex-1 p-[10px]">
            @yield('content')
        </main>
        
        <!-- FOOTER -->
        <footer class="py-4 px-8 border-t border-slate-200/50 bg-white text-center text-[11px] text-slate-400 flex flex-col sm:flex-row justify-between gap-2">
            <span>&copy; {{ date('Y') }} Sillerico &amp; Abogados - Todos los derechos reservados.</span>
            <span>Sistema Administrativo de Control de Procesos (v1.0 Mockup)</span>
        </footer>

    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        
        // Custom event listeners for styling or transitions if needed
        window.addEventListener('alpine:initialized', () => {
            // Re-create icons when elements update dynamically
            Alpine.effect(() => {
                lucide.createIcons();
            });
        });
    </script>
</body>
</html>
