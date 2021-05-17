@extends('_layouts.master')

@section('body')
    <div class="flex flex-grow items-center justify-center">
        <div class="flex flex-col md:flex-row items-center md:items-start">
            <div class="flex-none">
                <img src="/assets/images/claudio-dekker.jpg" class="h-32 rounded-full shadow-md" alt="Claudio Dekker" />
            </div>
            <div class="mt-3 md:ml-6 md:mt-0">
                <h1 class="text-5xl font-bold text-blue-gray-600 text-center md:text-left">Claudio Dekker</h1>
                <div class="mt-8 md:mt-5 max-w-2xl post">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
