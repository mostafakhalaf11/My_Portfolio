<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">

    {{--
        SEO and link previews. Printed by the server because crawlers and preview bots
        (LinkedIn, WhatsApp, X, Slack) never run JavaScript. Data: App\Support\PageMeta.
        Matching `inertia` keys let Vue's <Head> replace page-specific tags during
        client-side navigation without duplicating them.
    --}}
    @php($meta = $page['props']['meta'] ?? null)
    @if ($meta)
        <title inertia>{{ $meta['title'] }}</title>
        <meta name="description" content="{{ $meta['description'] }}" inertia="description">
        <link rel="canonical" href="{{ $meta['url'] }}" inertia="canonical">

        <meta property="og:type" content="{{ $meta['type'] }}" inertia="og:type">
        <meta property="og:site_name" content="{{ $meta['siteName'] }}">
        <meta property="og:locale" content="en_US">
        <meta property="og:url" content="{{ $meta['url'] }}" inertia="og:url">
        <meta property="og:title" content="{{ $meta['title'] }}" inertia="og:title">
        <meta property="og:description" content="{{ $meta['description'] }}" inertia="og:description">
        <meta property="og:image" content="{{ $meta['image'] }}" inertia="og:image">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="{{ $meta['imageWidth'] }}">
        <meta property="og:image:height" content="{{ $meta['imageHeight'] }}">
        <meta property="og:image:alt" content="{{ $meta['imageAlt'] }}" inertia="og:image:alt">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $meta['title'] }}" inertia="twitter:title">
        <meta name="twitter:description" content="{{ $meta['description'] }}" inertia="twitter:description">
        <meta name="twitter:image" content="{{ $meta['image'] }}" inertia="twitter:image">
        <meta name="twitter:image:alt" content="{{ $meta['imageAlt'] }}" inertia="twitter:image:alt">

        @if ($meta['jsonLd'])
            {{-- JSON_HEX_TAG encodes < and >, so no content can close this script tag early. --}}
            <script type="application/ld+json">{!! json_encode($meta['jsonLd'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
        @endif
    @endif

    {{-- SVG for every current browser; the .ico (sizes hint keeps Chrome on the SVG) and PNG are fallbacks. --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Theme is resolved before paint so a dark visitor never sees a light flash. --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="min-h-dvh bg-surface font-sans text-ink antialiased">
    @inertia
</body>
</html>
