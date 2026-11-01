<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteShell from '../components/SiteShell.vue';
import ProjectLinks from '../components/ProjectLinks.vue';
import StageBadge from '../components/StageBadge.vue';

const props = defineProps({
    meta: { type: Object, required: true },
    profile: { type: Object, required: true },
    sections: { type: Object, default: () => ({}) },
    caseStudies: { type: Array, default: () => [] },
    skillGroups: { type: Array, default: () => [] },
    experiences: { type: Array, default: () => [] },
    certifications: { type: Array, default: () => [] },
    education: { type: Array, default: () => [] },
});

const section = (key) => props.sections?.[key] ?? {};
</script>

<template>
    <!-- Same values the server printed (resources/views/app.blade.php); head-key pairs them up. -->
    <Head>
        <title>{{ meta.title }}</title>
        <meta head-key="description" name="description" :content="meta.description" />
    </Head>

    <SiteShell :name="profile.name">
        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-[color:var(--color-wire)]">
            <div class="bg-blueprint pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-5xl px-5 py-16 sm:px-8 sm:py-24">
                <p
                    v-if="profile.isAvailable && section('hero').settings?.show_availability"
                    class="mb-5 inline-flex items-center gap-2 rounded-full border border-[color:var(--color-wire)] bg-[color:var(--color-surface-raised)] px-3 py-1 font-mono text-xs"
                >
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-[color:var(--color-signal)] opacity-60"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-[color:var(--color-signal)]"></span>
                    </span>
                    Open to full-stack and backend roles
                </p>

                <h1 class="max-w-3xl text-3xl font-bold tracking-tight text-balance sm:text-5xl">
                    {{ section('hero').heading ?? profile.headline }}
                </h1>

                <p class="mt-4 max-w-2xl text-base text-[color:var(--color-ink-muted)] sm:text-lg">
                    {{ section('hero').subheading }}
                </p>

                <p class="mt-6 max-w-2xl text-sm leading-relaxed text-[color:var(--color-ink-muted)]">
                    {{ profile.summary }}
                </p>

                <p
                    v-if="section('hero').settings?.now"
                    class="mt-5 flex max-w-2xl gap-3 font-mono text-xs leading-relaxed text-[color:var(--color-ink-muted)]"
                >
                    <span class="shrink-0 font-semibold text-[color:var(--color-signal)]">now →</span>
                    <span>{{ section('hero').settings.now }}</span>
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a
                        href="#projects"
                        class="rounded-md bg-[color:var(--color-signal)] px-4 py-2.5 text-sm font-medium text-[color:var(--color-surface)] transition hover:opacity-90"
                    >
                        See my projects
                    </a>
                    <a
                        v-if="profile.githubUrl"
                        :href="profile.githubUrl"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-2 rounded-md border border-[color:var(--color-wire)] px-4 py-2.5 text-sm font-medium transition hover:border-[color:var(--color-signal)]"
                    >
                        <svg viewBox="0 0 24 24" class="size-4" fill="currentColor" aria-hidden="true">
                            <path d="M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.56 9.56 0 0 1 5 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.57 4.93.36.31.68.92.68 1.85v2.75c0 .27.18.58.69.48A10 10 0 0 0 12 2Z" />
                        </svg>
                        GitHub
                    </a>
                    <a
                        v-if="profile.linkedinUrl"
                        :href="profile.linkedinUrl"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-2 rounded-md border border-[color:var(--color-wire)] px-4 py-2.5 text-sm font-medium transition hover:border-[color:var(--color-signal)]"
                    >
                        <svg viewBox="0 0 24 24" class="size-4" fill="currentColor" aria-hidden="true">
                            <path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0Z" />
                        </svg>
                        LinkedIn
                    </a>
                    <a
                        href="#contact"
                        class="rounded-md border border-[color:var(--color-signal)] px-4 py-2.5 text-sm font-medium text-[color:var(--color-signal)] transition hover:bg-[color:var(--color-signal-soft)]"
                    >
                        Get in touch
                    </a>
                    <a
                        v-if="profile.resumeUrl"
                        :href="profile.resumeUrl"
                        download
                        class="inline-flex items-center gap-2 rounded-md border border-[color:var(--color-wire)] px-4 py-2.5 text-sm font-medium transition hover:border-[color:var(--color-signal)]"
                    >
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 4v11m0 0-4-4m4 4 4-4M5 19h14" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Download CV
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured projects (CMS section key: case_studies) -->
        <section id="projects" class="border-b border-[color:var(--color-wire)] py-16 sm:py-20">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ section('case_studies').heading }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-[color:var(--color-ink-muted)]">
                    {{ section('case_studies').subheading }}
                </p>

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    <article
                        v-for="study in caseStudies"
                        :key="study.slug"
                        class="flex flex-col rounded-xl border border-[color:var(--color-wire)] bg-[color:var(--color-surface-raised)] p-5 transition hover:border-[color:var(--color-signal)]"
                    >
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                            <StageBadge :stage="study.stage" />
                            <span v-if="study.years" class="font-mono text-xs text-[color:var(--color-ink-muted)]">{{ study.years }}</span>
                        </div>

                        <p class="mt-3 font-mono text-xs text-[color:var(--color-ink-muted)]">
                            {{ study.clientLabel }}<span v-if="study.role"> · {{ study.role }}</span>
                        </p>

                        <h3 class="mt-2 text-lg font-semibold">
                            <Link :href="`/projects/${study.slug}`" class="hover:text-[color:var(--color-signal)]">
                                {{ study.title }}
                            </Link>
                        </h3>

                        <p class="mt-2 text-sm text-[color:var(--color-ink-muted)]">{{ study.summary }}</p>

                        <dl v-if="study.outcomes.length" class="mt-4 grid gap-2">
                            <div v-for="outcome in study.outcomes" :key="outcome.metric" class="flex gap-2 text-sm">
                                <dt class="text-[color:var(--color-ink-muted)]">{{ outcome.metric }}:</dt>
                                <dd class="font-mono font-medium text-[color:var(--color-signal)]">{{ outcome.value }}</dd>
                            </div>
                        </dl>

                        <ul class="mt-4 flex flex-wrap gap-1.5" role="list">
                            <li
                                v-for="tech in study.tech.slice(0, 6)"
                                :key="tech"
                                class="rounded border border-[color:var(--color-wire)] px-2 py-0.5 font-mono text-[11px] text-[color:var(--color-ink-muted)]"
                            >
                                {{ tech }}
                            </li>
                        </ul>

                        <!-- mt-auto pins the action row to the bottom so side-by-side cards line up. -->
                        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-5">
                            <ProjectLinks :live-url="study.liveUrl" :repo-url="study.repoUrl" />
                            <Link
                                :href="`/projects/${study.slug}`"
                                class="inline-flex items-center gap-1 text-sm font-medium text-[color:var(--color-signal)]"
                            >
                                Read the decisions →
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Stack -->
        <section id="stack" class="border-b border-[color:var(--color-wire)] py-16 sm:py-20">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ section('stack').heading }}</h2>
                <p class="mt-2 text-sm text-[color:var(--color-ink-muted)]">{{ section('stack').subheading }}</p>

                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    <div
                        v-for="group in skillGroups"
                        :key="group.name"
                        class="rounded-xl border border-[color:var(--color-wire)] p-5"
                    >
                        <h3 class="font-mono text-sm font-semibold text-[color:var(--color-signal)]">{{ group.name }}</h3>
                        <p class="mt-1 text-xs text-[color:var(--color-ink-muted)]">{{ group.description }}</p>

                        <ul class="mt-3 flex flex-wrap gap-1.5" role="list">
                            <li
                                v-for="skill in group.skills"
                                :key="skill.name"
                                class="rounded px-2 py-0.5 text-[11px]"
                                :class="
                                    skill.isPrimary
                                        ? 'bg-[color:var(--color-signal-soft)] font-medium text-[color:var(--color-signal)]'
                                        : 'border border-[color:var(--color-wire)] text-[color:var(--color-ink-muted)]'
                                "
                            >
                                {{ skill.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Experience -->
        <section id="experience" class="border-b border-[color:var(--color-wire)] py-16 sm:py-20">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ section('experience').heading }}</h2>

                <ol class="mt-8 space-y-8" role="list">
                    <li
                        v-for="job in experiences"
                        :key="`${job.company}-${job.title}`"
                        class="relative border-l border-[color:var(--color-wire)] pl-5 sm:pl-6"
                    >
                        <span
                            class="absolute top-1.5 -left-[5px] size-2.5 rounded-full"
                            :class="job.isCurrent ? 'bg-[color:var(--color-signal)]' : 'bg-[color:var(--color-wire)]'"
                        ></span>

                        <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <h3 class="font-semibold">{{ job.title }}</h3>
                            <span class="font-mono text-xs text-[color:var(--color-ink-muted)]">{{ job.period }}</span>
                        </div>

                        <p class="mt-0.5 text-sm text-[color:var(--color-ink-muted)]">
                            <a
                                v-if="job.companyUrl"
                                :href="job.companyUrl"
                                target="_blank"
                                rel="noopener"
                                class="font-medium text-[color:var(--color-ink)] hover:text-[color:var(--color-signal)]"
                            >{{ job.company }}</a>
                            <span v-else class="font-medium text-[color:var(--color-ink)]">{{ job.company }}</span>
                            <span v-if="job.location"> · {{ job.location }}</span>
                            <span v-if="job.summary"> · {{ job.summary }}</span>
                        </p>

                        <ul class="mt-3 space-y-1.5" role="list">
                            <li
                                v-for="highlight in job.highlights"
                                :key="highlight"
                                class="flex gap-2 text-sm text-[color:var(--color-ink-muted)]"
                            >
                                <span class="mt-2 size-1 shrink-0 rounded-full bg-[color:var(--color-signal)]"></span>
                                <span>{{ highlight }}</span>
                            </li>
                        </ul>
                    </li>
                </ol>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="py-16 sm:py-20">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ section('contact').heading }}</h2>
                <p class="mt-2 text-sm text-[color:var(--color-ink-muted)]">{{ section('contact').subheading }}</p>

                <dl class="mt-6 grid gap-3 font-mono text-sm sm:grid-cols-2">
                    <div class="flex gap-2">
                        <dt class="text-[color:var(--color-ink-muted)]">email</dt>
                        <dd><a :href="`mailto:${profile.email}`" class="text-[color:var(--color-signal)]">{{ profile.email }}</a></dd>
                    </div>
                    <div v-if="profile.phone" class="flex gap-2">
                        <dt class="text-[color:var(--color-ink-muted)]">phone</dt>
                        <dd><a :href="profile.phoneLink" class="text-[color:var(--color-signal)]">{{ profile.phone }}</a></dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="text-[color:var(--color-ink-muted)]">location</dt>
                        <dd>{{ profile.location }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="text-[color:var(--color-ink-muted)]">linkedin</dt>
                        <dd><a :href="profile.linkedinUrl" target="_blank" rel="noopener" class="text-[color:var(--color-signal)]">profile</a></dd>
                    </div>
                </dl>

                <div class="mt-10 grid gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="font-mono text-xs tracking-widest text-[color:var(--color-ink-muted)] uppercase">Education</h3>
                        <div v-for="entry in education" :key="entry.degree" class="mt-2 text-sm">
                            <p class="font-medium">{{ entry.degree }}</p>
                            <p class="text-[color:var(--color-ink-muted)]">{{ entry.institution }} · {{ entry.period }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-mono text-xs tracking-widest text-[color:var(--color-ink-muted)] uppercase">Certifications</h3>
                        <ul class="mt-2 space-y-1 text-sm" role="list">
                            <li v-for="cert in certifications" :key="cert.name">
                                <span class="font-medium">{{ cert.name }}</span>
                                <span class="text-[color:var(--color-ink-muted)]">, {{ cert.issuer }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="font-mono text-xs tracking-widest text-[color:var(--color-ink-muted)] uppercase">Languages</h3>
                    <ul class="mt-2 flex flex-wrap gap-2 text-sm" role="list">
                        <li
                            v-for="lang in profile.languages"
                            :key="lang.name"
                            class="rounded border border-[color:var(--color-wire)] px-2 py-0.5"
                        >
                            {{ lang.name }} <span class="text-[color:var(--color-ink-muted)]">({{ lang.level }})</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </SiteShell>
</template>
