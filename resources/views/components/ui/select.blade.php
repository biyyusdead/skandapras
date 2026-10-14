@props([
    'name',
    'label'    => null,
    'required' => false,
    'options'  => [],
    'placeholder' => '— Pilih —',
])

@php
    $hasError = $errors->has($name);
    $selected = old($name, $attributes->get('value'));
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-surface-700 mb-1.5">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->except('value')->merge([
            'class' => 'w-full px-3.5 py-2.5 text-sm border rounded-lg bg-white
                        transition-all duration-150 ease-smooth
                        focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                        cursor-pointer '
                . ($hasError
                    ? 'border-red-400 focus:ring-red-500/30 focus:border-red-500'
                    : 'border-surface-300 hover:border-surface-400')
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" @selected($selected == $value)>{{ $label }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i>
            {{ $message }}
        </p>
    @enderror
</div>