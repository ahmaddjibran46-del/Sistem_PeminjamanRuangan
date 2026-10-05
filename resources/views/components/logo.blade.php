@props(['sub' => 'Portal Administrasi', 'size' => 'h-9 w-9'])
<div class="flex items-center gap-2.5">
    <x-logo-mark :class="$size" />
    <span class="leading-tight">
        <span class="block text-base font-bold text-brand-800">PinjamRuang</span>
        <span class="block text-[10px] font-medium uppercase tracking-wide text-slate-500">{{ $sub }}</span>
    </span>
</div>
