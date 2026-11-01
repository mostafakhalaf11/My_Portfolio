<?php

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

/**
 * The schema.org block the server printed, decoded.
 *
 * @return array<string, mixed>
 */
function jsonLdFrom(string $html): array
{
    preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $html, $match);

    return json_decode($match[1] ?? 'null', true) ?? [];
}

// These assert on server HTML on purpose: link-preview bots never run JavaScript,
// so the tags only count if the server prints them.

it('prints link-preview tags into the server HTML of the home page', function () {
    $title = 'Mostafa Khalaf Mohamed — Full-Stack Software Engineer';

    expect($this->get('/')->assertOk()->getContent())
        ->toContain("<title inertia>{$title}</title>")
        ->toContain("<meta property=\"og:title\" content=\"{$title}\">")
        ->toContain('<meta property="og:image" content="'.asset('og/portfolio.png').'">')
        ->toContain('<meta property="og:image:width" content="1200">')
        ->toContain('<meta name="twitter:card" content="summary_large_image">')
        ->toContain('<link rel="canonical" href="'.url('/').'">')
        ->toContain('<meta name="description" content="Full-stack engineer');
});

it('hands Vue the same meta the server printed', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->where('meta.title', 'Mostafa Khalaf Mohamed — Full-Stack Software Engineer')
        ->where('meta.image', asset('og/portfolio.png'))
    );
});

it('describes the person with schema.org data tied to GitHub and LinkedIn', function () {
    $person = jsonLdFrom($this->get('/')->getContent());

    expect($person['@type'])->toBe('Person')
        ->and($person['name'])->toBe('Mostafa Khalaf Mohamed')
        ->and($person['jobTitle'])->toBe('Full-Stack Software Engineer')
        ->and($person['sameAs'])->toContain('https://github.com/mostafakhalaf11')
        ->and($person['worksFor']['name'])->toBe('78sWorks')
        ->and($person['alumniOf']['name'])->toBe('Assiut University');
});

it('gives each project its own title, description and card', function () {
    expect($this->get('/projects/stock-yard-commerce-rfq')->assertOk()->getContent())
        ->toContain('<meta property="og:type" content="article">')
        ->toContain('<title inertia>Stock Yard — Bilingual B2B Commerce and RFQ Platform | Mostafa Khalaf Mohamed</title>')
        ->toContain('<meta property="og:image" content="'.asset('og/projects/stock-yard-commerce-rfq.png').'">');
});

it('falls back to the site card for a project without its own image', function () {
    CaseStudy::create([
        'slug' => 'no-card-yet',
        'title' => 'New project',
        'summary' => 'Not rendered by og:render yet.',
        'status' => ContentStatus::Published,
    ]);

    expect($this->get('/projects/no-card-yet')->getContent())
        ->toContain('<meta property="og:image" content="'.asset('og/portfolio.png').'">');
});

it('cannot break out of the JSON-LD script with content from the CMS', function () {
    CaseStudy::create([
        'slug' => 'hostile',
        'title' => 'Title </script><script>alert(1)</script>',
        'summary' => 'Summary',
        'status' => ContentStatus::Published,
    ]);

    $html = $this->get('/projects/hostile')->assertOk()->getContent();

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->and(jsonLdFrom($html)['name'])->toBe('Title </script><script>alert(1)</script>');
});

it('lists the home page and every published project in the sitemap', function () {
    CaseStudy::create(['slug' => 'secret-draft', 'title' => 'Draft', 'summary' => 's', 'status' => ContentStatus::Draft]);

    $response = $this->get('/sitemap.xml')->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');

    $locations = [];
    foreach (simplexml_load_string($response->getContent())->url as $url) {
        $locations[] = (string) $url->loc;
    }

    expect($locations)->toBe([
        url('/'),
        route('projects.show', 'amg-erp-multi-tenant-hrm-payroll'),
        route('projects.show', 'stock-yard-commerce-rfq'),
    ]);
});

it('keeps the admin out of search results and points crawlers at the sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.url('/sitemap.xml'));
});

it('ships preview images light enough for WhatsApp', function (string $file) {
    // WhatsApp drops previews whose image is much over ~300 KB.
    expect(public_path($file))->toBeFile()
        ->and(filesize(public_path($file)))->toBeLessThan(300 * 1024);
})->with([
    'og/portfolio.png',
    'og/projects/amg-erp-multi-tenant-hrm-payroll.png',
    'og/projects/stock-yard-commerce-rfq.png',
]);
