@php $edit = $ruangan->exists; @endphp
<x-layouts.app :title="$edit ? 'Edit Ruangan' : 'Tambah Ruangan'">
    <a href="{{ route('admin.ruangan.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4" /> Manajemen Ruangan</a>
    <h1 class="flex flex-wrap items-center gap-3 text-3xl font-extrabold">{{ $edit ? 'Edit Ruangan' : 'Tambah Ruangan' }} @if($edit)<span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-bold text-brand-700">ID: {{ $ruangan->kode }}</span>@endif</h1>

    <form method="POST" enctype="multipart/form-data" action="{{ $edit ? route('admin.ruangan.update', $ruangan) : route('admin.ruangan.store') }}" x-data="{ loading: false, tersedia: {{ old('status', $ruangan->status) === 'tidak_tersedia' ? 'false' : 'true' }} }" @submit="loading = true" class="mt-6 space-y-6">
        @csrf @if($edit) @method('PUT') @endif

        <section class="card p-6"><h2 class="text-lg font-bold">A. Informasi Ruangan</h2><p class="text-sm text-slate-500">Nama, deskripsi, kapasitas, dan fasilitas ruangan.</p>
            <div class="mt-5 space-y-5">
                <x-input name="nama_ruangan" label="Nama Ruangan" :value="$ruangan->nama_ruangan" required />
                <x-textarea name="deskripsi" label="Deskripsi Ruangan" :value="$ruangan->deskripsi" rows="4" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-input name="kapasitas" type="number" min="1" label="Kapasitas (orang)" :value="$ruangan->kapasitas" required />
                    <x-input name="lokasi" label="Gedung & Lokasi" :value="$ruangan->lokasi" placeholder="Gedung Pusat, Lt. 1" />
                </div>
                <x-textarea name="fasilitas" label="Fasilitas Pendukung" :value="$ruangan->fasilitas" rows="3" placeholder="Pisahkan dengan koma: Proyektor 4K, Sound System, AC" />
            </div>
        </section>

        <section class="card p-6"><h2 class="text-lg font-bold">B. Foto Ruangan</h2><p class="text-sm text-slate-500">Format JPG atau PNG, maksimal 5 MB. Rasio 16:9 disarankan.</p>
            @if($edit && $ruangan->foto_url)
                <div class="mt-4 flex flex-wrap items-center gap-4"><img src="{{ $ruangan->foto_url }}" alt="Foto {{ $ruangan->nama_ruangan }} saat ini" class="h-32 w-56 rounded-xl object-cover">
                    <label class="flex items-center gap-2 text-sm text-red-700"><input type="checkbox" name="hapus_foto" value="1" class="rounded border-slate-300 text-red-600"> Hapus foto saat ini</label></div>
            @endif
            <x-file-upload name="foto" accept=".jpg,.jpeg,.png" hint="JPG atau PNG, maksimal 5 MB" class="mt-4" />
        </section>

        <section class="card p-6"><h2 class="text-lg font-bold">C. Status Ketersediaan</h2>
            <input type="hidden" name="status" :value="tersedia ? 'tersedia' : 'tidak_tersedia'">
            <div class="mt-4 flex items-center justify-between gap-4 rounded-xl bg-brand-50/60 p-4">
                <div><p class="font-semibold">Status Ketersediaan Ruangan <span class="ml-1 rounded-full bg-brand-700 px-2 py-0.5 text-xs text-white" x-text="tersedia ? 'Tersedia' : 'Tidak Tersedia'"></span></p>
                    <p class="text-sm text-slate-600">Aktifkan agar ruangan dapat dipinjam. Riwayat peminjaman tidak terhapus saat dinonaktifkan.</p></div>
                <button type="button" role="switch" :aria-checked="tersedia" aria-label="Ubah status ketersediaan" @click="tersedia = !tersedia" :class="tersedia ? 'bg-brand-700' : 'bg-slate-300'" class="relative h-7 w-12 shrink-0 rounded-full transition">
                    <span :class="tersedia ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 h-6 w-6 rounded-full bg-white shadow transition"></span></button>
            </div>
            @error('status')<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
        </section>

        <div class="card flex flex-wrap items-center justify-between gap-3 p-4">
            <p class="text-xs text-slate-500">@if($edit && $ruangan->diperbarui)Terakhir diperbarui: {{ $ruangan->diperbarui }}@endif</p>
            <div class="flex gap-3"><a href="{{ route('admin.ruangan.index') }}" class="btn-ghost">Batal</a>
                <button class="btn-primary" :disabled="loading"><x-icon name="check" class="h-4 w-4" /> {{ $edit ? 'Simpan Perubahan' : 'Tambah Ruangan' }}</button></div>
        </div>
    </form>
</x-layouts.app>
