@if ($pagination->pages->count() > 1)
    <nav class="mt-16 flex justify-between gap-6 border-t border-line pt-6 font-mono text-[13px] text-dim" aria-label="Pages">
        <span>
            @if ($pagination->previous)
                <a class="hover:text-ink" href="{{ $pagination->previous }}" title="Previous page">&larr; Newer</a>
            @endif
        </span>
        <span>{{ $pagination->currentPage }} / {{ $pagination->pages->count() }}</span>
        <span class="text-right">
            @if ($pagination->next)
                <a class="hover:text-ink" href="{{ $pagination->next }}" title="Next page">Older &rarr;</a>
            @endif
        </span>
    </nav>
@endif
