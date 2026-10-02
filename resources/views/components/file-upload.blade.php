@props(['name', 'label' => null, 'accept' => '.pdf,.jpg,.jpeg,.png', 'hint' => 'PDF, JPG, atau PNG. Maksimal 5 MB.', 'required' => false])
<div {{ $attributes->only('class') }} x-data="{ nama: null, over: false }">
    @if($label)<label for="{{ $name }}" class="label">{{ $label }}@if($required)<span class="text-red-600"> *</span>@endif</label>@endif
    <label for="{{ $name }}" @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop="over = false"
        :class="over ? 'border-brand-600 bg-brand-50' : 'border-slate-300 bg-white'"
        class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed px-4 py-6 text-center transition hover:border-brand-600 focus-within:ring-2 focus-within:ring-brand-600/30">
        <span class="grid h-10 w-10 place-items-center rounded-lg bg-brand-50 text-brand-700"><x-icon name="upload" /></span>
        <span class="text-sm font-semibold text-brand-700" x-text="nama ?? 'Pilih berkas atau tarik ke area ini'"></span>
        <span class="text-xs text-slate-500">{{ $hint }}</span>
        <input id="{{ $name }}" name="{{ $name }}" type="file" accept="{{ $accept }}" class="sr-only" @required($required)
            @change="nama = $event.target.files[0]?.name ?? null">
    </label>
    @error($name)<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
</div>
