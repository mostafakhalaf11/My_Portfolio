<script setup>
import { Link } from '@inertiajs/vue3';
import PageHead from '../components/PageHead.vue';
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

const highlights = (section('hero').settings?.proof ?? '')
    .split('·')
    .map((item) => item.trim())
    .filter(Boolean);

const visibleSkills = (group) =>
    section('stack').settings?.primary_only ? group.skills.filter((skill) => skill.isPrimary) : group.skills;
</script>

<template>
    <SiteShell :name="profile.name">
        <PageHead :meta="meta" />

        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-[color:var(--color-wire)]">
            <div class="bg-blueprint pointer-events-none absolute inset-0 opacity-70" aria-hidden="true"></div>

            <div
                class="relative mx-auto grid max-w-5xl items-center gap-10 px-5 py-10 sm:px-8 sm:py-20 lg:grid-cols-[minmax(0,1fr)_auto] lg:gap-14"
            >
                <div>
                    <div class="mb-5 flex items-center gap-3">
                        <!-- Mobile and tablet: a small avatar beside the badge keeps the hero short. -->
                        <img
                            v-if="profile.avatarUrl"
                            :src="profile.avatarUrl"
                            :alt="profile.name"
                            width="56"
                            height="56"
                            class="size-14 shrink-0 rounded-full object-cover ring-2 ring-[color:var(--color-wire)] ring-offset-2 ring-offset-[color:var(--color-surface)] lg:hidden"
                        />
                        <p
                            v-if="profile.isAvailable && section('hero').settings?.show_availability"
                            class="inline-flex items-center gap-2 rounded-full border border-[color:var(--color-wire)] bg-[color:var(--color-surface-raised)] px-3 py-1 font-mono text-xs"
                        >
                            <span class="relative flex size-2">
                                <span class="absolute inline-flex size-full animate-ping rounded-full bg-[color:var(--color-signal)] opacity-60"></span>
                                <span class="relative inline-flex size-2 rounded-full bg-[color:var(--color-signal)]"></span>
                            </span>
                            Open to full-stack and backend roles
                        </p>
                    </div>

                    <h1 class="max-w-3xl text-3xl font-bold tracking-tight text-balance sm:text-5xl">
                        {{ section('hero').heading ?? profile.headline }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-base text-[color:var(--color-ink-muted)] sm:text-lg">
                        {{ section('hero').subheading }}
                    </p>

                    <!-- Each "·"-separated capability stays on one line, so phrases never split. -->
                    <ul
                        v-if="highlights.length"
                        class="mt-4 flex flex-wrap gap-x-4 gap-y-1.5 font-mono text-xs text-[color:var(--color-signal)] sm:text-sm"
                        role="list"
                    >
                        <li v-for="item in highlights" :key="item" class="inline-flex items-center gap-2 whitespace-nowrap">
                            <span class="size-1 rounded-full bg-[color:var(--color-signal)]" aria-hidden="true"></span>{{ item }}
                        </li>
                    </ul>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a
                            href="#projects"
                            class="rounded-md bg-[color:var(--color-signal)] px-4 py-2.5 text-sm font-medium text-[color:var(--color-surface)] transition hover:opacity-90"
                        >
                            View projects
                        </a>
                        <a
                            v-if="profile.resumeUrl"
                            :href="profile.resumeUrl"
                            download
                            class="rounded-md border border-[color:var(--color-wire)] px-4 py-2.5 text-sm font-medium transition hover:border-[color:var(--color-signal)]"
                        >
                            Download CV
                        </a>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-[color:var(--color-ink-muted)]">
                        <a
                            v-if="profile.githubUrl"
                            :href="profile.githubUrl"
                            target="_blank"
                            rel="noopener"
                            class="hover:text-[color:var(--color-signal)]"
                        >
                            GitHub
                        </a>
                        <a
                            v-if="profile.linkedinUrl"
                            :href="profile.linkedinUrl"
                            target="_blank"
                            rel="noopener"
                            class="hover:text-[color:var(--color-signal)]"
                        >
                            LinkedIn
                        </a>
                        <a
                            href="#contact"
                            class="hover:text-[color:var(--color-signal)]"
                        >
                            Contact
                        </a>
                    </div>
                </div>

                <!-- Desktop: portrait in a blueprint frame, the same corner-tick language as the grid. -->
                <figure v-if="profile.avatarUrl" class="relative hidden lg:block">
                    <div class="relative size-64 p-3">
                        <span class="absolute top-0 left-0 size-5 border-t-2 border-l-2 border-[color:var(--color-signal)]" aria-hidden="true"></span>
                        <span class="absolute top-0 right-0 size-5 border-t-2 border-r-2 border-[color:var(--color-signal)]" aria-hidden="true"></span>
                        <span class="absolute bottom-0 left-0 size-5 border-b-2 border-l-2 border-[color:var(--color-signal)]" aria-hidden="true"></span>
                        <span class="absolute right-0 bottom-0 size-5 border-r-2 border-b-2 border-[color:var(--color-signal)]" aria-hidden="true"></span>
                        <img
                            :src="profile.avatarUrl"
                            :alt="profile.name"
                            width="232"
                            height="232"
                            fetchpriority="high"
                            class="size-full rounded-full object-cover shadow-xl ring-1 ring-[color:var(--color-wire)] shadow-black/20"
                        />
                    </div>
                    <figcaption class="mt-4 text-center font-mono text-xs text-[color:var(--color-ink-muted)]">
                        <span class="text-[color:var(--color-signal)]">~/</span>{{ profile.location }}
                    </figcaption>
                </figure>
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
                                Explore {{ study.title.split(' — ')[0] }} case study
                                <span aria-hidden="true">→</span>
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="about" class="border-b border-[color:var(--color-wire)] py-14 sm:py-16">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <h2 class="text-xl font-bold tracking-tight sm:text-2xl">About</h2>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-[color:var(--color-ink-muted)] sm:text-base">
                    {{ profile.summary }}
                </p>
                <p
                    v-if="section('hero').settings?.now"
                    class="mt-4 flex max-w-3xl gap-3 font-mono text-xs leading-relaxed text-[color:var(--color-ink-muted)] sm:text-sm"
                >
                    <span class="shrink-0 font-semibold text-[color:var(--color-signal)]">Now</span>
                    <span>{{ section('hero').settings.now }}</span>
                </p>
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
                                v-for="skill in visibleSkills(group)"
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
