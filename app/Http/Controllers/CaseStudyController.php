<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Profile;
use App\Support\PageMeta;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudyController extends Controller
{
    public function show(CaseStudy $caseStudy): Response
    {
        abort_unless($caseStudy->isPublished(), 404);

        $profile = Profile::current();

        return Inertia::render('CaseStudies/Show', [
            'meta' => PageMeta::make(
                title: "{$caseStudy->title} | {$profile->name}",
                description: $caseStudy->summary,
                siteName: $profile->name,
                // A rendered card for this project if one exists, otherwise the site card.
                image: PageMeta::image("og/projects/{$caseStudy->slug}.png"),
                imageAlt: $caseStudy->title,
                type: 'article',
                jsonLd: array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => $caseStudy->title,
                    'description' => $caseStudy->summary,
                    'url' => url()->current(),
                    'author' => ['@type' => 'Person', 'name' => $profile->name, 'url' => url('/')],
                    'dateCreated' => $caseStudy->year_from ? (string) $caseStudy->year_from : null,
                    'keywords' => implode(', ', $caseStudy->tech ?? []) ?: null,
                ]),
            ),
            'profile' => ['name' => $profile->name],
            'study' => [
                'slug' => $caseStudy->slug,
                'title' => $caseStudy->title,
                'subtitle' => $caseStudy->subtitle,
                'clientLabel' => $caseStudy->client_label,
                'role' => $caseStudy->role,
                'years' => $caseStudy->yearsLabel(),
                'summary' => $caseStudy->summary,
                'context' => $caseStudy->context,
                'constraints' => $caseStudy->constraints,
                'optionsRejected' => $caseStudy->options_rejected ?? [],
                'decision' => $caseStudy->decision,
                'implementation' => $caseStudy->implementation,
                'outcomes' => $caseStudy->outcomes ?? [],
                'diagram' => $caseStudy->architecture_diagram,
                'tech' => $caseStudy->tech ?? [],
                'repoUrl' => $caseStudy->repo_url,
                'liveUrl' => $caseStudy->live_url,
                'stage' => $caseStudy->stage ? ['value' => $caseStudy->stage->value, 'label' => $caseStudy->stage->getLabel()] : null,
                'roadmap' => $caseStudy->roadmap ?? [],
            ],
        ]);
    }
}
