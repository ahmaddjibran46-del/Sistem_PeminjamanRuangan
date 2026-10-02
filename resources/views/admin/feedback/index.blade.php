<x-layouts.app title="Feedback">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-extrabold text-slate-900">Semua Feedback</h1>
        <nav class="card flex gap-1 p-1 text-sm font-semibold" aria-label="Filter feedback">
            <a href="{{ route('admin.feedback.index') }}" class="rounded-lg px-3 py-1.5 {{ request('filter') !== 'baru' ? 'bg-brand-50 text-brand-800' : 'text-slate-600' }}">Semua Feedback <span class="ml-1 rounded-full bg-brand-100 px-1.5 text-xs">{{ $semua }}</span></a>
            <a href="{{ route('admin.feedback.index', ['filter' => 'baru']) }}" class="rounded-lg px-3 py-1.5 {{ request('filter') === 'baru' ? 'bg-amber-100 text-amber-900' : 'text-slate-600' }}">Baru <span class="ml-1 rounded-full bg-amber-100 px-1.5 text-xs">{{ $jumlahBaru }}</span></a>
        </nav>
    </div>

    @if($daftar->isEmpty())
        <div class="card mt-6"><x-empty-state icon="message" title="Belum ada feedback." text="Masukan dari mahasiswa akan muncul di sini." /></div>
    @else
    <div class="card mt-6 grid overflow-hidden lg:grid-cols-5">
        <div class="border-b border-slate-100 lg:col-span-2 lg:border-b-0 lg:border-r">
            <form method="GET" role="search" class="p-3">
                @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
                <div class="relative"><x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input name="q" value="{{ request('q') }}" placeholder="Cari pengirim atau kata kunci..." aria-label="Cari feedback" class="field pl-9"></div>
            </form>
            <ul class="max-h-[560px] divide-y divide-slate-100 overflow-y-auto">
                @foreach($daftar as $f)
                    <li><a href="{{ route('admin.feedback.show', array_filter(['feedback' => $f, 'filter' => request('filter'), 'q' => request('q')])) }}" @if($dipilih && $dipilih->is($f)) aria-current="true" @endif
                        class="block border-l-4 px-4 py-3 hover:bg-slate-50 {{ $dipilih && $dipilih->is($f) ? 'border-brand-700 bg-brand-50/60' : 'border-transparent' }}">
                        <div class="flex items-center justify-between gap-2"><p class="text-sm {{ $f->dibaca_at ? 'font-medium' : 'font-bold' }}">{{ $f->mahasiswa->nama }}</p><time class="shrink-0 text-xs {{ $f->dibaca_at ? 'text-slate-400' : 'font-bold text-amber-700' }}">{{ $f->created_at->translatedFormat('d M, H:i') }}</time></div>
                        <p class="text-xs font-semibold text-brand-700">{{ $f->ruangan?->nama_ruangan ?? 'Umum' }}</p>
                        <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $f->isi_feedback }}</p>
                    </a></li>
                @endforeach
            </ul>
            <div class="border-t border-slate-100 p-3">{{ $daftar->links() }}</div>
        </div>

        <article class="p-6 lg:col-span-3">
            @if($dipilih)
                <div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-full bg-brand-100 font-bold text-brand-800">{{ $dipilih->mahasiswa->inisial }}</span>
                    <div><p class="text-lg font-bold">{{ $dipilih->mahasiswa->nama }} <span class="ml-1 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700">Sudah Dibaca</span></p><p class="font-mono text-xs text-slate-500">NIM {{ $dipilih->mahasiswa->nim }}</p></div></div>
                <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm"><p class="text-xs text-slate-500">Ruangan</p><p class="font-semibold">{{ $dipilih->ruangan?->nama_ruangan ?? 'Umum (tidak terkait ruangan tertentu)' }}</p></div>
                <p class="mt-6 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Pesan Lengkap · {{ $dipilih->created_at->translatedFormat('l, d F Y · H:i') }}</p>
                <div class="mt-2 whitespace-pre-line rounded-xl bg-brand-50/60 p-4 text-sm leading-relaxed">{{ $dipilih->isi_feedback }}</div>

                @if($dipilih->balasan_admin)
                    <div class="mt-5 rounded-xl border border-brand-200 bg-white p-4"><p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Tanggapan Admin · {{ $dipilih->dibalas_at?->translatedFormat('d M Y, H:i') }}</p><p class="mt-1 whitespace-pre-line text-sm">{{ $dipilih->balasan_admin }}</p></div>
                @else
                    <form method="POST" action="{{ route('admin.feedback.balas', $dipilih) }}" class="mt-5 rounded-xl bg-brand-50/50 p-4" x-data="{ loading: false }" @submit="loading = true">@csrf
                        <x-textarea name="balasan_admin" label="Tulis Tanggapan Resmi Admin" rows="4" max="1000" required />
                        <button class="btn-primary mt-3" :disabled="loading"><x-icon name="send" class="h-4 w-4" /> Kirim Tanggapan</button>
                    </form>
                @endif
            @endif
        </article>
    </div>
    @endif
</x-layouts.app>
