@props(['title' => null])
@php
    $admin = request()->routeIs('admin.*');
    $user = auth($admin ? 'admin' : 'mahasiswa')->user();
    $menu = $admin ? [
        ['Dashboard Utama', 'admin.dashboard', 'dashboard', ['admin.dashboard']],
        ['Daftar Pengajuan', 'admin.pengajuan.index', 'clipboard', ['admin.pengajuan.*']],
        ['Manajemen Ruangan', 'admin.ruangan.index', 'building', ['admin.ruangan.*']],
        ['Feedback', 'admin.feedback.index', 'message', ['admin.feedback.*']],
    ] : [
        ['Lihat Ruangan', 'ruangan.index', 'building', ['ruangan.*', 'peminjaman.create']],
        ['Riwayat Peminjaman', 'peminjaman.index', 'history', ['peminjaman.index', 'peminjaman.show']],
        ['Feedback', 'feedback.index', 'message', ['feedback.*']],
    ];
@endphp
<x-layouts.base :title="$title" :bg="$admin ? 'bg-surface' : 'bg-mint'">
<div x-data="{ drawer: false }" @keydown.escape.window="drawer = false" class="flex min-h-screen">
    {{-- Overlay mobile --}}
    <div x-show="drawer" x-cloak x-transition.opacity @click="drawer = false" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

    <aside :class="drawer ? 'translate-x-0' : '-translate-x-full'" aria-label="Navigasi utama"
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-100 bg-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <div class="flex h-16 items-center justify-between px-5">
            <x-logo :sub="$admin ? 'Admin Portal' : 'Portal Administrasi'" />
            <button class="lg:hidden" @click="drawer = false" aria-label="Tutup menu"><x-icon name="x" /></button>
        </div>
        <nav class="flex-1 space-y-1 px-3 py-4">
            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menu Utama</p>
            @foreach($menu as [$label, $rute, $ikon, $pola])
                @php $aktif = request()->routeIs(...$pola); @endphp
                <a href="{{ route($rute) }}" @if($aktif) aria-current="page" @endif
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $aktif ? 'bg-brand-700 text-white shadow-sm' : 'text-slate-700 hover:bg-brand-50' }}">
                    <x-icon :name="$ikon" class="h-[18px] w-[18px]" /> {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-slate-100 p-3">
            @unless($admin)
                <div class="mb-2 flex items-center gap-3 rounded-xl bg-brand-50 p-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-700 text-xs font-bold text-white">{{ $user->inisial }}</span>
                    <div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $user->nama }}</p><p class="text-xs text-slate-500">{{ $user->nim }}</p></div>
                </div>
            @endunless
            <form method="POST" action="{{ route($admin ? 'admin.logout' : 'logout') }}">@csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50"><x-icon name="logout" class="h-[18px] w-[18px]" /> Keluar Sistem</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-100 bg-white/90 px-4 backdrop-blur sm:px-6">
            <button class="lg:hidden" @click="drawer = true" aria-label="Buka menu"><x-icon name="menu" class="h-6 w-6" /></button>
            @if($admin)
                <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="relative max-w-xl flex-1" role="search">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input name="q" type="search" placeholder="Cari no. pengajuan, nama pemohon, atau ruangan..." aria-label="Cari pengajuan" class="field bg-brand-50/60 pl-9">
                </form>
                <div class="ml-auto flex items-center gap-3">
                    <div class="hidden text-right leading-tight sm:block"><p class="text-sm font-semibold">{{ $user->nama }}</p><p class="text-xs text-slate-500">Administrator</p></div>
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-700 text-xs font-bold text-white">{{ $user->inisial }}</span>
                </div>
            @endif
        </header>

        <main id="konten" class="mx-auto w-full max-w-7xl flex-1 p-4 sm:p-6 lg:p-8">
            @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
            @if(session('error'))<x-alert type="error" :message="session('error')" />@endif
            {{ $slot }}
        </main>
    </div>
</div>
</x-layouts.base>
