@extends('_layouts.master')

@section('body')
    <div>
        <span class="text-xl text-blue-gray-500 font-semibold">{{ $page->getDate()->format('F j, Y') }}</span>
        <h1 class="block text-5xl font-extrabold text-gray-900 tracking-tight mb-4">{{ $page->title }}</h1>

        <div class="mt-6 mb-16 post">
            @yield('content')
        </div>

        @php
            $next = $page->getNext();
            $previous = $page->getPrevious();
        @endphp
        @if ($next || $previous)
            <nav class="flex justify-between text-sm md:text-base border-t border-blue-gray-200 pt-3">
                <div>
                    @if ($next)
                        <a href="{{ $next->getUrl() }}" title="Older Post: {{ $next->title }}" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">
                            &LeftArrow; {{ $next->title }}
                        </a>
                    @endif
                </div>

                <div>
                    @if ($previous)
                        <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}" class="text-blue-500 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300">
                            {{ $previous->title }} &RightArrow;
                        </a>
                    @endif
                </div>
            </nav>
        @endif
    </div>
@endsection
