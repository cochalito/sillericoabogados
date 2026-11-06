<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Monitor,
    Phone,
    MessageSquare,
    MapPin,
    ChevronRight,
    Search,
    FileText,
    Scale,
    Users,
    Image as ImageIcon,
    Info,
    ExternalLink,
    Mail,
    Facebook,
    Youtube,
    Twitter,
    Menu,
    X,
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
            activeImageIndex.value =
                (activeImageIndex.value + 1) % galleryImages.length;
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
    activeImageIndex.value =
        (activeImageIndex.value + 1) % galleryImages.length;
};

const prevSlide = () => {
    activeImageIndex.value =
        (activeImageIndex.value - 1 + galleryImages.length) %
        galleryImages.length;
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
    message: '',
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
            message: '',
        };
    }, 3500);
};

// State and logic for Curtain Navigation Transition (Transición de Cortina entre Menús)
const isCurtainActive = ref(false);
const curtainDirection = ref<'l2r' | 'r2l'>('l2r');
const curtainPhase = ref<'idle' | 'covering' | 'covered' | 'uncovering'>(
    'idle',
);
const targetMenuLabel = ref('');
const currentSectionHref = ref('#servicios');
const isMobileMenuOpen = ref(false);

const navigateToSection = (
    targetHref: string,
    label?: string,
    forceDirection?: 'l2r' | 'r2l',
) => {
    if (typeof window === 'undefined') {
        return;
    }

    const targetId = targetHref.replace('#', '');
    const targetEl = document.getElementById(targetId);

    if (!targetEl) {
        return;
    }

    isMobileMenuOpen.value = false;

    const foundItem = menuItems.find((item) => item.href === targetHref);
    const title = label || foundItem?.label || 'Sección';

    if (forceDirection) {
        curtainDirection.value = forceDirection;
    } else {
        const currentIndex = menuItems.findIndex(
            (item) => item.href === currentSectionHref.value,
        );
        const targetIndex = menuItems.findIndex(
            (item) => item.href === targetHref,
        );

        if (targetIndex >= 0 && currentIndex >= 0) {
            curtainDirection.value =
                targetIndex >= currentIndex ? 'l2r' : 'r2l';
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
                targetEl.scrollIntoView({
                    behavior: 'instant' as ScrollBehavior,
                });
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
        content:
            'Brindar servicios jurídicos integrales, estratégicos y especializados con excelencia profesional, ética, liderazgo jurídico y compromiso institucional, orientados a la defensa efectiva de los intereses de nuestros clientes, la solución eficiente de conflictos y la consolidación de una firma de abogados moderna, confiable y reconocida por su calidad humana, técnica y académica, promoviendo el trabajo colaborativo.',
    },
    vision: {
        title: 'Visión de Excelencia',
        content:
            'Consolidarse como una firma de abogados líder, moderna y prestigiosa a nivel nacional e internacional, reconocida por su excelencia jurídica, liderazgo institucional, ética profesional y capacidad estratégica, integrando un equipo multidisciplinario de abogados altamente especializados comprometidos con la defensa efectiva de los derechos.',
    },
    principios: [
        {
            title: 'Ética Profesional',
            desc: 'Actuar bajo estrictos principios morales y rectitud jurídica.',
        },
        {
            title: 'Confidencialidad',
            desc: 'Reserva absoluta sobre clientes, procesos y estrategias.',
        },
        {
            title: 'Lealtad',
            desc: 'Fidelidad inquebrantable a las normas y a nuestros patrocinados.',
        },
        {
            title: 'Excelencia',
            desc: 'Alta calidad técnica, académica y humana en cada caso.',
        },
        {
            title: 'Transparencia',
            desc: 'Claridad en honorarios, procesos e información institucional.',
        },
        {
            title: 'Disciplina',
            desc: 'Rigurosidad, orden y compromiso constante con el derecho.',
        },
    ],
};

const reglamentoChapters = [
    {
        title: 'Capítulo I: Disposiciones Generales',
        articles: [
            {
                num: 'Art. 1',
                title: 'Naturaleza',
                desc: 'Sillerico & Asociados es una firma jurídica privada dedicada al ejercicio profesional del derecho, asesoría integral y patrocinio legal.',
            },
            {
                num: 'Art. 2',
                title: 'Denominación',
                desc: 'Uso obligatorio del nombre, logotipo e imagen corporativa para todos los integrantes.',
            },
            {
                num: 'Art. 3',
                title: 'Domicilio y Organización',
                desc: 'Oficinas centrales en Edif. Park Inn, Piso 6 Of. 66, Calle Federico Suazo y Bueno No. 1598, La Paz.',
            },
        ],
    },
    {
        title: 'Capítulo II: Estructura Organizacional',
        articles: [
            {
                num: 'Art. 4',
                title: 'Dirección General',
                desc: 'Administración y representación general a cargo del Socio Fundador.',
            },
            {
                num: 'Art. 5',
                title: 'Subdirección',
                desc: 'Coordinación jurídica y supervisión operativa de la firma.',
            },
            {
                num: 'Art. 6',
                title: 'Abogados Asociados',
                desc: 'Llevar procesos propios e institucionales utilizando los recursos comunes.',
            },
        ],
    },
    {
        title: 'Capítulo III: Áreas Comunes y Normas',
        articles: [
            {
                num: 'Art. 8-10',
                title: 'Uso de Infraestructura',
                desc: 'Regulación de la sala de reuniones, recepción y kitchenette. Se prohíbe el consumo de bebidas alcohólicas.',
            },
            {
                num: 'Art. 11',
                title: 'Secretaría y Recepción',
                desc: 'Preservar absoluta confidencialidad y coordinar agendas de forma profesional.',
            },
        ],
    },
    {
        title: 'Capítulo VI: Ética y Confidencialidad',
        articles: [
            {
                num: 'Art. 20',
                title: 'Confidencialidad Absoluta',
                desc: 'Toda información de clientes, procesos y estrategias es estrictamente confidencial, incluso tras retirarse de la firma.',
            },
            {
                num: 'Art. 21',
                title: 'Conducta Profesional',
                desc: 'Respeto mutuo, puntualidad, honestidad y prohibición de dañar la imagen institucional.',
            },
        ],
    },
];

// Data structures matching user requests
const menuItems = [
    { label: 'Servicios', href: '#servicios', icon: Scale },
    { label: 'Artículos, Normas y Leyes', href: '#articulos', icon: FileText },
    { label: 'Equipo Profesional', href: '#equipo', icon: Users },
    { label: 'Galería', href: '#galeria', icon: ImageIcon },
    { label: 'Sobre la Firma', href: '#firma', icon: Info },
    { label: 'Contactos', href: '#contacto', icon: Mail },
];

const contactLinks = {
    whatsapp: 'https://wa.me/59177234317', // WhatsApp direct link
    facebook: 'https://www.facebook.com/share/18kruBp6GV/?mibextid=wwXIfr',
    phones: ['2900080', '2900370'],
    fax: '2900370',
    whatsappNumber: '+591 77234317',
    emails: ['contacto@sillericoasociados.com', 'info@sillericoasociados.com'],
    address:
        'Edificio Park Inn, Piso 6, Of. 66, Calle Federico Suazo y Bueno, No. 1598 de la ciudad de La Paz - Bolivia.',
    locationUrl: 'https://maps.google.com/?q=Edificio+Park+Inn+La+Paz',
};

const featuredService = {
    title: 'Derecho Penal y Procesal Penal',
    description:
        'Brindamos defensa técnica especializada y asesoría preventiva en materia penal, diseñando estrategias jurídicas orientadas a proteger los derechos de nuestros clientes, minimizar riesgos legales y garantizar una representación efectiva en todas las etapas del proceso penal.',
    icon: '⚜️',
    highlights: [
        'Litigios penales',
        'Delitos corporativos',
        'Apelaciones y recursos',
        'Derecho procesal penal',
        'Criminal compliance',
    ],
};

const services = [
    {
        title: 'Derecho Comercial y Corporativo',
        description:
            'Acompañamos a empresas y emprendedores en cada etapa de su desarrollo, proporcionando asesoría estratégica, gobierno corporativo, elaboración de contratos y prevención de contingencias legales que fortalezcan la estabilidad y el crecimiento de sus negocios.',
        icon: '⚖️',
        highlights: [
            'Gobierno corporativo',
            'Contratos comerciales',
            'Propiedad societaria',
        ],
    },
    {
        title: 'Derecho de Familia y Sucesiones',
        description:
            'Gestión experta en procesos familiares con la máxima reserva, divorcios, custodias, testamentos, partición de herencias y sucesiones complejas.',
        icon: '🏛️',
        highlights: [
            'Planificación sucesoria',
            'Procesos de divorcio',
            'Acuerdos prematrimoniales',
        ],
    },
    {
        title: 'Propiedad Intelectual y Patentes',
        description:
            'Registro de marcas, patentes de invención, derechos de autor, secretos industriales y litigios de protección de propiedad intelectual en el país e internacional.',
        icon: '🛡️',
        highlights: [
            'Registro de marcas',
            'Derechos de autor',
            'Secretos comerciales',
        ],
    },
    {
        title: 'Derecho Laboral y Seguridad Social',
        description:
            'Asesoramos a empleadores y trabajadores en la adecuada gestión de las relaciones laborales, promoviendo el cumplimiento de la normativa vigente, la prevención de conflictos y la defensa eficaz de sus derechos e intereses.',
        icon: '👔',
        highlights: [
            'Negociación colectiva',
            'Seguridad social',
            'Defensa patronal',
        ],
    },
    {
        title: 'Arbitraje y Resolución de Conflictos',
        description:
            'Mediación experta y arbitraje en controversias comerciales nacionales e internacionales como una alternativa ágil y altamente confidencial al litigio tradicional.',
        icon: '🤝',
        highlights: [
            'Mediación comercial',
            'Arbitraje de inversiones',
            'Estrategias preventivas',
        ],
    },
    {
        title: 'Derecho Constitucional y Derechos Humanos',
        description:
            'Protegemos los derechos fundamentales mediante acciones constitucionales, asesoría especializada y estrategias preventivas que aseguran el respeto de las garantías constitucionales y el cumplimiento de los estándares nacionales e internacionales de derechos humanos.',
        icon: '🏛️',
        highlights: [
            'Amparo constitucional',
            'Acciones de libertad',
            'Corte Interamericana',
        ],
    },
    {
        title: 'Derecho Civil y Contractual',
        description:
            'Ofrecemos soluciones jurídicas integrales para la elaboración, revisión y ejecución de contratos, así como la prevención y resolución de conflictos civiles, brindando seguridad jurídica en las relaciones patrimoniales y comerciales.',
        icon: '📜',
        highlights: [
            'Contratos y obligaciones',
            'Derechos reales',
            'Responsabilidad civil',
        ],
    },
    {
        title: 'Derecho Tributario y Aduanero',
        description:
            'Brindamos asesoramiento especializado en planificación tributaria, cumplimiento normativo y defensa administrativa y judicial, orientando a nuestros clientes hacia una gestión fiscal segura, eficiente y preventiva frente a riesgos tributarios y aduaneros.',
        icon: '📊',
        highlights: [
            'Impugnación tributaria',
            'Planificación fiscal',
            'Procesos aduaneros',
        ],
    },
];

const getServiceWhatsappUrl = (serviceTitle: string) => {
    const message = `Estoy interesado en su servicio de ${serviceTitle}`;

    return `${contactLinks.whatsapp}?text=${encodeURIComponent(message)}`;
};

interface PublicationItem {
    id?: number;
    codigo?: string;
    title: string;
    date?: string;
    author?: string;
    description?: string;
    category: string;
    tipo?: string;
    tipo_label?: string;
    has_file?: boolean;
    file_type?: string;
    pdf?: string | null;
}

const props = defineProps<{
    publications?: PublicationItem[];
}>();

const defaultPublications: PublicationItem[] = [
    {
        title: 'Novedades y Cambios en el Marco Regulatorio Financiero en Bolivia',
        date: '15 de Mayo, 2026',
        author: 'Dr. Alan Sillerico Segurondo',
        description:
            'Un análisis profundo sobre las nuevas resoluciones de la ASFI y su impacto en la estructuración de créditos y garantías corporativas.',
        category: 'articulos',
        pdf: '/files/marco_regulatorio_bolivia.pdf',
    },
    {
        title: 'El Arbitraje Comercial como Vía Eficiente ante la Burocracia Judicial',
        date: '10 de Abril, 2026',
        author: 'Dra. Anghela Soliz de Sillerico',
        description:
            'Estudio de caso sobre cómo las cláusulas arbitrales reducen los tiempos de resolución de controversias entre socios comparado con la justicia ordinaria.',
        category: 'articulos',
        pdf: '/files/arbitraje_comercial.pdf',
    },
    {
        title: 'Guía sobre Incentivos Fiscales para Empresas Exportadoras',
        date: '28 de Marzo, 2026',
        author: 'Dr. Álvaro Arias Antequera',
        description:
            'Explicación detallada de exenciones arancelarias e impuestos internos para empresas radicadas en parques industriales nacionales.',
        category: 'normas',
        pdf: '/files/incentivos_fiscales_exportacion.pdf',
    },
    {
        title: 'Estrategias Defensivas en el Litigio Penal y Criminal Compliance',
        date: '15 de Marzo, 2026',
        author: 'Dr. Bismarck Molina',
        description:
            'Análisis doctrinario sobre la prevención del delito en personas jurídicas y las mejores prácticas en litigación penal estratégica en tribunales.',
        category: 'articulos',
        pdf: '/files/litigio_penal_compliance.pdf',
    },
    {
        title: 'La Responsabilidad Civil Contractual en la Era de los Contratos Digitales',
        date: '02 de Marzo, 2026',
        author: 'Dr. Mauricio Mercado Foronda',
        description:
            'Reflexiones jurídicas sobre el cumplimiento de obligaciones, firmas digitales y validez de contratos mercantiles electrónicos en la jurisprudencia boliviana.',
        category: 'articulos',
        pdf: '/files/responsabilidad_civil_contratos.pdf',
    },
    {
        title: 'Patrocinio en Materia de Trabajo y Defensa Patronal Preventiva',
        date: '20 de Febrero, 2026',
        author: 'Dr. Eduardo Yupanqui Quispe',
        description:
            'Estudio de contingencias laborales comunes en la mediana y gran empresa, enfocado en auditorías laborales preventivas y relaciones sindicales.',
        category: 'normas',
        pdf: '/files/defensa_patronal_laboral.pdf',
    },
    {
        title: 'Análisis Constitucional de la Acción de Libertad como Garantía Primaria',
        date: '05 de Febrero, 2026',
        author: 'Dra. Amalia Paucara Mamani',
        description:
            'Una revisión exhaustiva del procesamiento del hábeas corpus boliviano y la jurisprudencia relevante emitida por el Tribunal Constitucional Plurinacional.',
        category: 'normas',
        pdf: '/files/accion_libertad_constitucional.pdf',
    },
];

const publications = computed<PublicationItem[]>(() => {
    if (props.publications && props.publications.length > 0) {
        return props.publications;
    }

    return defaultPublications;
});

const currentPage = ref(1);
const itemsPerPage = 6;

const filteredPublications = computed(() => {
    const keyword = searchKeyword.value.toLowerCase().trim();

    return publications.value.filter((pub) => {
        const title = (pub.title || '').toLowerCase();
        const description = (pub.description || '').toLowerCase();
        const author = (pub.author || '').toLowerCase();
        const codigo = (pub.codigo || '').toLowerCase();

        const matchesKeyword =
            !keyword ||
            title.includes(keyword) ||
            description.includes(keyword) ||
            author.includes(keyword) ||
            codigo.includes(keyword);

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
        credentials:
            'Abogado y académico con más de 25 años de trayectoria profesional. Especialista en litigio complejo, estrategia corporativa y dirección general de casos de alto perfil.',
        image: '/images/avatar_alan.png',
        type: 'Abogado Asociado',
    },
    {
        name: 'Dra. Anghela Soliz de Sillerico',
        role: 'Socia y Subdirectora',
        credentials:
            'Licenciada en derecho de la Universidad de Aquino Bolivia UDABOL. Experta en derecho penal y derecho de familia. Cuenta con diplomados en educación superior, derecho penal y procesal penal, derecho constitucional y procesal constitucional, y derecho penal y litigación.',
        image: '/images/avatar_anghela.jpeg',
        type: 'Socia Subdirectora',
    },
    {
        name: 'Dr. Mauricio Mercado Foronda',
        role: 'Abogado Asociado',
        credentials:
            'Abogado con amplia trayectoria en derecho civil y procesal civil, egresado de la Maestría en Derecho Civil y Procesal Civil de la Universidad Andina Simón Bolívar. Cuenta con sólida experiencia en litigios civiles, penales, laborales, familiares y administrativos, así como en asesoría legal corporativa y bancaria.',
        image: '/images/avatar_mauricio.jpeg',
        type: 'Abogado Asociado',
    },
    {
        name: 'Dr. Álvaro Arias Antequera',
        role: 'Abogado Asociado',
        credentials:
            'Especialista en derecho societario, fusiones, adquisiciones y estructuración comercial. Asesora a empresas nacionales y extranjeras en transacciones corporativas y societarias.',
        image: '/images/avatar_alvaro.jpeg',
        type: 'Abogado Asociado',
    },
    {
        name: 'Dr. Bismarck Molina',
        role: 'Abogado Asociado',
        credentials:
            'Especialista en derecho penal, defensa litigante y criminal compliance. Cuenta con una destacada experiencia en litigio penal estratégico y representación en tribunales nacionales.',
        image: '/images/avatar_bismarck.png',
        type: 'Abogado Asociado',
    },
    {
        name: 'Dr. Eduardo Yupanqui Quispe',
        role: 'Abogado Asociado',
        credentials:
            'Especializado en derecho laboral, seguridad social y patrocinio en materia de trabajo. Dedicado a la resolución preventiva de conflictos y defensa patronal corporativa.',
        image: '/images/avatar_eduardo.jpeg',
        type: 'Abogado Asociado',
    },
    {
        name: 'Dra. Amalia Paucara Mamani',
        role: 'Abogada Asociada',
        credentials:
            'Apoyo legal estratégico en derecho civil e investigación procesal. Destaca por su rigurosidad académica, dinamismo y compromiso con el patrocinio ético de la firma.',
        image: '/images/avatar_amalia.png',
        type: 'Abogada Asociada',
    },
    {
        name: 'Dra. Massiel Rullier Loza',
        role: 'Abogada',
        credentials:
            'Asistencia legal especializada en derecho penal y procesal. Encargada del seguimiento estratégico de procesos ante entes reguladores e investigación de jurisprudencia.',
        image: '/images/avatar_massiel.jpeg',
        type: 'Abogada',
    },
];

const visiblePartnersOrder = [
    'Dra. Anghela Soliz de Sillerico',
    'Dr. Mauricio Mercado Foronda',
    'Dr. Eduardo Yupanqui Quispe',
    'Dr. Álvaro Arias Antequera',
    'Dra. Massiel Rullier Loza',
];

const visiblePartners = computed(() => {
    return visiblePartnersOrder
        .map((name) => partners.find((p) => p.name === name))
        .filter((p): p is (typeof partners)[number] => Boolean(p));
});

const getPartnerWhatsappUrl = (partnerName: string) => {
    const message = `Hola, quisiera agendar una consulta con ${partnerName}`;

    return `${contactLinks.whatsapp}?text=${encodeURIComponent(message)}`;
};

const getPartnerInitials = (name: string): string => {
    const clean = name.replace(/^(Dr\.|Dra\.)\s+/i, '').trim();
    const parts = clean.split(/\s+/);

    if (parts.length >= 2) {
        return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase();
    }

    return (parts[0] ? parts[0].slice(0, 2) : 'SA').toUpperCase();
};

const galleryImages = [
    {
        title: 'Despacho de Presidencia y Dirección General',
        desc: 'Espacio ejecutivo diseñado con solemnidad e iluminación cálida, concebido para el análisis de casos complejos y la alta dirección estratégica.',
        src: '/images/galeria01.png',
    },
    {
        title: 'Sala Principal de Directorio y Arbitrajes',
        desc: 'Infraestructura corporativa de alto nivel para reuniones del consejo, negociaciones de fusiones y audiencias de mediación confidencial.',
        src: '/images/galeria02.png',
    },
    {
        title: 'Área de Consultoría y Atención a Clientes',
        desc: 'Ambiente distinguido y confortable enfocado en brindar una atención personalizada, privada y con la máxima reserva profesional.',
        src: '/images/galeria03.png',
    },
    {
        title: 'Centro de Análisis e Investigación Jurídica',
        desc: 'Área reservada para el estudio multidisciplinario de doctrinas, jurisprudencia y preparación rigurosa de estrategias procesales.',
        src: '/images/galeria04.png',
    },
    {
        title: 'Recepción Corporativa e Instalaciones',
        desc: 'Instalaciones modernas que combinan la distinción del derecho clásico con la vanguardia arquitectónica en el corazón de La Paz.',
        src: '/images/galeria05.png',
    },
];
</script>

<template>
    <Head title="Sillerico & Asociados - Firma de Abogados en Bolivia">
        <meta
            name="description"
            content="Sillerico & Asociados es una firma jurídica líder en Bolivia especializada en Derecho Comercial, Corporativo, Penal, Civil, Laboral, Tributario y Constitucional."
        />
        <meta
            name="keywords"
            content="abogados Bolivia, firma de abogados La Paz, derecho penal, derecho civil, litigios corporativos, Sillerico y Asociados"
        />
        <meta
            property="og:title"
            content="Sillerico & Asociados - Firma de Abogados"
        />
        <meta
            property="og:description"
            content="Asesoría jurídica integral, defensa litigante y visión preventiva en Bolivia."
        />
        <meta property="og:type" content="website" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="min-h-screen bg-[#082a20] font-['Plus_Jakarta_Sans',sans-serif] text-amber-100/90 transition-colors duration-300 dark:bg-[#041510] dark:text-amber-100/90"
    >
        <!-- CABECERA INTEGRADA EN DOS NIVELES (GLASSMORPHIC) -->
        <header
            class="animate-fade-in sticky top-0 z-40 w-full border-b border-[#c5a059]/20 bg-[#082a20]/95 shadow-lg backdrop-blur-md transition-all duration-300 dark:bg-[#031510]/95"
        >
            <div
                class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3.5 md:gap-8 md:px-8"
            >
                <!-- Logotipo S&A (Gold circular emblem spanning both levels - enlarged and overlapping in flow) -->
                <a href="#" class="group relative z-10 flex-shrink-0">
                    <img
                        id="header-logo-box"
                        src="/images/logo-splash.png"
                        alt="Sillerico & Asociados"
                        class="relative -mt-3 -mb-12 h-32 w-32 object-contain drop-shadow-[0_4px_12px_rgba(0,0,0,0.4)] transition-transform duration-300 group-hover:scale-105 md:-mt-5 md:-mb-16 md:h-40 md:w-40"
                        width="160"
                        height="160"
                        fetchpriority="high"
                        decoding="async"
                    />
                </a>

                <!-- Contenedor del menú en dos niveles -->
                <div
                    class="flex min-h-[64px] flex-1 flex-col justify-between py-1 md:min-h-[80px]"
                >
                    <!-- Nivel 1: Enlaces y Redes Sociales (Facebook, WhatsApp, YouTube, X, Teléfono - Alineados a la derecha, estilo Linktree) -->
                    <div
                        class="mb-2 flex flex-wrap items-center justify-end gap-4 border-b border-[#c5a059]/15 pb-2.5 text-xs text-amber-200/80"
                    >
                        <!-- Iconos de Contacto y Redes Sociales (Estilo Linktree burbujas con escala en hover) -->
                        <div class="flex items-center gap-2">
                            <!-- Teléfono con icono clicable para llamadas (Burbuja Gold estilo Linktree) -->
                            <a
                                :href="
                                    'tel:' +
                                    contactLinks.phones[0].replace(/\s+/g, '')
                                "
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#c5a059] text-[#082a20] shadow-md transition-all duration-300 hover:scale-110 hover:brightness-110"
                                title="Llamar"
                            >
                                <Phone class="h-3.5 w-3.5" />
                            </a>

                            <!-- WhatsApp (Burbuja Verde) -->
                            <a
                                :href="contactLinks.whatsapp"
                                target="_blank"
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#25D366] text-white shadow-md transition-all duration-300 hover:scale-110 hover:brightness-110"
                                title="WhatsApp"
                            >
                                <svg
                                    class="h-3.5 w-3.5 fill-white"
                                    viewBox="0 0 16 16"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"
                                    />
                                </svg>
                            </a>

                            <!-- Facebook -->
                            <a
                                :href="contactLinks.facebook"
                                target="_blank"
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#1877F2] text-white shadow-md transition-all duration-300 hover:scale-110 hover:brightness-110"
                                title="Facebook"
                            >
                                <svg
                                    class="h-3.5 w-3.5 fill-white"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                    />
                                </svg>
                            </a>

                            <!-- YouTube -->
                            <a
                                href="https://youtube.com"
                                target="_blank"
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#FF0000] text-white shadow-md transition-all duration-300 hover:scale-110 hover:brightness-110"
                                title="YouTube"
                            >
                                <svg
                                    class="h-3.5 w-3.5 fill-white"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.516 0-9.387.507a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.871.507 9.386.507 9.386.507s7.516 0 9.387-.507a3.003 3.003 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                    />
                                </svg>
                            </a>

                            <!-- X (Twitter) -->
                            <a
                                href="https://x.com"
                                target="_blank"
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#14171A] text-white shadow-md transition-all duration-300 hover:scale-110 hover:brightness-110"
                                title="X (Twitter)"
                            >
                                <svg
                                    class="h-3 w-3 fill-white"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                                    />
                                </svg>
                            </a>
                            <!-- Sistema (Acceso al Sistema) -->
                            <a
                                href="https://sistema.sillericoasociados.com"
                                target="_blank"
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#c5a059] text-emerald-950 shadow-md transition-all duration-300 hover:scale-110 hover:bg-[#d6b46c]"
                                title="Acceso al Sistema"
                            >
                                <Monitor class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>

                    <!-- Nivel 2: Menús del Sistema y Botones de Acción -->
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center justify-between">
                            <!-- Navigation Links (Desktop) -->
                            <nav class="hidden items-center gap-6 lg:flex">
                                <a
                                    v-for="item in menuItems"
                                    :key="item.label"
                                    :href="item.href"
                                    @click.prevent="
                                        navigateToSection(item.href, item.label)
                                    "
                                    :class="[
                                        'relative cursor-pointer font-[\'Cinzel\',serif] text-sm font-bold tracking-wider transition-colors lg:text-base',
                                        currentSectionHref === item.href
                                            ? 'text-[#c5a059] after:w-full'
                                            : 'text-amber-100/90 hover:text-[#c5a059]',
                                        'after:absolute after:bottom-[-4px] after:left-0 after:h-[2px] after:bg-[#c5a059] after:transition-all hover:after:w-full',
                                    ]"
                                >
                                    {{ item.label }}
                                </a>
                            </nav>

                            <!-- Navigation Bar (Mobile Header) -->
                            <div
                                class="flex w-full items-center justify-between lg:hidden"
                            >
                                <span
                                    class="font-['Cinzel',serif] text-sm font-bold tracking-wider text-amber-100/90"
                                >
                                    SILLERICO & ASOCIADOS
                                </span>
                                <button
                                    @click="
                                        isMobileMenuOpen = !isMobileMenuOpen
                                    "
                                    class="cursor-pointer rounded-lg border border-[#c5a059]/30 bg-[#051f18] p-1.5 text-[#c5a059] transition-colors hover:bg-[#082a20]"
                                    aria-label="Abrir menú"
                                >
                                    <Menu
                                        v-if="!isMobileMenuOpen"
                                        class="h-5 w-5"
                                    />
                                    <X v-else class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Navigation Drawer -->
                        <div
                            v-if="isMobileMenuOpen"
                            class="mt-3 flex animate-in flex-col space-y-2 border-t border-[#c5a059]/20 pt-3 duration-200 fade-in slide-in-from-top-2 lg:hidden"
                        >
                            <a
                                v-for="item in menuItems"
                                :key="item.label"
                                :href="item.href"
                                @click.prevent="
                                    navigateToSection(item.href, item.label)
                                "
                                :class="[
                                    'flex w-full cursor-pointer items-center gap-3 rounded-xl border px-4 py-2.5 font-[\'Cinzel\',serif] text-xs font-bold tracking-wider transition-all sm:text-sm',
                                    currentSectionHref === item.href
                                        ? 'border-[#c5a059]/40 bg-[#c5a059]/20 text-[#c5a059] shadow-sm'
                                        : 'border-[#c5a059]/15 bg-[#051f18]/90 text-amber-100/95 hover:bg-[#c5a059]/10 hover:text-[#c5a059]',
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    class="h-4 w-4 shrink-0 text-[#c5a059]"
                                />
                                <span class="leading-tight whitespace-normal">{{
                                    item.label
                                }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- 3. SECCIÓN HERO PRINCIPAL -->
        <section
            class="relative flex w-full flex-col items-center justify-center overflow-hidden border-b border-[#c5a059]/10 bg-[#082a20] pt-[5px] pb-10 lg:pt-[5px] lg:pb-12"
        >
            <!-- Fondo sutil de iluminación esmeralda/dorada -->
            <div
                class="pointer-events-none absolute inset-0 z-0 overflow-hidden"
            >
                <div
                    class="absolute top-1/4 left-1/2 h-[700px] w-[700px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#c5a059]/5 blur-3xl"
                ></div>
                <div
                    class="absolute right-1/4 bottom-1/4 h-96 w-96 rounded-full bg-emerald-500/5 blur-3xl"
                ></div>
            </div>

            <div
                class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center space-y-6 px-4 text-center md:px-8"
            >
                <!-- Logo Central + Motto Badge Sobrepuesto en la Parte Superior -->
                <div
                    class="relative flex w-full flex-col items-center justify-center pt-2"
                >
                    <!-- 1. Recuadro de Motto sobrepuesto en la parte superior del Logo (Posicionado adecuadamente debajo del menú) -->
                    <div
                        class="animate-on-reveal z-20 -mb-6 flex items-center justify-center delay-100 sm:-mb-8"
                    >
                        <div
                            class="inline-flex items-center gap-2.5 rounded-full border border-[#c5a059]/45 bg-[#051f18]/95 px-6 py-2.5 text-xs font-semibold text-amber-200/95 shadow-2xl backdrop-blur-md sm:text-sm"
                        >
                            <Scale class="h-4 w-4 text-[#c5a059]" />
                            <span
                                class="font-['Cinzel',serif] tracking-wider italic"
                                >“Liderazgo jurídico y compromiso
                                institucional”</span
                            >
                        </div>
                    </div>

                    <!-- 2. Logo Central (Perfecto como está) -->
                    <div
                        class="animate-on-reveal z-10 my-[-40px] flex w-full max-w-2xl items-center justify-center overflow-hidden delay-200 [clip-path:inset(40px_0_40px_0)] md:max-w-3xl lg:max-w-4xl"
                    >
                        <img
                            src="/images/logo-main.jpg"
                            alt="Sillerico & Asociados - Emblema Principal"
                            class="h-auto max-h-[380px] w-full scale-115 [mask-image:radial-gradient(ellipse_70%_65%_at_50%_50%,black_25%,transparent_82%)] object-cover object-center brightness-110 contrast-110 filter [-webkit-mask-image:radial-gradient(ellipse_70%_65%_at_50%_50%,black_25%,transparent_82%)] sm:max-h-[470px] md:max-h-[560px] lg:max-h-[630px]"
                            width="1600"
                            height="1600"
                            fetchpriority="high"
                            decoding="async"
                        />
                    </div>
                </div>

                <!-- 3. Recuadro de Declaración Institucional + Botones (Con el ancho estándar max-w-6xl) -->
                <div
                    class="animate-on-reveal relative z-20 -mt-20 flex w-full max-w-6xl flex-col justify-between space-y-6 rounded-2xl border border-[#c5a059]/35 bg-[#051f18]/85 p-6 shadow-2xl backdrop-blur-md delay-300 sm:-mt-24 md:-mt-28 md:p-10"
                >
                    <!-- Mensaje Institucional de Visión Preventiva -->
                    <div class="space-y-4 text-center">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 px-4 py-1.5 text-xs font-semibold tracking-widest text-[#c5a059] uppercase"
                        >
                            <Scale class="h-3.5 w-3.5" />
                            <span class="font-['Cinzel',serif] text-xs"
                                >Visión Jurídica Preventiva</span
                            >
                        </div>

                        <div class="relative px-2 py-1 sm:px-6 md:px-10">
                            <p
                                class="text-justify font-['Cinzel',serif] text-sm leading-relaxed font-normal text-neutral-100 sm:text-center sm:text-base md:text-lg md:leading-loose lg:text-xl"
                            >
                                En
                                <strong class="font-semibold text-[#c5a059]"
                                    >SILLERICO & ASOCIADOS</strong
                                >
                                entendemos que
                                <span
                                    class="font-semibold text-amber-200 italic"
                                    >el mejor litigio es aquel que pudo
                                    evitarse</span
                                >. Por ello, complementamos nuestra experiencia
                                en representación judicial con una sólida visión
                                preventiva, orientada a identificar riesgos,
                                fortalecer la seguridad jurídica y ofrecer
                                soluciones estratégicas que protejan los
                                intereses de nuestros clientes antes, durante y
                                después de cada decisión.
                            </p>
                        </div>
                    </div>

                    <!-- Botones de Acción Integrados al Pie del Recuadro -->
                    <div
                        class="flex w-full flex-col items-center justify-center gap-4 border-t border-[#c5a059]/20 pt-6 sm:flex-row"
                    >
                        <button
                            @click="isReglamentoModalOpen = true"
                            class="flex w-full cursor-pointer items-center justify-center gap-2.5 rounded-lg border border-[#c5a059] bg-transparent px-8 py-3.5 text-sm font-bold tracking-wider text-[#c5a059] shadow-lg transition-all hover:bg-[#c5a059]/10 sm:w-auto md:text-base"
                        >
                            <FileText class="h-5 w-5" />
                            <span>Reglamento Interno</span>
                        </button>
                        <button
                            @click="scrollToContact"
                            class="flex w-full cursor-pointer items-center justify-center gap-2.5 rounded-lg bg-[#c5a059] px-8 py-3.5 text-sm font-bold tracking-wider text-emerald-950 shadow-xl shadow-emerald-950/20 transition-all hover:bg-[#d6b46c] active:scale-98 sm:w-auto md:text-base"
                        >
                            <span>Agendar una Consulta</span>
                            <ChevronRight class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. SECCIÓN SERVICIOS (INTERACTIVE CARD GRID) -->
        <section
            id="servicios"
            class="mx-auto max-w-7xl px-4 py-20 md:px-8 md:py-28"
        >
            <div class="mx-auto mb-16 max-w-3xl space-y-4 text-center">
                <span
                    class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                    >Áreas de Práctica Jurídica</span
                >
                <h2
                    class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                >
                    Nuestras Especializaciones y Servicios
                </h2>
                <div class="mx-auto mt-2 h-[2px] w-16 bg-[#c5a059]"></div>
                <p
                    class="text-sm font-light text-neutral-300 md:text-base dark:text-neutral-400"
                >
                    Ofrecemos asesoría legal integral con abogados
                    especializados en cada rama del derecho nacional e
                    internacional, brindándote la mayor seguridad jurídica.
                </p>
            </div>

            <!-- Tarjeta de Especialidad Destacada (Derecho Penal y Defensa Litigante) -->
            <div
                class="group relative mb-12 overflow-hidden rounded-2xl border border-[#c5a059] bg-gradient-to-br from-[#0b3c2e] to-[#051c15] p-8 shadow-[0_0_20px_rgba(197,160,89,0.15)] transition-all duration-500 hover:border-[#d6b46c] hover:shadow-[0_0_30px_rgba(197,160,89,0.25)] md:p-10"
            >
                <!-- Shine Overlay Effect -->
                <div
                    class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-[#c5a059]/5 to-transparent transition-transform duration-1000 group-hover:translate-x-full"
                ></div>

                <div
                    class="relative z-10 grid grid-cols-1 items-center gap-8 lg:grid-cols-12"
                >
                    <!-- Columna Izquierda (Info y Descripción) -->
                    <div class="space-y-5 lg:col-span-8">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 px-3 py-1 text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                        >
                            <span>⭐ Especialización Principal</span>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex size-14 shrink-0 items-center justify-center rounded-xl border border-[#c5a059]/40 bg-[#c5a059]/20 text-3xl shadow-lg"
                            >
                                {{ featuredService.icon }}
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="font-['Cinzel',serif] text-2xl leading-tight font-extrabold text-amber-100 transition-colors group-hover:text-[#c5a059] md:text-3xl"
                                >
                                    {{ featuredService.title }}
                                </h3>
                                <p
                                    class="text-sm leading-relaxed font-light text-neutral-200 md:text-base"
                                >
                                    {{ featuredService.description }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 pt-4">
                            <a
                                :href="
                                    getServiceWhatsappUrl(featuredService.title)
                                "
                                target="_blank"
                                class="group/btn flex cursor-pointer items-center gap-2 rounded-lg bg-[#c5a059] px-6 py-3 text-xs font-bold tracking-wider text-emerald-950 shadow-md transition-all hover:bg-[#d6b46c] md:text-sm"
                            >
                                <MessageSquare class="h-4 w-4" />
                                <span>Contactar</span>
                                <ChevronRight
                                    class="h-4 w-4 transition-transform group-hover/btn:translate-x-0.5"
                                />
                            </a>
                        </div>
                    </div>

                    <!-- Columna Derecha (Highlights Destacados) -->
                    <div
                        class="space-y-4 border-t border-[#c5a059]/20 pt-6 lg:col-span-4 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8"
                    >
                        <h4
                            class="font-['Cinzel',serif] text-xs font-bold tracking-widest text-amber-200/70 uppercase"
                        >
                            Áreas de Enfoque
                        </h4>
                        <ul class="space-y-3">
                            <li
                                v-for="hi in featuredService.highlights"
                                :key="hi"
                                class="flex items-center gap-3 text-sm font-light text-neutral-200"
                            >
                                <span
                                    class="size-2 shrink-0 rounded-full bg-[#c5a059]"
                                ></span>
                                <span>{{ hi }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Grid para las otras 8 especialidades -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-[#c5a059]/20 bg-[#0b3629]/60 p-8 shadow-sm transition-all duration-300 hover:border-[#c5a059]/50 hover:shadow-xl dark:border-neutral-800 dark:bg-[#071619] dark:hover:border-[#c5a059]/50"
                >
                    <!-- Hover effect back panel -->
                    <div
                        class="absolute inset-0 bg-[#082a20]/0 transition-colors duration-300 group-hover:bg-[#082a20]/2 dark:group-hover:bg-[#c5a059]/2"
                    ></div>

                    <div class="z-10 space-y-4">
                        <div
                            class="flex size-12 items-center justify-center rounded-lg border border-[#c5a059]/20 bg-[#c5a059]/15 text-2xl transition-transform duration-300 group-hover:scale-110 dark:border-[#c5a059]/20 dark:bg-[#c5a059]/10"
                        >
                            {{ service.icon }}
                        </div>

                        <h3
                            class="font-['Cinzel',serif] text-lg font-bold text-amber-100 transition-colors group-hover:text-[#c5a059] dark:text-white dark:group-hover:text-[#c5a059]"
                        >
                            {{ service.title }}
                        </h3>

                        <p
                            class="text-sm leading-relaxed font-light text-neutral-300 dark:text-neutral-300"
                        >
                            {{ service.description }}
                        </p>
                    </div>

                    <!-- Bullet Highlights -->
                    <div
                        class="z-10 mt-6 flex flex-wrap gap-2 border-t border-[#c5a059]/15 pt-4 dark:border-neutral-800/80"
                    >
                        <span
                            v-for="hi in service.highlights"
                            :key="hi"
                            class="rounded bg-[#082a20]/40 px-2 py-0.5 text-[10px] font-semibold text-amber-200/90 dark:bg-neutral-800 dark:text-[#c5a059]"
                        >
                            {{ hi }}
                        </span>
                    </div>

                    <!-- Botón Contactar al pie del cuadro -->
                    <div class="z-10 mt-6 border-t border-[#c5a059]/15 pt-4">
                        <a
                            :href="getServiceWhatsappUrl(service.title)"
                            target="_blank"
                            class="group/btn flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-[#c5a059]/40 bg-[#082a20] px-4 py-2.5 text-xs font-bold tracking-wider text-[#c5a059] shadow-md transition-all duration-300 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 md:text-sm"
                        >
                            <MessageSquare class="h-4 w-4" />
                            <span>Contactar</span>
                            <ChevronRight
                                class="h-4 w-4 transition-transform group-hover/btn:translate-x-0.5"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. SECCIÓN ARTÍCULOS, NORMAS Y LEYES (DYNAMIC FILTER SYSTEM) -->
        <section
            id="articulos"
            class="border-y border-[#c5a059]/15 bg-[#051c16] py-20 md:py-28"
        >
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <!-- Header -->
                <div
                    class="mb-12 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end"
                >
                    <div class="space-y-3">
                        <span
                            id="normas"
                            class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                            >Artículos, Normas y Leyes</span
                        >
                        <h2
                            class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                        >
                            Centro de Análisis Jurídico
                        </h2>
                        <div class="h-[2px] w-16 bg-[#c5a059]"></div>
                    </div>

                    <!-- Live search input -->
                    <div class="relative w-full md:w-80">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-amber-200/60"
                        />
                        <input
                            v-model="searchKeyword"
                            type="text"
                            placeholder="Buscar artículos o leyes..."
                            class="w-full rounded-lg border border-[#c5a059]/20 bg-[#0b3629]/50 py-2.5 pr-4 pl-9 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none dark:text-white"
                        />
                    </div>
                </div>

                <!-- Segmented Button Group (Radio Group style) -->
                <div class="mb-8 flex justify-start">
                    <div
                        class="inline-flex flex-wrap gap-1 rounded-2xl border border-[#c5a059]/30 bg-[#051f18]/80 p-1.5 shadow-xl backdrop-blur-md sm:flex-nowrap dark:bg-neutral-900"
                    >
                        <!-- 1. Artículos Doctrinarios -->
                        <button
                            @click="selectedCategory = 'articulos'"
                            :class="[
                                'flex cursor-pointer items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold tracking-wider transition-all duration-300 sm:px-6 sm:text-sm',
                                selectedCategory === 'articulos'
                                    ? 'scale-[1.02] bg-gradient-to-r from-[#c5a059] to-[#d6b46c] font-bold text-emerald-950 shadow-md shadow-[#c5a059]/20'
                                    : 'text-amber-200/80 hover:bg-[#0b3629]/50 hover:text-amber-100',
                            ]"
                        >
                            <span
                                class="size-2 shrink-0 rounded-full transition-all duration-300"
                                :class="
                                    selectedCategory === 'articulos'
                                        ? 'scale-110 bg-emerald-950'
                                        : 'bg-[#c5a059]/40'
                                "
                            ></span>
                            <span>Artículos Doctrinarios</span>
                        </button>

                        <!-- 2. Leyes & Compendios -->
                        <button
                            @click="selectedCategory = 'normas'"
                            :class="[
                                'flex cursor-pointer items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold tracking-wider transition-all duration-300 sm:px-6 sm:text-sm',
                                selectedCategory === 'normas'
                                    ? 'scale-[1.02] bg-gradient-to-r from-[#c5a059] to-[#d6b46c] font-bold text-emerald-950 shadow-md shadow-[#c5a059]/20'
                                    : 'text-amber-200/80 hover:bg-[#0b3629]/50 hover:text-amber-100',
                            ]"
                        >
                            <span
                                class="size-2 shrink-0 rounded-full transition-all duration-300"
                                :class="
                                    selectedCategory === 'normas'
                                        ? 'scale-110 bg-emerald-950'
                                        : 'bg-[#c5a059]/40'
                                "
                            ></span>
                            <span>Leyes & Compendios</span>
                        </button>

                        <!-- 3. Todos -->
                        <button
                            @click="selectedCategory = 'todos'"
                            :class="[
                                'flex cursor-pointer items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold tracking-wider transition-all duration-300 sm:px-6 sm:text-sm',
                                selectedCategory === 'todos'
                                    ? 'scale-[1.02] bg-gradient-to-r from-[#c5a059] to-[#d6b46c] font-bold text-emerald-950 shadow-md shadow-[#c5a059]/20'
                                    : 'text-amber-200/80 hover:bg-[#0b3629]/50 hover:text-amber-100',
                            ]"
                        >
                            <span
                                class="size-2 shrink-0 rounded-full transition-all duration-300"
                                :class="
                                    selectedCategory === 'todos'
                                        ? 'scale-110 bg-emerald-950'
                                        : 'bg-[#c5a059]/40'
                                "
                            ></span>
                            <span>Todos</span>
                        </button>
                    </div>
                </div>

                <!-- Dynamic Grid List -->
                <!-- Dynamic Grid List (Card format) -->
                <div
                    v-if="paginatedPublications.length > 0"
                    class="grid grid-cols-1 gap-8 lg:grid-cols-2"
                >
                    <div
                        v-for="pub in paginatedPublications"
                        :key="pub.id || pub.codigo || pub.title"
                        class="flex flex-col justify-between rounded-xl border border-[#c5a059]/15 bg-[#0b3629]/60 p-6 shadow-sm transition-all hover:shadow-md md:p-8"
                    >
                        <div class="space-y-4">
                            <!-- Top Row: Code Badge, Author & Date -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-2 text-[10px] font-bold tracking-wider text-amber-200/80 uppercase"
                            >
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        v-if="pub.codigo"
                                        class="rounded border border-[#c5a059]/40 bg-[#c5a059]/20 px-2 py-0.5 font-mono text-[10px] font-semibold text-[#e6c987]"
                                    >
                                        {{ pub.codigo }}
                                    </span>
                                    <span class="text-[#c5a059]"
                                        >Por: {{ pub.author }}</span
                                    >
                                </div>
                                <span
                                    class="rounded border border-[#c5a059]/10 bg-[#082a20] px-2.5 py-0.5 text-[10px] font-semibold text-amber-200"
                                >
                                    {{ pub.date }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3
                                class="font-['Cinzel',serif] text-base leading-tight font-bold text-neutral-100 md:text-lg dark:text-white"
                            >
                                {{ pub.title }}
                            </h3>

                            <!-- Description -->
                            <p
                                v-if="pub.description"
                                class="text-xs leading-relaxed font-light text-neutral-300 md:text-sm dark:text-neutral-300"
                            >
                                {{ pub.description }}
                            </p>
                        </div>

                        <!-- Bottom Row: Download Button or Availability Notice -->
                        <div
                            class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-[#c5a059]/15 pt-4"
                        >
                            <span
                                class="text-[10px] font-semibold tracking-wider text-amber-200/70 uppercase"
                            >
                                {{
                                    pub.tipo_label ||
                                    (pub.category === 'normas'
                                        ? 'Ley / Código'
                                        : 'Artículo Doctrinal')
                                }}
                            </span>

                            <a
                                v-if="pub.pdf"
                                :href="pub.pdf"
                                download
                                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#c5a059] px-4 py-2 text-xs font-bold text-emerald-950 shadow-md transition-all hover:bg-[#d6b46c] active:scale-95"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    />
                                </svg>
                                <span
                                    >Descargar
                                    {{ pub.file_type || 'Documento' }}</span
                                >
                            </a>

                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-lg border border-[#c5a059]/10 bg-[#082a20]/60 px-3 py-1.5 text-xs text-neutral-400 italic"
                            >
                                <Info
                                    class="h-3.5 w-3.5 shrink-0 text-[#c5a059]/60"
                                />
                                <span
                                    >Disponible para consulta
                                    institucional</span
                                >
                            </span>
                        </div>
                    </div>
                </div>

                <!-- No Results Fallback -->
                <div
                    v-else
                    class="rounded-xl border border-dashed border-[#c5a059]/20 bg-[#0b3629]/30 py-16 text-center"
                >
                    <FileText
                        class="mx-auto mb-4 h-12 w-12 text-[#c5a059] opacity-50"
                    />
                    <h3 class="text-lg font-semibold text-white">
                        No se hallaron resultados
                    </h3>
                    <p class="text-sm text-neutral-300 dark:text-neutral-400">
                        Intenta utilizar otras palabras clave en la barra de
                        búsqueda.
                    </p>
                </div>

                <!-- Controles de Paginación -->
                <div
                    v-if="totalPages > 1"
                    class="mt-12 flex items-center justify-center gap-4"
                >
                    <button
                        @click="currentPage > 1 ? currentPage-- : null"
                        :disabled="currentPage === 1"
                        :class="[
                            'flex cursor-pointer items-center gap-1.5 rounded-lg border px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all',
                            currentPage === 1
                                ? 'cursor-not-allowed border-neutral-700 text-neutral-500 opacity-40'
                                : 'border-[#c5a059]/30 text-[#c5a059] hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950',
                        ]"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        <span>Anterior</span>
                    </button>

                    <span class="text-xs font-medium text-neutral-400">
                        Página
                        <strong class="text-white">{{ currentPage }}</strong> de
                        <strong class="text-white">{{ totalPages }}</strong>
                    </span>

                    <button
                        @click="currentPage < totalPages ? currentPage++ : null"
                        :disabled="currentPage === totalPages"
                        :class="[
                            'flex cursor-pointer items-center gap-1.5 rounded-lg border px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all',
                            currentPage === totalPages
                                ? 'cursor-not-allowed border-neutral-700 text-neutral-500 opacity-40'
                                : 'border-[#c5a059]/30 text-[#c5a059] hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950',
                        ]"
                    >
                        <span>Siguiente</span>
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- 6. SECCIÓN EQUIPO PROFESIONAL (SENIOR PARTNERS) -->
        <section
            id="equipo"
            class="mx-auto max-w-7xl px-4 py-20 md:px-8 md:py-28"
        >
            <div class="mx-auto mb-16 max-w-3xl space-y-4 text-center">
                <span
                    class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                    >Nuestro Talento Humano</span
                >
                <h2
                    class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                >
                    Socios y Abogados Litigantes
                </h2>
                <div class="mx-auto mt-2 h-[2px] w-16 bg-[#c5a059]"></div>
                <p
                    class="text-sm font-light text-neutral-300 md:text-base dark:text-neutral-400"
                >
                    Un cuerpo legal multidisciplinar y de amplia reputación
                    académica nacional encargado de defender sus derechos
                    corporativos y personales.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="partner in visiblePartners"
                    :key="partner.name"
                    class="group flex flex-col justify-between rounded-xl border border-[#c5a059]/20 bg-[#0b3629]/60 p-6 shadow-sm transition-all duration-300 hover:shadow-xl dark:border-neutral-800/80 dark:bg-[#071619]/60"
                >
                    <div class="space-y-6">
                        <!-- Professional Portrait Image -->
                        <div
                            class="relative aspect-square w-full overflow-hidden rounded-lg border border-[#c5a059]/30 bg-[#082a20]/45 shadow-inner transition-all duration-300 group-hover:border-[#c5a059] dark:bg-[#061e18]/45"
                        >
                            <img
                                v-if="partner.image"
                                :src="partner.image"
                                :alt="partner.name"
                                class="h-full w-full object-cover object-top transition-transform duration-500 group-hover:scale-[1.04]"
                                width="400"
                                height="400"
                                loading="lazy"
                                decoding="async"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#082a20] to-[#041510]"
                            >
                                <span
                                    class="font-['Cinzel',serif] text-5xl font-bold text-[#c5a059]/40 transition-all duration-500 select-none group-hover:scale-105 group-hover:text-[#c5a059] md:text-6xl"
                                >
                                    {{ getPartnerInitials(partner.name) }}
                                </span>
                            </div>

                            <!-- Subtle overlay on hover -->
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#051f18]/65 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            ></div>

                            <!-- Decorative Scales detail in background (only when showing initials) -->
                            <svg
                                v-if="!partner.image"
                                class="absolute right-2 bottom-2 h-16 w-16 text-white/2 opacity-[0.03]"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm-1 3h2v2h-2V7zm0 4h2v6h-2v-6z"
                                />
                            </svg>
                        </div>

                        <div class="space-y-2">
                            <h3
                                class="font-['Cinzel',serif] text-base leading-tight font-bold text-neutral-100 transition-colors group-hover:text-amber-500 dark:text-[#c5a059]"
                            >
                                {{ partner.name }}
                            </h3>
                            <p
                                class="flex min-h-[32px] items-center text-xs font-semibold tracking-wider text-neutral-300 uppercase dark:text-neutral-300"
                            >
                                {{ partner.role }}
                            </p>
                            <div class="h-[2px] w-8 bg-[#c5a059]"></div>
                        </div>

                        <p
                            class="text-xs leading-relaxed font-light text-neutral-300 dark:text-neutral-300"
                        >
                            {{ partner.credentials }}
                        </p>
                    </div>

                    <div
                        class="mt-6 flex items-center justify-between border-t border-[#c5a059]/15 pt-4 dark:border-neutral-800/80"
                    >
                        <span
                            class="text-[10px] font-semibold tracking-widest text-neutral-400 uppercase"
                            >{{ partner.type || 'Socio' }}</span
                        >
                        <a
                            :href="getPartnerWhatsappUrl(partner.name)"
                            target="_blank"
                            class="flex cursor-pointer items-center gap-1 text-xs font-bold text-[#c5a059] hover:underline"
                        >
                            <span>Agendar cita</span>
                            <ChevronRight class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. SECCIÓN GALERÍA INSTITUCIONAL (PREMIUM GRID) -->
        <section
            id="galeria"
            class="border-y border-[#c5a059]/15 bg-[#051c16] py-20 md:py-28"
        >
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="mx-auto mb-16 max-w-3xl space-y-4 text-center">
                    <span
                        class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                        >Infraestructura Corporativa</span
                    >
                    <h2
                        class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                    >
                        Nuestras Instalaciones en La Paz
                    </h2>
                    <div class="mx-auto mt-2 h-[2px] w-16 bg-[#c5a059]"></div>
                    <p
                        class="text-sm font-light text-neutral-300 md:text-base dark:text-neutral-400"
                    >
                        Instalaciones corporativas de primer nivel diseñadas
                        para ofrecerle la mayor privacidad y sofisticación
                        durante sus visitas de negocios.
                    </p>
                </div>

                <!-- Carrusel Interactivo de Instalaciones -->
                <div
                    class="group/carousel relative mx-auto h-[350px] w-full max-w-5xl overflow-hidden rounded-2xl border border-[#c5a059]/30 bg-emerald-950/40 shadow-2xl select-none sm:h-[450px] md:h-[550px]"
                    @mouseenter="pauseAutoplay"
                    @mouseleave="resumeAutoplay"
                >
                    <!-- Slides -->
                    <div class="relative h-full w-full">
                        <div
                            v-for="(img, idx) in galleryImages"
                            :key="img.title"
                            :class="[
                                'absolute inset-0 h-full w-full transition-all duration-700 ease-in-out',
                                activeImageIndex === idx
                                    ? 'z-10 scale-100 opacity-100'
                                    : 'pointer-events-none z-0 scale-105 opacity-0',
                            ]"
                        >
                            <!-- Background Image -->
                            <img
                                :src="img.src"
                                :alt="img.title"
                                class="h-full w-full object-cover"
                                width="1400"
                                height="900"
                                loading="lazy"
                                decoding="async"
                            />

                            <!-- Gradient Overlay (Bottom text overlay) -->
                            <div
                                class="absolute inset-0 z-10 bg-gradient-to-t from-[#041510] via-[#041510]/50 to-transparent"
                            ></div>

                            <!-- Content Overlay (Overimposed at the bottom) -->
                            <div
                                class="absolute inset-x-0 bottom-0 z-20 flex flex-col justify-end space-y-3 p-8 text-left sm:p-12"
                            >
                                <span
                                    class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                                    >Instalación {{ idx + 1 }} de
                                    {{ galleryImages.length }}</span
                                >
                                <h3
                                    class="font-['Cinzel',serif] text-xl leading-tight font-bold text-white sm:text-2xl md:text-3xl"
                                >
                                    {{ img.title }}
                                </h3>
                                <p
                                    class="max-w-3xl text-sm leading-relaxed font-light text-neutral-200 sm:text-base"
                                >
                                    {{ img.desc }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Left/Right Navigation Arrows -->
                    <button
                        @click="prevSlide"
                        class="absolute top-1/2 left-4 z-30 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#051f18]/85 text-[#c5a059] opacity-0 shadow-lg transition-all duration-300 group-hover/carousel:opacity-100 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 sm:size-12"
                        aria-label="Anterior"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>

                    <button
                        @click="nextSlide"
                        class="absolute top-1/2 right-4 z-30 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#051f18]/85 text-[#c5a059] opacity-0 shadow-lg transition-all duration-300 group-hover/carousel:opacity-100 hover:border-[#c5a059] hover:bg-[#c5a059] hover:text-emerald-950 sm:size-12"
                        aria-label="Siguiente"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>

                    <!-- Navigation Indicator Dots (Bottom-Right overlay) -->
                    <div
                        class="absolute right-8 bottom-6 z-30 flex gap-2.5 sm:right-12"
                    >
                        <button
                            v-for="(_, idx) in galleryImages"
                            :key="idx"
                            @click="goToSlide(idx)"
                            :class="[
                                'h-2 cursor-pointer rounded-full transition-all duration-300',
                                activeImageIndex === idx
                                    ? 'w-8 bg-[#c5a059]'
                                    : 'w-2 bg-white/40 hover:bg-white/70',
                            ]"
                            :aria-label="'Ir a imagen ' + (idx + 1)"
                        ></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. SECCIÓN SOBRE LA FIRMA (VALORES E HISTORIA) -->
        <section
            id="firma"
            class="mx-auto max-w-7xl px-4 py-20 md:px-8 md:py-28"
        >
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                <!-- History & Mission -->
                <div class="space-y-6">
                    <div class="space-y-3">
                        <span
                            class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                            >Trayectoria y Liderazgo</span
                        >
                        <h2
                            class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                        >
                            Nuestra Historia
                        </h2>
                        <div class="h-[2px] w-16 bg-[#c5a059]"></div>
                    </div>

                    <!-- Recuadro contenedor de Nuestra Historia con scroll personalizado -->
                    <div
                        class="custom-gold-scrollbar max-h-[300px] space-y-4 overflow-y-auto rounded-xl border border-[#c5a059]/20 bg-[#0b3629]/40 p-4 pr-3 text-justify text-xs leading-relaxed font-light text-neutral-300 shadow-inner sm:p-5 sm:text-sm md:text-base dark:text-neutral-300"
                    >
                        <p>
                            <strong class="font-semibold text-[#c5a059]"
                                >SILLERICO & ASOCIADOS – FIRMA DE
                                ABOGADOS</strong
                            >
                            es el resultado de más de dieciséis años de
                            experiencia profesional, dedicación y compromiso con
                            la defensa de los derechos e intereses de personas,
                            empresas e instituciones.
                        </p>

                        <p>
                            Nuestra historia comienza con el ejercicio
                            independiente del Derecho por parte de nuestro
                            Director General, Alan Sillerico Segurondo, quien
                            inició su trayectoria profesional con una marcada
                            orientación hacia el Derecho Penal, área en la que
                            consolidó una amplia experiencia en la defensa
                            técnica, la litigación oral y la asesoría jurídica
                            especializada.
                        </p>

                        <p>
                            A lo largo de los años, el ejercicio constante de la
                            profesión, la formación académica permanente y la
                            confianza depositada por nuestros clientes
                            permitieron ampliar progresivamente el ámbito de
                            actuación profesional. Lo que inició como una
                            práctica especializada en materia penal evolucionó
                            hacia una atención jurídica integral, incorporando
                            servicios en Derecho Constitucional, Civil,
                            Comercial, Laboral, Administrativo, Tributario,
                            Familiar y otras áreas del ordenamiento jurídico
                            boliviano.
                        </p>

                        <p>
                            Esta evolución respondió a una convicción clara:
                            ofrecer a cada cliente soluciones jurídicas
                            completas, estratégicas y de alta calidad,
                            manteniendo siempre los principios de ética,
                            responsabilidad, confidencialidad y excelencia
                            profesional.
                        </p>

                        <p>
                            Como consecuencia natural de ese crecimiento nació
                            <strong class="font-semibold text-[#c5a059]"
                                >SILLERICO & ASOCIADOS – FIRMA DE
                                ABOGADOS</strong
                            >, concebida como una organización jurídica moderna
                            que integra experiencia, especialización y trabajo
                            en equipo. La firma reúne profesionales
                            comprometidos con la prestación de servicios legales
                            de excelencia, orientados tanto a la prevención como
                            a la solución eficaz de conflictos jurídicos.
                        </p>
                    </div>

                    <!-- Tabbed Misión, Visión, Principios System -->
                    <div
                        class="mt-6 space-y-5 rounded-2xl border border-[#c5a059]/25 bg-[#0b3629]/60 p-6 shadow-xl backdrop-blur-sm"
                    >
                        <div
                            class="flex justify-start gap-3 overflow-x-auto border-b border-[#c5a059]/20 pb-2"
                        >
                            <button
                                @click="activeTab = 'mision'"
                                :class="[
                                    'cursor-pointer border-b-2 px-5 py-2 font-[\'Cinzel\',serif] text-xs font-bold tracking-wider whitespace-nowrap transition-all sm:text-sm',
                                    activeTab === 'mision'
                                        ? 'rounded-t-lg border-[#c5a059] bg-[#c5a059]/10 text-[#c5a059]'
                                        : 'border-transparent text-neutral-400 hover:text-amber-100',
                                ]"
                            >
                                Misión
                            </button>
                            <button
                                @click="activeTab = 'vision'"
                                :class="[
                                    'cursor-pointer border-b-2 px-5 py-2 font-[\'Cinzel\',serif] text-xs font-bold tracking-wider whitespace-nowrap transition-all sm:text-sm',
                                    activeTab === 'vision'
                                        ? 'rounded-t-lg border-[#c5a059] bg-[#c5a059]/10 text-[#c5a059]'
                                        : 'border-transparent text-neutral-400 hover:text-amber-100',
                                ]"
                            >
                                Visión
                            </button>
                            <button
                                @click="activeTab = 'principios'"
                                :class="[
                                    'cursor-pointer border-b-2 px-5 py-2 font-[\'Cinzel\',serif] text-xs font-bold tracking-wider whitespace-nowrap transition-all sm:text-sm',
                                    activeTab === 'principios'
                                        ? 'rounded-t-lg border-[#c5a059] bg-[#c5a059]/10 text-[#c5a059]'
                                        : 'border-transparent text-neutral-400 hover:text-amber-100',
                                ]"
                            >
                                Principios
                            </button>
                        </div>

                        <!-- Tab Content Misión / Visión -->
                        <div
                            v-if="
                                activeTab === 'mision' || activeTab === 'vision'
                            "
                            class="flex min-h-[120px] flex-col justify-center space-y-2 transition-all duration-300"
                        >
                            <h4
                                class="font-['Cinzel',serif] text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                            >
                                {{ reglamentoTabs[activeTab].title }}
                            </h4>
                            <p
                                class="text-justify text-xs leading-relaxed font-light text-neutral-200 sm:text-sm"
                            >
                                {{ reglamentoTabs[activeTab].content }}
                            </p>
                        </div>

                        <!-- Tab Content Principios -->
                        <div
                            v-else-if="activeTab === 'principios'"
                            class="grid min-h-[120px] grid-cols-1 items-center gap-3 transition-all duration-300 sm:grid-cols-2"
                        >
                            <div
                                v-for="p in reglamentoTabs.principios"
                                :key="p.title"
                                class="flex flex-col rounded-xl border border-[#c5a059]/20 bg-[#082a20]/90 p-3 shadow-sm"
                            >
                                <span
                                    class="font-['Cinzel',serif] text-xs font-bold text-[#c5a059]"
                                    >{{ p.title }}</span
                                >
                                <span
                                    class="mt-1 text-[11px] leading-normal font-light text-neutral-300"
                                    >{{ p.desc }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Values Card Grid (Aesthetic presentation) -->
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-2xl border-2 border-[#c5a059]/40 bg-gradient-to-tr from-[#082a20] to-[#041510] p-8 text-white shadow-2xl md:p-10 dark:from-[#071d18] dark:to-[#030d0a]"
                >
                    <div
                        class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent opacity-[0.03]"
                    ></div>

                    <div class="z-10 space-y-6">
                        <span
                            class="font-['Cinzel',serif] text-xs font-bold tracking-widest text-amber-200/60 uppercase"
                            >Valores de la Firma</span
                        >
                        <h3
                            class="font-['Cinzel',serif] text-2xl leading-tight font-bold text-amber-200"
                        >
                            Nuestros Pilares Fundamentales
                        </h3>
                        <p
                            class="text-xs leading-relaxed font-light text-neutral-300"
                        >
                            Cada caso y cliente es atendido bajo directrices de
                            absoluta responsabilidad institucional.
                        </p>

                        <div class="space-y-4 pt-4">
                            <div class="flex items-start gap-4">
                                <div
                                    class="mt-1 flex size-5 items-center justify-center rounded-full border border-amber-500/40 bg-amber-500/20 text-[10px] text-amber-300"
                                >
                                    ✓
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-semibold text-amber-200"
                                    >
                                        Honestidad y Transparencia
                                    </h4>
                                    <p
                                        class="text-[11px] leading-relaxed font-light text-neutral-300"
                                    >
                                        Comunicación clara de posibilidades
                                        reales desde la primera consulta sin
                                        falsas promesas.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="mt-1 flex size-5 items-center justify-center rounded-full border border-amber-500/40 bg-amber-500/20 text-[10px] text-amber-300"
                                >
                                    ✓
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-semibold text-amber-200"
                                    >
                                        Absoluta Confidencialidad
                                    </h4>
                                    <p
                                        class="text-[11px] leading-relaxed font-light text-neutral-300"
                                    >
                                        Reserva estricta y protección rigurosa
                                        de toda la documentación corporativa y
                                        familiar.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="mt-1 flex size-5 items-center justify-center rounded-full border border-amber-500/40 bg-amber-500/20 text-[10px] text-amber-300"
                                >
                                    ✓
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-semibold text-amber-200"
                                    >
                                        Disciplina Jurídica
                                    </h4>
                                    <p
                                        class="text-[11px] leading-relaxed font-light text-neutral-300"
                                    >
                                        Rigurosidad académica en la
                                        investigación legislativa aplicada a
                                        cada demanda o defensa civil.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="z-10 mt-8 flex items-center justify-between border-t border-white/10 pt-6 text-xs text-neutral-300"
                    >
                        <span>S&A • SILLERICO & ASOCIADOS</span>
                        <Scale class="h-4 w-4 text-[#c5a059]" />
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. SECCIÓN CONTACTO Y UBICACIÓN (INTEGRATED ON PAGE - SPLIT SCREEN) -->
        <section
            id="contacto"
            class="animate-on-reveal mx-auto max-w-7xl border-t border-[#c5a059]/15 px-4 py-20 md:px-8 md:py-28"
        >
            <div class="grid grid-cols-1 items-stretch gap-12 lg:grid-cols-12">
                <!-- Columna Izquierda: Consulta Rápida (Formulario) (7 columnas) -->
                <div
                    class="flex flex-col justify-center rounded-2xl border border-[#c5a059]/20 bg-[#0b3629]/40 p-8 shadow-xl backdrop-blur-sm sm:p-10 lg:col-span-7 dark:bg-[#071619]/40"
                >
                    <div class="mb-8 space-y-2 text-left">
                        <span
                            class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                            >Contacto Inmediato</span
                        >
                        <h2
                            class="font-['Cinzel',serif] text-3xl font-bold text-neutral-100 md:text-4xl dark:text-white"
                        >
                            Consulta Rápida
                        </h2>
                        <div class="mt-2 h-[2px] w-12 bg-[#c5a059]"></div>
                        <p
                            class="mt-3 text-sm leading-relaxed font-light text-neutral-300 dark:text-neutral-400"
                        >
                            Completa tus datos básicos para agendar una reunión
                            o recibir asesoramiento legal por parte de nuestros
                            abogados especialistas.
                        </p>
                    </div>

                    <form
                        @submit.prevent="submitContactForm"
                        class="space-y-5"
                        v-if="!formSubmitted"
                    >
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="space-y-1.5 text-left">
                                <label
                                    class="text-[10px] font-bold tracking-widest text-neutral-400 uppercase dark:text-neutral-400"
                                    >Nombre Completo</label
                                >
                                <input
                                    v-model="contactForm.name"
                                    type="text"
                                    required
                                    placeholder="Ej. Juan Pérez"
                                    class="w-full rounded-lg border border-[#c5a059]/20 bg-[#051f18]/40 p-3 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                />
                            </div>

                            <div class="space-y-1.5 text-left">
                                <label
                                    class="text-[10px] font-bold tracking-widest text-neutral-400 uppercase dark:text-neutral-400"
                                    >Correo Electrónico</label
                                >
                                <input
                                    v-model="contactForm.email"
                                    type="email"
                                    required
                                    placeholder="juan@ejemplo.com"
                                    class="w-full rounded-lg border border-[#c5a059]/20 bg-[#051f18]/40 p-3 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="space-y-1.5 text-left">
                                <label
                                    class="text-[10px] font-bold tracking-widest text-neutral-400 uppercase dark:text-neutral-400"
                                    >Teléfono / WhatsApp</label
                                >
                                <input
                                    v-model="contactForm.phone"
                                    type="tel"
                                    required
                                    placeholder="+591 700 00000"
                                    class="w-full rounded-lg border border-[#c5a059]/20 bg-[#051f18]/40 p-3 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                />
                            </div>

                            <div class="space-y-1.5 text-left">
                                <label
                                    class="text-[10px] font-bold tracking-widest text-neutral-400 uppercase dark:text-neutral-400"
                                    >Área de Interés</label
                                >
                                <select
                                    v-model="contactForm.area"
                                    class="w-full rounded-lg border border-[#c5a059]/20 bg-[#051f18]/40 p-3 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                >
                                    <option>
                                        Derecho Comercial y Corporativo
                                    </option>
                                    <option>
                                        Derecho de Familia y Sucesiones
                                    </option>
                                    <option>
                                        Propiedad Intelectual y Patentes
                                    </option>
                                    <option>
                                        Derecho Penal y Procesal Penal
                                    </option>
                                    <option>
                                        Derecho Laboral y Seguridad Social
                                    </option>
                                    <option>
                                        Arbitraje y Resolución de Conflictos
                                    </option>
                                    <option>
                                        Derecho Constitucional y Derechos
                                        Humanos
                                    </option>
                                    <option>Derecho Civil y Contractual</option>
                                    <option>
                                        Derecho Tributario y Aduanero
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-left">
                            <label
                                class="text-[10px] font-bold tracking-widest text-neutral-400 uppercase dark:text-neutral-400"
                                >Mensaje / Detalle de la Consulta</label
                            >
                            <textarea
                                v-model="contactForm.message"
                                required
                                rows="4"
                                placeholder="Describe brevemente tu caso o consulta legal..."
                                class="w-full resize-none rounded-lg border border-[#c5a059]/20 bg-[#051f18]/40 p-3 text-xs text-neutral-100 transition-all focus:border-[#c5a059] focus:outline-none sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full cursor-pointer rounded-lg border border-[#c5a059]/30 bg-[#c5a059] px-10 py-4 text-xs font-bold tracking-wider text-emerald-950 uppercase transition-all hover:bg-[#d6b46c] sm:w-auto sm:text-sm"
                        >
                            Enviar Solicitud
                        </button>
                    </form>

                    <!-- Success Message -->
                    <div
                        v-else
                        class="animate-in space-y-4 py-16 text-center duration-200 fade-in"
                    >
                        <div
                            class="mx-auto flex size-16 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-100 text-3xl text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400"
                        >
                            ✓
                        </div>
                        <h4
                            class="font-['Cinzel',serif] text-base font-bold text-neutral-800 dark:text-[#c5a059]"
                        >
                            ¡Solicitud Recibida!
                        </h4>
                        <p
                            class="mx-auto max-w-xs text-xs leading-relaxed font-light text-neutral-500 dark:text-neutral-400"
                        >
                            Un asesor legal de Sillerico & Asociados se
                            contactará contigo por teléfono o WhatsApp de forma
                            inmediata.
                        </p>
                    </div>
                </div>

                <!-- Columna Derecha: Ubicación e Información de Oficina (5 columnas) -->
                <div
                    class="flex flex-col justify-between rounded-2xl border border-[#c5a059]/25 bg-[#051f18] p-8 text-amber-100/90 shadow-xl sm:p-10 lg:col-span-5"
                >
                    <div class="space-y-6">
                        <div class="space-y-2 text-left">
                            <span
                                class="text-xs font-bold tracking-widest text-[#c5a059] uppercase"
                                >Oficinas & Contactos</span
                            >
                            <h2
                                class="font-['Cinzel',serif] text-3xl font-bold text-white"
                            >
                                Nuestra Ubicación
                            </h2>
                            <div class="mt-2 h-[2px] w-12 bg-[#c5a059]"></div>
                        </div>

                        <div class="space-y-5 text-left">
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 text-[#c5a059]"
                                >
                                    <MapPin class="h-5 w-5" />
                                </div>
                                <div class="space-y-1">
                                    <span
                                        class="block text-[10px] font-bold tracking-widest text-amber-200/50 uppercase"
                                        >Dirección Principal</span
                                    >
                                    <p
                                        class="text-xs leading-relaxed font-light text-neutral-200 sm:text-sm"
                                    >
                                        Edificio Park Inn, Piso 6, Of. 66, Calle
                                        Federico Suazo y Bueno, No. 1598 de la
                                        ciudad de La Paz - Bolivia.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 text-[#c5a059]"
                                >
                                    <Phone class="h-5 w-5" />
                                </div>
                                <div class="space-y-1">
                                    <span
                                        class="block text-[10px] font-bold tracking-widest text-amber-200/50 uppercase"
                                        >Teléfonos de Contacto</span
                                    >
                                    <div
                                        class="space-y-1 text-xs leading-relaxed font-light text-neutral-200 sm:text-sm"
                                    >
                                        <p>
                                            <strong
                                                class="font-medium text-amber-100"
                                                >Teléfono:</strong
                                            >
                                            2900080 &ndash; 2900370
                                        </p>
                                        <p>
                                            <strong
                                                class="font-medium text-amber-100"
                                                >Fax:</strong
                                            >
                                            2900370
                                        </p>
                                        <p
                                            class="flex items-center gap-1.5 pt-0.5"
                                        >
                                            <strong
                                                class="font-medium text-amber-100"
                                                >WhatsApp:</strong
                                            >
                                            <a
                                                :href="`${contactLinks.whatsapp}?text=${encodeURIComponent('Hola, quisiera realizar una consulta.')}`"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="font-medium text-emerald-400 underline underline-offset-2 transition-colors hover:text-emerald-300"
                                                title="Abrir chat de WhatsApp"
                                            >
                                                +591 77234317
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 text-[#c5a059]"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <span
                                        class="block text-[10px] font-bold tracking-widest text-amber-200/50 uppercase"
                                        >Horario de Atención</span
                                    >
                                    <p
                                        class="text-xs leading-relaxed font-light text-neutral-200 sm:text-sm"
                                    >
                                        Lunes a Viernes: 09:00 - 18:30
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Google Map Iframe (Interactive map) -->
                    <div
                        class="mt-8 overflow-hidden rounded-xl border border-[#c5a059]/30 shadow-lg"
                    >
                        <iframe
                            src="https://maps.google.com/maps?q=Edificio%20Park%20Inn%20La%20Paz&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
                            class="h-48 w-full border-0 sm:h-56 md:h-64"
                            :allowfullscreen="true"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. PIE DE PÁGINA (COMPLETE INTEGRATED CONTACT & MAP) -->
        <footer
            class="border-t border-[#c5a059]/20 bg-[#041510] pt-16 pb-8 text-xs text-[#eae5da] md:text-sm dark:bg-[#020a0d]"
        >
            <div
                class="mx-auto mb-12 grid max-w-7xl grid-cols-1 gap-12 px-4 md:grid-cols-12 md:px-8"
            >
                <!-- Corporate Presentation -->
                <div class="space-y-4 md:col-span-4">
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
                        <span
                            class="font-['Cinzel',serif] text-base font-bold tracking-wider text-[#c5a059]"
                            >SILLERICO & ASOCIADOS</span
                        >
                    </div>

                    <p class="leading-relaxed font-light text-neutral-400">
                        Consorcio Jurídico especializado en la defensa
                        corporativa, patrimonial y asesoría mercantil de
                        empresas. Brindando certeza de forma integral en el
                        país.
                    </p>

                    <div class="flex items-center gap-3 pt-2">
                        <a
                            :href="contactLinks.whatsapp"
                            target="_blank"
                            class="flex size-7 items-center justify-center rounded-full border border-[#c5a059]/20 bg-[#051f18] text-white transition-colors hover:bg-[#082a20]"
                            title="WhatsApp"
                        >
                            <MessageSquare
                                class="h-3.5 w-3.5 text-emerald-400"
                            />
                        </a>
                        <a
                            :href="contactLinks.facebook"
                            target="_blank"
                            class="flex size-7 items-center justify-center rounded-full border border-[#c5a059]/20 bg-[#051f18] text-white transition-colors hover:bg-[#082a20]"
                            title="Facebook"
                        >
                            <Facebook class="h-3.5 w-3.5 text-[#c5a059]" />
                        </a>
                        <a
                            href="https://youtube.com"
                            target="_blank"
                            class="flex size-7 items-center justify-center rounded-full border border-[#c5a059]/20 bg-[#051f18] text-white transition-colors hover:bg-[#082a20]"
                            title="YouTube"
                        >
                            <Youtube class="h-3.5 w-3.5 text-[#c5a059]" />
                        </a>
                        <a
                            href="https://x.com"
                            target="_blank"
                            class="flex size-7 items-center justify-center rounded-full border border-[#c5a059]/20 bg-[#051f18] text-white transition-colors hover:bg-[#082a20]"
                            title="X (Twitter)"
                        >
                            <Twitter class="h-3.5 w-3.5 text-[#c5a059]" />
                        </a>
                    </div>
                </div>

                <!-- Sitemap Quick Links -->
                <div class="space-y-4 md:col-span-3">
                    <h3
                        class="font-['Cinzel',serif] text-sm font-bold tracking-widest text-[#c5a059] uppercase"
                    >
                        Mapa de Sitio
                    </h3>
                    <ul class="space-y-2">
                        <li v-for="item in menuItems" :key="item.label">
                            <a
                                :href="item.href"
                                @click.prevent="
                                    navigateToSection(item.href, item.label)
                                "
                                class="flex cursor-pointer items-center gap-1.5 font-light text-neutral-400 transition-colors hover:text-[#c5a059]"
                            >
                                <ChevronRight
                                    class="h-3.5 w-3.5 text-[#c5a059]"
                                />
                                <span>{{ item.label }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Technical Contact Panel -->
                <div class="space-y-4 md:col-span-5">
                    <h3
                        class="font-['Cinzel',serif] text-sm font-bold tracking-widest text-[#c5a059] uppercase"
                    >
                        Ubicación y Contacto
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <MapPin
                                class="mt-0.5 h-4 w-4 shrink-0 text-[#c5a059]"
                            />
                            <div
                                class="flex flex-col font-light text-neutral-400"
                            >
                                <span class="font-semibold text-neutral-300"
                                    >Oficina Central:</span
                                >
                                <span>{{ contactLinks.address }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <Phone
                                class="mt-0.5 h-4 w-4 shrink-0 text-[#c5a059]"
                            />
                            <div
                                class="flex flex-col space-y-0.5 font-light text-neutral-400"
                            >
                                <span class="font-semibold text-neutral-300"
                                    >Teléfonos de Atención:</span
                                >
                                <span
                                    ><strong
                                        class="font-medium text-neutral-300"
                                        >Teléfono:</strong
                                    >
                                    2900080 - 2900370</span
                                >
                                <span
                                    ><strong
                                        class="font-medium text-neutral-300"
                                        >Fax:</strong
                                    >
                                    2900370</span
                                >
                                <span>
                                    <strong class="font-medium text-neutral-300"
                                        >WhatsApp:</strong
                                    >
                                    <a
                                        :href="`${contactLinks.whatsapp}?text=${encodeURIComponent('Hola, quisiera realizar una consulta.')}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="ml-1 text-emerald-400 underline underline-offset-2 hover:text-emerald-300"
                                    >
                                        +591 77234317
                                    </a>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <Mail
                                class="mt-0.5 h-4 w-4 shrink-0 text-[#c5a059]"
                            />
                            <div
                                class="flex flex-col font-light text-neutral-400"
                            >
                                <span class="font-semibold text-neutral-300"
                                    >Correo Electrónico:</span
                                >
                                <span>{{ contactLinks.emails[0] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mockup Interactive map grid (WOW style) -->
                    <a
                        :href="contactLinks.locationUrl"
                        target="_blank"
                        class="group relative block h-24 w-full overflow-hidden rounded-lg border border-[#c5a059]/30 bg-[#051c15] pt-2 shadow-inner transition-colors hover:bg-[#07241b]"
                    >
                        <div
                            class="absolute inset-0 bg-[radial-gradient(#c5a059_1px,transparent_1px)] [background-size:16px_16px] opacity-20"
                        ></div>
                        <div
                            class="absolute inset-0 z-10 flex flex-col items-center justify-center p-2 text-center"
                        >
                            <MapPin
                                class="mb-1 h-5 w-5 text-[#c5a059] transition-transform group-hover:scale-110"
                            />
                            <span
                                class="text-[10px] font-semibold tracking-wider text-white uppercase"
                                >Ver Mapa de Ubicación en Google Maps</span
                            >
                            <span class="text-[8px] text-neutral-400"
                                >Clic para abrir navegación</span
                            >
                        </div>
                        <div class="absolute right-1 bottom-1 text-white/40">
                            <ExternalLink class="h-3.5 w-3.5" />
                        </div>
                    </a>
                </div>
            </div>

            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 border-t border-neutral-800 px-4 pt-8 text-center text-[10px] text-neutral-500 sm:flex-row md:px-8 md:text-xs"
            >
                <span
                    >© 2026 Sillerico & Asociados. Todos los derechos
                    reservados.</span
                >
                <span>Diseñado con Excelencia y Rigor Académico Jurídico.</span>
            </div>
        </footer>

        <!-- 11. DIALOG MODAL FOR REGLAMENTO INTERNO -->
        <div
            v-if="isReglamentoModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-950/80 p-4 backdrop-blur-sm"
        >
            <div
                class="relative flex max-h-[85vh] w-full max-w-2xl animate-in flex-col rounded-xl border border-[#c5a059]/30 bg-[#051f18] p-6 text-[#eae5da] shadow-2xl duration-200 zoom-in-95 fade-in"
                @click.stop
            >
                <button
                    @click="isReglamentoModalOpen = false"
                    class="absolute top-4 right-4 cursor-pointer text-lg font-bold text-amber-100/60 hover:text-white"
                >
                    ✕
                </button>

                <div class="mb-4 shrink-0 space-y-2 text-center">
                    <div
                        class="mx-auto flex size-10 items-center justify-center rounded-full border border-[#c5a059]/30 bg-[#c5a059]/10 text-xl text-[#c5a059]"
                    >
                        📜
                    </div>
                    <h3
                        class="font-['Cinzel',serif] text-base font-bold text-[#c5a059] md:text-lg"
                    >
                        Reglamento Interno de Trabajo
                    </h3>
                    <p class="text-xs font-light text-neutral-400">
                        Sillerico & Asociados – Consorcio Jurídico
                    </p>
                </div>

                <div
                    class="custom-scrollbar flex-1 space-y-6 overflow-y-auto pr-2"
                >
                    <div
                        v-for="ch in reglamentoChapters"
                        :key="ch.title"
                        class="space-y-3"
                    >
                        <h4
                            class="border-b border-[#c5a059]/20 pb-1 font-['Cinzel',serif] text-xs font-bold tracking-wider text-[#c5a059] uppercase md:text-sm"
                        >
                            {{ ch.title }}
                        </h4>
                        <div class="space-y-3 pl-2">
                            <div
                                v-for="art in ch.articles"
                                :key="art.num"
                                class="space-y-1"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded bg-[#c5a059]/15 px-1.5 py-0.5 text-[10px] font-bold tracking-widest text-[#c5a059] uppercase"
                                        >{{ art.num }}</span
                                    >
                                    <span
                                        class="font-['Cinzel',serif] text-xs font-semibold text-white"
                                        >{{ art.title }}</span
                                    >
                                </div>
                                <p
                                    class="pl-1 text-xs leading-relaxed font-light text-neutral-300"
                                >
                                    {{ art.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-6 flex shrink-0 justify-end border-t border-[#c5a059]/20 pt-4"
                >
                    <button
                        @click="isReglamentoModalOpen = false"
                        class="cursor-pointer rounded bg-[#c5a059] px-6 py-2 text-xs font-bold tracking-wider text-emerald-950 transition-all hover:bg-[#d6b46c]"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <SplashScreen
            :show="showSplash"
            @reveal="handleSplashReveal"
            @finish="handleSplashFinish"
        />

        <!-- 12. CURTAIN TRANSITION OVERLAY (TRANSICIÓN DE CORTINA ENTRE MENÚS) -->
        <div
            v-if="isCurtainActive"
            class="pointer-events-auto fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden"
        >
            <!-- Sliding Panel -->
            <div
                class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-[#03150f] via-[#082a20] to-[#020d09] shadow-2xl transition-transform duration-700 ease-[cubic-bezier(0.77,0,0.175,1)]"
                :style="{
                    transform:
                        curtainDirection === 'l2r'
                            ? curtainPhase === 'idle'
                                ? 'translateX(-100%)'
                                : curtainPhase === 'uncovering'
                                  ? 'translateX(100%)'
                                  : 'translateX(0%)'
                            : curtainPhase === 'idle'
                              ? 'translateX(100%)'
                              : curtainPhase === 'uncovering'
                                ? 'translateX(-100%)'
                                : 'translateX(0%)',
                }"
            >
                <!-- Shining Gold Leading Edge Line -->
                <div
                    :class="[
                        'absolute top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#c5a059] via-amber-200 to-[#c5a059] shadow-[0_0_35px_#c5a059]',
                        curtainDirection === 'l2r' ? 'right-0' : 'left-0',
                    ]"
                ></div>

                <!-- Faint Radial Background Glow -->
                <div
                    class="pointer-events-none absolute h-[600px] w-[600px] rounded-full bg-[#c5a059]/15 blur-3xl"
                ></div>

                <!-- Center Vertical Logo Image inside Curtain (logo-main.jpg sin texto) -->
                <div
                    class="relative z-10 flex animate-in items-center justify-center p-4 duration-300 zoom-in-95 fade-in"
                >
                    <div
                        class="relative flex max-h-[70vh] max-w-[85vw] items-center justify-center overflow-hidden rounded-2xl border-2 border-[#c5a059]/50 bg-[#051f18]/90 p-3 shadow-[0_0_60px_rgba(197,160,89,0.45)] backdrop-blur-md sm:max-w-md"
                    >
                        <img
                            src="/images/logo-main.jpg"
                            alt="Sillerico & Asociados Logo Principal"
                            class="max-h-[55vh] w-auto rounded-xl object-contain drop-shadow-[0_6px_20px_rgba(0,0,0,0.7)] filter sm:max-h-[60vh]"
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
.text-center p,
p.text-center {
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
    transition:
        opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1),
        transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
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
