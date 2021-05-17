---
permalink: 404.html
---

@extends('_layouts.master')

@section('body')
<div class="flex flex-grow items-center justify-center">
    <div>
        <h2 class="text-xl sm:text-4xl font-bold mb-2">Not the droids you're looking for..</h2>
        <p class="text-blue-gray-700 dark:text-gray-600 mb-8">
            <span class="rounded mr-1 px-2 py-1 bg-rose-600 text-white">404</span>
            The page you tried to reach doesn't exist.
        </p>
        <hr class="md:mt-12 mb-8 w-1/4">
        <a class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300" href="/">Move along..</a>
    </div>
</div>
@endsection
