<x-layouts.app title="Manajemen Ruangan">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-extrabold text-slate-900">Daftar Ruangan</h1>
        <a href="{{ route('admin.ruangan.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Tambah Ruangan</a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Inventaris" :value="$total" sub="Unit ruang kerja & acara" icon="building" />
        <x-stat-card label="Siap Digunakan" :value="$tersedia" sub="Ruang tersedia ({{ $total ? round($tersedia / $total * 100) : 0 }}%)" icon="circle-check" tone="green" />
        <x-stat-card label="Tidak Tersedia" :value="$tidakTersedia" sub="Ruang tidak tersedia" icon="calendar" tone="amber" />
    </div>

    <div class="card mt-6 flex flex-wrap items-center justify-between gap-3 p-3">
        <nav class="flex flex-wrap gap-2" aria-label="Filter status ruangan">
            @foreach([''=>['Semua Ruangan', $total], 'tersedia'=>['Tersedia', $tersedia], 'tidak_tersedia'=>['Tidak Tersedia', $tidakTersedia]] as $k => [$l, $n])
                <a href="{{ route('admin.ruangan.index', array_filter(['status' => $k, 'q' => request('q')])) }}" class="rounded-lg px-3.5 py-2 text-sm font-semibold {{ (string) request('status') === (string) $k ? 'bg-brand-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $l }} <span class="text-xs opacity-80">{{ $n }}</span></a>
            @endforeach
        </nav>
        <form method="GET" role="search" class="relative w-full sm:w-72">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau fasilitas..." aria-label="Cari ruangan" class="field pl-9">
        </form>
    </div>

    @if($ruangan->isEmpty())
        <div class="card mt-6"><x-empty-state icon="building" title="Belum ada ruangan." text="Tambahkan ruangan pertama atau ubah filter pencarian."><a href="{{ route('admin.ruangan.create') }}" class="btn-primary">Tambah Ruangan</a></x-empty-state></div>
    @else
        <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">@foreach($ruangan as $r)<x-room-card :r="$r" :dipakai="$dipakai->get($r->id_ruangan)" admin />@endforeach</div>
        <div class="mt-8">{{ $ruangan->links() }}</div>
    @endif
</x-layouts.app>
