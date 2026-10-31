<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Profile;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudyController extends Controller
{
    public function show(CaseStudy $caseStudy): Response
    {
        abort_unless($caseStudy->isPublished(), 404);

        return Inertia::render('CaseStudies/Show', [
            'profile' => ['name' => Profile::current()->name],
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
