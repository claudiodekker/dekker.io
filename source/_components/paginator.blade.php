@if ($pagination->pages->count() > 1)
    <nav class="flex text-base my-8">
        @if ($previous = $pagination->previous)
            <a href="{{ $previous }}" title="Previous Page" class="bg-slate-200 hover:bg-slate-300 rounded mr-3 px-5 py-3 focus:outline-none focus:ring ring-offset-1 ring-blue-300">&LeftArrow;</a>
        @else
            <span class="bg-slate-200 rounded mr-3 px-5 py-3 opacity-50 cursor-not-allowed">&LeftArrow;</span>
        @endif

        @foreach ($pagination->pages as $pageNumber => $path)
            <a href="{{ $path }}" title="Go to Page {{ $pageNumber }}" class="hover:bg-slate-300 rounded mr-3 px-5 py-3 font-medium rounded focus:outline-none focus:ring ring-offset-1 ring-blue-300 {{ $pagination->currentPage === $pageNumber ? 'bg-slate-300 text-slate-600' : 'bg-slate-200 text-blue-500' }}">{{ $pageNumber }}</a>
        @endforeach

        @if ($next = $pagination->next)
            <a href="{{ $next }}" title="Next Page" class="bg-slate-200 hover:bg-slate-300 rounded mr-3 px-5 py-3 focus:outline-none focus:ring ring-offset-1 ring-blue-300">&RightArrow;</a>
        @else
            <span class="bg-slate-200 rounded mr-3 px-5 py-3 opacity-50 cursor-not-allowed">&RightArrow;</span>
        @endif
    </nav>
@endif
