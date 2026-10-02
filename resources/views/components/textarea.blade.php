@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'max' => null, 'required' => false])
<div {{ $attributes->only('class') }} @if($max) x-data="{ n: {{ mb_strlen(old($name, $value ?? '')) }} }" @endif>
    @if($label)<label for="{{ $name }}" class="label">{{ $label }}@if($required)<span class="text-red-600"> *</span>@endif</label>@endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required) @if($max) maxlength="{{ $max }}" @input="n = $event.target.value.length" @endif
        aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
        {{ $attributes->except('class')->merge(['class' => 'field'.($errors->has($name) ? ' field-error' : '')]) }}>{{ old($name, $value) }}</textarea>
    <div class="mt-1 flex justify-between gap-3">
        <div>@error($name)<p class="text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror</div>
        @if($max)<p class="text-xs text-slate-400"><span x-text="n"></span>/{{ $max }}</p>@endif
    </div>
</div>
