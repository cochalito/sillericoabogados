<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { 
  Monitor, Phone, MessageSquare, MapPin, 
  ChevronRight, Search, FileText, Scale, Users, Image as ImageIcon, 
  Info, ExternalLink, Mail,
  Facebook, Youtube, Twitter, Menu, X
} from 'lucide-vue-next';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import SplashScreen from '@/components/SplashScreen.vue';

// Splash intro: show only on first visit per session
const showSplash = ref(false);

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    const key = 'sa_splash_shown';

    try {
        if (!window.sessionStorage.getItem(key)) {
            showSplash.value = true;
            window.sessionStorage.setItem(key, '1');
        } else {
            document.documentElement.classList.remove('splash-pending');
        }
    } catch {
        showSplash.value = true;
    }
});

const handleSplashReveal = () => {
    if (typeof window !== 'undefined') {
        document.documentElement.classList.remove('splash-pending');
    }
};

const handleSplashFinish = () => {
    showSplash.value = false;
};

// State for Article/Laws Filter
const searchKeyword = ref('');
const selectedCategory = ref('articulos');

// State for Gallery Carousel
const activeImageIndex = ref(0);
let galleryInterval: any = null;

const startAutoplay = () => {
  stopAutoplay();

  if (typeof window !== 'undefined') {
    galleryInterval = setInterval(() => {
      activeImageIndex.value = (activeImageIndex.value + 1) % galleryImages.length;
    }, 6000);
  }
};

const stopAutoplay = () => {
  if (galleryInterval) {
    clearInterval(galleryInterval);
    galleryInterval = null;
  }
};

const pauseAutoplay = () => {
  stopAutoplay();
};

const resumeAutoplay = () => {
  startAutoplay();
};

const nextSlide = () => {
  activeImageIndex.value = (activeImageIndex.value + 1) % galleryImages.length;
};

const prevSlide = () => {
  activeImageIndex.value = (activeImageIndex.value - 1 + galleryImages.length) % galleryImages.length;
};

const goToSlide = (index: number) => {
  activeImageIndex.value = index;
};

onMounted(() => {
  startAutoplay();
});

onUnmounted(() => {
  stopAutoplay();
});

// Contact Form State
const contactForm = ref({
  name: '',
  email: '',
  phone: '',
  area: 'Derecho Corporativo y Empresarial',
  message: ''
});
const formSubmitted = ref(false);

const submitContactForm = () => {
  formSubmitted.value = true;
  setTimeout(() => {
    formSubmitted.value = false;
    contactForm.value = {
      name: '',
      email: '',
      phone: '',
      area: 'Derecho Corporativo y Empresarial',
      message: ''
    };
  }, 3500);
};

// State and logic for Curtain Navigation Transition (Transición de Cortina entre Menús)
const isCurtainActive = ref(false);
const curtainDirection = ref<'l2r' | 'r2l'>('l2r');
const curtainPhase = ref<'idle' | 'covering' | 'covered' | 'uncovering'>('idle');
const targetMenuLabel = ref('');
const currentSectionHref = ref('#servicios');
const isMobileMenuOpen = ref(false);

const navigateToSection = (targetHref: string, label?: string, forceDirection?: 'l2r' | 'r2l') => {
  if (typeof window === 'undefined') {
return;
}

  const targetId = targetHref.replace('#', '');
  const targetEl = document.getElementById(targetId);

  if (!targetEl) {
return;
}

  isMobileMenuOpen.value = false;

  const foundItem = menuItems.find(item => item.href === targetHref);
  const title = label || foundItem?.label || 'Sección';

  if (forceDirection) {
    curtainDirection.value = forceDirection;
  } else {
    const currentIndex = menuItems.findIndex(item => item.href === currentSectionHref.value);
    const targetIndex = menuItems.findIndex(item => item.href === targetHref);

    if (targetIndex >= 0 && currentIndex >= 0) {
      curtainDirection.value = targetIndex >= currentIndex ? 'l2r' : 'r2l';
    } else {
      curtainDirection.value = 'l2r';
    }
  }

  targetMenuLabel.value = title;
  curtainPhase.value = 'idle';
  isCurtainActive.value = true;

  // Trigger smooth GPU sliding transition sequence (Ampliada a ~1.65s para apreciar logo-main.jpg)
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      curtainPhase.value = 'covering';

      // Stage 1: Curtain covers the screen (650ms)
      setTimeout(() => {
        curtainPhase.value = 'covered';
        
        // Instant scroll behind the curtain
        targetEl.scrollIntoView({ behavior: 'instant' as ScrollBehavior });
        currentSectionHref.value = targetHref;

        // Stage 2: Hold for 350ms to present logo-main.jpg, then slide off screen (650ms)
        setTimeout(() => {
          curtainPhase.value = 'uncovering';

          setTimeout(() => {
            curtainPhase.value = 'idle';
            isCurtainActive.value = false;
          }, 650);
        }, 350);
      }, 650);
    });
  });
};

const scrollToContact = () => {
  navigateToSection('#contacto', 'Contactos');
};

// Reglamento Interno state and structures
const activeTab = ref('mision'); // 'mision' | 'vision' | 'principios'
const isReglamentoModalOpen = ref(false);

const reglamentoTabs = {
  mision: {
    title: 'Misión Institucional',
    content: 'Brindar servicios jurídicos integrales, estratégicos y especializados con excelencia profesional, ética, liderazgo jurídico y compromiso institucional, orientados a la defensa efectiva de los intereses de nuestros clientes, la solución eficiente de conflictos y la consolidación de una firma de abogados moderna, confiable y reconocida por su calidad humana, técnica y académica, promoviendo el trabajo colaborativo.'
  },
  vision: {
    title: 'Visión de Excelencia',
    content: 'Consolidarse como una firma de abogados líder, moderna y prestigiosa a nivel nacional e internacional, reconocida por su excelencia jurídica, liderazgo institucional, ética profesional y capacidad estratégica, integrando un equipo multidisciplinario de abogados altamente especializados comprometidos con la defensa efectiva de los derechos.'
  },
  principios: [
    { title: 'Ética Profesional', desc: 'Actuar bajo estrictos principios morales y rectitud jurídica.' },
    { title: 'Confidencialidad', desc: 'Reserva absoluta sobre clientes, procesos y estrategias.' },
    { title: 'Lealtad', desc: 'Fidelidad inquebrantable a las normas y a nuestros patrocinados.' },
    { title: 'Excelencia', desc: 'Alta calidad técnica, académica y humana en cada caso.' },
    { title: 'Transparencia', desc: 'Claridad en honorarios, procesos e información institucional.' },
    { title: 'Disciplina', desc: 'Rigurosidad, orden y compromiso constante con el derecho.' }
  ]
};

const reglamentoChapters = [
  {
    title: 'Capítulo I: Disposiciones Generales',
    articles: [
      { num: 'Art. 1', title: 'Naturaleza', desc: 'Sillerico & Asociados es una firma jurídica privada dedicada al ejercicio profesional del derecho, asesoría integral y patrocinio legal.' },
      { num: 'Art. 2', title: 'Denominación', desc: 'Uso obligatorio del nombre, logotipo e imagen corporativa para todos los integrantes.' },
      { num: 'Art. 3', title: 'Domicilio y Organización', desc: 'Oficinas centrales en Edif. Park Inn, Piso 6 Of. 66, Calle Federico Suazo y Bueno No. 1598, La Paz.' }
    ]
  },
  {
    title: 'Capítulo II: Estructura Organizacional',
    articles: [
      { num: 'Art. 4', title: 'Dirección General', desc: 'Administración y representación general a cargo del Socio Fundador.' },
      { num: 'Art. 5', title: 'Subdirección', desc: 'Coordinación jurídica y supervisión operativa de la firma.' },
      { num: 'Art. 6', title: 'Abogados Asociados', desc: 'Llevar procesos propios e institucionales utilizando los recursos comunes.' }
    ]
  },
  {
    title: 'Capítulo III: Áreas Comunes y Normas',
    articles: [
      { num: 'Art. 8-10', title: 'Uso de Infraestructura', desc: 'Regulación de la sala de reuniones, recepción y kitchenette. Se prohíbe el consumo de bebidas alcohólicas.' },
      { num: 'Art. 11', title: 'Secretaría y Recepción', desc: 'Preservar absoluta confidencialidad y coordinar agendas de forma profesional.' }
    ]
  },
  {
    title: 'Capítulo VI: Ética y Confidencialidad',
    articles: [
      { num: 'Art. 20', title: 'Confidencialidad Absoluta', desc: 'Toda información de clientes, procesos y estrategias es estrictamente confidencial, incluso tras retirarse de la firma.' },
      { num: 'Art. 21', title: 'Conducta Profesional', desc: 'Respeto mutuo, puntualidad, honestidad y prohibición de dañar la imagen institucional.' }
    ]
  }
];

// Data structures matching user requests
const menuItems = [
  { label: 'Servicios', href: '#servicios', icon: Scale },
  { label: 'Artículos, Normas y Leyes', href: '#articulos', icon: FileText },
  { label: 'Equipo Profesional', href: '#equipo', icon: Users },
  { label: 'Galería', href: '#galeria', icon: ImageIcon },
  { label: 'Sobre la Firma', href: '#firma', icon: Info },
  { label: 'Contactos', href: '#contacto', icon: Mail }
];

const contactLinks = {
  whatsapp: 'https://wa.me/59177234317', // WhatsApp direct link
  facebook: 'https://www.facebook.com/share/18kruBp6GV/?mibextid=wwXIfr',
  phones: ['+591 77234317', '+591 2 2443020'],
  emails: ['contacto@sillericoasociados.com', 'info@sillericoasociados.com'],
  address: 'Av. Mariscal Santa Cruz, Edificio Hansa, Piso 12, La Paz, Bolivia',
  locationUrl: 'https://maps.google.com/?q=Edificio+Hansa+La+Paz'
};

const featuredService = {
  title: 'Derecho Penal y Procesal Penal',
  description: 'Brindamos defensa técnica especializada y asesoría preventiva en materia penal, diseñando estrategias jurídicas orientadas a proteger los derechos de nuestros clientes, minimizar riesgos legales y garantizar una representación efectiva en todas las etapas del proceso penal.',
  icon: '⚜️',
  highlights: ['Litigios penales', 'Delitos corporativos', 'Apelaciones y recursos', 'Derecho procesal penal', 'Criminal compliance']
};

const services = [
  {
    title: 'Derecho Comercial y Corporativo',
    description: 'Acompañamos a empresas y emprendedores en cada etapa de su desarrollo, proporcionando asesoría estratégica, gobierno corporativo, elaboración de contratos y prevención de contingencias legales que fortalezcan la estabilidad y el crecimiento de sus negocios.',
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
    title: 'Derecho Laboral y Seguridad Social',
    description: 'Asesoramos a empleadores y trabajadores en la adecuada gestión de las relaciones laborales, promoviendo el cumplimiento de la normativa vigente, la prevención de conflictos y la defensa eficaz de sus derechos e intereses.',
    icon: '👔',
    highlights: ['Negociación colectiva', 'Seguridad social', 'Defensa patronal']
  },
  {
    title: 'Arbitraje y Resolución de Conflictos',
    description: 'Mediación experta y arbitraje en controversias comerciales nacionales e internacionales como una alternativa ágil y altamente confidencial al litigio tradicional.',
    icon: '🤝',
    highlights: ['Mediación comercial', 'Arbitraje de inversiones', 'Estrategias preventivas']
  },
  {
    title: 'Derecho Constitucional y Derechos Humanos',
    description: 'Protegemos los derechos fundamentales mediante acciones constitucionales, asesoría especializada y estrategias preventivas que aseguran el respeto de las garantías constitucionales y el cumplimiento de los estándares nacionales e internacionales de derechos humanos.',
    icon: '🏛️',
    highlights: ['Amparo constitucional', 'Acciones de libertad', 'Corte Interamericana']
  },
  {
    title: 'Derecho Civil y Contractual',
    description: 'Ofrecemos soluciones jurídicas integrales para la elaboración, revisión y ejecución de contratos, así como la prevención y resolución de conflictos civiles, brindando seguridad jurídica en las relaciones patrimoniales y comerciales.',
    icon: '📜',
    highlights: ['Contratos y obligaciones', 'Derechos reales', 'Responsabilidad civil']
  },
  {
    title: 'Derecho Tributario y Aduanero',
    description: 'Brindamos asesoramiento especializado en planificación tributaria, cumplimiento normativo y defensa administrativa y judicial, orientando a nuestros clientes hacia una gestión fiscal segura, eficiente y preventiva frente a riesgos tributarios y aduaneros.',
    icon: '📊',
    highlights: ['Impugnación tributaria', 'Planificación fiscal', 'Procesos aduaneros']
  }
];

const getServiceWhatsappUrl = (serviceTitle: string) => {
  const message = `Estoy interesado en su servicio de ${serviceTitle}`;

  return `${contactLinks.whatsapp}?text=${encodeURIComponent(message)}`;
};

const publications = ref([
  {
    title: 'Novedades y Cambios en el Marco Regulatorio Financiero en Bolivia',
    date: '15 de Mayo, 2026',
    author: 'Dr. Alan Sillerico Segurondo',
    description: 'Un análisis profundo sobre las nuevas resoluciones de la ASFI y su impacto en la estructuración de créditos y garantías corporativas.',
    category: 'articulos',
    pdf: '/files/marco_regulatorio_bolivia.pdf'
  },
  {
    title: 'El Arbitraje Comercial como Vía Eficiente ante la Burocracia Judicial',
    date: '10 de Abril, 2026',
    author: 'Dra. Anghela Soliz de Sillerico',
    description: 'Estudio de caso sobre cómo las cláusulas arbitrales reducen los tiempos de resolución de controversias entre socios comparado con la justicia ordinaria.',
    category: 'articulos',
    pdf: '/files/arbitraje_comercial.pdf'
  },
  {
    title: 'Guía sobre Incentivos Fiscales para Empresas Exportadoras',
    date: '28 de Marzo, 2026',
    author: 'Dr. Álvaro Arias Antequera',
    description: 'Explicación detallada de exenciones arancelarias e impuestos internos para empresas radicadas en parques industriales nacionales.',
    category: 'normas',
    pdf: '/files/incentivos_fiscales_exportacion.pdf'
  },
  {
    title: 'Estrategias Defensivas en el Litigio Penal y Criminal Compliance',
    date: '15 de Marzo, 2026',
    author: 'Dr. Bismarck Molina',
    description: 'Análisis doctrinario sobre la prevención del delito en personas jurídicas y las mejores prácticas en litigación penal estratégica en tribunales.',
    category: 'articulos',
    pdf: '/files/litigio_penal_compliance.pdf'
  },
  {
    title: 'La Responsabilidad Civil Contractual en la Era de los Contratos Digitales',
    date: '02 de Marzo, 2026',
    author: 'Dr. Mauricio Mercado Foronda',
    description: 'Reflexiones jurídicas sobre el cumplimiento de obligaciones, firmas digitales y validez de contratos mercantiles electrónicos en la jurisprudencia boliviana.',
    category: 'articulos',
    pdf: '/files/responsabilidad_civil_contratos.pdf'
  },
  {
    title: 'Patrocinio en Materia de Trabajo y Defensa Patronal Preventiva',
    date: '20 de Febrero, 2026',
    author: 'Dr. Eduardo Yupanqui Quispe',
    description: 'Estudio de contingencias laborales comunes en la mediana y gran empresa, enfocado en auditorías laborales preventivas y relaciones sindicales.',
    category: 'normas',
    pdf: '/files/defensa_patronal_laboral.pdf'
  },
  {
    title: 'Análisis Constitucional de la Acción de Libertad como Garantía Primaria',
    date: '05 de Febrero, 2026',
    author: 'Dra. Amalia Paucara Mamani',
    description: 'Una revisión exhaustiva del procesamiento del hábeas corpus boliviano y la jurisprudencia relevante emitida por el Tribunal Constitucional Plurinacional.',
    category: 'normas',
    pdf: '/files/accion_libertad_constitucional.pdf'
  }
]);

const currentPage = ref(1);
const itemsPerPage = 4;

const filteredPublications = computed(() => {
  return publications.value.filter(pub => {
    const keyword = searchKeyword.value.toLowerCase();
    const matchesKeyword = pub.title.toLowerCase().includes(keyword) ||
                           pub.description.toLowerCase().includes(keyword) ||
                           pub.author.toLowerCase().includes(keyword);
    
    if (selectedCategory.value === 'todos') {
      return matchesKeyword;
    }

    return pub.category === selectedCategory.value && matchesKeyword;
  });
});

const paginatedPublications = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;

  return filteredPublications.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredPublications.value.length / itemsPerPage) || 1;
});

// Reset pagination page on search keyword or category change
watch([searchKeyword, selectedCategory], () => {
  currentPage.value = 1;
});

const partners = [
  {
    name: 'Dr. Alan Sillerico Segurondo',
    role: 'Abogado Asociado',
    credentials: 'Abogado y académico con más de 25 años de trayectoria profesional. Especialista en litigio complejo, estrategia corporativa y dirección general de casos de alto perfil.',
    image: '/images/avatar_alan.png',
    type: 'Abogado Asociado'
  },
  {
    name: 'Dra. Anghela Soliz de Sillerico',
    role: 'Socia y Subdirectora',
    credentials: 'Licenciada en derecho de la Universidad de Aquino Bolivia UDABOL. Experta en derecho penal y derecho de familia. Cuenta con diplomados en educación superior, derecho penal y procesal penal, derecho constitucional y procesal constitucional, y derecho penal y litigación.',
    image: '/images/avatar_anghela.jpeg',
    type: 'Socia Subdirectora'
  },
  {
    name: 'Dr. Mauricio Mercado Foronda',
    role: 'Abogado Asociado',
    credentials: 'Abogado con amplia trayectoria en derecho civil y procesal civil, egresado de la Maestría en Derecho Civil y Procesal Civil de la Universidad Andina Simón Bolívar. Cuenta con sólida experiencia en litigios civiles, penales, laborales, familiares y administrativos, así como en asesoría legal corporativa y bancaria.',
    image: '/images/avatar_mauricio.jpeg',
    type: 'Abogado Asociado'
  },
  {
    name: 'Dr. Álvaro Arias Antequera',
    role: 'Abogado Asociado',
    credentials: 'Especialista en derecho societario, fusiones, adquisiciones y estructuración comercial. Asesora a empresas nacionales y extranjeras en transacciones corporativas y societarias.',
    image: '/images/avatar_alvaro.jpeg',
    type: 'Abogado Asociado'
  },
  {
    name: 'Dr. Bismarck Molina',
    role: 'Abogado Asociado',
    credentials: 'Especialista en derecho penal, defensa litigante y criminal compliance. Cuenta con una destacada experiencia en litigio penal estratégico y representación en tribunales nacionales.',
    image: '/images/avatar_bismarck.png',
    type: 'Abogado Asociado'
  },
  {
    name: 'Dr. Eduardo Yupanqui Quispe',
    role: 'Abogado Asociado',
    credentials: 'Especializado en derecho laboral, seguridad social y patrocinio en materia de trabajo. Dedicado a la resolución preventiva de conflictos y defensa patronal corporativa.',
    image: '/images/avatar_eduardo.jpeg',
    type: 'Abogado Asociado'
  },
  {
    name: 'Dra. Amalia Paucara Mamani',
    role: 'Abogada Asociada',
    credentials: 'Apoyo legal estratégico en derecho civil e investigación procesal. Destaca por su rigurosidad académica, dinamismo y compromiso con el patrocinio ético de la firma.',
    image: '/images/avatar_amalia.png',
    type: 'Abogada Asociada'
  },
  {
    name: 'Dra. Massiel Rullier Loza',
    role: 'Abogada',
    credentials: 'Asistencia legal especializada en derecho penal y procesal. Encargada del seguimiento estratégico de procesos ante entes reguladores e investigación de jurisprudencia.',
    image: '/images/avatar_massiel.jpeg',
    type: 'Abogada'
  }
];

const visiblePartnersOrder = [
  'Dra. Anghela Soliz de Sillerico',
  'Dr. Mauricio Mercado Foronda',
  'Dr. Eduardo Yupanqui Quispe',
  'Dr. Álvaro Arias Antequera',
  'Dra. Massiel Rullier Loza'
];

const visiblePartners = computed(() => {
  return visiblePartnersOrder
    .map(name => partners.find(p => p.name === name))
    .filter((p): p is typeof partners[number] => Boolean(p));
});

const getPartnerWhatsappUrl = (partnerName: string) => {
  const message = `Hola, quisiera agendar una consulta con ${partnerName}`;

  return `${contactLinks.whatsapp}?text=${encodeURIComponent(message)}`;
};

const galleryImages = [
  {
    title: 'Despacho de Presidencia y Dirección General',
    desc: 'Espacio ejecutivo diseñado con solemnidad e iluminación cálida, concebido para el análisis de casos complejos y la alta dirección estratégica.',
    src: '/images/galeria01.png'
  },
  {
    title: 'Sala Principal de Directorio y Arbitrajes',
    desc: 'Infraestructura corporativa de alto nivel para reuniones del consejo, negociaciones de fusiones y audiencias de mediación confidencial.',
    src: '/images/galeria02.png'
  },
  {
    title: 'Área de Consultoría y Atención a Clientes',
    desc: 'Ambiente distinguido y confortable enfocado en brindar una atención personalizada, privada y con la máxima reserva profesional.',
    src: '/images/galeria03.png'
  },
  {
    title: 'Centro de Análisis e Investigación Jurídica',
    desc: 'Área reservada para el estudio multidisciplinario de doctrinas, jurisprudencia y preparación rigurosa de estrategias procesales.',
    src: '/images/galeria04.png'
  },
  {
    title: 'Recepción Corporativa e Instalaciones',
    desc: 'Instalaciones modernas que combinan la distinción del derecho clásico con la vanguardia arquitectónica en el corazón de La Paz.',
    src: '/images/galeria05.png'
  }
];
</script>

<template>
  <Head title="Sillerico & Asociados - Firma de Abogados en Bolivia">
    <meta name="description" content="Sillerico & Asociados es una firma jurídica líder en Bolivia especializada en Derecho Comercial, Corporativo, Penal, Civil, Laboral, Tributario y Constitucional." />
    <meta name="keywords" content="abogados Bolivia, firma de abogados La Paz, derecho penal, derecho civil, litigios corporativos, Sillerico y Asociados" />
    <meta property="og:title" content="Sillerico & Asociados - Firma de Abogados" />
    <meta property="og:description" content="Asesoría jurídica integral, defensa litigante y visión preventiva en Bolivia." />
    <meta property="og:type" content="website" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  </Head>

  <div class="min-h-screen font-['Plus_Jakarta_Sans',sans-serif] transition-colors duration-300 bg-[#082a20] text-amber-100/90 dark:bg-[#041510] dark:text-amber-100/90">
    
    <!-- CABECERA INTEGRADA EN DOS NIVELES (GLASSMORPHIC) -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-md bg-[#082a20]/95 dark:bg-[#031510]/95 border-b border-[#c5a059]/20 shadow-lg transition-all duration-300 animate-fade-in">
      <div class="max-w-7xl mx-auto px-4 md:px-8 py-3.5 flex items-center gap-4 md:gap-8">
        
        <!-- Logotipo S&A (Gold circular emblem spanning both levels - enlarged and overlapping in flow) -->
        <a href="#" class="flex-shrink-0 group relative z-10">
          <img 
            id="header-logo-box" 
            src="/images/logo-splash.png" 
            alt="Sillerico & Asociados" 
            class="h-32 w-32 md:h-40 md:w-40 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_4px_12px_rgba(0,0,0,0.4)] relative -mt-3 -mb-12 md:-mt-5 md:-mb-16"
            width="160"
            height="160"
            fetchpriority="high"
            decoding="async"
          />
        </a>

        <!-- Contenedor del menú en dos niveles -->
        <div class="flex-1 flex flex-col justify-between py-1 min-h-[64px] md:min-h-[80px]">
          
          <!-- Nivel 1: Enlaces y Redes Sociales (Facebook, WhatsApp, YouTube, X, Teléfono - Alineados a la derecha, estilo Linktree) -->
          <div class="flex items-center justify-end border-b border-[#c5a059]/15 pb-2.5 mb-2 text-xs text-amber-200/80 gap-4 flex-wrap">
            
            <!-- Iconos de Contacto y Redes Sociales (Estilo Linktree burbujas con escala en hover) -->
            <div class="flex items-center gap-2">
              <!-- Teléfono con icono clicable para llamadas (Burbuja Gold estilo Linktree) -->
              <a :href="'tel:' + contactLinks.phones[0].replace(/\s+/g, '')" class="w-7 h-7 rounded-full bg-[#c5a059] flex items-center justify-center text-[#082a20] transition-all duration-300 hover:scale-110 hover:brightness-110 shadow-md" title="Llamar">
                <Phone class="w-3.5 h-3.5" />
              </a>

              <!-- WhatsApp (Burbuja Verde) -->
              <a :href="contactLinks.whatsapp" target="_blank" class="w-7 h-7 rounded-full bg-[#25D366] flex items-center justify-center text-white transition-all duration-300 hover:scale-110 hover:brightness-110 shadow-md" title="WhatsApp">
                <svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                </svg>
              </a>

              <!-- Facebook -->
              <a :href="contactLinks.facebook" target="_blank" class="w-7 h-7 rounded-full bg-[#1877F2] flex items-center justify-center text-white transition-all duration-300 hover:scale-110 hover:brightness-110 shadow-md" title="Facebook">
                <svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>

              <!-- YouTube -->
              <a href="https://youtube.com" target="_blank" class="w-7 h-7 rounded-full bg-[#FF0000] flex items-center justify-center text-white transition-all duration-300 hover:scale-110 hover:brightness-110 shadow-md" title="YouTube">
                <svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.516 0-9.387.507a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.871.507 9.386.507 9.386.507s7.516 0 9.387-.507a3.003 3.003 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
              </a>

              <!-- X (Twitter) -->
              <a href="https://x.com" target="_blank" class="w-7 h-7 rounded-full bg-[#14171A] flex items-center justify-center text-white transition-all duration-300 hover:scale-110 hover:brightness-110 shadow-md" title="X (Twitter)">
                <svg class="w-3 h-3 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
              </a>
              <!-- Sistema (Acceso al Sistema) -->
              <a href="http://localhost:8090/" target="_blank" class="w-7 h-7 rounded-full bg-[#c5a059] hover:bg-[#d6b46c] flex items-center justify-center text-emerald-950 transition-all duration-300 hover:scale-110 shadow-md" title="Acceso al Sistema">
                <Monitor class="w-3.5 h-3.5" />
              </a>
            </div>
          </div>

          <!-- Nivel 2: Menús del Sistema y Botones de Acción -->
          <div class="flex flex-col justify-center">
            <div class="flex items-center justify-between">
              <!-- Navigation Links (Desktop) -->
              <nav class="hidden lg:flex items-center gap-6">
                <a 
                  v-for="item in menuItems" 
                  :key="item.label" 
                  :href="item.href"
                  @click.prevent="navigateToSection(item.href, item.label)"
                  :class="[
                    'text-sm lg:text-base font-[\'Cinzel\',serif] font-bold tracking-wider transition-colors relative cursor-pointer',
                    currentSectionHref === item.href ? 'text-[#c5a059] after:w-full' : 'text-amber-100/90 hover:text-[#c5a059]',
                    'after:absolute after:bottom-[-4px] after:left-0 after:h-[2px] after:bg-[#c5a059] hover:after:w-full after:transition-all'
                  ]"
                >
                  {{ item.label }}
                </a>
              </nav>

              <!-- Navigation Bar (Mobile Header) -->
              <div class="lg:hidden flex items-center justify-between w-full">
                <span class="text-amber-100/90 font-['Cinzel',serif] text-sm font-bold tracking-wider">
                  SILLERICO & ASOCIADOS
                </span>
                <button 
                  @click="isMobileMenuOpen = !isMobileMenuOpen" 
                  class="p-1.5 rounded-lg border border-[#c5a059]/30 bg-[#051f18] text-[#c5a059] hover:bg-[#082a20] transition-colors cursor-pointer"
                  aria-label="Abrir menú"
                >
                  <Menu v-if="!isMobileMenuOpen" class="w-5 h-5" />
                  <X v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div v-if="isMobileMenuOpen" class="lg:hidden mt-3 pt-3 border-t border-[#c5a059]/20 flex flex-col space-y-2 animate-in fade-in slide-in-from-top-2 duration-200">
              <a 
                v-for="item in menuItems" 
                :key="item.label" 
                :href="item.href"
                @click.prevent="navigateToSection(item.href, item.label)"
                :class="[
                  'w-full px-4 py-2.5 rounded-xl font-[\'Cinzel\',serif] text-xs sm:text-sm font-bold tracking-wider transition-all flex items-center gap-3 cursor-pointer border',
                  currentSectionHref === item.href 
                    ? 'bg-[#c5a059]/20 text-[#c5a059] border-[#c5a059]/40 shadow-sm' 
                    : 'bg-[#051f18]/90 text-amber-100/95 border-[#c5a059]/15 hover:bg-[#c5a059]/10 hover:text-[#c5a059]'
                ]"
              >
                <component :is="item.icon" class="w-4 h-4 text-[#c5a059] shrink-0" />
                <span class="whitespace-normal leading-tight">{{ item.label }}</span>
              </a>
            </div>
          </div>

        </div>
      </div>
    </header>

    <!-- 3. SECCIÓN HERO PRINCIPAL -->
    <section class="relative w-full overflow-hidden pt-[5px] pb-10 lg:pt-[5px] lg:pb-12 flex flex-col items-center justify-center bg-[#082a20] border-b border-[#c5a059]/10">
      
      <!-- Fondo sutil de iluminación esmeralda/dorada -->
      <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full bg-[#c5a059]/5 blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full bg-emerald-500/5 blur-3xl"></div>
      </div>

      <div class="max-w-6xl mx-auto px-4 md:px-8 w-full flex flex-col items-center text-center relative z-10 space-y-6">
        
        <!-- Logo Central + Motto Badge Sobrepuesto en la Parte Superior -->
        <div class="relative w-full flex flex-col items-center justify-center pt-2">
          
          <!-- 1. Recuadro de Motto sobrepuesto en la parte superior del Logo (Posicionado adecuadamente debajo del menú) -->
          <div class="z-20 -mb-6 sm:-mb-8 animate-on-reveal delay-100 flex items-center justify-center">
            <div class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full bg-[#051f18]/95 border border-[#c5a059]/45 text-xs sm:text-sm font-semibold text-amber-200/95 shadow-2xl backdrop-blur-md">
              <Scale class="w-4 h-4 text-[#c5a059]" />
              <span class="font-['Cinzel',serif] italic tracking-wider">“Liderazgo jurídico y compromiso institucional”</span>
            </div>
          </div>

          <!-- 2. Logo Central (Perfecto como está) -->
          <div class="animate-on-reveal delay-200 z-10 w-full max-w-2xl md:max-w-3xl lg:max-w-4xl flex items-center justify-center overflow-hidden [clip-path:inset(40px_0_40px_0)] my-[-40px]">
            <img 
              src="/images/logo-main.jpg" 
              alt="Sillerico & Asociados - Emblema Principal" 
              class="w-full h-auto max-h-[380px] sm:max-h-[470px] md:max-h-[560px] lg:max-h-[630px] object-cover object-center filter brightness-110 contrast-110 [mask-image:radial-gradient(ellipse_70%_65%_at_50%_50%,black_25%,transparent_82%)] [-webkit-mask-image:radial-gradient(ellipse_70%_65%_at_50%_50%,black_25%,transparent_82%)] scale-115"
              width="1600"
              height="1600"
              fetchpriority="high"
              decoding="async"
            />
          </div>

        </div>

        <!-- 3. Recuadro de Declaración Institucional + Botones (Con el ancho estándar max-w-6xl) -->
        <div class="-mt-20 sm:-mt-24 md:-mt-28 z-20 relative w-full max-w-6xl bg-[#051f18]/85 border border-[#c5a059]/35 rounded-2xl p-6 md:p-10 space-y-6 shadow-2xl backdrop-blur-md animate-on-reveal delay-300 flex flex-col justify-between">
          
          <!-- Mensaje Institucional de Visión Preventiva -->
          <div class="space-y-4 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 text-xs font-semibold text-[#c5a059] uppercase tracking-widest">
              <Scale class="w-3.5 h-3.5" />
              <span class="font-['Cinzel',serif] text-xs">Visión Jurídica Preventiva</span>
            </div>

            <div class="relative px-2 sm:px-6 md:px-10 py-1">
              <p class="font-['Cinzel',serif] text-sm sm:text-base md:text-lg lg:text-xl text-neutral-100 font-normal leading-relaxed md:leading-loose text-justify sm:text-center">
                En <strong class="text-[#c5a059] font-semibold">SILLERICO & ASOCIADOS</strong> entendemos que <span class="text-amber-200 italic font-semibold">el mejor litigio es aquel que pudo evitarse</span>. Por ello, complementamos nuestra experiencia en representación judicial con una sólida visión preventiva, orientada a identificar riesgos, fortalecer la seguridad jurídica y ofrecer soluciones estratégicas que protejan los intereses de nuestros clientes antes, durante y después de cada decisión.
              </p>
            </div>
          </div>

          <!-- Botones de Acción Integrados al Pie del Recuadro -->
          <div class="pt-6 border-t border-[#c5a059]/20 flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
            <button 
              @click="isReglamentoModalOpen = true"
              class="w-full sm:w-auto bg-transparent border border-[#c5a059] hover:bg-[#c5a059]/10 text-[#c5a059] font-bold px-8 py-3.5 rounded-lg text-sm md:text-base tracking-wider transition-all flex items-center justify-center gap-2.5 cursor-pointer shadow-lg"
            >
              <FileText class="w-5 h-5" />
              <span>Reglamento Interno</span>
            </button>
            <button 
              @click="scrollToContact"
              class="w-full sm:w-auto bg-[#c5a059] hover:bg-[#d6b46c] text-emerald-950 font-bold px-8 py-3.5 rounded-lg text-sm md:text-base tracking-wider transition-all shadow-xl shadow-emerald-950/20 active:scale-98 flex items-center justify-center gap-2.5 cursor-pointer"
            >
              <span>Agendar una Consulta</span>
              <ChevronRight class="w-5 h-5" />
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- 4. SECCIÓN SERVICIOS (INTERACTIVE CARD GRID) -->
    <section id="servicios" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Áreas de Práctica Jurídica</span>
        <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
          Nuestras Especializaciones y Servicios
        </h2>
        <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
        <p class="text-sm md:text-base text-neutral-300 dark:text-neutral-400 font-light">
          Ofrecemos asesoría legal integral con abogados especializados en cada rama del derecho nacional e internacional, brindándote la mayor seguridad jurídica.
        </p>
      </div>

      <!-- Tarjeta de Especialidad Destacada (Derecho Penal y Defensa Litigante) -->
      <div class="mb-12 relative group bg-gradient-to-br from-[#0b3c2e] to-[#051c15] p-8 md:p-10 rounded-2xl border border-[#c5a059] shadow-[0_0_20px_rgba(197,160,89,0.15)] hover:shadow-[0_0_30px_rgba(197,160,89,0.25)] hover:border-[#d6b46c] transition-all duration-500 overflow-hidden">
        <!-- Shine Overlay Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#c5a059]/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
          <!-- Columna Izquierda (Info y Descripción) -->
          <div class="lg:col-span-8 space-y-5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 text-xs font-bold uppercase tracking-widest text-[#c5a059]">
              <span>⭐ Especialización Principal</span>
            </div>
            
            <div class="flex items-start gap-4">
              <div class="size-14 rounded-xl bg-[#c5a059]/20 border border-[#c5a059]/40 flex items-center justify-center text-3xl shrink-0 shadow-lg">
                {{ featuredService.icon }}
              </div>
              <div class="space-y-2">
                <h3 class="font-['Cinzel',serif] text-2xl md:text-3xl font-extrabold text-amber-100 group-hover:text-[#c5a059] transition-colors leading-tight">
                  {{ featuredService.title }}
                </h3>
                <p class="text-sm md:text-base text-neutral-200 font-light leading-relaxed">
                  {{ featuredService.description }}
                </p>
              </div>
            </div>
            
            <div class="pt-4 flex flex-wrap gap-4 items-center">
              <a 
                :href="getServiceWhatsappUrl(featuredService.title)"
                target="_blank"
                class="bg-[#c5a059] hover:bg-[#d6b46c] text-emerald-950 font-bold px-6 py-3 rounded-lg text-xs md:text-sm tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer group/btn"
              >
                <MessageSquare class="w-4 h-4" />
                <span>Contactar</span>
                <ChevronRight class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" />
              </a>
            </div>
          </div>
          
          <!-- Columna Derecha (Highlights Destacados) -->
          <div class="lg:col-span-4 border-t lg:border-t-0 lg:border-l border-[#c5a059]/20 pt-6 lg:pt-0 lg:pl-8 space-y-4">
            <h4 class="font-['Cinzel',serif] text-xs font-bold text-amber-200/70 tracking-widest uppercase">Áreas de Enfoque</h4>
            <ul class="space-y-3">
              <li 
                v-for="hi in featuredService.highlights" 
                :key="hi"
                class="flex items-center gap-3 text-sm text-neutral-200 font-light"
              >
                <span class="size-2 rounded-full bg-[#c5a059] shrink-0"></span>
                <span>{{ hi }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Grid para las otras 8 especialidades -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="service in services" 
          :key="service.title"
          class="group relative bg-[#0b3629]/60 dark:bg-[#071619] p-8 rounded-xl border border-[#c5a059]/20 dark:border-neutral-800 hover:border-[#c5a059]/50 dark:hover:border-[#c5a059]/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden"
        >
          <!-- Hover effect back panel -->
          <div class="absolute inset-0 bg-[#082a20]/0 group-hover:bg-[#082a20]/2 dark:group-hover:bg-[#c5a059]/2 transition-colors duration-300"></div>

          <div class="space-y-4 z-10">
            <div class="size-12 rounded-lg bg-[#c5a059]/15 dark:bg-[#c5a059]/10 border border-[#c5a059]/20 dark:border-[#c5a059]/20 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
              {{ service.icon }}
            </div>
            
            <h3 class="font-['Cinzel',serif] text-lg font-bold text-amber-100 dark:text-white group-hover:text-[#c5a059] dark:group-hover:text-[#c5a059] transition-colors">
              {{ service.title }}
            </h3>
            
            <p class="text-sm text-neutral-300 dark:text-neutral-300 font-light leading-relaxed">
              {{ service.description }}
            </p>
          </div>

          <!-- Bullet Highlights -->
          <div class="mt-6 pt-4 border-t border-[#c5a059]/15 dark:border-neutral-800/80 z-10 flex flex-wrap gap-2">
            <span 
              v-for="hi in service.highlights" 
              :key="hi"
              class="text-[10px] font-semibold bg-[#082a20]/40 dark:bg-neutral-800 px-2 py-0.5 rounded text-amber-200/90 dark:text-[#c5a059]"
            >
              {{ hi }}
            </span>
          </div>

          <!-- Botón Contactar al pie del cuadro -->
          <div class="mt-6 pt-4 border-t border-[#c5a059]/15 z-10">
            <a 
              :href="getServiceWhatsappUrl(service.title)"
              target="_blank"
              class="w-full bg-[#082a20] hover:bg-[#c5a059] text-[#c5a059] hover:text-emerald-950 font-bold px-4 py-2.5 rounded-lg text-xs md:text-sm tracking-wider transition-all duration-300 border border-[#c5a059]/40 hover:border-[#c5a059] shadow-md flex items-center justify-center gap-2 cursor-pointer group/btn"
            >
              <MessageSquare class="w-4 h-4" />
              <span>Contactar</span>
              <ChevronRight class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" />
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. SECCIÓN ARTÍCULOS, NORMAS Y LEYES (DYNAMIC FILTER SYSTEM) -->
    <section id="articulos" class="py-20 md:py-28 bg-[#051c16] border-y border-[#c5a059]/15">
      <div class="max-w-7xl mx-auto px-4 md:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
          <div class="space-y-3">
            <span id="normas" class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Artículos, Normas y Leyes</span>
            <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
              Centro de Análisis Jurídico
            </h2>
            <div class="w-16 h-[2px] bg-[#c5a059]"></div>
          </div>

          <!-- Live search input -->
          <div class="relative w-full md:w-80">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-amber-200/60" />
            <input 
              v-model="searchKeyword"
              type="text" 
              placeholder="Buscar artículos o leyes..."
              class="w-full text-xs bg-[#0b3629]/50 border border-[#c5a059]/20 rounded-lg py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white"
            />
          </div>
        </div>

        <!-- Segmented Button Group (Radio Group style) -->
        <div class="mb-8 flex justify-start">
          <div class="inline-flex p-1.5 bg-[#051f18]/80 dark:bg-neutral-900 border border-[#c5a059]/30 rounded-2xl shadow-xl backdrop-blur-md gap-1 flex-wrap sm:flex-nowrap">
            <!-- 1. Artículos Doctrinarios -->
            <button 
              @click="selectedCategory = 'articulos'"
              :class="[
                'px-4 sm:px-6 py-2 rounded-xl text-xs sm:text-sm font-semibold tracking-wider transition-all duration-300 flex items-center gap-2 cursor-pointer',
                selectedCategory === 'articulos' 
                  ? 'bg-gradient-to-r from-[#c5a059] to-[#d6b46c] text-emerald-950 font-bold shadow-md shadow-[#c5a059]/20 scale-[1.02]' 
                  : 'text-amber-200/80 hover:text-amber-100 hover:bg-[#0b3629]/50'
              ]"
            >
              <span 
                class="size-2 rounded-full transition-all duration-300 shrink-0"
                :class="selectedCategory === 'articulos' ? 'bg-emerald-950 scale-110' : 'bg-[#c5a059]/40'"
              ></span>
              <span>Artículos Doctrinarios</span>
            </button>

            <!-- 2. Leyes & Compendios -->
            <button 
              @click="selectedCategory = 'normas'"
              :class="[
                'px-4 sm:px-6 py-2 rounded-xl text-xs sm:text-sm font-semibold tracking-wider transition-all duration-300 flex items-center gap-2 cursor-pointer',
                selectedCategory === 'normas' 
                  ? 'bg-gradient-to-r from-[#c5a059] to-[#d6b46c] text-emerald-950 font-bold shadow-md shadow-[#c5a059]/20 scale-[1.02]' 
                  : 'text-amber-200/80 hover:text-amber-100 hover:bg-[#0b3629]/50'
              ]"
            >
              <span 
                class="size-2 rounded-full transition-all duration-300 shrink-0"
                :class="selectedCategory === 'normas' ? 'bg-emerald-950 scale-110' : 'bg-[#c5a059]/40'"
              ></span>
              <span>Leyes & Compendios</span>
            </button>

            <!-- 3. Todos -->
            <button 
              @click="selectedCategory = 'todos'"
              :class="[
                'px-4 sm:px-6 py-2 rounded-xl text-xs sm:text-sm font-semibold tracking-wider transition-all duration-300 flex items-center gap-2 cursor-pointer',
                selectedCategory === 'todos' 
                  ? 'bg-gradient-to-r from-[#c5a059] to-[#d6b46c] text-emerald-950 font-bold shadow-md shadow-[#c5a059]/20 scale-[1.02]' 
                  : 'text-amber-200/80 hover:text-amber-100 hover:bg-[#0b3629]/50'
              ]"
            >
              <span 
                class="size-2 rounded-full transition-all duration-300 shrink-0"
                :class="selectedCategory === 'todos' ? 'bg-emerald-950 scale-110' : 'bg-[#c5a059]/40'"
              ></span>
              <span>Todos</span>
            </button>
          </div>
        </div>

        <!-- Dynamic Grid List -->
        <!-- Dynamic Grid List (Card format) -->
        <div v-if="paginatedPublications.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div 
            v-for="pub in paginatedPublications" 
            :key="pub.title"
            class="bg-[#0b3629]/60 p-6 md:p-8 rounded-xl border border-[#c5a059]/15 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
          >
            <div class="space-y-4">
              <!-- Top Row: Author & Date -->
              <div class="flex justify-between items-center text-[10px] tracking-wider uppercase font-bold text-amber-200/80">
                <span class="text-[#c5a059]">Por: {{ pub.author }}</span>
                <span class="bg-[#082a20] text-amber-200 px-2.5 py-0.5 rounded text-[10px] font-semibold">{{ pub.date }}</span>
              </div>
              
              <!-- Title -->
              <h3 class="font-['Cinzel',serif] text-base md:text-lg font-bold text-neutral-100 dark:text-white leading-tight">
                {{ pub.title }}
              </h3>
              
              <!-- Description -->
              <p class="text-xs md:text-sm text-neutral-300 dark:text-neutral-300 font-light leading-relaxed">
                {{ pub.description }}
              </p>
            </div>

            <!-- Bottom Row: Download PDF Button -->
            <div class="mt-6 pt-4 border-t border-[#c5a059]/15 flex justify-end">
              <a 
                :href="pub.pdf" 
                download
                class="inline-flex items-center gap-2 bg-[#c5a059] hover:bg-[#d6b46c] text-emerald-950 px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-md active:scale-95 cursor-pointer"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Descargar PDF</span>
              </a>
            </div>
          </div>
        </div>

        <!-- No Results Fallback -->
        <div v-else class="text-center py-16 bg-[#0b3629]/30 rounded-xl border border-dashed border-[#c5a059]/20">
          <FileText class="w-12 h-12 text-[#c5a059] mx-auto mb-4 opacity-50" />
          <h3 class="font-semibold text-lg text-white">No se hallaron resultados</h3>
          <p class="text-sm text-neutral-300 dark:text-neutral-400">Intenta utilizar otras palabras clave en la barra de búsqueda.</p>
        </div>

        <!-- Controles de Paginación -->
        <div v-if="totalPages > 1" class="mt-12 flex items-center justify-center gap-4">
          <button 
            @click="currentPage > 1 ? currentPage-- : null"
            :disabled="currentPage === 1"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase transition-all border flex items-center gap-1.5 cursor-pointer',
              currentPage === 1 
                ? 'opacity-40 border-neutral-700 text-neutral-500 cursor-not-allowed' 
                : 'border-[#c5a059]/30 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 text-[#c5a059]'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Anterior</span>
          </button>
          
          <span class="text-xs text-neutral-400 font-medium">
            Página <strong class="text-white">{{ currentPage }}</strong> de <strong class="text-white">{{ totalPages }}</strong>
          </span>
          
          <button 
            @click="currentPage < totalPages ? currentPage++ : null"
            :disabled="currentPage === totalPages"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase transition-all border flex items-center gap-1.5 cursor-pointer',
              currentPage === totalPages 
                ? 'opacity-40 border-neutral-700 text-neutral-500 cursor-not-allowed' 
                : 'border-[#c5a059]/30 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 text-[#c5a059]'
            ]"
          >
            <span>Siguiente</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- 6. SECCIÓN EQUIPO PROFESIONAL (SENIOR PARTNERS) -->
    <section id="equipo" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Nuestro Talento Humano</span>
        <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
          Socios y Abogados Litigantes
        </h2>
        <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
        <p class="text-sm md:text-base text-neutral-300 dark:text-neutral-400 font-light">
          Un cuerpo legal multidisciplinar y de amplia reputación académica nacional encargado de defender sus derechos corporativos y personales.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div 
          v-for="partner in visiblePartners" 
          :key="partner.name"
          class="bg-[#0b3629]/60 dark:bg-[#071619]/60 rounded-xl border border-[#c5a059]/20 dark:border-neutral-800/80 p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
        >
          <div class="space-y-6">
            <!-- Professional Portrait Image -->
            <div class="relative w-full aspect-square rounded-lg bg-[#082a20]/45 dark:bg-[#061e18]/45 border border-[#c5a059]/30 overflow-hidden shadow-inner group-hover:border-[#c5a059] transition-all duration-300">
              <img 
                v-if="partner.image" 
                :src="partner.image" 
                :alt="partner.name"
                class="w-full h-full object-cover object-top group-hover:scale-[1.04] transition-transform duration-500" 
                width="400"
                height="400"
                loading="lazy"
                decoding="async"
              />
              <div 
                v-else
                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#082a20] to-[#041510]"
              >
                <span class="font-['Cinzel',serif] text-5xl md:text-6xl font-bold text-[#c5a059]/40 group-hover:scale-105 group-hover:text-[#c5a059] transition-all duration-500 select-none">
                  {{ partner.initials }}
                </span>
              </div>
              
              <!-- Subtle overlay on hover -->
              <div class="absolute inset-0 bg-gradient-to-t from-[#051f18]/65 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

              <!-- Decorative Scales detail in background (only when showing initials) -->
              <svg v-if="!partner.image" class="absolute bottom-2 right-2 w-16 h-16 text-white/2 opacity-[0.03]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm-1 3h2v2h-2V7zm0 4h2v6h-2v-6z"/>
              </svg>
            </div>

            <div class="space-y-2">
              <h3 class="font-['Cinzel',serif] text-base font-bold text-neutral-100 dark:text-[#c5a059] group-hover:text-amber-500 transition-colors leading-tight">
                {{ partner.name }}
              </h3>
              <p class="text-xs uppercase tracking-wider font-semibold text-neutral-300 dark:text-neutral-300 min-h-[32px] flex items-center">
                {{ partner.role }}
              </p>
              <div class="w-8 h-[2px] bg-[#c5a059]"></div>
            </div>

            <p class="text-xs text-neutral-300 dark:text-neutral-300 font-light leading-relaxed">
              {{ partner.credentials }}
            </p>
          </div>

          <div class="mt-6 pt-4 border-t border-[#c5a059]/15 dark:border-neutral-800/80 flex items-center justify-between">
            <span class="text-[10px] text-neutral-400 uppercase tracking-widest font-semibold">{{ partner.type || 'Socio' }}</span>
            <a 
              :href="getPartnerWhatsappUrl(partner.name)"
              target="_blank"
              class="text-xs font-bold text-[#c5a059] hover:underline flex items-center gap-1 cursor-pointer"
            >
              <span>Agendar cita</span>
              <ChevronRight class="w-3.5 h-3.5" />
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. SECCIÓN GALERÍA INSTITUCIONAL (PREMIUM GRID) -->
    <section id="galeria" class="py-20 md:py-28 bg-[#051c16] border-y border-[#c5a059]/15">
      <div class="max-w-7xl mx-auto px-4 md:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Infraestructura Corporativa</span>
          <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
            Nuestras Instalaciones en La Paz
          </h2>
          <div class="w-16 h-[2px] bg-[#c5a059] mx-auto mt-2"></div>
          <p class="text-sm md:text-base text-neutral-300 dark:text-neutral-400 font-light">
            Instalaciones corporativas de primer nivel diseñadas para ofrecerle la mayor privacidad y sofisticación durante sus visitas de negocios.
          </p>
        </div>

        <!-- Carrusel Interactivo de Instalaciones -->
        <div 
          class="relative w-full max-w-5xl mx-auto h-[350px] sm:h-[450px] md:h-[550px] rounded-2xl border border-[#c5a059]/30 overflow-hidden shadow-2xl group/carousel select-none bg-emerald-950/40"
          @mouseenter="pauseAutoplay"
          @mouseleave="resumeAutoplay"
        >
          <!-- Slides -->
          <div class="w-full h-full relative">
            <div 
              v-for="(img, idx) in galleryImages" 
              :key="img.title"
              :class="[
                'absolute inset-0 w-full h-full transition-all duration-700 ease-in-out',
                activeImageIndex === idx ? 'opacity-100 z-10 scale-100' : 'opacity-0 z-0 scale-105 pointer-events-none'
              ]"
            >
              <!-- Background Image -->
              <img 
                :src="img.src" 
                :alt="img.title"
                class="w-full h-full object-cover" 
                width="1400"
                height="900"
                loading="lazy"
                decoding="async"
              />
              
              <!-- Gradient Overlay (Bottom text overlay) -->
              <div class="absolute inset-0 bg-gradient-to-t from-[#041510] via-[#041510]/50 to-transparent z-10"></div>
              
              <!-- Content Overlay (Overimposed at the bottom) -->
              <div class="absolute bottom-0 inset-x-0 p-8 sm:p-12 z-20 flex flex-col justify-end text-left space-y-3">
                <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Instalación {{ idx + 1 }} de {{ galleryImages.length }}</span>
                <h3 class="font-['Cinzel',serif] text-xl sm:text-2xl md:text-3xl font-bold text-white leading-tight">
                  {{ img.title }}
                </h3>
                <p class="text-sm sm:text-base text-neutral-200 font-light max-w-3xl leading-relaxed">
                  {{ img.desc }}
                </p>
              </div>
            </div>
          </div>

          <!-- Left/Right Navigation Arrows -->
          <button 
            @click="prevSlide"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-30 size-10 sm:size-12 rounded-full bg-[#051f18]/85 border border-[#c5a059]/30 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 text-[#c5a059] flex items-center justify-center transition-all duration-300 opacity-0 group-hover/carousel:opacity-100 cursor-pointer shadow-lg"
            aria-label="Anterior"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <button 
            @click="nextSlide"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-30 size-10 sm:size-12 rounded-full bg-[#051f18]/85 border border-[#c5a059]/30 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 text-[#c5a059] flex items-center justify-center transition-all duration-300 opacity-0 group-hover/carousel:opacity-100 cursor-pointer shadow-lg"
            aria-label="Siguiente"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <!-- Navigation Indicator Dots (Bottom-Right overlay) -->
          <div class="absolute bottom-6 right-8 sm:right-12 z-30 flex gap-2.5">
            <button 
              v-for="(_, idx) in galleryImages" 
              :key="idx"
              @click="goToSlide(idx)"
              :class="[
                'h-2 rounded-full transition-all duration-300 cursor-pointer',
                activeImageIndex === idx ? 'w-8 bg-[#c5a059]' : 'w-2 bg-white/40 hover:bg-white/70'
              ]"
              :aria-label="'Ir a imagen ' + (idx + 1)"
            ></button>
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
            <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
              Nuestra Historia
            </h2>
            <div class="w-16 h-[2px] bg-[#c5a059]"></div>
          </div>

          <!-- Recuadro contenedor de Nuestra Historia con scroll personalizado -->
          <div class="max-h-[300px] overflow-y-auto pr-3 space-y-4 text-xs sm:text-sm md:text-base text-neutral-300 dark:text-neutral-300 leading-relaxed font-light text-justify bg-[#0b3629]/40 border border-[#c5a059]/20 rounded-xl p-4 sm:p-5 shadow-inner custom-gold-scrollbar">
            <p>
              <strong class="text-[#c5a059] font-semibold">SILLERICO & ASOCIADOS – FIRMA DE ABOGADOS</strong> es el resultado de más de dieciséis años de experiencia profesional, dedicación y compromiso con la defensa de los derechos e intereses de personas, empresas e instituciones.
            </p>

            <p>
              Nuestra historia comienza con el ejercicio independiente del Derecho por parte de nuestro Director General, Alan Sillerico Segurondo, quien inició su trayectoria profesional con una marcada orientación hacia el Derecho Penal, área en la que consolidó una amplia experiencia en la defensa técnica, la litigación oral y la asesoría jurídica especializada.
            </p>

            <p>
              A lo largo de los años, el ejercicio constante de la profesión, la formación académica permanente y la confianza depositada por nuestros clientes permitieron ampliar progresivamente el ámbito de actuación profesional. Lo que inició como una práctica especializada en materia penal evolucionó hacia una atención jurídica integral, incorporando servicios en Derecho Constitucional, Civil, Comercial, Laboral, Administrativo, Tributario, Familiar y otras áreas del ordenamiento jurídico boliviano.
            </p>

            <p>
              Esta evolución respondió a una convicción clara: ofrecer a cada cliente soluciones jurídicas completas, estratégicas y de alta calidad, manteniendo siempre los principios de ética, responsabilidad, confidencialidad y excelencia profesional.
            </p>

            <p>
              Como consecuencia natural de ese crecimiento nació <strong class="text-[#c5a059] font-semibold">SILLERICO & ASOCIADOS – FIRMA DE ABOGADOS</strong>, concebida como una organización jurídica moderna que integra experiencia, especialización y trabajo en equipo. La firma reúne profesionales comprometidos con la prestación de servicios legales de excelencia, orientados tanto a la prevención como a la solución eficaz de conflictos jurídicos.
            </p>
          </div>

          <!-- Tabbed Misión, Visión, Principios System -->
          <div class="mt-6 bg-[#0b3629]/60 border border-[#c5a059]/25 rounded-2xl p-6 space-y-5 shadow-xl backdrop-blur-sm">
            <div class="flex justify-start border-b border-[#c5a059]/20 pb-2 gap-3 overflow-x-auto">
              <button 
                @click="activeTab = 'mision'"
                :class="[
                  'px-5 py-2 text-xs sm:text-sm font-bold font-[\'Cinzel\',serif] tracking-wider transition-all border-b-2 cursor-pointer whitespace-nowrap',
                  activeTab === 'mision' 
                    ? 'text-[#c5a059] border-[#c5a059] bg-[#c5a059]/10 rounded-t-lg' 
                    : 'text-neutral-400 border-transparent hover:text-amber-100'
                ]"
              >
                Misión
              </button>
              <button 
                @click="activeTab = 'vision'"
                :class="[
                  'px-5 py-2 text-xs sm:text-sm font-bold font-[\'Cinzel\',serif] tracking-wider transition-all border-b-2 cursor-pointer whitespace-nowrap',
                  activeTab === 'vision' 
                    ? 'text-[#c5a059] border-[#c5a059] bg-[#c5a059]/10 rounded-t-lg' 
                    : 'text-neutral-400 border-transparent hover:text-amber-100'
                ]"
              >
                Visión
              </button>
              <button 
                @click="activeTab = 'principios'"
                :class="[
                  'px-5 py-2 text-xs sm:text-sm font-bold font-[\'Cinzel\',serif] tracking-wider transition-all border-b-2 cursor-pointer whitespace-nowrap',
                  activeTab === 'principios' 
                    ? 'text-[#c5a059] border-[#c5a059] bg-[#c5a059]/10 rounded-t-lg' 
                    : 'text-neutral-400 border-transparent hover:text-amber-100'
                ]"
              >
                Principios
              </button>
            </div>

            <!-- Tab Content Misión / Visión -->
            <div v-if="activeTab === 'mision' || activeTab === 'vision'" class="space-y-2 min-h-[120px] flex flex-col justify-center transition-all duration-300">
              <h4 class="font-['Cinzel',serif] text-xs font-bold text-[#c5a059] tracking-widest uppercase">
                {{ reglamentoTabs[activeTab].title }}
              </h4>
              <p class="text-xs sm:text-sm text-neutral-200 font-light leading-relaxed text-justify">
                {{ reglamentoTabs[activeTab].content }}
              </p>
            </div>

            <!-- Tab Content Principios -->
            <div v-else-if="activeTab === 'principios'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-h-[120px] items-center transition-all duration-300">
              <div 
                v-for="p in reglamentoTabs.principios" 
                :key="p.title"
                class="flex flex-col p-3 bg-[#082a20]/90 rounded-xl border border-[#c5a059]/20 shadow-sm"
              >
                <span class="text-xs font-bold text-[#c5a059] font-['Cinzel',serif]">{{ p.title }}</span>
                <span class="text-[11px] text-neutral-300 font-light mt-1 leading-normal">{{ p.desc }}</span>
              </div>
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

    <!-- 9. SECCIÓN CONTACTO Y UBICACIÓN (INTEGRATED ON PAGE - SPLIT SCREEN) -->
    <section id="contacto" class="py-20 md:py-28 max-w-7xl mx-auto px-4 md:px-8 border-t border-[#c5a059]/15 animate-on-reveal">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">
        
        <!-- Columna Izquierda: Consulta Rápida (Formulario) (7 columnas) -->
        <div class="lg:col-span-7 bg-[#0b3629]/40 dark:bg-[#071619]/40 rounded-2xl border border-[#c5a059]/20 p-8 sm:p-10 flex flex-col justify-center shadow-xl backdrop-blur-sm">
          <div class="text-left mb-8 space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Contacto Inmediato</span>
            <h2 class="font-['Cinzel',serif] text-3xl md:text-4xl font-bold text-neutral-100 dark:text-white">
              Consulta Rápida
            </h2>
            <div class="w-12 h-[2px] bg-[#c5a059] mt-2"></div>
            <p class="text-sm text-neutral-300 dark:text-neutral-400 font-light mt-3 leading-relaxed">
              Completa tus datos básicos para agendar una reunión o recibir asesoramiento legal por parte de nuestros abogados especialistas.
            </p>
          </div>

          <form @submit.prevent="submitContactForm" class="space-y-5" v-if="!formSubmitted">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div class="space-y-1.5 text-left">
                <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-400 dark:text-neutral-400">Nombre Completo</label>
                <input 
                  v-model="contactForm.name"
                  type="text" 
                  required
                  placeholder="Ej. Juan Pérez"
                  class="w-full text-xs sm:text-sm bg-[#051f18]/40 dark:bg-neutral-800 border border-[#c5a059]/20 dark:border-neutral-700 rounded-lg p-3 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white"
                />
              </div>

              <div class="space-y-1.5 text-left">
                <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-400 dark:text-neutral-400">Correo Electrónico</label>
                <input 
                  v-model="contactForm.email"
                  type="email" 
                  required
                  placeholder="juan@ejemplo.com"
                  class="w-full text-xs sm:text-sm bg-[#051f18]/40 dark:bg-neutral-800 border border-[#c5a059]/20 dark:border-neutral-700 rounded-lg p-3 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div class="space-y-1.5 text-left">
                <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-400 dark:text-neutral-400">Teléfono / WhatsApp</label>
                <input 
                  v-model="contactForm.phone"
                  type="tel" 
                  required
                  placeholder="+591 700 00000"
                  class="w-full text-xs sm:text-sm bg-[#051f18]/40 dark:bg-neutral-800 border border-[#c5a059]/20 dark:border-neutral-700 rounded-lg p-3 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white"
                />
              </div>

              <div class="space-y-1.5 text-left">
                <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-400 dark:text-neutral-400">Área de Interés</label>
                <select 
                  v-model="contactForm.area"
                  class="w-full text-xs sm:text-sm bg-[#051f18]/40 dark:bg-neutral-800 border border-[#c5a059]/20 dark:border-neutral-700 rounded-lg p-3 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white"
                >
                  <option>Derecho Comercial y Corporativo</option>
                  <option>Derecho de Familia y Sucesiones</option>
                  <option>Propiedad Intelectual y Patentes</option>
                  <option>Derecho Penal y Procesal Penal</option>
                  <option>Derecho Laboral y Seguridad Social</option>
                  <option>Arbitraje y Resolución de Conflictos</option>
                  <option>Derecho Constitucional y Derechos Humanos</option>
                  <option>Derecho Civil y Contractual</option>
                  <option>Derecho Tributario y Aduanero</option>
                </select>
              </div>
            </div>

            <div class="space-y-1.5 text-left">
              <label class="text-[10px] uppercase font-bold tracking-widest text-neutral-400 dark:text-neutral-400">Mensaje / Detalle de la Consulta</label>
              <textarea 
                v-model="contactForm.message"
                required
                rows="4"
                placeholder="Describe brevemente tu caso o consulta legal..."
                class="w-full text-xs sm:text-sm bg-[#051f18]/40 dark:bg-neutral-800 border border-[#c5a059]/20 dark:border-neutral-700 rounded-lg p-3 focus:outline-none focus:border-[#c5a059] transition-all text-neutral-100 dark:text-white resize-none"
              ></textarea>
            </div>

            <button 
              type="submit"
              class="w-full sm:w-auto bg-[#c5a059] hover:bg-[#d6b46c] text-emerald-950 font-bold px-10 py-4 rounded-lg text-xs sm:text-sm tracking-wider border border-[#c5a059]/30 transition-all uppercase cursor-pointer"
            >
              Enviar Solicitud
            </button>
          </form>

          <!-- Success Message -->
          <div v-else class="py-16 text-center space-y-4 animate-in fade-in duration-200">
            <div class="size-16 rounded-full bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-500/30 flex items-center justify-center mx-auto text-emerald-600 dark:text-emerald-400 text-3xl">
              ✓
            </div>
            <h4 class="font-['Cinzel',serif] text-base font-bold text-neutral-800 dark:text-[#c5a059]">¡Solicitud Recibida!</h4>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-xs mx-auto font-light leading-relaxed">Un asesor legal de Sillerico & Asociados se contactará contigo por teléfono o WhatsApp de forma inmediata.</p>
          </div>
        </div>

        <!-- Columna Derecha: Ubicación e Información de Oficina (5 columnas) -->
        <div class="lg:col-span-5 bg-[#051f18] text-amber-100/90 rounded-2xl border border-[#c5a059]/25 p-8 sm:p-10 flex flex-col justify-between shadow-xl">
          <div class="space-y-6">
            <div class="space-y-2 text-left">
              <span class="text-xs font-bold uppercase tracking-widest text-[#c5a059]">Oficinas & Contactos</span>
              <h2 class="font-['Cinzel',serif] text-3xl font-bold text-white">Nuestra Ubicación</h2>
              <div class="w-12 h-[2px] bg-[#c5a059] mt-2"></div>
            </div>

            <div class="space-y-5 text-left">
              <div class="flex items-start gap-4">
                <div class="size-10 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 flex items-center justify-center text-[#c5a059] shrink-0">
                  <MapPin class="w-5 h-5" />
                </div>
                <div class="space-y-1">
                  <span class="text-[10px] uppercase font-bold tracking-widest text-amber-200/50 block">Dirección Principal</span>
                  <p class="text-xs sm:text-sm text-neutral-200 leading-relaxed font-light">
                    Av. Mariscal Santa Cruz, Edificio Hansa, Piso 12, La Paz, Bolivia.
                  </p>
                </div>
              </div>

              <div class="flex items-start gap-4">
                <div class="size-10 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 flex items-center justify-center text-[#c5a059] shrink-0">
                  <Phone class="w-5 h-5" />
                </div>
                <div class="space-y-1">
                  <span class="text-[10px] uppercase font-bold tracking-widest text-amber-200/50 block">Teléfonos de Contacto</span>
                  <p class="text-xs sm:text-sm text-neutral-200 leading-relaxed font-light">
                    +591 77234317 / +591 2 2443020
                  </p>
                </div>
              </div>

              <div class="flex items-start gap-4">
                <div class="size-10 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 flex items-center justify-center text-[#c5a059] shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="space-y-1">
                  <span class="text-[10px] uppercase font-bold tracking-widest text-amber-200/50 block">Horario de Atención</span>
                  <p class="text-xs sm:text-sm text-neutral-200 leading-relaxed font-light">
                    Lunes a Viernes: 09:00 - 18:30
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Google Map Iframe (Interactive map) -->
          <div class="mt-8 overflow-hidden rounded-xl border border-[#c5a059]/30 shadow-lg">
            <iframe 
              src="https://maps.google.com/maps?q=-16.4973762,-68.1359219(Edificio%20Hansa)&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed" 
              class="w-full h-48 sm:h-56 md:h-64 border-0" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>

      </div>
    </section>

    <!-- 10. PIE DE PÁGINA (COMPLETE INTEGRATED CONTACT & MAP) -->
    <footer class="bg-[#041510] text-[#eae5da] dark:bg-[#020a0d] border-t border-[#c5a059]/20 pt-16 pb-8 text-xs md:text-sm">
      <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">
        
        <!-- Corporate Presentation -->
        <div class="md:col-span-4 space-y-4">
          <div class="flex items-center gap-2.5">
            <img 
              src="/images/logo-splash.png" 
              alt="Sillerico & Asociados" 
              class="h-8 w-8 object-contain"
              width="32"
              height="32"
              loading="lazy"
              decoding="async"
            />
            <span class="font-['Cinzel',serif] text-base font-bold tracking-wider text-[#c5a059]">SILLERICO & ASOCIADOS</span>
          </div>
          
          <p class="text-neutral-400 font-light leading-relaxed">
            Consorcio Jurídico especializado en la defensa corporativa, patrimonial y asesoría mercantil de empresas. Brindando certeza de forma integral en el país.
          </p>

          <div class="flex items-center gap-3 pt-2">
            <a :href="contactLinks.whatsapp" target="_blank" class="size-7 rounded-full bg-[#051f18] hover:bg-[#082a20] border border-[#c5a059]/20 flex items-center justify-center text-white transition-colors" title="WhatsApp">
              <MessageSquare class="w-3.5 h-3.5 text-emerald-400" />
            </a>
            <a :href="contactLinks.facebook" target="_blank" class="size-7 rounded-full bg-[#051f18] hover:bg-[#082a20] border border-[#c5a059]/20 flex items-center justify-center text-white transition-colors" title="Facebook">
              <Facebook class="w-3.5 h-3.5 text-[#c5a059]" />
            </a>
            <a href="https://youtube.com" target="_blank" class="size-7 rounded-full bg-[#051f18] hover:bg-[#082a20] border border-[#c5a059]/20 flex items-center justify-center text-white transition-colors" title="YouTube">
              <Youtube class="w-3.5 h-3.5 text-[#c5a059]" />
            </a>
            <a href="https://x.com" target="_blank" class="size-7 rounded-full bg-[#051f18] hover:bg-[#082a20] border border-[#c5a059]/20 flex items-center justify-center text-white transition-colors" title="X (Twitter)">
              <Twitter class="w-3.5 h-3.5 text-[#c5a059]" />
            </a>
          </div>
        </div>

        <!-- Sitemap Quick Links -->
        <div class="md:col-span-3 space-y-4">
          <h3 class="font-['Cinzel',serif] text-sm font-bold tracking-widest text-[#c5a059] uppercase">Mapa de Sitio</h3>
          <ul class="space-y-2">
            <li v-for="item in menuItems" :key="item.label">
              <a 
                :href="item.href" 
                @click.prevent="navigateToSection(item.href, item.label)"
                class="text-neutral-400 hover:text-[#c5a059] transition-colors flex items-center gap-1.5 font-light cursor-pointer"
              >
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



    <!-- 11. DIALOG MODAL FOR REGLAMENTO INTERNO -->
    <div v-if="isReglamentoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/80 backdrop-blur-sm">
      <div 
        class="bg-[#051f18] text-[#eae5da] rounded-xl border border-[#c5a059]/30 max-w-2xl w-full p-6 relative shadow-2xl animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[85vh]"
        @click.stop
      >
        <button 
          @click="isReglamentoModalOpen = false" 
          class="absolute top-4 right-4 text-amber-100/60 hover:text-white text-lg font-bold cursor-pointer"
        >
          ✕
        </button>

        <div class="text-center mb-4 space-y-2 shrink-0">
          <div class="size-10 rounded-full bg-[#c5a059]/10 border border-[#c5a059]/30 flex items-center justify-center mx-auto text-xl text-[#c5a059]">
            📜
          </div>
          <h3 class="font-['Cinzel',serif] text-base md:text-lg font-bold text-[#c5a059]">Reglamento Interno de Trabajo</h3>
          <p class="text-xs text-neutral-400 font-light">Sillerico & Asociados – Consorcio Jurídico</p>
        </div>

        <div class="overflow-y-auto pr-2 space-y-6 flex-1 custom-scrollbar">
          <div v-for="ch in reglamentoChapters" :key="ch.title" class="space-y-3">
            <h4 class="font-['Cinzel',serif] text-xs md:text-sm font-bold text-[#c5a059] border-b border-[#c5a059]/20 pb-1 uppercase tracking-wider">
              {{ ch.title }}
            </h4>
            <div class="space-y-3 pl-2">
              <div v-for="art in ch.articles" :key="art.num" class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="text-[10px] font-bold text-[#c5a059] bg-[#c5a059]/15 px-1.5 py-0.5 rounded uppercase tracking-widest">{{ art.num }}</span>
                  <span class="text-xs font-semibold text-white font-['Cinzel',serif]">{{ art.title }}</span>
                </div>
                <p class="text-xs text-neutral-300 font-light leading-relaxed pl-1">
                  {{ art.desc }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-[#c5a059]/20 flex justify-end shrink-0">
          <button 
            @click="isReglamentoModalOpen = false"
            class="bg-[#c5a059] hover:bg-[#d6b46c] text-emerald-950 font-bold px-6 py-2 rounded text-xs tracking-wider transition-all cursor-pointer"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <SplashScreen :show="showSplash" @reveal="handleSplashReveal" @finish="handleSplashFinish" />

    <!-- 12. CURTAIN TRANSITION OVERLAY (TRANSICIÓN DE CORTINA ENTRE MENÚS) -->
    <div 
      v-if="isCurtainActive" 
      class="fixed inset-0 z-[9999] pointer-events-auto overflow-hidden flex items-center justify-center"
    >
      <!-- Sliding Panel -->
      <div 
        class="absolute inset-0 bg-gradient-to-br from-[#03150f] via-[#082a20] to-[#020d09] flex flex-col items-center justify-center shadow-2xl transition-transform duration-700 ease-[cubic-bezier(0.77,0,0.175,1)]"
        :style="{
          transform: 
            curtainDirection === 'l2r'
              ? (curtainPhase === 'idle' ? 'translateX(-100%)' : (curtainPhase === 'uncovering' ? 'translateX(100%)' : 'translateX(0%)'))
              : (curtainPhase === 'idle' ? 'translateX(100%)' : (curtainPhase === 'uncovering' ? 'translateX(-100%)' : 'translateX(0%)'))
        }"
      >
        <!-- Shining Gold Leading Edge Line -->
        <div 
          :class="[
            'absolute top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#c5a059] via-amber-200 to-[#c5a059] shadow-[0_0_35px_#c5a059]',
            curtainDirection === 'l2r' ? 'right-0' : 'left-0'
          ]"
        ></div>

        <!-- Faint Radial Background Glow -->
        <div class="absolute w-[600px] h-[600px] rounded-full bg-[#c5a059]/15 blur-3xl pointer-events-none"></div>

        <!-- Center Vertical Logo Image inside Curtain (logo-main.jpg sin texto) -->
        <div class="relative z-10 flex items-center justify-center p-4 animate-in fade-in zoom-in-95 duration-300">
          <div class="relative max-h-[70vh] max-w-[85vw] sm:max-w-md rounded-2xl bg-[#051f18]/90 border-2 border-[#c5a059]/50 shadow-[0_0_60px_rgba(197,160,89,0.45)] flex items-center justify-center p-3 backdrop-blur-md overflow-hidden">
            <img 
              src="/images/logo-main.jpg" 
              alt="Sillerico & Asociados Logo Principal" 
              class="max-h-[55vh] sm:max-h-[60vh] w-auto object-contain rounded-xl filter drop-shadow-[0_6px_20px_rgba(0,0,0,0.7)]"
              width="800"
              height="800"
              loading="lazy"
              decoding="async"
            />
          </div>
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

/* Justificación tipográfica uniforme en todos los párrafos del sitio */
p {
  text-align: justify;
  text-justify: inter-word;
}
.text-center p, p.text-center {
  text-align: center;
}

/* Fade in animation (no translate Y transition for perfect logo landing) */
.animate-fade-in {
  opacity: 0;
  transition: opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1);
}
html:not(.splash-pending) .animate-fade-in {
  opacity: 1;
}

/* Staggered entrance animations on reveal */
.animate-on-reveal {
  opacity: 0;
  transform: translateY(16px);
  transition: opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1), transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
}

/* When the splash screen is not pending, animate in */
html:not(.splash-pending) .animate-on-reveal {
  opacity: 1;
  transform: translateY(0);
}

/* Delay/Stagger classes */
.delay-100 {
  transition-delay: 100ms;
}
.delay-200 {
  transition-delay: 200ms;
}
.delay-300 {
  transition-delay: 300ms;
}
.delay-400 {
  transition-delay: 400ms;
}
.delay-500 {
  transition-delay: 500ms;
}
.delay-600 {
  transition-delay: 600ms;
}

/* Custom Gold & Emerald Scrollbar */
.custom-gold-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-gold-scrollbar::-webkit-scrollbar-track {
  background: rgba(5, 31, 24, 0.6);
  border-radius: 9999px;
}
.custom-gold-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(197, 160, 89, 0.45);
  border-radius: 9999px;
  border: 1px solid rgba(197, 160, 89, 0.25);
}
.custom-gold-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(214, 180, 108, 0.85);
  box-shadow: 0 0 8px rgba(197, 160, 89, 0.6);
}
/* Firefox support */
.custom-gold-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(197, 160, 89, 0.45) rgba(5, 31, 24, 0.6);
}

/* If the user has prefers-reduced-motion, disable the movement but keep the fade */
@media (prefers-reduced-motion: reduce) {
  .animate-on-reveal {
    transform: none !important;
    transition-duration: 0.3s !important;
    transition-delay: 0s !important;
  }
}
</style>
