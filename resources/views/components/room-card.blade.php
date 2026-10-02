@props(['r', 'dipakai' => null, 'admin' => false])
<article class="card flex flex-col overflow-hidden">
    <div class="relative aspect-[16/10] bg-brand-50">
        @if($r->foto_url)
            <img src="{{ $r->foto_url }}" alt="Foto ruangan {{ $r->nama_ruangan }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="grid h-full w-full place-items-center text-brand-600/40"><x-icon name="building" class="h-14 w-14" /></div>
        @endif
        <div class="absolute right-3 top-3">
            @if(! $r->is_tersedia)
                <x-status-badge status="tidak_tersedia" class="bg-white/95" />
            @elseif($dipakai)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Dipakai s/d {{ substr($dipakai, 0, 5) }}</span>
            @else
                <x-status-badge status="tersedia" class="bg-white/95" />
            @endif
        </div>
        <span class="absolute bottom-3 left-3 rounded bg-white/90 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">{{ $r->kode }}</span>
    </div>
    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-lg font-bold leading-snug text-slate-900">
            <a href="{{ $admin ? route('admin.ruangan.edit', $r) : route('ruangan.show', $r) }}" class="hover:text-brand-700">{{ $r->nama_ruangan }}</a>
        </h3>
        <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-600"><x-icon name="users" class="h-4 w-4 text-brand-700" /> Kapasitas: <strong>{{ $r->kapasitas }} orang</strong></p>
        <div class="mt-3 flex flex-wrap gap-1.5">
            @forelse(array_slice($r->daftar_fasilitas, 0, 4) as $f)<span class="chip">{{ $f }}</span>@empty<span class="text-xs text-slate-400">Fasilitas belum diisi</span>@endforelse
        </div>
        <div class="mt-auto flex gap-2 pt-5">
            @if($admin)
                <a href="{{ route('admin.ruangan.edit', $r) }}" class="btn-soft w-full"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
            @else
                <a href="{{ route('ruangan.show', $r) }}" class="btn-soft flex-1">Detail</a>
                @if($r->is_tersedia)
                    <a href="{{ route('peminjaman.create', ['ruangan' => $r->id_ruangan]) }}" class="btn-primary flex-1">Pinjam</a>
                @else
                    <button type="button" disabled class="btn-primary flex-1" title="Ruangan sedang tidak tersedia">Pinjam</button>
                @endif
            @endif
        </div>
    </div>
</article>
