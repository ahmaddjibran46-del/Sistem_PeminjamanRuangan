<x-layouts.app title="Feedback">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-3xl font-extrabold text-slate-900">Feedback Saya</h1><p class="mt-1 text-sm text-slate-600">Riwayat masukan yang pernah Anda kirim.</p></div>
        <a href="{{ route('feedback.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Tulis Feedback</a>
    </div>

    <div class="mt-6 space-y-4">
        @forelse($daftar as $f)
            <article class="card p-5">
                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                    <span class="font-semibold text-brand-700">{{ $f->ruangan?->nama_ruangan ?? 'Umum' }}</span>
                    <time>{{ $f->created_at->translatedFormat('d M Y, H:i') }}</time>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $f->isi_feedback }}</p>
                @if($f->balasan_admin)
                    <div class="mt-4 rounded-xl bg-brand-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Tanggapan Admin · {{ $f->dibalas_at?->translatedFormat('d M Y') }}</p>
                        <p class="mt-1 whitespace-pre-line text-sm">{{ $f->balasan_admin }}</p>
                    </div>
                @endif
            </article>
        @empty
            <div class="card"><x-empty-state icon="message" title="Belum ada feedback." text="Masukan Anda sangat berarti bagi kami." /></div>
        @endforelse
    </div>
    <div class="mt-6">{{ $daftar->links() }}</div>
</x-layouts.app>
