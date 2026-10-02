<x-layouts.app title="Ajukan Peminjaman">
    <a href="{{ route('ruangan.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</a>
    <h1 class="text-3xl font-extrabold text-slate-900">Ajukan Peminjaman</h1>
    <p class="mt-1 text-sm text-slate-600">Lengkapi formulir berikut. Pengajuan akan diverifikasi oleh admin.</p>

    <form method="POST" action="{{ route('peminjaman.store') }}" enctype="multipart/form-data" novalidate
        x-data="{ loading: false, kap: {{ (int) optional($ruangan->firstWhere('id_ruangan', old('id_ruangan', $dipilih)))->kapasitas }} }" @submit="loading = true" class="card mt-6 space-y-5 p-6">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input name="nama_pengaju" label="Nama Pengaju" :value="auth()->user()->nama" required />
            <x-input name="no_telfon" label="Nomor Telepon / WhatsApp" :value="auth()->user()->no_telfon" maxlength="16" placeholder="081234567890" required />
        </div>

        <div>
            <label for="id_ruangan" class="label">Ruangan <span class="text-red-600">*</span></label>
            <select id="id_ruangan" name="id_ruangan" required class="field {{ $errors->has('id_ruangan') ? 'field-error' : '' }}"
                @change="kap = $event.target.selectedOptions[0].dataset.kap || 0">
                <option value="">Pilih ruangan</option>
                @foreach($ruangan as $r)
                    <option value="{{ $r->id_ruangan }}" data-kap="{{ $r->kapasitas }}" @selected((string) old('id_ruangan', $dipilih) === (string) $r->id_ruangan) @disabled(! $r->is_tersedia)>
                        {{ $r->nama_ruangan }} (kapasitas {{ $r->kapasitas }}){{ $r->is_tersedia ? '' : ' - tidak tersedia' }}
                    </option>
                @endforeach
            </select>
            @error('id_ruangan')<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input name="tanggal_mulai" type="datetime-local" label="Tanggal & Jam Mulai" required />
            <x-input name="tanggal_selesai" type="datetime-local" label="Tanggal & Jam Selesai" required />
        </div>

        <x-input name="jumlah_peserta" type="number" min="1" label="Jumlah Peserta" required x-bind:max="kap || null" />
        <p class="-mt-3 text-xs text-slate-500" x-show="kap > 0" x-cloak>Kapasitas ruangan: <strong x-text="kap"></strong> orang.</p>

        <x-textarea name="alasan" label="Keperluan / Alasan Peminjaman" rows="4" max="1000" required />
        <x-file-upload name="dokumen_pendukung" label="Dokumen Pendukung" required />

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('ruangan.index') }}" class="btn-ghost">Batal</a>
            <button class="btn-primary" :disabled="loading"><span x-show="!loading">Kirim Pengajuan</span><span x-show="loading" x-cloak>Mengirim...</span></button>
        </div>
    </form>
</x-layouts.app>
