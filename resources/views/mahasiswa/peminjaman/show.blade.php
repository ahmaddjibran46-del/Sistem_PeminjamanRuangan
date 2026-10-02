<x-layouts.app title="Detail Peminjaman">
    <a href="{{ route('peminjaman.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Riwayat</a>

    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><p class="text-xs font-semibold text-slate-500">{{ $p->nomor }}</p><h1 class="text-2xl font-extrabold">{{ $p->ruangan->nama_ruangan }}</h1></div>
            <x-status-badge :status="$p->status" class="text-sm" />
        </div>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Mulai</dt><dd class="mt-1 font-semibold">{{ $p->tanggal_mulai->translatedFormat('l, d F Y · H:i') }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Selesai</dt><dd class="mt-1 font-semibold">{{ $p->tanggal_selesai->translatedFormat('l, d F Y · H:i') }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Nama Pengaju</dt><dd class="mt-1 font-semibold">{{ $p->nama_pengaju }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Jumlah Peserta</dt><dd class="mt-1 font-semibold">{{ $p->jumlah_peserta }} orang</dd></div>
        </dl>

        <h2 class="mt-6 text-sm font-bold">Keperluan</h2>
        <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $p->alasan }}</p>

        @if($p->punya_dokumen)
            <a href="{{ route('peminjaman.dokumen', $p) }}" target="_blank" rel="noopener" class="btn-soft mt-5"><x-icon name="file" class="h-4 w-4" /> Lihat Dokumen Pendukung</a>
        @endif

        @if($p->catatan_admin)
            <div class="mt-6 rounded-xl border {{ $p->status === 'ditolak' ? 'border-red-200 bg-red-50' : 'border-brand-200 bg-brand-50' }} p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Admin</p>
                <p class="mt-1 whitespace-pre-line text-sm">{{ $p->catatan_admin }}</p>
            </div>
        @endif

        @if($p->status === 'selesai')
            <div class="mt-6 border-t border-slate-100 pt-5"><a href="{{ route('feedback.create') }}" class="btn-primary">Tulis Feedback</a></div>
        @endif
    </div>
</x-layouts.app>
