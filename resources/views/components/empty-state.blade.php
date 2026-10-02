@props(['title', 'text' => null, 'icon' => 'info'])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <span class="grid h-14 w-14 place-items-center rounded-full bg-brand-50 text-brand-700"><x-icon :name="$icon" class="h-7 w-7" /></span>
    <p class="mt-4 text-base font-semibold text-slate-800">{{ $title }}</p>
    @if($text)<p class="mt-1 max-w-sm text-sm text-slate-500">{{ $text }}</p>@endif
    <div class="mt-4">{{ $slot }}</div>
</div>
