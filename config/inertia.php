<?php

// Merged over the package's config; only the overrides live here.
return [

    'testing' => [

        'ensure_pages_exist' => true,

        /*
         * The pages directory is lowercase (resources/js/app.js globs ./pages/**). The package
         * default `js/Pages` only matched on Windows' case-insensitive filesystem, so every
         * assertInertia() component check failed on Linux CI.
         */
        'page_paths' => [
            resource_path('js/pages'),
        ],

        'page_extensions' => ['vue'],

    ],

];
