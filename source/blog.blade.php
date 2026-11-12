---
pagination:
 collection: blog
 perPage: 10
title: Blog
---
@extends('_layouts.master')

@section('body')
    <section class="mx-auto max-w-[712px] animate-rise px-6 pt-12 text-base leading-[1.7] md:pt-24 md:text-[17px]">
        <h1 class="mb-7 text-[28px] leading-[1.2] font-semibold tracking-[-.025em] text-ink md:text-[34px]">Blog</h1>

        @foreach ($pagination->items as $post)
            <a class="group block border-t border-line py-7" href="{{ $post->getUrl() }}">
                <time class="block font-mono text-[13px] text-dim" datetime="{{ $post->getDate()->format('Y-m-d') }}">{{ $post->getDate()->format('F j, Y') }}</time>
                <h2 class="mt-1.5 mb-2 text-[22px] leading-[1.3] font-semibold tracking-[-.015em] text-ink transition-colors group-hover:text-accent">{{ $post->title }}</h2>
                <p>{!! $post->getExcerpt(280) !!}</p>
            </a>
        @endforeach

        @include('_components.paginator')
    </section>
@endsection
