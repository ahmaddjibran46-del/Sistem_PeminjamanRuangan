@php
$awalHari = 8; $akhirHari = 18; $rentang = ($akhirHari - $awalHari) * 60;
$pos = fn ($jam) => max(0, min(100, ((int) substr($jam, 0, 2) * 60 + (int) substr($jam, 3, 2) - $awalHari * 60) / $rentang * 100));
$sameDay = $p->tanggal_mulai->isSameDay($p->tanggal_selesai);
@endphp
<x-layouts.app title="Detail Pengajuan">
    <div class="space-y-6" x-data="{ dialog: null }">
        <div class="card flex flex-wrap items-center justify-between gap-4 p-5">
            <div>
                <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Daftar Pengajuan</a>
                <h1 class="mt-2 flex items-center gap-3 text-2xl font-extrabold"><span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-icon name="clipboard" /></span>Detail Pengajuan & Verifikasi Dokumen</h1>
            </div>
            <div class="text-right"><p class="font-mono text-xs text-slate-500">#{{ $p->nomor }}</p><x-status-badge :status="$p->status" class="mt-1 text-sm" /></div>
        </div>

        @if($bentrok)
            <x-alert type="error">Jadwal ruangan bentrok dengan peminjaman lain yang sudah disetujui. Pengajuan ini tidak dapat disetujui pada waktu tersebut.</x-alert>
        @endif

        <section class="card p-6"><h2 class="flex items-center gap-2 font-bold"><x-icon name="file" class="h-5 w-5 text-brand-700" /> Dokumen Pendukung</h2>
            @if($p->punya_dokumen)
                <a href="{{ route('admin.pengajuan.dokumen', $p) }}" target="_blank" rel="noopener" class="mt-4 flex items-center justify-between rounded-xl bg-brand-50 p-4 hover:bg-brand-100">
                    <span class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-lg bg-brand-700 text-white"><x-icon name="file" /></span><span><span class="block text-sm font-semibold">{{ basename($p->dokumen_pendukung) }}</span><span class="text-xs text-slate-500">Klik untuk membuka dokumen</span></span></span>
                    <x-icon name="eye" class="text-slate-500" />
                </a>
            @else<p class="mt-3 text-sm text-slate-500">Tidak ada dokumen yang dilampirkan.</p>@endif
        </section>

        <section class="card p-6"><h2 class="flex items-center gap-2 font-bold"><x-icon name="user" class="h-5 w-5 text-brand-700" /> Data Pemohon</h2>
            <div class="mt-4 flex items-center gap-4"><span class="grid h-14 w-14 place-items-center rounded-full bg-brand-700 text-lg font-bold text-white">{{ $p->mahasiswa->inisial }}</span>
                <div><p class="text-lg font-bold">{{ $p->nama_pengaju }}</p><p class="font-mono text-xs text-slate-500">NIM {{ $p->mahasiswa->nim }}</p></div></div>
            <div class="mt-4 inline-block rounded-xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Nomor Telepon</p><p class="text-sm font-semibold">{{ $p->no_telfon }}</p></div>
        </section>

        <section class="card p-6"><h2 class="flex items-center gap-2 font-bold"><x-icon name="building" class="h-5 w-5 text-brand-700" /> Ruangan</h2>
            <div class="relative mt-4 h-40 overflow-hidden rounded-xl bg-gradient-to-r from-brand-800 to-brand-600">
                @if($p->ruangan->foto_url)<img src="{{ $p->ruangan->foto_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-70">@endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-3 left-4 text-white"><p class="text-lg font-bold">{{ $p->ruangan->nama_ruangan }}</p><p class="text-xs opacity-80">{{ $p->ruangan->lokasi ?: $p->ruangan->kode }}</p></div>
                <span class="absolute bottom-3 right-4 rounded-md bg-white px-2 py-1 text-xs font-bold text-brand-800">Kapasitas {{ $p->ruangan->kapasitas }}</span>
            </div>
            @if($p->jumlah_peserta > $p->ruangan->kapasitas)<p class="mt-3 text-sm font-semibold text-red-700">Peringatan: jumlah peserta melebihi kapasitas ruangan.</p>@endif
        </section>

        <section class="card p-6"><h2 class="flex items-center gap-2 font-bold"><x-icon name="clock" class="h-5 w-5 text-brand-700" /> Waktu & Jadwal Reservasi</h2>
            <p class="mt-4 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Keperluan</p>
            <p class="whitespace-pre-line text-sm font-semibold">{{ $p->alasan }}</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Mulai</p><p class="text-sm font-semibold">{{ $p->tanggal_mulai->translatedFormat('D, d M Y · H:i') }}</p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Selesai</p><p class="text-sm font-semibold">{{ $p->tanggal_selesai->translatedFormat('D, d M Y · H:i') }}</p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Jumlah Peserta</p><p class="text-sm font-semibold">{{ $p->jumlah_peserta }} orang</p></div>
            </div>
            @if($sameDay)
                <div class="mt-5" aria-label="Linimasa pemakaian ruangan {{ $p->tanggal_mulai->translatedFormat('d M Y') }}">
                    <div class="flex justify-between text-xs text-slate-500"><span>{{ sprintf('%02d:00', $awalHari) }}</span><span>{{ sprintf('%02d:00', ($awalHari + $akhirHari) / 2) }}</span><span>{{ sprintf('%02d:00', $akhirHari) }}</span></div>
                    <div class="relative mt-1 h-7 overflow-hidden rounded-lg bg-brand-50">
                        @foreach($jadwalHari as $j)<div class="absolute inset-y-0 bg-slate-400/70 text-center text-[10px] leading-7 text-white" style="left: {{ $pos($j->jam_mulai) }}%; width: {{ max(2, $pos($j->jam_selesai) - $pos($j->jam_mulai)) }}%">terpakai</div>@endforeach
                        <div class="absolute inset-y-0 bg-brand-700 text-center text-[10px] font-bold leading-7 text-white" style="left: {{ $pos($p->tanggal_mulai->format('H:i')) }}%; width: {{ max(2, $pos($p->tanggal_selesai->format('H:i')) - $pos($p->tanggal_mulai->format('H:i'))) }}%">#{{ $p->nomor }}</div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Hijau: pengajuan ini · Abu-abu: jadwal lain yang sudah terpakai.</p>
                </div>
            @endif
        </section>

        {{-- Keputusan --}}
        <section class="card border-t-4 border-t-brand-700 p-6">
            @if($p->status === 'menunggu')
                <h2 class="flex items-center gap-2 font-bold">Keputusan Verifikator</h2>
                <form method="POST" x-ref="form" class="mt-4">
                    @csrf
                    <label for="catatan_admin" class="label">Catatan / Instruksi untuk Pemohon <span class="font-normal text-slate-500">(wajib jika menolak)</span></label>
                    <textarea id="catatan_admin" name="catatan_admin" rows="4" maxlength="1000" class="field {{ $errors->has('catatan_admin') ? 'field-error' : '' }}" placeholder="Tulis catatan atau alasan keputusan...">{{ old('catatan_admin') }}</textarea>
                    @error('catatan_admin')<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <button type="button" @click="dialog = 'tolak'" class="btn-danger border border-red-100 py-3"><x-icon name="circle-x" class="h-4 w-4" /> Tolak Pengajuan</button>
                        <button type="button" @click="dialog = 'setujui'" class="btn-primary py-3" @disabled($bentrok)><x-icon name="circle-check" class="h-4 w-4" /> Setujui Pengajuan</button>
                    </div>

                    <div x-show="dialog" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" @keydown.escape.window="dialog = null">
                        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="dialog = null">
                            <h3 class="text-lg font-bold" x-text="dialog === 'setujui' ? 'Setujui pengajuan ini?' : 'Tolak pengajuan ini?'"></h3>
                            <p class="mt-2 text-sm text-slate-600" x-text="dialog === 'setujui' ? 'Jadwal ruangan akan dibuat dan pemohon akan melihat status Disetujui.' : 'Pemohon akan melihat status Ditolak beserta catatan Anda.'"></p>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" class="btn-ghost" @click="dialog = null">Batal</button>
                                <button type="submit" formaction="{{ route('admin.pengajuan.setujui', $p) }}" x-show="dialog === 'setujui'" class="btn-primary">Ya, Setujui</button>
                                <button type="submit" formaction="{{ route('admin.pengajuan.tolak', $p) }}" x-show="dialog === 'tolak'" class="btn-danger-solid">Ya, Tolak</button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <h2 class="font-bold">Keputusan Verifikator</h2>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <x-status-badge :status="$p->status" class="text-sm" />
                    @if($p->status === 'disetujui')
                        <form method="POST" action="{{ route('admin.pengajuan.selesai', $p) }}" onsubmit="return confirm('Tandai peminjaman ini sebagai selesai?')">@csrf
                            <button class="btn-soft"><x-icon name="check" class="h-4 w-4" /> Tandai Selesai</button></form>
                    @endif
                </div>
                @if($p->catatan_admin)<div class="mt-4 rounded-xl bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Admin</p><p class="mt-1 whitespace-pre-line text-sm">{{ $p->catatan_admin }}</p></div>@endif
            @endif
        </section>
    </div>
</x-layouts.app>
