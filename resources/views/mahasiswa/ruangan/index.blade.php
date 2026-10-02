<x-layouts.app title="Lihat Ruangan">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-slate-900">Lihat Ruangan</h1>
        <div class="flex gap-3">
            <div class="card flex items-center gap-3 px-4 py-2.5"><x-icon name="building" class="text-brand-700" /><div class="leading-tight"><p class="text-[10px] text-slate-500">Total Ruangan</p><p class="text-sm font-bold">{{ $total }} Unit</p></div></div>
            <div class="card flex items-center gap-3 px-4 py-2.5"><x-icon name="circle-check" class="text-brand-700" /><div class="leading-tight"><p class="text-[10px] text-slate-500">Siap Dipakai</p><p class="text-sm font-bold text-brand-700">{{ $siap }} Unit</p></div></div>
        </div>
    </div>

    <form method="GET" class="card mt-6 p-3" role="search">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input name="q" value="{{ request('q') }}" type="search" placeholder="Cari nama ruangan atau fasilitas..." aria-label="Cari ruangan" class="field border-0 bg-slate-50 pl-9">
        </div>
    </form>

    @if($ruangan->isEmpty())
        <div class="card mt-6"><x-empty-state icon="building" :title="request('q') ? 'Ruangan tidak ditemukan.' : 'Belum ada ruangan.'" :text="request('q') ? 'Coba kata kunci lain.' : null" /></div>
    @else
        <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($ruangan as $r)<x-room-card :r="$r" :dipakai="$dipakai->get($r->id_ruangan)" />@endforeach
        </div>
        <div class="mt-8">{{ $ruangan->links() }}</div>
    @endif
</x-layouts.app>
