@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'required' => false])
<div {{ $attributes->only('class') }}>
    @if($label)<label for="{{ $name }}" class="label">{{ $label }}@if($required)<span class="text-red-600"> *</span>@endif</label>@endif
    <select id="{{ $name }}" name="{{ $name }}" @required($required) aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
        {{ $attributes->except('class')->merge(['class' => 'field'.($errors->has($name) ? ' field-error' : '')]) }}>
        @if($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $k => $v)
            <option value="{{ $k }}" @selected((string) old($name, $value) === (string) $k)>{{ $v }}</option>
        @endforeach
    </select>
    @error($name)<p class="mt-1 text-xs font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
</div>
