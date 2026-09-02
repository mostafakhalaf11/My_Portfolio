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
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $profile = Profile::current();

        return Inertia::render('Home', [
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
                    'years' => trim(($s->year_from ?? '').($s->year_to ? '–'.$s->year_to : ($s->year_from ? '–present' : ''))),
                    'summary' => $s->summary,
                    'tech' => $s->tech ?? [],
                    'outcomes' => array_slice($s->outcomes ?? [], 0, 3),
                    'diagram' => $s->architecture_diagram,
                    'repoUrl' => $s->repo_url,
                    'liveUrl' => $s->live_url,
                ]),

            'skillGroups' => SkillGroup::with('skills')->orderBy('sort_order')->get()
                ->map(fn (SkillGroup $g) => [
                    'name' => $g->name,
                    'description' => $g->description,
                    'skills' => $g->skills->map(fn (Skill $s) => [
                        'name' => $s->name,
                        'isPrimary' => $s->is_primary,
                    ]),
                ]),

            'experiences' => Experience::published()->ordered()->get()
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

            'education' => EducationEntry::orderBy('sort_order')->get()
                ->map(fn (EducationEntry $e) => [
                    'degree' => $e->degree,
                    'institution' => $e->institution,
                    'location' => $e->location,
                    'period' => $e->started_on?->format('Y').' – '.$e->ended_on?->format('Y'),
                ]),
        ]);
    }
}
