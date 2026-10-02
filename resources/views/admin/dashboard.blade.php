<x-layouts.app title="Dashboard Utama">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-extrabold text-slate-900">Dashboard Utama</h1>
        <span class="card flex items-center gap-2 px-4 py-2 text-sm font-medium"><x-icon name="calendar" class="h-4 w-4" /> {{ $bulan }}</span>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Pengajuan Menunggu" :value="$menunggu" icon="clock" tone="amber" />
        <x-stat-card label="Pengajuan Disetujui" :value="$disetujui" icon="circle-check" tone="green" />
        <x-stat-card label="Pengajuan Ditolak" :value="$ditolak" icon="circle-x" tone="red" />
        <x-stat-card label="Feedback Baru" :value="$feedbackBaru" icon="message" />
    </div>
    <p class="mt-3 text-xs text-slate-500">{{ $ruanganTersedia }} dari {{ $totalRuangan }} ruangan berstatus tersedia.</p>

    <div class="mt-6 grid gap-6 lg:grid-cols-5">
        <section class="card p-6 lg:col-span-3" aria-labelledby="tren">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h2 id="tren" class="text-lg font-bold">Tren Pengajuan Peminjaman Mingguan</h2>
                <ul class="flex gap-3 text-xs text-slate-600">
                    <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-brand-700"></span>Disetujui</li>
                    <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-amber-500"></span>Menunggu</li>
                    <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-red-600"></span>Ditolak</li>
                </ul>
            </div>
            <div class="mt-6 flex h-56 items-end justify-between gap-2 border-b border-slate-100">
                @foreach($tren as $t)
                    @php $tot = $t['disetujui'] + $t['menunggu'] + $t['ditolak']; @endphp
                    <div class="flex h-full flex-1 flex-col items-center justify-end" title="{{ $t['label'] }}: {{ $tot }} pengajuan">
                        <div class="flex w-8 flex-col-reverse overflow-hidden rounded-full sm:w-10" style="height: {{ round($tot / $trenMax * 100) }}%">
                            @if($t['disetujui'])<div class="bg-brand-700" style="flex: {{ $t['disetujui'] }}"></div>@endif
                            @if($t['menunggu'])<div class="bg-amber-500" style="flex: {{ $t['menunggu'] }}"></div>@endif
                            @if($t['ditolak'])<div class="bg-red-600" style="flex: {{ $t['ditolak'] }}"></div>@endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between gap-2 text-xs text-slate-500">@foreach($tren as $t)<span class="flex-1 text-center">{{ $t['label'] }}</span>@endforeach</div>
            @if($tren->sum(fn ($t) => $t['disetujui'] + $t['menunggu'] + $t['ditolak']) === 0)
                <p class="mt-3 text-center text-sm text-slate-500">Belum ada pengajuan pada minggu ini.</p>
            @endif
        </section>

        <section class="card p-6 lg:col-span-2" aria-labelledby="aktivitas">
            <div class="flex items-center justify-between"><h2 id="aktivitas" class="text-lg font-bold">Aktivitas & Pengajuan Terbaru</h2>
                <a href="{{ route('admin.pengajuan.index') }}" class="text-xs font-bold text-brand-700 hover:underline">Lihat Semua</a></div>
            <div class="mt-4 space-y-2.5">
                @forelse($terbaru as $p)
                    <a href="{{ route('admin.pengajuan.show', $p) }}" class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 hover:bg-brand-50">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">{{ collect(explode(' ', $p->nama_pengaju))->take(2)->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('') }}</span>
                        <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">{{ $p->nama_pengaju }}</p><p class="truncate text-xs text-slate-500">{{ $p->ruangan->nama_ruangan }} · {{ $p->created_at->diffForHumans() }}</p></div>
                        <x-status-badge :status="$p->status" />
                    </a>
                @empty
                    <x-empty-state icon="clipboard" title="Belum ada pengajuan." class="!py-8" />
                @endforelse
            </div>
        </section>
    </div>

    <section class="card mt-6 p-6" aria-labelledby="hariini">
        <h2 id="hariini" class="flex items-center gap-2 text-lg font-bold"><span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-50 text-brand-700"><x-icon name="calendar" class="h-5 w-5" /></span> Jadwal Penggunaan Ruangan Hari Ini</h2>
        @if($hariIni->isEmpty())
            <x-empty-state icon="calendar" title="Tidak ada penggunaan ruangan hari ini." class="!py-10" />
        @else
            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($hariIni as $j)
                    @php $sedang = now()->format('H:i:s') >= $j->jam_mulai && now()->format('H:i:s') < $j->jam_selesai; @endphp
                    <article class="overflow-hidden rounded-2xl bg-slate-50 p-3">
                        <div class="relative aspect-[16/9] overflow-hidden rounded-xl bg-brand-50">
                            @if($j->ruangan->foto_url)<img src="{{ $j->ruangan->foto_url }}" alt="" class="h-full w-full object-cover" loading="lazy">@else<div class="grid h-full place-items-center text-brand-600/40"><x-icon name="building" class="h-12 w-12" /></div>@endif
                            <span class="absolute right-2 top-2 rounded-full px-2.5 py-1 text-xs font-bold {{ $sedang ? 'bg-brand-700 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $sedang ? 'Sedang Digunakan' : 'Mulai '.substr($j->jam_mulai, 0, 5) }}</span>
                        </div>
                        <h3 class="mt-3 font-bold">{{ $j->ruangan->nama_ruangan }}</h3>
                        <dl class="mt-2 space-y-1 rounded-lg bg-white p-3 text-xs">
                            <div class="flex justify-between"><dt class="text-slate-500">Jam</dt><dd class="font-semibold">{{ $j->jam }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Pemohon</dt><dd class="font-semibold">{{ $j->peminjaman->nama_pengaju }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Agenda</dt><dd class="truncate font-semibold text-brand-700">{{ $j->peminjaman->alasan }}</dd></div>
                        </dl>
                        <a href="{{ route('admin.pengajuan.show', $j->peminjaman) }}" class="btn-primary mt-3 w-full text-xs">Detail Jadwal <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
