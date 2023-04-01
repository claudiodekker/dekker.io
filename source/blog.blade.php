---
pagination:
 collection: blog
 perPage: 10
title: Blog
---
@extends('_layouts.master')

@section('body')
    <div>
        <h1 class="text-2xl font-bold text-slate-700 mb-6">Blog</h1>

        @foreach ($pagination->items as $post)
            <a href="{{ $post->getUrl() }}" class="block py-5 rounded text-blue-500 font-medium focus:shadow-lg focus:outline-none focus:ring ring-offset-2 ring-blue-300 mb-8 border-b-2 border-slate-200 last:border-0">
                <span class="text-sm text-slate-500 font-semibold">{{ $post->getDate()->format('F j, Y') }}</span>
                <h2 class="text-2xl text-gray-900 font-extrabold mb-3">{{ $post->title }}</h2>
                <p class="text-slate-600 mb-6">{!! $post->getExcerpt(280) !!}</p>
                <span>Read more</span>
            </a>
        @endforeach

        @include('_components.paginator')
    </div>
@endsection
