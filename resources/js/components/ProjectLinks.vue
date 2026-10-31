<script setup>
defineProps({
    liveUrl: { type: String, default: null },
    repoUrl: { type: String, default: null },
});

// The button names the destination ("Visit stock-yard.com"), so a visitor knows
// exactly where they are going before they leave the portfolio.
const host = (url) => {
    try {
        return new URL(url).host.replace(/^www\./, '');
    } catch {
        return url;
    }
};
</script>

<template>
    <div v-if="liveUrl || repoUrl" class="flex flex-wrap gap-2">
        <a
            v-if="liveUrl"
            :href="liveUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-1.5 rounded-md bg-[color:var(--color-signal)] px-3.5 py-2 text-sm font-medium text-[color:var(--color-surface)] transition hover:opacity-90"
        >
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z" stroke-linejoin="round" />
            </svg>
            Visit {{ host(liveUrl) }}
            <!-- SVG, not "↗": Windows swaps that glyph for a colour emoji. -->
            <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sr-only">(opens in a new tab)</span>
        </a>
        <a
            v-if="repoUrl"
            :href="repoUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-1.5 rounded-md border border-[color:var(--color-wire)] px-3.5 py-2 text-sm font-medium transition hover:border-[color:var(--color-signal)]"
        >
            Source code
            <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sr-only">(opens in a new tab)</span>
        </a>
    </div>
</template>
