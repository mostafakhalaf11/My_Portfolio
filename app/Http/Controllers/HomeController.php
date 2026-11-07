<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Certification;
use App\Models\EducationEntry;
use App\Models\Experience;
use App\Models\PageSection;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Support\PageMeta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $profile = Profile::current();
        $skillGroups = SkillGroup::with('skills')->orderBy('sort_order')->get();
        $experiences = Experience::published()->ordered()->get();
        $education = EducationEntry::orderBy('sort_order')->get();

        return Inertia::render('Home', [
            'meta' => $this->meta($profile, $skillGroups, $experiences, $education),

            'profile' => [
                'name' => $profile->name,
                'headline' => $profile->headline,
                'title' => $profile->title,
                'summary' => $profile->summary,
                'email' => $profile->email,
                'phone' => $profile->phone,
                'phoneLink' => $profile->phone_link,
                'location' => $profile->location,
                'githubUrl' => $profile->github_url,
                'linkedinUrl' => $profile->linkedin_url,
                'resumeUrl' => $profile->resumeUrl(),
                'avatarUrl' => $profile->avatarUrl(),
                'languages' => $profile->languages,
                'strengths' => $profile->strengths,
                'isAvailable' => $profile->is_available,
                'seo' => $profile->seo,
            ],

            'sections' => PageSection::visible()->where('page', 'home')->get()
                ->keyBy('key')
                ->map(fn (PageSection $s) => [
                    'heading' => $s->heading,
                    'subheading' => $s->subheading,
                    'settings' => $s->settings ?? [],
                ]),

            'caseStudies' => CaseStudy::published()->ordered()->get()
                ->map(fn (CaseStudy $s) => [
                    'slug' => $s->slug,
                    'title' => $s->title,
                    'subtitle' => $s->subtitle,
                    'clientLabel' => $s->client_label,
                    'role' => $s->role,
                    'years' => $s->yearsLabel(),
                    'summary' => $s->summary,
                    'tech' => $s->tech ?? [],
                    'outcomes' => array_slice($s->outcomes ?? [], 0, 3),
                    'repoUrl' => $s->repo_url,
                    'liveUrl' => $s->live_url,
                    'stage' => $s->stage ? ['value' => $s->stage->value, 'label' => $s->stage->getLabel()] : null,
                ]),

            'skillGroups' => $skillGroups
                ->map(fn (SkillGroup $g) => [
                    'name' => $g->name,
                    'description' => $g->description,
                    'skills' => $g->skills->map(fn (Skill $s) => [
                        'name' => $s->name,
                        'isPrimary' => $s->is_primary,
                    ]),
                ]),

            'experiences' => $experiences
                ->map(fn (Experience $e) => [
                    'title' => $e->title,
                    'company' => $e->company,
                    'companyUrl' => $e->company_url,
                    'location' => $e->location,
                    'type' => $e->employment_type,
                    'period' => $e->period(),
                    'isCurrent' => $e->isCurrent(),
                    'summary' => $e->summary,
                    'highlights' => $e->highlights ?? [],
                    'tech' => $e->tech ?? [],
                ]),

            'certifications' => Certification::orderBy('sort_order')->get()
                ->map(fn (Certification $c) => ['name' => $c->name, 'issuer' => $c->issuer, 'url' => $c->credential_url]),

            'education' => $education
                ->map(fn (EducationEntry $e) => [
                    'degree' => $e->degree,
                    'institution' => $e->institution,
                    'location' => $e->location,
                    'period' => $e->started_on?->format('Y').' – '.$e->ended_on?->format('Y'),
                ]),
        ]);
    }

    /**
     * Link-preview card plus a schema.org Person, which is what search engines use to
     * connect this site with the GitHub and LinkedIn profiles of the same person.
     *
     * @param  Collection<int, SkillGroup>  $skillGroups
     * @param  Collection<int, Experience>  $experiences
     * @param  Collection<int, EducationEntry>  $education
     * @return array<string, mixed>
     */
    private function meta(Profile $profile, Collection $skillGroups, Collection $experiences, Collection $education): array
    {
        $seo = $profile->seo ?? [];
        $description = $seo['description'] ?? Str::limit($profile->summary, 160);
        $image = PageMeta::image($seo['og_image'] ?? null);
        $currentRole = $experiences->first(fn (Experience $e) => $e->isCurrent());
        $school = $education->first();

        return PageMeta::make(
            title: $seo['title'] ?? "{$profile->name} — {$profile->headline}",
            description: $description,
            siteName: $profile->name,
            image: $image,
            imageAlt: "{$profile->name} — {$profile->headline}",
            jsonLd: array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $profile->name,
                'jobTitle' => $profile->headline,
                'description' => $description,
                'url' => url('/'),
                // A person's image is their photo; the link-preview card is only the fallback.
                'image' => $profile->avatarUrl() ?? $image,
                'email' => "mailto:{$profile->email}",
                'homeLocation' => $profile->location ? ['@type' => 'Place', 'name' => $profile->location] : null,
                'sameAs' => array_values(array_filter([$profile->github_url, $profile->linkedin_url])),
                'worksFor' => $currentRole ? array_filter([
                    '@type' => 'Organization',
                    'name' => $currentRole->company,
                    'url' => $currentRole->company_url,
                ]) : null,
                'alumniOf' => $school ? ['@type' => 'CollegeOrUniversity', 'name' => $school->institution] : null,
                'knowsAbout' => $skillGroups->flatMap->skills->where('is_primary', true)->pluck('name')->values()->all(),
            ]),
        );
    }
}
