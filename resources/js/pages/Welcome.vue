<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';
import { 
  Sun, Moon, Monitor, Phone, MessageSquare, MapPin, 
  ChevronRight, Search, FileText, Scale, Users, Image as ImageIcon, 
  Info, ExternalLink, Mail, Check, Star, ArrowUpRight
} from 'lucide-vue-next';

// Use standard Laravel appearance manager
const { appearance, updateAppearance, resolvedAppearance } = useAppearance();

// State for Article/Laws Filter
const searchKeyword = ref('');
const selectedCategory = ref('todos');

// Contact Dialog State
const isContactModalOpen = ref(false);
const contactForm = ref({
  name: '',
  email: '',
  phone: '',
  message: '',
  area: 'Derecho Corporativo'
});
const formSubmitted = ref(false);

const submitContactForm = () => {
  formSubmitted.value = true;
  setTimeout(() => {
    isContactModalOpen.value = false;
    formSubmitted.value = false;
    contactForm.value = {
      name: '',
      email: '',
      phone: '',
      message: '',
      area: 'Derecho Corporativo'
    };
  }, 2000);
};

// Data structures matching user requests
const menuItems = [
  { label: 'Servicios', href: '#servicios', icon: Scale },
  { label: 'Artículos', href: '#articulos', icon: FileText },
  { label: 'Normas y Leyes', href: '#normas', icon: Info },
  { label: 'Equipo Profesional', href: '#equipo', icon: Users },
  { label: 'Galería', href: '#galeria', icon: ImageIcon },
  { label: 'Sobre la Firma', href: '#firma', icon: Info }
];

const contactLinks = {
  whatsapp: 'https://wa.me/59170000000', // Mock standard WhatsApp
  facebook: 'https://facebook.com/sillericoasociados',
  phones: ['+591 2 2443020', '+591 706 12345'],
  emails: ['contacto@sillericoasociados.com', 'info@sillericoasociados.com'],
  address: 'Av. Mariscal Santa Cruz, Edificio Hansa, Piso 12, La Paz, Bolivia',
  locationUrl: 'https://maps.google.com/?q=Edificio+Hansa+La+Paz'
};

const services = [
  {
    title: 'Derecho Corporativo y Empresarial',
    description: 'Asesoramiento integral en constitución de sociedades, fusiones y adquisiciones, estructuración corporativa y cumplimiento legal empresarial.',
    icon: '⚖️',
    highlights: ['Gobierno corporativo', 'Contratos comerciales', 'Propiedad societaria']
  },
  {
    title: 'Derecho de Familia y Sucesiones',
    description: 'Gestión experta en procesos familiares con la máxima reserva, divorcios, custodias, testamentos, partición de herencias y sucesiones complejas.',
    icon: '🏛️',
    highlights: ['Planificación sucesoria', 'Procesos de divorcio', 'Acuerdos prematrimoniales']
  },
  {
    title: 'Propiedad Intelectual y Patentes',
    description: 'Registro de marcas, patentes de invención, derechos de autor, secretos industriales y litigios de protección de propiedad intelectual en el país e internacional.',
    icon: '🛡️',
    highlights: ['Registro de marcas', 'Derechos de autor', 'Secretos comerciales']
  },
  {
    title: 'Derecho Penal y Defensa Litigante',
    description: 'Defensa penal corporativa y personal rigurosa ante tribunales nacionales, con especialización en delitos financieros, patrimoniales y procesal penal.',
    icon: '⚜️',
    highlights: ['Litigios penales', 'Delitos corporativos', 'Apelaciones y recursos']
  },
  {
    title: 'Derecho Laboral y Social',
    description: 'Asesoría en contratos de trabajo, reestructuraciones salariales, representación ante ministerios del ramo, y prevención de contingencias laborales patronales.',
    icon: '👔',
    highlights: ['Negociación colectiva', 'Seguridad social', 'Defensa patronal']
  },
  {
    title: 'Arbitraje y Resolución de Conflictos',
    description: 'Mediación experta y arbitraje en controversias comerciales nacionales e internacionales como una alternativa ágil y altamente confidencial al litigio tradicional.',
    icon: '🤝',
    highlights: ['Mediación comercial', 'Arbitraje de inversiones', 'Estrategias preventivas']
  }
];

const articles = [
  {
    title: 'Novedades y Cambios en el Marco Regulatorio Financiero en Bolivia',
    date: '15 de Mayo, 2026',
    author: 'Dr. Alejandro Sillerico',
    excerpt: 'Un análisis profundo sobre las nuevas resoluciones de la ASFI y su impacto en la estructuración de créditos y garantías corporativas.',
    category: 'articulos',
    readTime: '5 min de lectura'
  },
  {
    title: 'Cómo Proteger los Activos Intangibles de tu Startup desde el Día Uno',
    date: '28 de Abril, 2026',
    author: 'Dra. Patricia Sillerico',
    excerpt: 'Guía práctica para emprendedores sobre registros de marcas comerciales, patentes de software y acuerdos de confidencialidad recíproca.',
    category: 'articulos',
    readTime: '8 min de lectura'
  },
  {
    title: 'El Arbitraje Comercial como Vía Eficiente ante la Burocracia Judicial',
    date: '10 de Abril, 2026',
    author: 'Dr. Fernando Asociado',
    excerpt: 'Estudio de caso sobre cómo las cláusulas arbitrales reducen los tiempos de resolución de controversias entre socios de un 75% comparado con la justicia ordinaria.',
    category: 'articulos',
    readTime: '6 min de lectura'
  }
];

const laws = [
  {
    title: 'Compendio Analítico del Nuevo Código de Comercio Comentado',
    date: 'Actualizado Mayo 2026',
    reference: 'Código Civil & Comercial',
    excerpt: 'Descarga de forma gratuita nuestro análisis de cambios normativos esenciales aplicables a sociedades anónimas y de responsabilidad limitada.',
    category: 'normas',
    readTime: 'PDF para descarga'
  },
  {
    title: 'Guía sobre Incentivos Fiscales para Empresas Exportadoras',
    date: 'Marzo 2026',
    reference: 'Ley General de Aduanas',
    excerpt: 'Explicación detallada de exenciones arancelarias e impuestos internos para empresas radicadas en parques industriales nacionales.',
    category: 'normas',
    readTime: 'PDF para descarga'
  }
];

const allPublications = computed(() => {
  const combined = [...articles, ...laws];
  return combined.filter(pub => {
    const matchesKeyword = pub.title.toLowerCase().includes(searchKeyword.value.toLowerCase()) || 
                           pub.excerpt.toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
                           pub.author?.toLowerCase().includes(searchKeyword.value.toLowerCase());
    
    if (selectedCategory.value === 'todos') {
      return matchesKeyword;
    }
    return pub.category === selectedCategory.value && matchesKeyword;
  });
});

const partners = [
  {
    name: 'Dr. Alejandro Sillerico R.',
    role: 'Socio Fundador & Director Corporativo',
    credentials: 'Graduado con honores de la Universidad Mayor de San Andrés, con Maestría en Derecho de los Negocios en Madrid, España. Más de 25 años de experiencia asesorando a corporaciones multinacionales.',
    initials: 'AS'
  },
  {
    name: 'Dra. Patricia Sillerico G.',
    role: 'Socia Principal & Especialista en Propiedad Intelectual',
    credentials: 'Doctora en Propiedad Industrial por la Universidad de Buenos Aires (UBA). Consultora externa registrada ante organismos internacionales de arbitraje marcario.',
    initials: 'PS'
  },
  {
    name: 'Dr. Fernando B. Asociado',
    role: 'Socio & Jefe de Litigación y Defensa Civil',
    credentials: 'Ex-magistrado de la corte de apelaciones, experto en litigio procesal complejo, arbitraje corporativo internacional y docencia universitaria de postgrado.',
    initials: 'FA'
  }
];

const galleryImages = [
  {
    title: 'Oficina Principal - Sala de Firmas',
    desc: 'Espacio elegante con luz natural y acabados de lujo para concretar transacciones de alto perfil corporativo.',
    icon: '🏛️'
  },
  {
    title: 'Sala de Conferencias y Arbitrajes',
    desc: 'Equipada con tecnología avanzada para sesiones de mediación y videoconferencias internacionales seguras.',
    icon: '💼'
  },
  {
    title: 'Nuestra Biblioteca Jurídica',
    desc: 'Un archivo completo con colecciones históricas y modernas de jurisprudencia civil, penal e internacional.',
    icon: '📚'
  },
  {
    title: 'Zona de Recepción y Consultas',
    desc: 'Diseño sobrio y acogedor diseñado para brindar la mayor confidencialidad y bienestar a nuestros distinguidos clientes.',
    icon: '✨'
  }
];
</script>

<template>
  <Head title="Sillerico & Asociados - Firma de Abogados">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  </Head>

  <div class="min-h-screen font-['Plus_Jakarta_Sans',sans-serif] transition-colors duration-300 bg-[#fbf9f6] text-neutral-800 dark:bg-[#060f12] dark:text-neutral-100">
    
    <!-- 1. BARRA DE ENLACES DE CONTACTO RÁPIDO (HEADER SUPERIOR) -->
    <div class="w-full bg-[#082a20] text-amber-200/90 dark:bg-[#031510] border-b border-[#c5a059]/20 text-xs py-2 px-4 md:px-8 flex flex-col md:flex-row justify-between items-center gap-2">
      <div class="flex items-center gap-4">
        <a :href="'tel:' + contactLinks.phones[0]" class="flex items-center gap-1.5 hover:text-white transition-colors">
          <Phone class="w-3.5 h-3.5" />
          <span>{{ contactLinks.phones[0] }}</span>
        </a>
        <a :href="contactLinks.locationUrl" target="_blank" class="flex items-center gap-1.5 hover:text-white transition-colors">
          <MapPin class="w-3.5 h-3.5" />
          <span>La Paz, Bolivia</span>
        </a>
      </div>
      <div class="flex items-center gap-4">
        <a :href="contactLinks.whatsapp" target="_blank" class="flex items-center gap-1.5 bg-emerald-700/40 hover:bg-emerald-700/60 px-2.5 py-0.5 rounded border border-emerald-500/30 text-white transition-all">
          <MessageSquare class="w-3 h-3 text-emerald-400 fill-emerald-400" />
          <span>WhatsApp Directo</span>
        </a>
        <a :href="contactLinks.facebook" target="_blank" class="hover:text-white transition-colors flex items-center gap-1">
          <span>Facebook</span>
          <ArrowUpRight class="w-2.5 h-2.5" />
        </a>
      </div>
    </div>

    <!-- 2. CABECERA PRINCIPAL Y NAVEGACIÓN (GLASSMORPHIC HEADER) -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-md bg-[#fbf9f6]/80 dark:bg-[#060f12]/80 border-b border-neutral-200/50 dark:border-neutral-800/50 shadow-sm transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 md:px-8 py-3.5 flex justify-between items-center">
        
        <!-- Logotipo S&A -->
        <a href="#" class="flex items-center gap-2.5 group">
          <div class="relative flex aspect-square size-10 items-center justify-center rounded-lg bg-[#082a20] dark:bg-[#0c3e30] border border-[#c5a059]/40 group-hover:scale-105 transition-transform">
            <span class="font-['Cinzel',serif] font-bold text-base text-[#c5a059]">S&A</span>
            <!-- Decorative wreath/scales subtle outline -->
            <div class="absolute inset-0.5 rounded border border-amber-500/20"></div>
          </div>
          <div class="flex flex-col">
            <span class="font-['Cinzel',serif] text-base md:text-lg font-bold tracking-wider leading-none text-[#082a20] dark:text-[#c5a059] group-hover:text-amber-600 transition-colors">
              SILLERICO & ASOCIADOS
            </span>
            <span class="text-[9px] uppercase tracking-widest font-semibold text-neutral-500 dark:text-[#c5a059]/70">
              Firma de Abogados
            </span>
          </div>
        </a>

        <!-- Menú de Navegación de Escritorio -->
        <nav class="hidden lg:flex items-center gap-6">
          <a 
            v-for="item in menuItems" 
            :key="item.label" 
            :href="item.href"
            class="text-xs md:text-sm font-medium hover:text-[#c5a059] transition-colors relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-[#c5a059] hover:after:w-full after:transition-all"
          >
            {{ item.label }}
          </a>
        </nav>

        <!-- Botones de Acción (Theme Toggle y Contacto) -->
        <div class="flex items-center gap-3">
          
          <!-- Botón de Modo Oscuro/Claro (Inertia useAppearance) -->
          <div class="flex items-center bg-neutral-200/50 dark:bg-neutral-800/50 p-1 rounded-lg border border-neutral-300/30 dark:border-neutral-700/30">
            <button 
              @click="updateAppearance(resolvedAppearance === 'dark' ? 'light' : 'dark')" 
              class="p-1.5 rounded-md hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-all text-neutral-700 dark:text-neutral-200"
              title="Cambiar Tema"
            >
              <Sun v-if="resolvedAppearance === 'dark'" class="w-4 h-4 text-amber-400" />
              <Moon v-else class="w-4 h-4 text-emerald-950" />
            </button>
          </div>

          <!-- Botón de Consulta -->
          <button 
            @click="isContactModalOpen = true"
            class="hidden sm:inline-flex items-center gap-1.5 bg-[#082a20] hover:bg-[#0c3e30] dark:bg-[#c5a059] dark:hover:bg-[#d6b46c] text-white dark:text-emerald-950 font-semibold px-4.5 py-2 rounded-lg text-xs md:text-sm tracking-wider border border-[#c5a059]/30 transition-all shadow-md active:scale-95"
          >
            <span>Consulta Rápida</span>
            <ChevronRight class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </header>

    <!-- 3. SECCIÓN HERO PRINCIPAL (PREMIUM PRESENTATION) -->
    <section class="relative w-full overflow-hidden py-20 lg:py-32 flex items-center justify-center bg-gradient-to-br from-[#f8f5ee] via-[#fbf9f6] to-[#eae5da] dark:from-[#050e10] dark:via-[#061517] dark:to-[#020708] border-b border-[#c5a059]/10">
      
      <!-- Subtle Decorative Background Circles -->
      <div class="absolute top-1/4 left-1/10 w-96 h-96 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>
      <div class="absolute bottom-1/4 right-1/10 w-96 h-96 rounded-full bg-amber-500/5 blur-3xl pointer-events-none"></div>
      
      <div class="max-w-7xl mx-auto px-4 md:px-8 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Hero Text -->
        <div class="lg:col-span-7 flex flex-col items-start text-left space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#082a20]/5 dark:bg-[#c5a059]/10 border border-[#082a20]/10 dark:border-[#c5a059]/20 text-xs font-semibold text-[#082a20] dark:text-[#c5a059]">
            <Scale class="w-3.5 h-3.5" />
            <span>Firma de Abogados Elite - La Paz, Bolivia</span>
          </div>

          <h1 class="font-['Cinzel',serif] text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-[#082a20] dark:text-[#c5a059] leading-[1.1] md:leading-[1.05]">
            Compromiso Inquebrantable, <br class="hidden sm:inline" />
            <span class="text-neutral-800 dark:text-white">Excelencia Legal.</span>
          </h1>

          <p class="text-base md:text-lg text-neutral-600 dark:text-neutral-300 max-w-xl leading-relaxed font-light">
            En <span class="font-semibold text-neutral-800 dark:text-white">Sillerico & Asociados</span> fusionamos la tradición del derecho de excelencia con un enfoque moderno y ágil para asegurar el éxito patrimonial y corporativo de nuestros distinguidos clientes.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto pt-2">
            <button 
              @click="isContactModalOpen = true"
              class="w-full sm:w-auto text-center bg-[#082a20] hover:bg-[#0c3e30] dark:bg-[#c5a059] dark:hover:bg-[#d6b46c] text-white dark:text-emerald-950 font-bold px-8 py-3.5 rounded-lg text-sm tracking-wider transition-all shadow-xl shadow-emerald-950/10 dark:shadow-amber-500/5 active:scale-98 flex items-center justify-center gap-2"
            >
              <span>Agendar una Consulta</span>
              <ChevronRight class="w-4 h-4" />
            </button>
            <a 
              href="#servicios" 
              class="w-full sm:w-auto text-center bg-transparent border border-[#082a20]/30 hover:border-[#082a20] dark:border-[#c5a059]/30 dark:hover:border-[#c5a059] text-[#082a20] dark:text-[#c5a059] font-bold px-8 py-3.5 rounded-lg text-sm tracking-wider transition-all flex items-center justify-center"
            >
              Explorar Áreas
            </a>
          </div>

          <!-- Quick trust indicators -->
          <div class="pt-6 grid grid-cols-3 gap-6 border-t border-neutral-300/40 dark:border-neutral-800/50 w-full max-w-lg">
            <div>
              <p class="font-['Cinzel',serif] text-2xl md:text-3xl font-bold text-[#082a20] dark:text-[#c5a059]">25+</p>
              <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-widest font-semibold">Años de Trayectoria</p>
            </div>
            <div>
              <p class="font-['Cinzel',serif] text-2xl md:text-3xl font-bold text-[#082a20] dark:text-[#c5a059]">98%</p>
              <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-widest font-semibold">Casos Exitosos</p>
            </div>
            <div>
              <p class="font-['Cinzel',serif] text-2xl md:text-3xl font-bold text-[#082a20] dark:text-[#c5a059]">500+</p>
              <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-widest font-semibold">Clientes Activos</p>
            </div>
          </div>
        </div>

        <!-- Hero Custom Emblem Graphic (Visual wow) -->
        <div class="lg:col-span-5 flex justify-center items-center relative">
          <div class="relative w-72 h-72 sm:w-80 sm:h-80 md:w-96 md:h-96 rounded-2xl bg-gradient-to-tr from-[#082a20] to-[#041510] dark:from-[#0d3f32] dark:to-[#061e18] p-8 flex flex-col justify-between border-2 border-[#c5a059]/40 shadow-2xl relative overflow-hidden group">
            
            <!-- Graphic background lines -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
            
            <div class="flex justify-between items-start z-10">
              <span class="font-['Cinzel',serif] text-amber-200/40 text-lg">S&A</span>
              <span class="text-amber-200/40 text-xs">BOLIVIA</span>
            </div>

            <!-- Majestic Scale Icon -->
            <div class="flex justify-center items-center z-10 py-4">
              <svg class="w-32 h-32 md:w-40 md:h-40 text-[#c5a059] fill-[#c5a059]/10 group-hover:scale-105 transition-transform duration-500" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Scale Base & Pillars -->
                <path d="M48 20h4v60h-4z" />
                <path d="M30 80h40v4H30z" />
                <path d="M40 84h20v2H40z" />
                <!-- Beam -->
                <path d="M15 28h70v3H15z" />
                <circle cx="50" cy="23" r="4" />
                <!-- Left Scale Cup -->
                <path d="M15 29.5l10 25h-20z" />
                <path d="M25 54.5v2H5v-2z" />
                <path d="M15 56.5a10 10 0 0 1-10-10h20a10 10 0 0 1-10 10z" />
                <!-- Right Scale Cup -->
                <path d="M85 29.5l10 25h-20z" />
                <path d="M95 54.5v2H75v-2z" />
                <path d="M85 56.5a10 10 0 0 1-10-10h20a10 10 0 0 1-10 10z" />
              </svg>
            </div>

            <div class="flex flex-col justify-end items-center text-center z-10 space-y-1">
              <span class="font-['Cinzel',serif] text-base font-bold text-amber-200 uppercase tracking-widest leading-none">
                LIDERAZGO JURÍDICO
              </span>
              <span class="text-[9px] text-[#c5a059] uppercase tracking-widest">
                Sillerico & Asociados
              </span>
            </div>
            
            <!-- Metallic edge highlights -->
            <div class="absolute inset-0 rounded-2xl border border-white/5 pointer-events-none"></div>
          </div>
          
          <!-- Decorative leaf graphic overlapping -->
          <div class="absolute -bottom-6 -left-6 bg-[#eae3d5] dark:bg-[#0f2a24] border border-[#c5a059]/30 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <span class="text-lg">⭐</span>
            <div class="flex flex-col">
              <span class="text-[10px] uppercase font-bold text-neutral-500 dark:text-[#c5a059]/70 tracking-widest">Calificación</span>
              <span class="text-xs font-bold text-neutral-800 dark:text-white">Firma 5 Estrellas</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. SECCIÓN SERVICIOS (INTERACTIVE CARD GRID) -->
    <section id="servicios" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Áreas de Práctica Jurídica</span>
        <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-800 dark:text-white">
          Nuestras Especializaciones y Servicios
        </h2>
        <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
        <p class="text-sm md:text-base text-neutral-500 dark:text-neutral-400 font-light">
          Ofrecemos asesoría legal integral con abogados especializados en cada rama del derecho nacional e internacional, brindándote la mayor seguridad jurídica.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="service in services" 
          :key="service.title"
          class="group relative bg-[#f7f5ef]/50 dark:bg-[#071619] p-8 rounded-xl border border-neutral-300/30 dark:border-neutral-800 hover:border-[#c5a059]/50 dark:hover:border-[#c5a059]/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden"
        >
          <!-- Hover effect back panel -->
          <div class="absolute inset-0 bg-[#082a20]/0 group-hover:bg-[#082a20]/2 dark:group-hover:bg-[#c5a059]/2 transition-colors duration-300"></div>

          <div class="space-y-4 z-10">
            <div class="size-12 rounded-lg bg-[#082a20]/10 dark:bg-[#c5a059]/10 border border-[#082a20]/10 dark:border-[#c5a059]/20 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
              {{ service.icon }}
            </div>
            
            <h3 class="font-['Cinzel',serif] text-lg font-bold text-[#082a20] dark:text-white group-hover:text-[#c5a059] dark:group-hover:text-[#c5a059] transition-colors">
              {{ service.title }}
            </h3>
            
            <p class="text-sm text-neutral-500 dark:text-neutral-300 font-light leading-relaxed">
              {{ service.description }}
            </p>
          </div>

          <!-- Bullet Highlights -->
          <div class="mt-6 pt-4 border-t border-neutral-300/40 dark:border-neutral-800/80 z-10 flex flex-wrap gap-2">
            <span 
              v-for="hi in service.highlights" 
              :key="hi"
              class="text-[10px] font-semibold bg-[#082a20]/5 dark:bg-neutral-800 px-2 py-0.5 rounded text-[#082a20] dark:text-[#c5a059]"
            >
              {{ hi }}
            </span>
          </div>

          <!-- Bottom arrow decorative -->
          <div class="absolute bottom-4 right-4 text-neutral-400 group-hover:text-[#c5a059] group-hover:translate-x-1 transition-all duration-300">
            <ChevronRight class="w-5 h-5" />
          </div>
        </div>
      </div>
    </section>

    <!-- 5. SECCIÓN ARTÍCULOS, NORMAS Y LEYES (DYNAMIC FILTER SYSTEM) -->
    <section id="articulos" class="py-20 md:py-28 bg-[#f5f2eb] dark:bg-[#040c0e]/80 border-y border-neutral-300/30 dark:border-neutral-900/50">
      <div class="max-w-7xl mx-auto px-4 md:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
          <div class="space-y-3">
            <span id="normas" class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Artículos, Normas y Leyes</span>
            <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-800 dark:text-white">
              Centro de Análisis Jurídico
            </h2>
            <div class="w-16 h-[2px] bg-[#c5a059]"></div>
          </div>

          <!-- Live search input -->
          <div class="relative w-full md:w-80">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" />
            <input 
              v-model="searchKeyword"
              type="text" 
              placeholder="Buscar artículos o leyes..."
              class="w-full text-xs bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-800 rounded-lg py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-800 dark:text-white"
            />
          </div>
        </div>

        <!-- Interactive Category Switcher -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-neutral-300/40 dark:border-neutral-800/80 pb-4">
          <button 
            @click="selectedCategory = 'todos'"
            :class="[
              'px-4 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase transition-all',
              selectedCategory === 'todos' 
                ? 'bg-[#082a20] text-white dark:bg-[#c5a059] dark:text-[#082a20]' 
                : 'bg-white/50 hover:bg-white dark:bg-neutral-900/50 dark:hover:bg-neutral-900 border border-neutral-300/30 dark:border-neutral-800 text-neutral-600 dark:text-neutral-300'
            ]"
          >
            Ver Todo
          </button>
          <button 
            @click="selectedCategory = 'articulos'"
            :class="[
              'px-4 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase transition-all',
              selectedCategory === 'articulos' 
                ? 'bg-[#082a20] text-white dark:bg-[#c5a059] dark:text-[#082a20]' 
                : 'bg-white/50 hover:bg-white dark:bg-neutral-900/50 dark:hover:bg-neutral-900 border border-neutral-300/30 dark:border-neutral-800 text-neutral-600 dark:text-neutral-300'
            ]"
          >
            Artículos Doctrinarios
          </button>
          <button 
            @click="selectedCategory = 'normas'"
            :class="[
              'px-4 py-1.5 rounded-lg text-xs font-semibold tracking-wider uppercase transition-all',
              selectedCategory === 'normas' 
                ? 'bg-[#082a20] text-white dark:bg-[#c5a059] dark:text-[#082a20]' 
                : 'bg-white/50 hover:bg-white dark:bg-neutral-900/50 dark:hover:bg-neutral-900 border border-neutral-300/30 dark:border-neutral-800 text-neutral-600 dark:text-neutral-300'
            ]"
          >
            Leyes & Compendios
          </button>
        </div>

        <!-- Dynamic Grid List -->
        <div v-if="allPublications.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div 
            v-for="pub in allPublications" 
            :key="pub.title"
            class="bg-white dark:bg-neutral-900/50 p-6 md:p-8 rounded-xl border border-neutral-200/50 dark:border-neutral-800/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
          >
            <div class="space-y-4">
              <div class="flex justify-between items-center text-[10px] tracking-wider uppercase font-bold text-neutral-500 dark:text-[#c5a059]">
                <span>{{ pub.reference || pub.author }}</span>
                <span class="bg-neutral-200/40 dark:bg-neutral-800 px-2 py-0.5 rounded text-xs lowercase font-normal">{{ pub.readTime }}</span>
              </div>
              
              <h3 class="font-['Cinzel',serif] text-base md:text-lg font-bold text-neutral-800 dark:text-white leading-tight">
                {{ pub.title }}
              </h3>
              
              <p class="text-xs md:text-sm text-neutral-500 dark:text-neutral-300 font-light leading-relaxed">
                {{ pub.excerpt }}
              </p>
            </div>

            <!-- Footer Details -->
            <div class="mt-6 pt-4 border-t border-neutral-200/50 dark:border-neutral-800/50 flex justify-between items-center">
              <span class="text-[10px] text-neutral-400 font-semibold">{{ pub.date }}</span>
              <a 
                href="#" 
                class="inline-flex items-center gap-1 text-xs font-bold text-[#082a20] dark:text-[#c5a059] hover:underline"
              >
                <span>Leer completo</span>
                <ChevronRight class="w-3.5 h-3.5" />
              </a>
            </div>
          </div>
        </div>

        <!-- No Results Fallback -->
        <div v-else class="text-center py-16 bg-white/40 dark:bg-neutral-900/20 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-800">
          <FileText class="w-12 h-12 text-[#c5a059] mx-auto mb-4 opacity-50" />
          <h3 class="font-semibold text-lg">No se hallaron resultados</h3>
          <p class="text-sm text-neutral-500 dark:text-neutral-400">Intenta utilizar otras palabras clave en la barra de búsqueda.</p>
        </div>
      </div>
    </section>

    <!-- 6. SECCIÓN EQUIPO PROFESIONAL (SENIOR PARTNERS) -->
    <section id="equipo" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Nuestro Talento Humano</span>
        <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-800 dark:text-white">
          Socios y Abogados Litigantes
        </h2>
        <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
        <p class="text-sm md:text-base text-neutral-500 dark:text-neutral-400 font-light">
          Un cuerpo legal multidisciplinar y de amplia reputación académica nacional encargado de defender sus derechos corporativos y personales.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          v-for="partner in partners" 
          :key="partner.name"
          class="bg-[#f7f5ef]/50 dark:bg-[#071619]/60 rounded-xl border border-neutral-300/30 dark:border-neutral-800/80 p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
        >
          <div class="space-y-6">
            <!-- Simulated Professional Portrait / Initials Graphic -->
            <div class="relative w-full aspect-square rounded-lg bg-gradient-to-br from-[#082a20] to-[#041510] dark:from-[#0d3f32] dark:to-[#061e18] flex items-center justify-center border border-[#c5a059]/30 overflow-hidden shadow-inner group-hover:border-[#c5a059] transition-colors">
              <span class="font-['Cinzel',serif] text-5xl md:text-6xl font-bold text-[#c5a059]/40 group-hover:scale-105 group-hover:text-[#c5a059] transition-all duration-500 select-none">
                {{ partner.initials }}
              </span>
              <!-- Scales detail in background -->
              <svg class="absolute bottom-2 right-2 w-16 h-16 text-white/2 opacity-[0.03]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm-1 3h2v2h-2V7zm0 4h2v6h-2v-6z"/>
              </svg>
            </div>

            <div class="space-y-2">
              <h3 class="font-['Cinzel',serif] text-base md:text-lg font-bold text-neutral-800 dark:text-[#c5a059] group-hover:text-amber-500 transition-colors">
                {{ partner.name }}
              </h3>
              <p class="text-xs uppercase tracking-wider font-semibold text-neutral-400 dark:text-neutral-300">
                {{ partner.role }}
              </p>
              <div class="w-8 h-[2px] bg-[#c5a059]"></div>
            </div>

            <p class="text-xs md:text-sm text-neutral-500 dark:text-neutral-300 font-light leading-relaxed">
              {{ partner.credentials }}
            </p>
          </div>

          <div class="mt-6 pt-4 border-t border-neutral-300/40 dark:border-neutral-800/80 flex items-center justify-between">
            <span class="text-[10px] text-neutral-400 uppercase tracking-widest font-semibold">Socio Consultor</span>
            <button 
              @click="isContactModalOpen = true"
              class="text-xs font-bold text-[#082a20] dark:text-[#c5a059] hover:underline flex items-center gap-1"
            >
              <span>Agendar cita</span>
              <ChevronRight class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. SECCIÓN GALERÍA INSTITUCIONAL (PREMIUM GRID) -->
    <section id="galeria" class="py-20 md:py-28 bg-[#f5f2eb] dark:bg-[#040c0e]/80 border-y border-neutral-300/30 dark:border-neutral-900/50">
      <div class="max-w-7xl mx-auto px-4 md:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Infraestructura Corporativa</span>
          <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-800 dark:text-white">
            Nuestras Instalaciones en La Paz
          </h2>
          <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
          <p class="text-sm md:text-base text-neutral-500 dark:text-neutral-400 font-light">
            Instalaciones corporativas de primer nivel diseñadas para ofrecerle la mayor privacidad y sofisticación durante sus visitas de negocios.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="img in galleryImages" 
            :key="img.title"
            class="group bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200/50 dark:border-neutral-800/80 p-6 flex flex-col justify-between hover:shadow-lg transition-all duration-300 shadow-sm"
          >
            <div class="space-y-4">
              <!-- High-end structural mock representation (WOW aesthetics) -->
              <div class="w-full aspect-video rounded-lg bg-[#082a20]/5 dark:bg-neutral-800 flex items-center justify-center text-4xl group-hover:scale-[1.03] transition-transform duration-300 border border-neutral-200 dark:border-neutral-800 shadow-inner select-none">
                {{ img.icon }}
              </div>
              <h3 class="font-['Cinzel',serif] text-sm font-bold text-[#082a20] dark:text-[#c5a059] leading-tight">
                {{ img.title }}
              </h3>
              <p class="text-[11px] text-neutral-500 dark:text-neutral-300 font-light leading-relaxed">
                {{ img.desc }}
              </p>
            </div>
            <div class="mt-4 pt-2 border-t border-neutral-100 dark:border-neutral-800 flex justify-end">
              <span class="text-[9px] uppercase tracking-widest font-semibold text-neutral-400 group-hover:text-[#c5a059] transition-colors">Ver Ampliado</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. SECCIÓN SOBRE LA FIRMA (VALORES E HISTORIA) -->
    <section id="firma" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        
        <!-- History & Mission -->
        <div class="space-y-6">
          <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Trayectoria y Liderazgo</span>
            <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-800 dark:text-white">
              Sobre Sillerico & Asociados
            </h2>
            <div class="w-16 h-[2px] bg-[#c5a059]"></div>
          </div>

          <p class="text-sm md:text-base text-neutral-600 dark:text-neutral-300 leading-relaxed font-light">
            Fundada hace más de dos décadas en la ciudad de La Paz, nuestra firma ha sabido consolidarse como un referente de liderazgo e idoneidad jurídica. Ofrecemos a nuestros clientes nacionales y extranjeros un servicio que combina la solidez del derecho clásico con respuestas ágiles a los desafíos de la economía digital y la globalización de negocios.
          </p>

          <p class="text-sm md:text-base text-neutral-600 dark:text-neutral-300 leading-relaxed font-light">
            Nuestro compromiso es siempre velar por sus intereses patrimoniales mediante soluciones preventivas estratégicas y, de ser necesario, una representación litigante de primer nivel ante todas las instancias judiciales y arbitrales.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
            <div class="p-5 rounded-lg bg-[#f7f5ef]/50 dark:bg-neutral-900 border border-neutral-300/30 dark:border-neutral-800">
              <span class="text-lg">🏛️</span>
              <h3 class="font-['Cinzel',serif] text-sm font-bold text-[#082a20] dark:text-[#c5a059] mt-2 mb-1">Misión</h3>
              <p class="text-[11px] text-neutral-500 dark:text-neutral-400 font-light">Brindar asesoramiento integral con los más altos estándares éticos, protegiendo los activos patrimoniales y la paz social de nuestros representados.</p>
            </div>
            <div class="p-5 rounded-lg bg-[#f7f5ef]/50 dark:bg-neutral-900 border border-neutral-300/30 dark:border-neutral-800">
              <span class="text-lg">🚀</span>
              <h3 class="font-['Cinzel',serif] text-sm font-bold text-[#082a20] dark:text-[#c5a059] mt-2 mb-1">Visión</h3>
              <p class="text-[11px] text-neutral-500 dark:text-neutral-400 font-light">Ser reconocidos de forma permanente como la firma de abogados elite referente de Bolivia, líder en resolución de controversias mercantiles complejas.</p>
            </div>
          </div>
        </div>

        <!-- Values Card Grid (Aesthetic presentation) -->
        <div class="bg-gradient-to-tr from-[#082a20] to-[#041510] dark:from-[#071d18] dark:to-[#030d0a] rounded-2xl border-2 border-[#c5a059]/40 p-8 md:p-10 shadow-2xl relative overflow-hidden text-white flex flex-col justify-between">
          <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
          
          <div class="space-y-6 z-10">
            <span class="font-['Cinzel',serif] text-xs font-bold text-amber-200/60 uppercase tracking-widest">Valores de la Firma</span>
            <h3 class="font-['Cinzel',serif] text-2xl font-bold text-amber-200 leading-tight">Nuestros Pilares Fundamentales</h3>
            <p class="text-xs text-neutral-300 font-light leading-relaxed">Cada caso y cliente es atendido bajo directrices de absoluta responsabilidad institucional.</p>

            <div class="space-y-4 pt-4">
              <div class="flex items-start gap-4">
                <div class="mt-1 size-5 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-[10px] text-amber-300">✓</div>
                <div>
                  <h4 class="font-semibold text-sm text-amber-200">Honestidad y Transparencia</h4>
                  <p class="text-[11px] text-neutral-300 font-light leading-relaxed">Comunicación clara de posibilidades reales desde la primera consulta sin falsas promesas.</p>
                </div>
              </div>
              
              <div class="flex items-start gap-4">
                <div class="mt-1 size-5 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-[10px] text-amber-300">✓</div>
                <div>
                  <h4 class="font-semibold text-sm text-amber-200">Absoluta Confidencialidad</h4>
                  <p class="text-[11px] text-neutral-300 font-light leading-relaxed">Reserva estricta y protección rigurosa de toda la documentación corporativa y familiar.</p>
                </div>
              </div>

              <div class="flex items-start gap-4">
                <div class="mt-1 size-5 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-[10px] text-amber-300">✓</div>
                <div>
                  <h4 class="font-semibold text-sm text-amber-200">Disciplina Jurídica</h4>
                  <p class="text-[11px] text-neutral-300 font-light leading-relaxed">Rigurosidad académica en la investigación legislativa aplicada a cada demanda o defensa civil.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 pt-6 border-t border-white/10 z-10 flex justify-between items-center text-xs text-neutral-300">
            <span>S&A • SILLERICO & ASOCIADOS</span>
            <Scale class="w-4 h-4 text-[#c5a059]" />
          </div>
        </div>
      </div>
    </section>

    <!-- 9. PIE DE PÁGINA (COMPLETE INTEGRATED CONTACT & MAP) -->
    <footer id="contacto" class="bg-[#041510] text-[#eae5da] dark:bg-[#020a0d] border-t border-[#c5a059]/20 pt-16 pb-8 text-xs md:text-sm">
      <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">
        
        <!-- Corporate Presentation -->
        <div class="md:col-span-4 space-y-4">
          <div class="flex items-center gap-2">
            <div class="flex aspect-square size-8 items-center justify-center rounded bg-[#c5a059] text-emerald-950 font-['Cinzel',serif] font-bold text-sm">
              S&A
            </div>
            <span class="font-['Cinzel',serif] text-base font-bold tracking-wider text-[#c5a059]">SILLERICO & ASOCIADOS</span>
          </div>
          
          <p class="text-neutral-400 font-light leading-relaxed">
            Consorcio Jurídico especializado en la defensa corporativa, patrimonial y asesoría mercantil de empresas. Brindando certeza de forma integral en el país.
          </p>

          <div class="flex items-center gap-3 pt-2">
            <a :href="contactLinks.whatsapp" target="_blank" class="size-8 rounded-full bg-emerald-900/40 hover:bg-emerald-900 border border-emerald-500/20 flex items-center justify-center text-white transition-colors">
              <MessageSquare class="w-4 h-4 text-emerald-400" />
            </a>
            <a :href="contactLinks.facebook" target="_blank" class="size-8 rounded-full bg-blue-950/40 hover:bg-blue-900 border border-blue-500/20 flex items-center justify-center text-white transition-colors">
              <span class="font-bold text-xs">fb</span>
            </a>
          </div>
        </div>

        <!-- Sitemap Quick Links -->
        <div class="md:col-span-3 space-y-4">
          <h3 class="font-['Cinzel',serif] text-sm font-bold tracking-widest text-[#c5a059] uppercase">Mapa de Sitio</h3>
          <ul class="space-y-2">
            <li v-for="item in menuItems" :key="item.label">
              <a :href="item.href" class="text-neutral-400 hover:text-white transition-colors flex items-center gap-1.5 font-light">
                <ChevronRight class="w-3.5 h-3.5 text-[#c5a059]" />
                <span>{{ item.label }}</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Technical Contact Panel -->
        <div class="md:col-span-5 space-y-4">
          <h3 class="font-['Cinzel',serif] text-sm font-bold tracking-widest text-[#c5a059] uppercase">Ubicación y Contacto</h3>
          
          <div class="space-y-3">
            <div class="flex items-start gap-3">
              <MapPin class="w-4 h-4 text-[#c5a059] mt-0.5 shrink-0" />
              <div class="flex flex-col text-neutral-400 font-light">
                <span class="font-semibold text-neutral-300">Oficina Central:</span>
                <span>{{ contactLinks.address }}</span>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <Phone class="w-4 h-4 text-[#c5a059] mt-0.5 shrink-0" />
              <div class="flex flex-col text-neutral-400 font-light">
                <span class="font-semibold text-neutral-300">Teléfonos de Atención:</span>
                <span v-for="phone in contactLinks.phones" :key="phone">{{ phone }}</span>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <Mail class="w-4 h-4 text-[#c5a059] mt-0.5 shrink-0" />
              <div class="flex flex-col text-neutral-400 font-light">
                <span class="font-semibold text-neutral-300">Correo Electrónico:</span>
                <span>{{ contactLinks.emails[0] }}</span>
              </div>
            </div>
          </div>
          
          <!-- Mockup Interactive map grid (WOW style) -->
          <a :href="contactLinks.locationUrl" target="_blank" class="block w-full h-24 bg-[#051c15] hover:bg-[#07241b] rounded-lg border border-[#c5a059]/30 relative overflow-hidden group shadow-inner transition-colors pt-2">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#c5a059_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-2 z-10">
              <MapPin class="w-5 h-5 text-[#c5a059] mb-1 group-hover:scale-110 transition-transform" />
              <span class="text-[10px] tracking-wider uppercase font-semibold text-white">Ver Mapa de Ubicación en Google Maps</span>
              <span class="text-[8px] text-neutral-400">Clic para abrir navegación</span>
            </div>
            <div class="absolute bottom-1 right-1 text-white/40">
              <ExternalLink class="w-3.5 h-3.5" />
            </div>
          </a>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 md:px-8 pt-8 border-t border-neutral-800 text-center text-neutral-500 text-[10px] md:text-xs flex flex-col sm:flex-row justify-between items-center gap-4">
        <span>© 2026 Sillerico & Asociados. Todos los derechos reservados.</span>
        <span>Diseñado con Excelencia y Rigor Académico Jurídico.</span>
      </div>
    </footer>

    <!-- 10. DIALOG MODAL FOR QUICK WhatsApp/EMAIL CONSULTATION -->
    <div v-if="isContactModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/80 backdrop-blur-sm">
      <div 
        class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-300/40 dark:border-neutral-800 max-w-md w-full p-6 relative shadow-2xl animate-in fade-in zoom-in-95 duration-200"
        @click.stop
      >
        <button 
          @click="isContactModalOpen = false" 
          class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 text-lg font-bold"
        >
          ✕
        </button>

        <div class="text-center mb-6 space-y-2">
          <div class="size-10 rounded-full bg-[#082a20]/10 dark:bg-[#c5a059]/10 border border-[#082a20]/10 dark:border-[#c5a059]/20 flex items-center justify-center mx-auto text-xl">
            ⚖️
          </div>
          <h3 class="font-['Cinzel',serif] text-base md:text-lg font-bold text-neutral-800 dark:text-[#c5a059]">Consulta Jurídica Inmediata</h3>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 font-light">Completa tus datos básicos para ponernos en contacto contigo.</p>
        </div>

        <form @submit.prevent="submitContactForm" class="space-y-4" v-if="!formSubmitted">
          <div class="space-y-1 text-left">
            <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-500 dark:text-neutral-400">Nombre Completo</label>
            <input 
              v-model="contactForm.name"
              type="text" 
              required
              placeholder="Ej. Juan Pérez"
              class="w-full text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2.5 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-800 dark:text-white"
            />
          </div>

          <div class="space-y-1 text-left">
            <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-500 dark:text-neutral-400">Correo Electrónico</label>
            <input 
              v-model="contactForm.email"
              type="email" 
              required
              placeholder="juan@ejemplo.com"
              class="w-full text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2.5 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-800 dark:text-white"
            />
          </div>

          <div class="space-y-1 text-left">
            <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-500 dark:text-neutral-400">Teléfono / WhatsApp</label>
            <input 
              v-model="contactForm.phone"
              type="tel" 
              required
              placeholder="+591 700 00000"
              class="w-full text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2.5 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-800 dark:text-white"
            />
          </div>

          <div class="space-y-1 text-left">
            <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-500 dark:text-neutral-400">Área de Interés</label>
            <select 
              v-model="contactForm.area"
              class="w-full text-xs bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2.5 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-800 dark:text-white"
            >
              <option>Derecho Corporativo</option>
              <option>Derecho de Familia</option>
              <option>Propiedad Intelectual</option>
              <option>Defensa Penal</option>
              <option>Arbitraje</option>
            </select>
          </div>

          <button 
            type="submit"
            class="w-full bg-[#082a20] hover:bg-[#0c3e30] dark:bg-[#c5a059] dark:hover:bg-[#d6b46c] text-white dark:text-emerald-950 font-bold py-3 rounded-lg text-xs tracking-wider border border-[#c5a059]/30 transition-all uppercase"
          >
            Enviar Solicitud
          </button>
        </form>

        <!-- Success Message -->
        <div v-else class="py-12 text-center space-y-4 animate-in fade-in duration-200">
          <div class="size-16 rounded-full bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-500/30 flex items-center justify-center mx-auto text-emerald-600 dark:text-emerald-400 text-3xl">
            ✓
          </div>
          <h4 class="font-['Cinzel',serif] text-base font-bold text-neutral-800 dark:text-[#c5a059]">¡Solicitud Recibida!</h4>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-xs mx-auto font-light">Un asesor legal de Sillerico & Asociados se contactará contigo por teléfono o WhatsApp de forma inmediata.</p>
        </div>
      </div>
    </div>

  </div>
</template>

<style>
/* CSS overrides to enable pristine serif headings */
.font-serif {
  font-family: 'Cinzel', serif;
}
html {
  scroll-behavior: smooth;
}
</style>
