@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])
<div {{ $attributes->only('class') }}>
    @if($label)<label for="{{ $name }}" class="label">{{ $label }}@if($required)<span class="text-red-600"> *</span>@endif</label>@endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($name, $value) }}"
        @required($required) aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
        {{ $attributes->except('class')->merge(['class' => 'field'.($errors->has($name) ? ' field-error' : '')]) }}>
    @if($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
</div>
