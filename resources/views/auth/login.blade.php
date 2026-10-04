<x-layouts.base title="Login" bg="bg-surface">
<div class="flex min-h-screen flex-col">
    <header class="border-b border-slate-100 bg-white px-6 py-3"><x-logo :sub="$admin ? 'Admin Portal' : 'Portal Administrasi'" /></header>

    <main id="konten" class="flex flex-1 items-center justify-center p-4 sm:p-8">
        <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-xl shadow-brand-700/10 md:grid-cols-2">
            {{-- Panel kiri --}}
            <section class="hidden flex-col bg-brand-50 p-8 md:flex">
                <div class="flex items-center gap-2">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-700 text-white"><x-icon name="building" /></span>
                    <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-700">Layanan Terpadu</span>
                </div>
                <h2 class="mt-6 text-2xl font-bold text-brand-800">{{ $admin ? 'Portal Administrasi Peminjaman' : 'Peminjaman Ruangan Kampus' }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Sistem informasi peminjaman ruang kuliah, laboratorium, aula, dan fasilitas kampus secara terintegrasi dan real-time.</p>
                <div class="mt-auto grid place-items-center rounded-2xl bg-white/70 py-10 text-brand-700"><x-icon name="building" class="h-24 w-24 opacity-80" /></div>
            </section>

            {{-- Form --}}
            <section class="p-6 sm:p-10">
                <h1 class="text-2xl font-bold text-slate-900">{{ $judul }}</h1>
                <p class="mt-1 text-sm text-slate-600">Silakan masuk untuk melanjutkan.</p>

                <form method="POST" action="{{ $action }}" class="mt-7 space-y-5" x-data="{ lihat: false, loading: false }" @submit="loading = true">
                    @csrf
                    <div>
                        <label for="username" class="label">Username</label>
                        <div class="relative">
                            <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan NIM atau NIP" autocomplete="username" autofocus required
                                aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}" class="field pl-9 {{ $errors->has('username') ? 'field-error' : '' }}">
                        </div>
                        @error('username')<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <div class="relative">
                            <input id="password" name="password" :type="lihat ? 'text' : 'password'" placeholder="Masukkan password" autocomplete="current-password" required class="field pr-10">
                            <button type="button" @click="lihat = !lihat" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="m2 2 20 20" x-show="lihat" x-cloak/>
                                </svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
                        <div class="mt-2 text-right">
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-brand-700 hover:underline">Lupa Password?</a>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-600"> Ingat sesi saya di perangkat akademik ini
                    </label>

                    <button type="submit" class="btn-primary w-full py-3" :disabled="loading"><span x-show="!loading">Login</span><span x-show="loading" x-cloak>Memproses...</span></button>
                </form>
            </section>
        </div>
    </main>

    <footer class="border-t border-slate-100 bg-white px-6 py-4 text-xs text-slate-500">
        <div class="flex items-center gap-2"><x-icon name="shield" class="h-4 w-4" /> © {{ date('Y') }} PinjamRuang · Portal Peminjaman Ruangan Kampus</div>
    </footer>
</div>
</x-layouts.base>