---
permalink: 404.html
title: Not found
noindex: true
---

@extends('_layouts.master')

@section('body')
    <section class="mind-trick mx-auto flex min-h-[calc(100dvh-172px)] max-w-[712px] animate-rise flex-col justify-center px-6 pb-16">
        <h1 class="text-[30px] leading-[1.15] font-semibold tracking-[-.03em] text-ink md:text-[38px]">Not the droids you're looking for..</h1>
        <p class="mt-4 flex items-center gap-3 text-[17px] md:text-[18px]">
            <span class="rounded-md bg-rose-600 px-2 py-0.5 font-mono text-[15px] text-white">404</span>
            <span data-missing>The page you tried to reach doesn't exist.</span>
        </p>
        <hr class="mt-11 w-32 border-line">
        <p class="mt-9 font-mono text-[13px] text-dim">You can go about your business.</p>
        <p class="mt-2 text-[17px] md:text-[18px]"><a class="text-accent hover:text-ink" href="/">Move along..</a></p>
    </section>

    <script>
        const path = decodeURIComponent(location.pathname)

        if (path !== '/404.html' && path !== '/404') {
            const requested = document.createElement('code')
            requested.className = 'font-mono text-[.9em] text-ink [overflow-wrap:anywhere]'
            requested.textContent = path.length > 48 ? `${path.slice(0, 48)}…` : path

            document.querySelector('[data-missing]').replaceChildren(requested, " isn't a page here.")
        }
    </script>
@endsection
