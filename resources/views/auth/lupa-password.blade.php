@php $wa = preg_replace('/\D/', '', (string) config('pinjamruang.kontak_bantuan')); @endphp
<x-layouts.base title="Lupa Password" bg="bg-surface">
<div class="flex min-h-screen flex-col">
    <header class="border-b border-slate-100 bg-white px-6 py-3"><x-logo sub="Portal Administrasi" /></header>

    <main id="konten" class="flex flex-1 items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl shadow-brand-700/10 sm:p-10">
            <h1 class="text-2xl font-bold text-slate-900">Lupa Password</h1>
            <p class="mt-1 text-sm text-slate-600">Password diatur ulang oleh pengelola sistem.</p>

            <div class="mt-6 rounded-xl bg-brand-50 p-4 text-sm leading-relaxed text-slate-700">
                Akun belum terhubung ke email atau kode OTP, sehingga password tidak dapat diatur ulang sendiri.
                Silakan hubungi pengelola dan sebutkan <strong>Username</strong> Anda
                (NIM untuk mahasiswa, NIP untuk admin). Pengelola akan memberikan password sementara.
            </div>

            @if($wa !== '')
                <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo, saya lupa password PinjamRuang. Mohon bantuan reset password.') }}"
                   target="_blank" rel="noopener" class="btn-primary mt-6 w-full py-3">Hubungi Pengelola via WhatsApp</a>
            @endif

            <a href="{{ route('login') }}" class="btn-ghost mt-3 w-full py-3"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Login</a>
        </div>
    </main>
</div>
</x-layouts.base>
