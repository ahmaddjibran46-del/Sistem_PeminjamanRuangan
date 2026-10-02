@php
$tabs = ['' => ['Semua Pengajuan', $semua], 'menunggu' => ['Menunggu Verifikasi', $hitung['menunggu'] ?? 0], 'disetujui' => ['Disetujui', $hitung['disetujui'] ?? 0], 'ditolak' => ['Ditolak', $hitung['ditolak'] ?? 0], 'selesai' => ['Selesai', $hitung['selesai'] ?? 0]];
@endphp
<x-layouts.app title="Daftar Pengajuan">
    <h1 class="text-3xl font-extrabold text-slate-900">Daftar Pengajuan Masuk & Verifikasi</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card accent tone="amber" label="Antrean Menunggu" :value="$hitung['menunggu'] ?? 0" sub="Pengajuan" icon="clock" />
        <x-stat-card accent tone="green" label="Disetujui Hari Ini" :value="$disetujuiHariIni" sub="Pengajuan" icon="circle-check" />
        <x-stat-card accent tone="red" label="Ditolak" :value="$hitung['ditolak'] ?? 0" sub="Pengajuan" icon="circle-x" />
    </div>

    <div class="card mt-6">
        <div class="space-y-4 p-4">
            <nav class="flex flex-wrap gap-2" aria-label="Filter status">
                @foreach($tabs as $k => [$label, $n])
                    <a href="{{ route('admin.pengajuan.index', array_filter(['status' => $k, 'q' => request('q'), 'tanggal' => request('tanggal')])) }}" @if((string) request('status') === (string) $k) aria-current="page" @endif
                        class="rounded-full px-3.5 py-1.5 text-sm font-semibold {{ (string) request('status') === (string) $k ? 'bg-amber-100 text-amber-900' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }} <span class="text-xs opacity-70">({{ $n }})</span></a>
                @endforeach
            </nav>
            <form method="GET" class="flex flex-wrap gap-3" role="search">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <div class="relative min-w-[220px] flex-1"><x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input name="q" value="{{ request('q') }}" placeholder="Cari nama pemohon, ruangan, atau no. pengajuan..." aria-label="Cari pengajuan" class="field pl-9"></div>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" aria-label="Filter tanggal" class="field w-auto">
                <button class="btn-soft">Terapkan</button>
            </form>
        </div>

        @if($daftar->isEmpty())
            <x-empty-state icon="clipboard" title="Tidak ada pengajuan ditemukan." text="Coba ubah filter atau kata kunci pencarian." class="border-t border-slate-100" />
        @else
            <div class="overflow-x-auto border-t border-slate-100">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-brand-50/60"><tr><th class="th">No. Pengajuan & Pemohon</th><th class="th">Ruangan</th><th class="th">Tanggal</th><th class="th">Waktu</th><th class="th">Peserta</th><th class="th">Status</th><th class="th text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($daftar as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="td"><div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">{{ $p->mahasiswa->inisial }}</span>
                                <div><p class="font-semibold">{{ $p->nama_pengaju }}</p><p class="font-mono text-[11px] text-slate-500">#{{ $p->nomor }}</p></div></div></td>
                            <td class="td font-semibold">{{ $p->ruangan->nama_ruangan }}</td>
                            <td class="td">{{ $p->tanggal_mulai->translatedFormat('D, d M Y') }}</td>
                            <td class="td whitespace-nowrap">{{ $p->tanggal_mulai->format('H:i') }} - {{ $p->tanggal_selesai->format('H:i') }}</td>
                            <td class="td"><span class="chip"><x-icon name="users" class="mr-1 h-3 w-3" />{{ $p->jumlah_peserta }} org</span></td>
                            <td class="td"><x-status-badge :status="$p->status" /></td>
                            <td class="td text-right"><a href="{{ route('admin.pengajuan.show', $p) }}" class="{{ $p->status === 'menunggu' ? 'btn-primary' : 'btn-soft' }} !px-3 !py-1.5 text-xs">{{ $p->status === 'menunggu' ? 'Tinjau' : 'Detail' }}</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 p-4">{{ $daftar->links() }}</div>
        @endif
    </div>
</x-layouts.app>
