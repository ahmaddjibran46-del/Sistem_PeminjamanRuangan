<x-layouts.app :title="$ruangan->nama_ruangan">
    <a href="{{ route('ruangan.index') }}"
        class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline"><x-icon
            name="arrow-left" class="h-4 w-4" /> Kembali ke Lihat Ruangan</a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="aspect-[16/8] bg-brand-50">
                    @if($ruangan->foto)
                        <img src="{{ asset('storage/' . $ruangan->foto) }}" alt="Foto ruangan {{ $ruangan->nama_ruangan }}"
                            class="h-full w-full object-cover">
                    @else<div class="grid h-full place-items-center text-brand-600/40"><x-icon name="building"
                    class="h-20 w-20" /></div>@endif
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">{{ $ruangan->kode }}</p>
                            <h1 class="text-2xl font-extrabold text-slate-900">{{ $ruangan->nama_ruangan }}</h1>
                        </div>
                        @if(!$ruangan->is_tersedia)<x-status-badge status="tidak_tersedia" />
                        @elseif($dipakai)<span
                            class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Sedang
                            dipakai s/d {{ substr($dipakai, 0, 5) }}</span>
                        @else<x-status-badge status="tersedia" />@endif
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $ruangan->deskripsi }}</p>
                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs text-slate-500">Kapasitas</dt>
                            <dd class="mt-1 flex items-center gap-2 font-bold"><x-icon name="users"
                                    class="h-4 w-4 text-brand-700" /> {{ $ruangan->kapasitas }} orang</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs text-slate-500">Lokasi</dt>
                            <dd class="mt-1 flex items-center gap-2 font-bold"><x-icon name="pin"
                                    class="h-4 w-4 text-brand-700" /> {{ $ruangan->lokasi ?: '-' }}</dd>
                        </div>
                    </dl>
                    <h2 class="mt-6 text-sm font-bold">Fasilitas</h2>
                    <div class="mt-2 flex flex-wrap gap-2">@forelse($ruangan->daftar_fasilitas as $f)<span
                    class="chip text-xs">{{ $f }}</span>@empty<span class="text-sm text-slate-400">Belum ada
                            data fasilitas.</span>@endforelse</div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card p-5">
                @if($ruangan->is_tersedia)
                    <a href="{{ route('peminjaman.create', ['ruangan' => $ruangan->id_ruangan]) }}"
                        class="btn-primary w-full py-3">Ajukan Peminjaman</a>
                @else
                    <button disabled class="btn-primary w-full py-3">Ajukan Peminjaman</button>
                    <p class="mt-2 text-xs text-red-700">Ruangan sedang tidak tersedia untuk dipinjam.</p>
                @endif
            </div>
            <div class="card p-5">
                <h2 class="flex items-center gap-2 font-bold"><x-icon name="calendar" class="h-4 w-4 text-brand-700" />
                    Jadwal Penggunaan</h2>
                @forelse($jadwal as $j)
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <span class="font-semibold">{{ $j->tanggal->translatedFormat('D, d M Y') }}</span><span
                            class="text-slate-600">{{ $j->jam }}</span>
                    </div>
                @empty
                    <x-empty-state icon="calendar" title="Belum ada jadwal terpakai."
                        text="Ruangan ini belum dipesan pada waktu mendatang." class="!py-8" />
                @endforelse
            </div>
        </aside>
    </div>
</x-layouts.app>