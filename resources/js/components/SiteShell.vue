<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    name: { type: String, default: '' },
});

const isDark = ref(true);
const menuOpen = ref(false);

const applyTheme = (dark) => {
    document.documentElement.classList.toggle('dark', dark);
    try {
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    } catch (e) {
        /* storage blocked — theme still applies for this page view */
    }
};

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    applyTheme(isDark.value);
};

const links = [
    { href: '/#case-studies', label: 'Case studies' },
    { href: '/#stack', label: 'Stack' },
    { href: '/#experience', label: 'Experience' },
    { href: '/#contact', label: 'Contact' },
];
</script>

<template>
    <div class="min-h-dvh">
        <a
            href="#main"
            class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:rounded focus:bg-[color:var(--color-surface-raised)] focus:px-3 focus:py-2"
        >
            Skip to content
        </a>

        <header
            class="sticky top-0 z-40 border-b border-[color:var(--color-wire)] bg-[color:var(--color-surface)]/85 backdrop-blur"
        >
            <div class="mx-auto flex h-14 max-w-5xl items-center justify-between px-5 sm:px-8">
                <Link href="/" class="font-mono text-sm font-medium tracking-tight">
                    <span class="text-[color:var(--color-signal)]">~/</span>{{ name || 'home' }}
                </Link>

                <nav class="hidden items-center gap-6 md:flex" aria-label="Primary">
                    <a
                        v-for="link in links"
                        :key="link.href"
                        :href="link.href"
                        class="text-sm text-[color:var(--color-ink-muted)] transition hover:text-[color:var(--color-ink)]"
                    >
                        {{ link.label }}
                    </a>
                </nav>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        class="grid size-11 place-items-center rounded-md text-[color:var(--color-ink-muted)] transition hover:text-[color:var(--color-ink)]"
                        :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
                        @click="toggleTheme"
                    >
                        <svg v-if="isDark" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4" stroke-linecap="round" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="grid size-11 place-items-center rounded-md text-[color:var(--color-ink-muted)] md:hidden"
                        :aria-expanded="menuOpen"
                        aria-label="Toggle navigation"
                        @click="menuOpen = !menuOpen"
                    >
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav
                v-if="menuOpen"
                class="border-t border-[color:var(--color-wire)] px-5 pb-3 md:hidden"
                aria-label="Mobile"
            >
                <a
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    class="block py-3 text-sm text-[color:var(--color-ink-muted)]"
                    @click="menuOpen = false"
                >
                    {{ link.label }}
                </a>
            </nav>
        </header>

        <main id="main">
            <slot />
        </main>

        <footer class="border-t border-[color:var(--color-wire)] py-8">
            <div
                class="mx-auto flex max-w-5xl flex-col gap-2 px-5 font-mono text-xs text-[color:var(--color-ink-muted)] sm:flex-row sm:items-center sm:justify-between sm:px-8"
            >
                <span>© {{ new Date().getFullYear() }} {{ name }}</span>
                <span>Laravel 13 · Inertia · Vue 3 · Filament 5</span>
            </div>
        </footer>
    </div>
</template>
