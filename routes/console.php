<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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
