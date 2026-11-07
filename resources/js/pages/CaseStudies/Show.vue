<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteShell from '../../components/SiteShell.vue';
import ArchitectureDiagram from '../../components/ArchitectureDiagram.vue';
import PageHead from '../../components/PageHead.vue';
import ProjectLinks from '../../components/ProjectLinks.vue';
import StageBadge from '../../components/StageBadge.vue';

const props = defineProps({
    meta: { type: Object, required: true },
    profile: { type: Object, required: true },
    study: { type: Object, required: true },
});

const implementationSections = computed(() =>
    (props.study.implementation ?? '')
        // Blank lines may hold stray spaces or CRLF when edited in the admin textarea.
        .split(/\r?\n[ \t]*\r?\n/)
        .map((paragraph) => paragraph.trim())
        .filter(Boolean)
        .map((paragraph) => {
            const [firstLine, ...bodyLines] = paragraph.split(/\r?\n/);
            const hasHeading = firstLine.trim().endsWith(':') && bodyLines.length > 0;

            return {
                heading: hasHeading ? firstLine.trim().slice(0, -1) : null,
                body: hasHeading ? bodyLines.join('\n') : paragraph,
            };
        }),
);
</script>

<template>
    <SiteShell :name="profile.name">
        <PageHead :meta="meta" />
        <article class="mx-auto max-w-3xl px-5 py-12 sm:px-8 sm:py-16">
            <Link href="/#projects" class="font-mono text-xs text-[color:var(--color-ink-muted)] hover:text-[color:var(--color-signal)]">
                ← all projects
            </Link>

            <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2">
                <StageBadge :stage="study.stage" />
                <span v-if="study.years" class="font-mono text-xs text-[color:var(--color-ink-muted)]">{{ study.years }}</span>
            </div>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-balance sm:text-4xl">{{ study.title }}</h1>
            <p v-if="study.subtitle" class="mt-3 text-lg text-[color:var(--color-ink-muted)]">{{ study.subtitle }}</p>
            <p v-if="study.summary" class="mt-5 max-w-2xl leading-relaxed text-[color:var(--color-ink-muted)]">
                {{ study.summary }}
            </p>

            <section v-if="study.context || study.role || study.clientLabel || study.outcomes.length" class="mt-8 grid gap-4 sm:grid-cols-2">
                <div v-if="study.context" class="rounded-lg border border-[color:var(--color-wire)] p-4 sm:col-span-2">
                    <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Challenge</h2>
                    <p class="mt-2 text-sm leading-relaxed text-[color:var(--color-ink-muted)]">{{ study.context }}</p>
                </div>
                <div v-if="study.role || study.clientLabel" class="rounded-lg border border-[color:var(--color-wire)] p-4">
                    <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">
                        {{ study.role ? 'My role' : 'Client' }}
                    </h2>
                    <p v-if="study.role" class="mt-2 text-sm leading-relaxed">{{ study.role }}</p>
                    <p v-if="study.clientLabel" class="text-xs text-[color:var(--color-ink-muted)]" :class="study.role ? 'mt-1' : 'mt-2'">
                        {{ study.clientLabel }}
                    </p>
                </div>
                <div v-if="study.outcomes.length" class="rounded-lg border border-[color:var(--color-wire)] p-4">
                    <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Key result</h2>
                    <p class="mt-2 font-mono text-2xl font-semibold text-[color:var(--color-signal)]">{{ study.outcomes[0].value }}</p>
                    <p class="mt-1 text-sm font-medium">{{ study.outcomes[0].metric }}</p>
                    <p v-if="study.outcomes[0].note" class="mt-1 text-xs leading-relaxed text-[color:var(--color-ink-muted)]">
                        {{ study.outcomes[0].note }}
                    </p>
                </div>
            </section>

            <ProjectLinks :live-url="study.liveUrl" :repo-url="study.repoUrl" class="mt-6" />

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

            <section v-if="implementationSections.length" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">Implementation</h2>
                <div class="mt-4 space-y-5">
                    <article v-for="(section, index) in implementationSections" :key="index">
                        <h3 v-if="section.heading" class="font-semibold">{{ section.heading }}</h3>
                        <p class="mt-1 leading-relaxed whitespace-pre-line text-[color:var(--color-ink-muted)]">{{ section.body }}</p>
                    </article>
                </div>
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

            <section v-if="study.roadmap.length" class="mt-10">
                <h2 class="font-mono text-xs tracking-widest text-[color:var(--color-signal)] uppercase">What's next</h2>
                <ul class="mt-4 space-y-2.5" role="list">
                    <li
                        v-for="item in study.roadmap"
                        :key="item"
                        class="flex gap-3 leading-relaxed text-[color:var(--color-ink-muted)]"
                    >
                        <span class="font-mono text-[color:var(--color-signal)]" aria-hidden="true">›</span>
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </section>

            <ProjectLinks :live-url="study.liveUrl" :repo-url="study.repoUrl" class="mt-10" />
        </article>
    </SiteShell>
</template>
