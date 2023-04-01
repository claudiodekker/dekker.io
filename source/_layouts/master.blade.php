<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}" class="bg-slate-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <link rel="canonical" href="{{ $page->getUrl() }}">
        <title>{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}</title>
        <link rel="home" href="{{ $page->domain . $page->baseUrl }}">
        <meta name="author" content="{{ $page->author ?? $page->siteAuthor }}" />
        <meta name="description" content="{{ $page->description ?? $page->siteDescription }}">
        {{-- ########## START SOCIAL TAGS ########## --}}
        <meta property="og:type" content="{{ $page->type ?? 'website' }}" />
        @if ($page->image)<meta property="og:image" content="{{ $page->domain . $page->image }}" />@endif
        {{-- Facebook --}}
        <meta property="og:url" content="{{ $page->domain . $page->getUrl() }}" />
        <meta property="og:title" content="{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}" />
        <meta property="og:description" content="{{ $page->getDescription(150) }}" />
        {{--    <meta name="fb:app_id" content="{{  config('social.fb_app_id') }}">--}}
        {{-- Twitter Summary Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@claudiodekker">
        <meta name="twitter:title" content="{{ $page->title ? ($page->siteName . ' - ' . $page->title) : $page->siteName }}">
        <meta name="twitter:creator" content="{{ $page->author ?? $page->siteAuthor }}">
        <meta name="twitter:description" content="{{ $page->getDescription(150) }}" />
        @if ($page->image)<meta name="twitter:image" content="{{ $page->domain . $page->image }}" />@endif
        {{-- ########## END SOCIAL TAGS ########## --}}
        <link rel="stylesheet" href="{{ mix('css/app.css', 'assets/build') }}">
        @if ($page->production)
            {{-- Fathom - beautiful, simple website analytics --}}
            <script src="https://cdn.usefathom.com/script.js" data-site="CULPJASS" defer></script>
        @endif
    </head>
    <body class="text-slate-600 font-sans antialiased">
        <div class="flex flex-col max-w-4xl mx-auto px-6 py-10 min-h-screen">
            <header class="mb-12 flex flex-col sm:flex-row items-center justify-between w-full border-b border-slate-200 pb-3">
                <a href="/" class="block text-slate-600 text-lg text-2xl md:text-3xl font-extrabold leading-none lg:leading-tight focus:outline-none focus:ring ring-blue-300 ring-offset-2 rounded">Claudio Dekker</a>
                <div class="flex uppercase tracking-wide text-xs sm:space-x-3 mt-1 sm:mt-0">
                    <a href="/blog" class="text-grey-dark font-semibold hover:text-black focus:text-black py-1 px-3 rounded focus:outline-none focus:ring ring-blue-300">Blog</a>
                </div>
            </header>
            @yield('body')
            <footer class="text-center text-xs opacity-50 mt-12">
                This site was built using <a href="http://jigsaw.tighten.co" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Jigsaw</a>,
                <a href="https://tailwindcss.com" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">TailwindCSS</a>,
                <a href="https://torchlight.dev/" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Torchlight</a>,
                and is hosted on <a href="https://netlify.com" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Netlify</a>.
                Source code available on <a href="https://github.com/claudiodekker/dekker.io" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">GitHub</a>.
            </footer>
        </div>
    </body>
</html>
