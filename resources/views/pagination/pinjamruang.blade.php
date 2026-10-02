@if ($paginator->hasPages())
<nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">Menampilkan <strong>{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data</p>
    <div class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-white text-slate-300"><x-icon name="chevron-left" class="h-4 w-4" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="grid h-9 w-9 place-items-center rounded-lg bg-white text-slate-600 hover:bg-brand-50"><x-icon name="chevron-left" class="h-4 w-4" /></a>
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))<span class="px-1 text-slate-400">{{ $element }}</span>@endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid h-9 w-9 place-items-center rounded-lg bg-brand-700 text-sm font-semibold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="grid h-9 w-9 place-items-center rounded-lg bg-white text-sm text-slate-600 hover:bg-brand-50" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="grid h-9 w-9 place-items-center rounded-lg bg-white text-slate-600 hover:bg-brand-50"><x-icon name="chevron-right" class="h-4 w-4" /></a>
        @else
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-white text-slate-300"><x-icon name="chevron-right" class="h-4 w-4" /></span>
        @endif
    </div>
</nav>
@endif
