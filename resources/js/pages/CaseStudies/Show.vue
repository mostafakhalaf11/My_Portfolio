<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteShell from '../../components/SiteShell.vue';
import ArchitectureDiagram from '../../components/ArchitectureDiagram.vue';

defineProps({
    profile: { type: Object, required: true },
    study: { type: Object, required: true },
});
</script>

<template>
    <Head>
        <title>{{ study.title }}</title>
        <meta name="description" :content="study.summary" />
    </Head>

    <SiteShell :name="profile.name">
        <article class="mx-auto max-w-3xl px-5 py-12 sm:px-8 sm:py-16">
            <Link href="/#case-studies" class="font-mono text-xs text-[color:var(--color-ink-muted)] hover:text-[color:var(--color-signal)]">
                ← back
            </Link>

            <p class="mt-6 font-mono text-xs text-[color:var(--color-ink-muted)]">
                {{ study.clientLabel }}<span v-if="study.role"> · {{ study.role }}</span>
            </p>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-balance sm:text-4xl">{{ study.title }}</h1>
            <p v-if="study.subtitle" class="mt-3 text-lg text-[color:var(--color-ink-muted)]">{{ study.subtitle }}</p>

            <ul class="mt-5 flex flex-wrap gap-1.5" role="list">
                <li
                    v-for="tech in study.tech"
                    :key="tech"
                    class="rounded border border-[color:var(--color-wire)] px-2 py-0.5 font-mono text-[11px] text-[color:var(--color-ink-muted)]"
                >
                    {{ tech }}
                </li>
            </ul>

            <div
                v-if="study.diagram?.nodes?.length"
                class="mt-10 rounded-xl border border-[color:var(--color-wire)] bg-[color:var(--color-surface-raised)]/70 p-4 sm:p-6"
            >
                <ArchitectureDiagram :diagram="study.diagram" :height="380" />
            </div>

            <section v-if="study.context" class="mt-12">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Context</h2>
                <p class="mt-3 leading-relaxed text-[color:var(--color-ink-muted)]">{{ study.context }}</p>
            </section>

            <section v-if="study.constraints" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Constraints</h2>
                <p class="mt-3 leading-relaxed text-[color:var(--color-ink-muted)]">{{ study.constraints }}</p>
            </section>

            <section v-if="study.optionsRejected.length" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Options rejected</h2>
                <div class="mt-4 space-y-3">
                    <div
                        v-for="option in study.optionsRejected"
                        :key="option.option"
                        class="rounded-lg border border-[color:var(--color-wire)] p-4"
                    >
                        <p class="font-medium">{{ option.option }}</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-[color:var(--color-ink-muted)]">{{ option.why_rejected }}</p>
                    </div>
                </div>
            </section>

            <section v-if="study.decision" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Decision</h2>
                <p class="mt-3 leading-relaxed text-[color:var(--color-ink-muted)]">{{ study.decision }}</p>
            </section>

            <section v-if="study.implementation" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Implementation</h2>
                <p class="mt-3 leading-relaxed text-[color:var(--color-ink-muted)]">{{ study.implementation }}</p>
            </section>

            <section v-if="study.outcomes.length" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Outcomes</h2>
                <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="outcome in study.outcomes"
                        :key="outcome.metric"
                        class="rounded-lg border border-[color:var(--color-wire)] bg-[color:var(--color-surface-raised)] p-4"
                    >
                        <dt class="text-xs text-[color:var(--color-ink-muted)]">{{ outcome.metric }}</dt>
                        <dd class="mt-1 font-mono text-lg font-semibold text-[color:var(--color-signal)]">{{ outcome.value }}</dd>
                        <p v-if="outcome.note" class="mt-1 text-xs text-[color:var(--color-ink-muted)]">{{ outcome.note }}</p>
                    </div>
                </dl>
            </section>

            <div v-if="study.repoUrl || study.liveUrl" class="mt-10 flex flex-wrap gap-3">
                <a
                    v-if="study.liveUrl"
                    :href="study.liveUrl"
                    target="_blank"
                    rel="noopener"
                    class="rounded-md border border-[color:var(--color-wire)] px-4 py-2 text-sm transition hover:border-[color:var(--color-signal)]"
                >
                    Live site
                </a>
                <a
                    v-if="study.repoUrl"
                    :href="study.repoUrl"
                    target="_blank"
                    rel="noopener"
                    class="rounded-md border border-[color:var(--color-wire)] px-4 py-2 text-sm transition hover:border-[color:var(--color-signal)]"
                >
                    Repository
                </a>
            </div>
        </article>
    </SiteShell>
</template>
