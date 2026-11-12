@extends('_layouts.master')

@section('styles')
    @include('_components.prose')
@endsection

@section('body')
    <article class="mx-auto max-w-[712px] animate-rise px-6 pt-12 text-base leading-[1.7] md:pt-24 md:text-[17px]">
        <header>
            <time class="block font-mono text-[13px] text-dim" datetime="{{ $page->getDate()->format('Y-m-d') }}">{{ $page->getDate()->format('F j, Y') }}</time>
            <h1 class="mt-2.5 text-[28px] leading-[1.2] font-semibold tracking-[-.025em] text-ink md:text-[34px]">{{ $page->title }}</h1>
        </header>

        <div class="prose mt-11">
            @yield('content')
        </div>

        @php
            $older = $page->getNext();
            $newer = $page->getPrevious();
        @endphp
        @if ($older || $newer)
            <nav class="mt-16 flex justify-between gap-6 border-t border-line pt-6 font-mono text-[13px] text-dim" aria-label="Posts">
                <span>
                    @if ($older)
                        <a class="hover:text-ink" href="{{ $older->getUrl() }}" title="Older post: {{ $older->title }}">&larr; {{ $older->title }}</a>
                    @endif
                </span>
                <span class="text-right">
                    @if ($newer)
                        <a class="hover:text-ink" href="{{ $newer->getUrl() }}" title="Newer post: {{ $newer->title }}">{{ $newer->title }} &rarr;</a>
                    @endif
                </span>
            </nav>
        @endif
    </article>
@endsection
