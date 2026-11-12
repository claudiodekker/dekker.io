@extends('_layouts.master')

@section('body')
    <article class="letter mx-auto max-w-[614px] px-6 pt-12 text-[17px] leading-[1.6] tracking-[-.005em] md:pt-24 md:text-[18px]">
        @yield('content')
        @include('_components.signature')
        <div class="mt-11 animate-rise font-mono text-[13px] leading-[1.7] text-dim [animation-delay:1.69s]">{{ $page->location }}<br>{{ $page->signed }}</div>
        <div class="mt-18 flex animate-rise justify-between gap-x-4 border-t border-line pt-6 font-mono text-[min(12px,calc((100vw-64px)/31.5))] leading-[1.7] whitespace-nowrap text-dim [animation-delay:1.69s]">
            <span>PGP</span>
            <a class="hover:text-soft" href="https://keybase.io/claudiodekker/pgp_keys.asc?fingerprint=08fdb44b6d6320215d1ee8c13025ffe233ccc24b">08FD B44B 6D63 2021 5D1E E8C1 3025 FFE2 33CC C24B</a>
        </div>
    </article>
@endsection
