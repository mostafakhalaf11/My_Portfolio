<?php

use App\Models\CaseStudy;
use App\Models\PageSection;
use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Finder\Finder;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * `view:cache` compiles realpath()s, but the runtime finder resolves "<hint>/<name>.blade.php"
 * with a forward slash. On Windows the two strings differ, so every precompiled Filament view
 * is a miss and the first admin request compiles ~200 views itself — long enough to hit
 * max_execution_time. This compiles each view under the exact path the runtime will ask for.
 */
Artisan::command('views:warm', function () {
    $finder = $this->laravel['view']->getFinder();
    $compiler = $this->laravel['blade.compiler'];

    $roots = collect($finder->getPaths())
        ->merge(collect($finder->getHints())->flatten())
        ->unique()
        ->filter(fn (string $root) => is_dir($root));

    $compiled = $failed = 0;

    foreach ($roots as $root) {
        foreach (Finder::create()->in($root)->files()->name('*.blade.php') as $file) {
            $path = $root.'/'.str_replace('\\', '/', $file->getRelativePathname());

            if (! $compiler->isExpired($path)) {
                continue;
            }

            try {
                $compiler->compile($path);
                $compiled++;
            } catch (Throwable $e) {
                // A broken vendor stub must not fail `composer install`; the runtime reports it if used.
                $failed++;
            }
        }
    }

    $this->components->info("Compiled {$compiled} views".($failed ? ", skipped {$failed} that failed to compile" : '').'.');
})->purpose('Precompile Blade views at the paths the web runtime resolves');

/*
 * Link-preview cards (public/og/*.png), drawn from resources/og/card.html with the live CMS
 * content: one for the home page, one per published project. Run after changing a project's
 * title, subtitle, stage or tech, then commit the images. Needs a local Chrome or Edge.
 */
Artisan::command('og:render
    {--browser= : Path to a Chrome or Edge binary (auto-detected when omitted)}
    {--home-tags=Laravel,PHP 8.4,Vue 3,Nuxt 4,Filament,MySQL,Redis,Docker : Chips on the home card}', function () {
    $browser = $this->option('browser') ?: collect([
        'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
        'C:\Program Files\Google\Chrome\Application\chrome.exe',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/usr/bin/google-chrome',
        '/usr/bin/chromium',
    ])->first(fn (string $path) => is_file($path));

    if (! $browser) {
        $this->components->error('No Chrome or Edge found. Pass --browser=<path>.');

        return Command::FAILURE;
    }

    $template = 'file:///'.ltrim(str_replace('\\', '/', resource_path('og/card.html')), '/');

    $render = function (array $params, string $relativePath) use ($browser, $template) {
        $target = public_path($relativePath);
        File::ensureDirectoryExists(dirname($target));

        Process::timeout(60)->run([
            $browser, '--headless=new', '--disable-gpu', '--hide-scrollbars',
            '--virtual-time-budget=5000', '--window-size=1200,630',
            '--screenshot='.$target,
            $template.'?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986),
        ])->throw();

        // WhatsApp silently drops previews whose image is much over ~300 KB.
        $kb = (int) round(filesize($target) / 1024);
        $kb > 300
            ? $this->components->warn("{$relativePath}: {$kb} KB, too heavy for WhatsApp previews")
            : $this->components->info("{$relativePath}: {$kb} KB");
    };

    $profile = Profile::current();

    $render([
        'title' => PageSection::where('page', 'home')->where('key', 'hero')->value('heading') ?? $profile->headline,
        'sub' => $profile->headline.' · '.str_replace(' | ', ' · ', $profile->title),
        'badge' => $profile->is_available ? 'Open to full-stack and backend roles' : '',
        'tone' => 'available',
        'tags' => $this->option('home-tags'),
    ], 'og/portfolio.png');

    foreach (CaseStudy::published()->ordered()->get() as $study) {
        $render([
            'kicker' => 'Featured project',
            'title' => $study->title,
            'sub' => $study->subtitle ?? '',
            'badge' => $study->stage?->getLabel() ?? '',
            'tone' => $study->stage?->value ?? 'available',
            'tags' => implode(',', array_slice($study->tech ?? [], 0, 6)),
        ], "og/projects/{$study->slug}.png");
    }

    return Command::SUCCESS;
})->purpose('Render the link-preview images in public/og from the current content');
