<x-layouts.app title="Riwayat Peminjaman">
    <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Peminjaman</h1>
    <p class="mt-1 text-sm text-slate-600">Lihat daftar, pantau verifikasi administrasi, dan status peminjaman ruangan Anda.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Pengajuan" :value="$total" icon="file" />
        <x-stat-card label="Disetujui" :value="($hitung['disetujui'] ?? 0) + ($hitung['selesai'] ?? 0)" icon="circle-check" tone="green" />
        <x-stat-card label="Ditolak / Batal" :value="$hitung['ditolak'] ?? 0" icon="circle-x" tone="red" />
    </div>

    <form method="GET" class="card mt-6 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5" role="search">
        <div class="relative lg:col-span-2"><x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input name="q" value="{{ request('q') }}" placeholder="Cari ruangan atau keperluan..." aria-label="Cari riwayat" class="field pl-9"></div>
        <select name="status" aria-label="Filter status" class="field"><option value="">Semua status</option>
            @foreach(['menunggu', 'disetujui', 'ditolak', 'selesai'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
        <select name="ruangan" aria-label="Filter ruangan" class="field"><option value="">Semua ruangan</option>
            @foreach($ruanganOpsi as $r)<option value="{{ $r->id_ruangan }}" @selected((string) request('ruangan') === (string) $r->id_ruangan)>{{ $r->nama_ruangan }}</option>@endforeach</select>
        <div class="flex gap-2"><input type="date" name="tanggal" value="{{ request('tanggal') }}" aria-label="Filter tanggal" class="field"><button class="btn-soft">Terapkan</button></div>
    </form>

    <div class="card mt-6 overflow-hidden">
        @if($daftar->isEmpty())
            <x-empty-state icon="history" title="Belum ada riwayat peminjaman." text="Pengajuan yang Anda buat akan muncul di sini.">
                <a href="{{ route('ruangan.index') }}" class="btn-primary">Lihat Ruangan</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px]">
                    <thead class="bg-brand-50/60"><tr><th class="th">No</th><th class="th">Ruangan</th><th class="th">Tanggal</th><th class="th">Waktu</th><th class="th">Keperluan</th><th class="th">Status</th><th class="th text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($daftar as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="td text-slate-500">{{ $daftar->firstItem() + $loop->index }}</td>
                            <td class="td font-semibold">{{ $p->ruangan->nama_ruangan }}<p class="text-xs font-normal text-slate-500">{{ $p->nomor }}</p></td>
                            <td class="td">{{ $p->tanggal_mulai->translatedFormat('d M Y') }}@unless($p->tanggal_mulai->isSameDay($p->tanggal_selesai))<p class="text-xs text-slate-500">s/d {{ $p->tanggal_selesai->translatedFormat('d M Y') }}</p>@endunless</td>
                            <td class="td whitespace-nowrap">{{ $p->tanggal_mulai->format('H:i') }} - {{ $p->tanggal_selesai->format('H:i') }}</td>
                            <td class="td max-w-[220px] truncate" title="{{ $p->alasan }}">{{ $p->alasan }}</td>
                            <td class="td"><x-status-badge :status="$p->status" /></td>
                            <td class="td text-right">
                                @if($p->status === 'ditolak' && $p->catatan_admin)<a href="{{ route('peminjaman.show', $p) }}" class="btn-danger mr-1 !px-3 !py-1.5 text-xs">Alasan</a>@endif
                                <a href="{{ route('peminjaman.show', $p) }}" class="btn-soft !px-3 !py-1.5 text-xs"><x-icon name="eye" class="h-3.5 w-3.5" /> Detail</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 p-4">{{ $daftar->links() }}</div>
        @endif
    </div>
</x-layouts.app>
