@props(['type' => 'success', 'message' => null])
@php
$tone = ['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-800', 'info' => 'border-brand-200 bg-brand-50 text-brand-800'][$type];
@endphp
<div x-data="{ show: true }" x-show="show" role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->merge(['class' => "mb-5 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm $tone"]) }}>
    <x-icon :name="$type === 'error' ? 'alert' : 'circle-check'" class="mt-0.5 h-4 w-4 shrink-0" />
    <div class="flex-1">{{ $message ?? $slot }}</div>
    <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Tutup notifikasi"><x-icon name="x" class="h-4 w-4" /></button>
</div>
