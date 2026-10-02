@props(['label', 'value', 'icon' => 'info', 'tone' => 'brand', 'sub' => null, 'accent' => false])
@php
$ikon = ['brand' => 'bg-brand-50 text-brand-700', 'amber' => 'bg-amber-100 text-amber-600', 'red' => 'bg-red-100 text-red-600', 'green' => 'bg-emerald-100 text-emerald-700'][$tone];
$garis = ['brand' => 'border-t-brand-700', 'amber' => 'border-t-amber-400', 'red' => 'border-t-red-600', 'green' => 'border-t-brand-700'][$tone];
@endphp
<div {{ $attributes->merge(['class' => 'card flex items-start justify-between gap-3 p-5'.($accent ? " border-t-4 $garis" : '')]) }}>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>
        @if($sub)<p class="mt-1 text-xs text-slate-500">{{ $sub }}</p>@endif
    </div>
    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $ikon }}"><x-icon :name="$icon" /></span>
</div>
