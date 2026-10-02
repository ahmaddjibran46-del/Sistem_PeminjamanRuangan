@props(['status'])
@php
$map = [
 'menunggu' => ['Menunggu', 'bg-amber-100 text-amber-800', 'bg-amber-500'],
 'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-800', 'bg-emerald-600'],
 'ditolak' => ['Ditolak', 'bg-red-100 text-red-700', 'bg-red-600'],
 'selesai' => ['Selesai', 'bg-slate-100 text-slate-700', 'bg-slate-500'],
 'tersedia' => ['Tersedia', 'bg-emerald-100 text-emerald-800', 'bg-emerald-600'],
 'tidak_tersedia' => ['Tidak Tersedia', 'bg-red-100 text-red-700', 'bg-red-600'],
];
[$label, $warna, $titik] = $map[$status] ?? [ucfirst($status), 'bg-slate-100 text-slate-700', 'bg-slate-500'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold $warna"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $titik }}"></span>{{ $slot->isEmpty() ? $label : $slot }}
</span>
