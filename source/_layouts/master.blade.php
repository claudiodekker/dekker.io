<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}" class="bg-blue-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="canonical" href="{{ $page->getUrl() }}">
        <link rel="stylesheet" href="{{ mix('css/app.css', 'assets/build') }}">
        <title>{{ $page->basename . ' - ' . $page->title }}</title>
        @if($page->description)<meta name="description" content="{{ $page->description }}">@endif
        <meta name="author" content="{{ $page->author }}" />
        {{-- All Common --}}
        <meta property="og:type" content="{{ $page->type }}" />
        @if ($page->image)<meta property="og:image" content="{{ $page->domain . $page->image }}" />@endif
        {{-- Facebook --}}
        <meta property="og:url" content="{{ $page->getUrl() }}" />
        <meta property="og:title" content="{{ $page->basename . ' - ' . $page->title }}" />
        @if($page->description)<meta property="og:description" content="{{ $page->description }}" />@endif
        {{--    <meta name="fb:app_id" content="{{  config('social.fb_app_id') }}">--}}
        {{-- Twitter Summary Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@claudiodekker">
        <meta name="twitter:creator" content="{{ $page->author }}">
        <meta name="twitter:title" content="{{ $page->basename . ' - ' . $page->title }}">
        @if($page->description)<meta name="twitter:description" content="{{ $page->description }}">@endif
        @if ($page->image)<meta name="twitter:image" content="{{ $page->domain . $page->image }}" />@endif
        <!-- Fathom - beautiful, simple website analytics -->
        <script src="https://cdn.usefathom.com/script.js" data-site="CULPJASS" defer></script>
        <!-- / Fathom -->
    </head>
    <body class="text-blue-gray-600 font-sans antialiased">
        <div class="flex flex-col max-w-4xl mx-auto px-6 py-10 min-h-screen">
            <header class="mb-12 flex flex-col sm:flex-row items-center justify-between w-full border-b border-blue-gray-200 pb-3">
                <a href="/" class="block text-blue-gray-600 text-lg text-2xl md:text-3xl font-extrabold leading-none lg:leading-tight focus:outline-none focus:ring ring-blue-300 ring-offset-2 rounded">Claudio Dekker</a>
                <div class="flex uppercase tracking-wide text-xs sm:space-x-3 mt-1 sm:mt-0">
                    <a href="/blog" class="text-grey-dark font-semibold hover:text-black focus:text-black py-1 px-3 rounded focus:outline-none focus:ring ring-blue-300">Blog</a>
                </div>
            </header>
            @yield('body')
            <footer class="text-center text-xs opacity-50 mt-12">
                This site was built using <a href="http://jigsaw.tighten.co" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Jigsaw</a>
                <a href="https://tailwindcss.com" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">TailwindCSS</a>,
                <a href="https://prismjs.com" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Prism</a>,
                and is hosted on <a href="https://netlify.com" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">Netlify</a>.
                Source code available on <a href="https://github.com/claudiodekker/dekker.io" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">GitHub</a>.
            </footer>
        </div>
    </body>
</html>
