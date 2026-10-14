@props([
    'name',
    'label'    => null,
    'type'     => 'text',
    'required' => false,
    'hint'     => null,
    'icon'     => null,
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

    <div class="relative">
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full py-2.5 text-sm border rounded-lg bg-white
                            transition-all duration-150 ease-smooth
                            focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 focus:bg-white
                            placeholder:text-surface-400 '
                    . ($icon ? 'pl-9 pr-3 ' : 'px-3.5 ')
                    . ($hasError
                        ? 'border-red-400 focus:ring-red-500/30 focus:border-red-500'
                        : 'border-surface-300 hover:border-surface-400')
            ]) }}
        >
    </div>

    @if ($hint && !$hasError)
        <p class="mt-1.5 text-xs text-surface-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i>
            {{ $message }}
        </p>
    @enderror
</div>