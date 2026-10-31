<script setup>
defineProps({
    stage: { type: Object, default: null }, // { value: 'live' | 'staging' | 'development', label }
});

// Full class strings, not concatenated, so Tailwind's scanner sees every one.
const TONES = {
    live: {
        pill: 'bg-[color:var(--color-signal-soft)] text-[color:var(--color-signal)]',
        dot: 'bg-[color:var(--color-signal)]',
    },
    staging: {
        pill: 'bg-[color:var(--color-caution-soft)] text-[color:var(--color-caution)]',
        dot: 'bg-[color:var(--color-caution)]',
    },
    development: {
        pill: 'border border-[color:var(--color-wire)] text-[color:var(--color-ink-muted)]',
        dot: 'bg-[color:var(--color-ink-muted)]',
    },
};
</script>

<template>
    <span
        v-if="stage"
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 font-mono text-[11px] font-medium"
        :class="TONES[stage.value]?.pill ?? TONES.development.pill"
    >
        <span class="relative flex size-1.5">
            <span
                v-if="stage.value === 'live'"
                class="absolute inline-flex size-full animate-ping rounded-full bg-[color:var(--color-signal)] opacity-60"
            ></span>
            <span class="relative inline-flex size-1.5 rounded-full" :class="TONES[stage.value]?.dot ?? TONES.development.dot"></span>
        </span>
        {{ stage.label }}
    </span>
</template>
