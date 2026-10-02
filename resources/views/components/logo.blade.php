@props(['sub' => 'Portal Administrasi'])
<div class="flex items-center gap-2.5">
    <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-white"><x-icon name="building" class="h-5 w-5" /></span>
    <span class="leading-tight">
        <span class="block text-base font-bold text-brand-800">PinjamRuang</span>
        <span class="block text-[10px] font-medium uppercase tracking-wide text-slate-500">{{ $sub }}</span>
    </span>
</div>
