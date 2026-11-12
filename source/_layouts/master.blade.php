<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}</title>
        <meta name="description" content="{{ $page->getDescription() }}">
        <meta name="author" content="{{ $page->author ?? $page->siteAuthor }}">
        <meta name="theme-color" content="#070c1a" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#f5f7fc" media="(prefers-color-scheme: light)">
        @if ($page->noindex)
            <meta name="robots" content="noindex">
        @else
            <link rel="canonical" href="{{ $page->domain . $page->getUrl() }}">
        @endif
        <link rel="home" href="{{ $page->domain . $page->baseUrl }}">
        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="preload" href="/assets/fonts/geist-400.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/assets/fonts/geist-mono-400.woff2" as="font" type="font/woff2" crossorigin>
        <meta property="og:type" content="{{ $page->type ?? 'website' }}">
        <meta property="og:url" content="{{ $page->domain . $page->getUrl() }}">
        <meta property="og:title" content="{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}">
        <meta property="og:description" content="{{ $page->getDescription() }}">
        @if ($page->image)<meta property="og:image" content="{{ $page->domain . $page->image }}">@endif
        <meta name="twitter:card" content="{{ $page->image ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:site" content="@claudiodekker">
        <meta name="twitter:title" content="{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}">
        <meta name="twitter:creator" content="{{ $page->author ?? $page->siteAuthor }}">
        <meta name="twitter:description" content="{{ $page->getDescription() }}">
        @if ($page->image)<meta name="twitter:image" content="{{ $page->domain . $page->image }}">@endif
        <style>{!! $page->styles() !!}</style>
        @yield('styles')
        @if ($page->production)
            <script src="https://cdn.usefathom.com/script.js" data-site="CULPJASS" defer></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="backdrop relative min-h-dvh overflow-hidden pb-24 text-soft">
            <nav class="mx-auto flex max-w-[1080px] items-center justify-between px-6 py-7 font-mono text-[13px] tracking-[.02em] text-dim" aria-label="Main">
                <a class="text-ink" href="/">Claudio Dekker</a>
                <ul class="flex gap-4 md:gap-7">
                    <li><a class="hover:text-ink" href="https://github.com/claudiodekker">GitHub</a></li>
                    <li><a class="hover:text-ink" href="https://x.com/claudiodekker">X</a></li>
                </ul>
            </nav>
            <main>
                @yield('body')
            </main>
        </div>
    </body>
</html>
