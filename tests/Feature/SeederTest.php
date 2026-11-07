<?php

use App\Models\CaseStudy;
use App\Models\Certification;
use App\Models\EducationEntry;
use App\Models\Experience;
use App\Models\PageSection;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\SkillGroup;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('seeds the profile from the resume', function () {
    $profile = Profile::current();

    expect($profile->name)->toBe('Mostafa Khalaf Mohamed')
        ->and($profile->email)->toBe('mostafkhalaf11@gmail.com')
        ->and($profile->headline)->toBe('Full-Stack Software Engineer')
        ->and($profile->summary)->toContain('2+ years of experience')
        ->and($profile->languages)->toHaveCount(3)
        ->and($profile->languages[0])->toMatchArray(['name' => 'Arabic', 'level' => 'Native'])
        ->and($profile->strengths)->not->toBeEmpty()
        ->and($profile->seo['description'])->not->toBeEmpty();
});

it('seeds every skill group from the resume', function () {
    expect(SkillGroup::pluck('name')->all())->toBe([
        'Backend & Architecture',
        'Data & Infrastructure',
        'Testing & Quality',
        'Frontend',
        'AI-Assisted Engineering',
    ]);

    expect(Skill::count())->toBeGreaterThan(40)
        ->and(Skill::where('is_primary', true)->count())->toBeGreaterThan(0);
});

it('seeds experience in reverse chronological order with the current role open-ended', function () {
    $roles = Experience::published()->ordered()->get();

    expect($roles)->toHaveCount(4)
        ->and($roles->first()->company)->toBe('78s Works')
        ->and($roles->first()->isCurrent())->toBeTrue()
        ->and($roles->first()->period())->toBe('Jul 2025 - Present')
        ->and($roles->first()->highlights)->toHaveCount(4)
        ->and($roles->last()->company)->toBe('KB-Denim');
});

it('seeds case studies with the full structured narrative', function () {
    $studies = CaseStudy::published()->ordered()->get();

    expect($studies)->toHaveCount(2);

    foreach ($studies as $study) {
        expect($study->context)->not->toBeEmpty()
            ->and($study->constraints)->not->toBeEmpty()
            ->and($study->decision)->not->toBeEmpty()
            ->and($study->implementation)->not->toBeEmpty()
            ->and($study->options_rejected)->toHaveCount(3)
            ->and($study->outcomes)->not->toBeEmpty()
            ->and($study->architecture_diagram['nodes'])->not->toBeEmpty()
            ->and($study->architecture_diagram['edges'])->not->toBeEmpty()
            ->and($study->tech)->not->toBeEmpty();

        foreach ($study->options_rejected as $option) {
            expect($option)->toHaveKeys(['option', 'why_rejected']);
        }

        foreach ($study->outcomes as $outcome) {
            expect($outcome)->toHaveKeys(['metric', 'value']);
        }
    }
});

it('keeps client names out of case study records', function () {
    // The confidentiality decision is enforced by a test, not by memory.
    $forbidden = ['Destinia', 'destinia'];

    foreach (CaseStudy::all() as $study) {
        $haystack = json_encode($study->toArray());

        foreach ($forbidden as $needle) {
            expect($haystack)->not->toContain($needle);
        }

        expect($study->client_label)->not->toBeEmpty();
    }
});

it('every diagram edge points at a declared node', function () {
    foreach (CaseStudy::all() as $study) {
        $ids = collect($study->architecture_diagram['nodes'])->pluck('id')->all();

        foreach ($study->architecture_diagram['edges'] as $edge) {
            expect($ids)->toContain($edge['from'])
                ->and($ids)->toContain($edge['to']);
        }
    }
});

it('seeds credentials and homepage sections', function () {
    expect(Certification::count())->toBe(3)
        ->and(EducationEntry::first()->institution)->toBe('Assiut University')
        ->and(PageSection::visible()->pluck('key')->all())
        ->toBe(['hero', 'case_studies', 'stack', 'experience', 'contact']);
});

it('is idempotent — re-seeding updates instead of duplicating', function () {
    $before = [
        Profile::count(), SkillGroup::count(), Skill::count(),
        Experience::count(), CaseStudy::count(), Certification::count(),
        EducationEntry::count(), PageSection::count(),
    ];

    $this->seed(DatabaseSeeder::class);

    $after = [
        Profile::count(), SkillGroup::count(), Skill::count(),
        Experience::count(), CaseStudy::count(), Certification::count(),
        EducationEntry::count(), PageSection::count(),
    ];

    expect($after)->toBe($before);
});
