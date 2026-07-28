<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    (e: 'reveal'): void;
    (e: 'finish'): void;
}>();

const phase = ref<'enter' | 'hold' | 'reveal' | 'done'>(props.show ? 'enter' : 'done');
const logoVisible = ref(false);

const destTop = ref(0);
const destLeft = ref(0);
const destScale = ref(0.125);

let timers: ReturnType<typeof setTimeout>[] = [];

const clearTimers = () => {
    timers.forEach((t) => clearTimeout(t));
    timers = [];
};

const updateDestinationCoordinates = () => {
    if (typeof window === 'undefined') return;
    const headerLogo = document.getElementById('header-logo-box');
    const splashLogo = document.querySelector('.splash-logo');
    if (headerLogo && splashLogo) {
        const headerRect = headerLogo.getBoundingClientRect();
        const splashRect = splashLogo.getBoundingClientRect();
        
        // Target the center of the header logo
        destLeft.value = headerRect.left + headerRect.width / 2;
        destTop.value = headerRect.top + headerRect.height / 2;
        
        // Calculate the scale ratio dynamically based on the rendered width
        destScale.value = splashRect.width > 0 ? (headerRect.width / splashRect.width) : 0.125;
    }
};

const runSequence = () => {
    clearTimers();
    phase.value = 'enter';
    logoVisible.value = false;

    timers.push(
        setTimeout(() => {
            logoVisible.value = true;
        }, 120),
    );

    timers.push(
        setTimeout(() => {
            phase.value = 'hold';
        }, 1200),
    );

    timers.push(
        setTimeout(() => {
            updateDestinationCoordinates();
            phase.value = 'reveal';
            emit('reveal');
        }, 1600),
    );

    timers.push(
        setTimeout(() => {
            phase.value = 'done';
            emit('finish');
        }, 2400),
    );
};

onMounted(() => {
    if (props.show) {
        runSequence();
    } else {
        phase.value = 'done';
    }
});

watch(
    () => props.show,
    (val) => {
        if (val) {
            runSequence();
        }
    },
);

onBeforeUnmount(() => {
    clearTimers();
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="phase !== 'done'"
            class="splash-root"
            :class="{
                'splash-enter': phase === 'enter' || phase === 'hold',
                'splash-reveal': phase === 'reveal',
            }"
            aria-hidden="true"
        >
            <div class="splash-bg absolute inset-0"></div>

            <div class="splash-glow absolute inset-0 pointer-events-none"></div>

            <div class="splash-logo-wrap">
                <div
                    class="splash-logo-inner"
                    :class="{
                        'is-visible': logoVisible,
                    }"
                    :style="phase === 'reveal' ? {
                        top: `${destTop}px`,
                        left: `${destLeft}px`,
                        transform: `translate(-50%, -50%) scale(${destScale})`
                    } : {}"
                >
                    <div
                        class="splash-ring absolute inset-0 -m-6 rounded-full border border-[#c5a059]/30"
                    ></div>
                    <div
                        class="splash-ring splash-ring-2 absolute inset-0 -m-12 rounded-full border border-[#c5a059]/15"
                    ></div>
                    <img
                        src="/images/logo-splash.png"
                        alt="Sillerico &amp; Asociados"
                        class="splash-logo relative h-64 w-64 md:h-80 md:w-80 object-contain drop-shadow-[0_10px_40px_rgba(0,0,0,0.6)]"
                    />
                </div>

                <div
                    class="splash-text flex flex-col items-center text-center"
                    :class="{ 'is-visible': logoVisible }"
                >
                    <span
                        class="font-['Cinzel',serif] text-[#c5a059] text-sm md:text-base font-bold tracking-[0.4em] uppercase"
                    >
                        Sillerico &amp; Asociados
                    </span>
                    <div
                        class="splash-line mt-4 h-px bg-gradient-to-r from-transparent via-[#c5a059] to-transparent"
                    ></div>
                    <span
                        class="mt-4 text-[10px] md:text-xs tracking-[0.5em] uppercase text-amber-200/70 font-light"
                    >
                        Firma de Abogados
                    </span>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.splash-root {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background-color: transparent;
    pointer-events: auto;
    transition: pointer-events 0s;
}

.splash-reveal {
    pointer-events: none;
}

.splash-bg {
    background:
        radial-gradient(
            ellipse at center,
            rgba(197, 160, 89, 0.12) 0%,
            transparent 55%
        ),
        linear-gradient(180deg, #082a20 0%, #041510 100%);
    opacity: 1;
    transition: opacity 0.8s ease-in-out;
}

.splash-reveal .splash-bg {
    opacity: 0;
}

.splash-glow {
    background:
        radial-gradient(
            circle at 50% 50%,
            rgba(197, 160, 89, 0.08) 0%,
            transparent 40%
        );
    animation: splashGlow 2.4s ease-in-out infinite;
    transform-origin: center;
    opacity: 1;
    transition: opacity 0.6s ease-in-out;
}

.splash-reveal .splash-glow {
    opacity: 0;
}

@keyframes splashGlow {
    0%,
    100% {
        opacity: 0.6;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
}

/* Absolute centering layout for morph transition */
.splash-logo-wrap {
    position: absolute;
    inset: 0;
}

.splash-logo-inner {
    position: absolute;
    top: calc(50vh - 100px);
    left: 50%;
    transform: translate(-50%, -50%) scale(1);
    opacity: 0;
    transition:
        opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        top 0.8s cubic-bezier(0.25, 1, 0.3, 1),
        left 0.8s cubic-bezier(0.25, 1, 0.3, 1),
        transform 0.8s cubic-bezier(0.25, 1, 0.3, 1);
}

@media (max-width: 767px) {
    .splash-logo-inner {
        top: calc(50vh - 80px);
    }
}

.splash-logo-inner.is-visible {
    opacity: 1;
}

.splash-text {
    position: absolute;
    top: calc(50vh + 140px);
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    opacity: 0;
    transition:
        opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        transform 1s cubic-bezier(0.22, 1, 0.36, 1);
}

@media (max-width: 767px) {
    .splash-text {
        top: calc(50vh + 110px);
    }
}

.splash-text.is-visible {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

/* Fade out text and rings instantly on reveal */
.splash-reveal .splash-text {
    opacity: 0 !important;
    transform: translate(-50%, -30%) scale(0.8);
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.splash-ring {
    animation: ringPulse 3s ease-in-out infinite;
    transform-origin: center;
    opacity: 0.6;
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.splash-reveal .splash-ring {
    opacity: 0 !important;
    transform: scale(0.5);
}

.splash-ring-2 {
    animation-delay: 0.5s;
}

@keyframes ringPulse {
    0%,
    100% {
        transform: scale(1);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.04);
        opacity: 1;
    }
}

.splash-line {
    width: 0;
    transition: width 0.9s cubic-bezier(0.22, 1, 0.36, 1) 0.25s;
}

.splash-text.is-visible .splash-line {
    width: 220px;
}

@media (prefers-reduced-motion: reduce) {
    .splash-logo-inner,
    .splash-text,
    .splash-line {
        transition: none !important;
        animation: none !important;
    }
}
</style>
