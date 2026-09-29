<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión - Sillerico &amp; Abogados</title>

    <!-- Tailwind v4 via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Dark Emerald Luxury Background with Warm Gold Ambient Lighting */
        .login-bg-container {
            background-color: #082a20;
            background-image: 
                linear-gradient(135deg, rgba(8, 42, 32, 0.88) 0%, rgba(4, 22, 17, 0.82) 40%, rgba(8, 42, 32, 0.92) 100%),
                url("{{ asset('images/login-bg.jpg') }}");
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }

        /* Pure Crisp White Card with Subtle Luxury Elevation */
        .fluent-card {
            background-color: #ffffff;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(197, 160, 89, 0.25);
        }
        
        .fluent-input {
            border: 1px solid #d1d5db;
            transition: all 0.2s ease-in-out;
        }
        .fluent-input:focus {
            border-color: #082a20;
            box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.25);
            outline: none;
        }
    </style>
</head>
<body class="h-full login-bg-container text-slate-800 font-sans antialiased flex flex-col justify-between min-h-screen">

    <!-- Top Spacing -->
    <div class="h-4 sm:h-8"></div>

    <!-- Main Container -->
    <main class="w-full flex-1 flex flex-col items-center justify-center px-4 py-6"
          x-data="microsoftAuth()">

        <!-- Centered Card -->
        <div class="fluent-card w-full max-w-[420px] rounded-3xl p-8 sm:p-10 transition-all duration-300">
            
            <!-- Error Banner (Estilo Microsoft) -->
            <template x-if="errorMessage">
                <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-start gap-2.5 animate-fadeIn">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                    <div class="flex-1 leading-relaxed text-left" x-text="errorMessage"></div>
                    <button type="button" @click="errorMessage = ''" class="text-rose-400 hover:text-rose-600 cursor-pointer">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </template>

            <!-- Status Banner (Ej. Logout Notificación) -->
            @if(session('status'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span class="flex-1 leading-relaxed text-left">{{ session('status') }}</span>
                </div>
            @endif

            <!-- ========================================================= -->
            <!-- 1ER PASO: IDENTIFICADOR (ALINEADO AL CENTRO)              -->
            <!-- ========================================================= -->
            <div x-show="step === 1" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col text-center">
                
                <!-- 1. Logo de la firma alineado al centro -->
                <div class="flex justify-center mb-5">
                    <img src="{{ asset('images/logo-splash.png') }}" 
                         alt="Sillerico &amp; Abogados" 
                         class="h-36 sm:h-44 w-auto object-contain drop-shadow-md">
                </div>

                <!-- 2. Label Iniciar sesión -->
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight mb-6">Iniciar sesión</h1>

                <!-- Formulario Paso 1 -->
                <form @submit.prevent="submitStep1()" class="space-y-5">
                    
                    <!-- 4. Label del campo usuario junto a su caja de texto -->
                    <div class="text-left">
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Usuario
                        </label>
                        <input id="login" 
                               name="login" 
                               type="text" 
                               x-ref="loginField"
                               x-model="loginInput" 
                               placeholder="Ingrese su usuario o correo" 
                               autocomplete="username" 
                               required
                               class="fluent-input w-full px-3.5 py-2.5 text-sm rounded-xl text-slate-900 placeholder:text-slate-400">
                    </div>

                    <!-- 5. Boton Siguiente alineado al centro tomando todo el ancho del cuadro -->
                    <div class="pt-2">
                        <button type="submit" 
                                :disabled="loading || !loginInput.trim()"
                                class="w-full bg-brand-green hover:bg-brand-green-hover text-[#F7E8A7] font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="animate-spin h-4 w-4 text-[#F7E8A7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Verificando...' : 'Siguiente'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ========================================================= -->
            <!-- 2DO PASO: INGRESE PASSWORD                                -->
            <!-- ========================================================= -->
            <div x-show="step === 2" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col text-center"
                 style="display: none;">
                
                <!-- 1. Logo de la firma alineado al centro -->
                <div class="flex justify-center mb-5">
                    <img src="{{ asset('images/logo-splash.png') }}" 
                         alt="Sillerico &amp; Abogados" 
                         class="h-36 sm:h-44 w-auto object-contain drop-shadow-md">
                </div>

                <!-- 2. Label Ingrese password -->
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight mb-3">Ingrese password</h1>

                <!-- 3. Boton con flecha atras para volver a ingresar el username.. a su lado el nombre del username colocado -->
                <div class="flex justify-center mb-5">
                    <button type="button" 
                            @click="backToStep1()" 
                            title="Haz clic para volver a ingresar el usuario"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs text-slate-700 transition-colors group cursor-pointer max-w-full">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-slate-500 group-hover:text-brand-green transition-transform group-hover:-translate-x-0.5"></i>
                        <span class="font-semibold truncate max-w-[220px]" x-text="userAccount.email || loginInput"></span>
                    </button>
                </div>

                <form @submit.prevent="submitStep2()" class="space-y-4">
                    
                    <!-- 4. Label del campo password.. junto a su caja de password -->
                    <div class="text-left">
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Contraseña
                        </label>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   x-ref="passwordField"
                                   x-model="passwordInput" 
                                   placeholder="Escriba su contraseña" 
                                   autocomplete="current-password" 
                                   required
                                   class="fluent-input w-full px-3.5 py-2.5 pr-10 text-sm rounded-xl text-slate-900 placeholder:text-slate-400">
                            
                            <!-- Toggle ver/ocultar contraseña -->
                            <button type="button" 
                                    @click="togglePasswordVisibility()" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                <template x-if="!showPassword">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </template>
                                <template x-if="showPassword">
                                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- 6. Link alineado a la derecha: ¿Olvidaste tu contraseña? -->
                    <div class="flex justify-end text-xs pt-1">
                        <a href="#" @click.prevent="showForgotHelp = true" class="text-brand-gold hover:underline font-semibold">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <!-- 7. Boton que toma todo el ancho de Iniciar sesion -->
                    <div class="pt-3">
                        <button type="submit" 
                                :disabled="loading || !passwordInput"
                                class="w-full bg-brand-green hover:bg-brand-green-hover text-[#F7E8A7] font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="animate-spin h-4 w-4 text-[#F7E8A7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Iniciando sesión...' : 'Iniciar sesión'"></span>
                        </button>
                    </div>
                </form>
            </div>



        </div>

        <!-- Modal ¿Olvidó su contraseña? -->
        <div x-show="showForgotHelp" 
             @click.self="showForgotHelp = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 animate-fadeIn text-center">
                <div class="w-10 h-10 rounded-full bg-brand-gold/15 text-brand-gold flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Restablecer Contraseña</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4 text-left">
                    Por motivos de seguridad y confidencialidad procesal del bufete, solicite el restablecimiento de su clave al Director General o al administrador TI (<span class="font-semibold text-slate-800">password123</span> en entorno local).
                </p>
                <button type="button" @click="showForgotHelp = false" class="w-full py-2 bg-brand-green text-brand-gold text-xs font-bold rounded-xl hover:bg-brand-green-hover transition-colors cursor-pointer">
                    Entendido
                </button>
            </div>
        </div>

    </main>

    <!-- Footer con Estilo Legal y Contraste sobre Fondo Verde Oscuro -->
    <footer class="w-full py-4 px-6 text-center text-xs text-slate-300/80">
        <div class="max-w-[420px] mx-auto flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[11px]">
            <a href="#" @click.prevent="alert('Términos de confidencialidad institucional aplicados según Código de Ética de la Abogacía.')" class="hover:text-brand-gold hover:underline transition-colors">Términos de uso</a>
            <span class="text-slate-500">•</span>
            <a href="#" @click.prevent="alert('Privacidad protegida bajo el Secreto Profesional y Confidencialidad Jurídica.')" class="hover:text-brand-gold hover:underline transition-colors">Privacidad y confidencialidad</a>
            <span class="text-slate-500">•</span>
            <a href="#" @click.prevent="alert('Soporte TI: soporte@sillericoabogados.com | Tel: +591 77234317')" class="hover:text-brand-gold hover:underline transition-colors">Soporte técnico</a>
        </div>
        <div class="mt-2 text-[10px] text-slate-400">
            &copy; {{ date('Y') }} Sillerico &amp; Abogados Asociados S.C. Todos los derechos reservados.
        </div>
    </footer>

    <!-- Alpine Controller Script -->
    <script>
        function microsoftAuth() {
            return {
                step: 1,
                loginInput: '{{ old('login', '') }}',
                passwordInput: '',
                showPassword: false,
                remember: true,
                loading: false,
                errorMessage: '{{ $errors->first() }}',
                userAccount: {
                    id: null,
                    name: '',
                    email: '',
                    cargo: '',
                    iniciales: '',
                    color: ''
                },
                showForgotHelp: false,

                init() {
                    this.$nextTick(() => {
                        lucide.createIcons();
                        if (this.$refs.loginField) {
                            this.$refs.loginField.focus();
                        }
                    });
                },

                togglePasswordVisibility() {
                    this.showPassword = !this.showPassword;
                    this.$nextTick(() => {
                        lucide.createIcons();
                        if (this.$refs.passwordField) {
                            this.$refs.passwordField.focus();
                        }
                    });
                },

                async submitStep1() {
                    const identifier = this.loginInput.trim();
                    if (!identifier) return;

                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch('{{ route('login.check-user') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ login: identifier })
                        });

                        const data = await response.json();

                        if (response.ok && data.exists) {
                            this.userAccount = data.user;
                            this.step = 2;
                            this.errorMessage = '';
                            this.$nextTick(() => {
                                lucide.createIcons();
                                if (this.$refs.passwordField) {
                                    this.$refs.passwordField.focus();
                                }
                            });
                        } else {
                            this.errorMessage = data.message || 'No pudimos encontrar una cuenta institucional con ese correo o usuario.';
                            this.$nextTick(() => lucide.createIcons());
                        }
                    } catch (error) {
                        console.error('Error al verificar cuenta:', error);
                        this.errorMessage = 'Hubo un inconveniente de conexión con el servidor. Por favor intente nuevamente.';
                        this.$nextTick(() => lucide.createIcons());
                    } finally {
                        this.loading = false;
                    }
                },

                async submitStep2() {
                    if (!this.passwordInput) return;

                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch('{{ route('login.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                login: this.userAccount.email || this.loginInput,
                                password: this.passwordInput,
                                remember: this.remember
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            window.location.href = data.redirect || '{{ route('dashboard') }}';
                        } else {
                            this.errorMessage = data.message || 'La contraseña que escribió no es correcta.';
                            this.$nextTick(() => {
                                lucide.createIcons();
                                if (this.$refs.passwordField) {
                                    this.$refs.passwordField.select();
                                }
                            });
                        }
                    } catch (error) {
                        console.error('Error al iniciar sesión:', error);
                        this.errorMessage = 'Ocurrió un error al procesar el inicio de sesión. Verifique su conexión.';
                        this.$nextTick(() => lucide.createIcons());
                    } finally {
                        this.loading = false;
                    }
                },

                backToStep1() {
                    this.step = 1;
                    this.passwordInput = '';
                    this.errorMessage = '';
                    this.$nextTick(() => {
                        lucide.createIcons();
                        if (this.$refs.loginField) {
                            this.$refs.loginField.focus();
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
