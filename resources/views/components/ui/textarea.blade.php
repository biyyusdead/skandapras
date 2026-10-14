@props([
    'name',
    'label'    => null,
    'required' => false,
    'rows'     => 4,
])

@php
    $hasError = $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-surface-700 mb-1.5">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->merge([
            'class' => 'w-full px-3.5 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition '
                . ($hasError ? 'border-red-500' : 'border-surface-300')
        ]) }}
    >{{ $slot }}</textarea>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>