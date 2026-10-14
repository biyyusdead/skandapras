@props([
    'href' => null,
    'icon' => 'eye',
    'title' => 'Aksi',
    'variant' => 'default', // default | danger | warning | success | info
    'onclick' => null,
])

@php
    $base = 'inline-flex items-center justify-center w-8 h-8 rounded-lg
             transition-all duration-150 ease-smooth active:scale-90';

    // ============================================
    // ICON → WARNA OTOMATIS (kalau variant tidak di-set)
    // ============================================
    $iconColors = [
        // Delete → merah
        'trash'         => 'text-red-500 hover:bg-red-50 hover:text-red-600',
        'trash-2'       => 'text-red-500 hover:bg-red-50 hover:text-red-600',

        // Edit → kuning/amber
        'pencil'        => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'pencil-line'   => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'pencil-ruler'  => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'edit'          => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'edit-2'        => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'edit-3'        => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'square-pen'    => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'pen'           => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'pen-line'      => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',

        // View → biru
        'eye'           => 'text-sky-500 hover:bg-sky-50 hover:text-sky-600',
        'view'          => 'text-sky-500 hover:bg-sky-50 hover:text-sky-600',

        // Download → hijau
        'download'      => 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600',

        // Check → hijau
        'check'         => 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600',
        'check-circle'  => 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600',
    ];

    // ============================================
    // VARIANT OVERRIDE
    // ============================================
    $variants = [
        'default' => 'text-surface-500 hover:bg-primary-50 hover:text-primary-600',
        'danger'  => 'text-red-500 hover:bg-red-50 hover:text-red-600',
        'warning' => 'text-amber-500 hover:bg-amber-50 hover:text-amber-600',
        'success' => 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600',
        'info'    => 'text-sky-500 hover:bg-sky-50 hover:text-sky-600',
    ];

    // Prioritas: auto-color dari icon > variant eksplisit > default
    if (isset($iconColors[$icon])) {
        $classes = "{$base} {$iconColors[$icon]}";
    } elseif ($variant !== 'default' && isset($variants[$variant])) {
        $classes = "{$base} {$variants[$variant]}";
    } else {
        $classes = "{$base} {$variants['default']}";
    }

    // ============================================
    // ICON MAP — Lucide → Bootstrap Icons
    // ============================================
    $iconMap = [
        // Edit / Pencil
        'pencil'             => 'bi-pencil',
        'pencil-line'        => 'bi-pencil',
        'pencil-ruler'       => 'bi-pencil',
        'pen'                => 'bi-pen',
        'pen-line'           => 'bi-pen',
        'pen-tool'           => 'bi-pen',
        'edit'               => 'bi-pencil',
        'edit-2'             => 'bi-pencil',
        'edit-3'             => 'bi-pencil-square',
        'square-pen'         => 'bi-pencil-square',
        'signature'          => 'bi-pen',
        'notebook-pen'       => 'bi-pencil-square',

        // Trash / Delete
        'trash'              => 'bi-trash',
        'trash-2'            => 'bi-trash',

        // Eye / View
        'eye'                => 'bi-eye',
        'eye-off'            => 'bi-eye-slash',
        'view'               => 'bi-eye',

        // Add / Remove
        'plus'               => 'bi-plus-lg',
        'minus'              => 'bi-dash-lg',
        'x'                  => 'bi-x-lg',
        'check'              => 'bi-check-lg',

        // Save / Copy
        'save'               => 'bi-save',
        'copy'               => 'bi-clipboard',
        'clipboard'          => 'bi-clipboard',
        'clipboard-check'    => 'bi-clipboard-check',

        // Download / Upload
        'download'           => 'bi-download',
        'upload'             => 'bi-upload',
        'external-link'      => 'bi-box-arrow-up-right',

        // More
        'more-vertical'      => 'bi-three-dots-vertical',
        'more-horizontal'    => 'bi-three-dots',

        // Search / Filter
        'search'             => 'bi-search',
        'filter'             => 'bi-funnel',
        'settings'           => 'bi-gear',

        // Share
        'share'              => 'bi-share',
        'share-2'            => 'bi-share',
        'bookmark'           => 'bi-bookmark',
        'heart'              => 'bi-heart',
        'star'               => 'bi-star',
        'thumbs-up'          => 'bi-hand-thumbs-up',

        // User
        'user'               => 'bi-person',
        'user-circle'        => 'bi-person-circle',
        'users'              => 'bi-people',

        // Communication
        'bell'               => 'bi-bell',
        'bell-off'           => 'bi-bell-slash',
        'mail'               => 'bi-envelope',
        'phone'              => 'bi-telephone',
        'printer'            => 'bi-printer',

        // Time
        'calendar'           => 'bi-calendar',
        'clock'              => 'bi-clock',
        'history'            => 'bi-clock-history',

        // Status
        'info'               => 'bi-info-circle',
        'alert-circle'       => 'bi-exclamation-circle',
        'alert-triangle'     => 'bi-exclamation-triangle',
        'check-circle'       => 'bi-check-circle',
        'x-circle'           => 'bi-x-circle',
        'help-circle'        => 'bi-question-circle',

        // Location
        'map-pin'            => 'bi-geo-alt',
        'map'                => 'bi-map',

        // Content
        'file-text'          => 'bi-file-text',
        'file'               => 'bi-file-earmark',
        'newspaper'          => 'bi-newspaper',
        'image'              => 'bi-image',
        'tag'                => 'bi-tag',
        'link'               => 'bi-link-45deg',

        // Education
        'graduation-cap'     => 'bi-mortarboard',
        'award'              => 'bi-award',
        'trophy'             => 'bi-trophy',
        'crown'              => 'bi-award-fill',
        'star-filled'        => 'bi-star-fill',

        // Building
        'building'           => 'bi-building',
        'building-2'         => 'bi-buildings',
        'door-open'          => 'bi-door-open',

        // Package / Box
        'package'            => 'bi-box-seam',
        'box'                => 'bi-box',
        'layers'             => 'bi-layers',

        // Tech
        'cpu'                => 'bi-cpu',
        'server'             => 'bi-server',
        'code'               => 'bi-code-slash',
        'network'            => 'bi-diagram-3',
        'wrench'             => 'bi-wrench',
        'calculator'         => 'bi-calculator',
        'palette'            => 'bi-palette',
        'briefcase'          => 'bi-briefcase',
        'rocket'             => 'bi-rocket',
        'sparkles'           => 'bi-stars',

        // Misc
        'refresh-cw'         => 'bi-arrow-clockwise',
        'rotate-cw'          => 'bi-arrow-clockwise',
        'loader'             => 'bi-arrow-clockwise',
        'mouse-pointer-click'=> 'bi-cursor',
        'lightbulb'          => 'bi-lightbulb',
        'shield-check'       => 'bi-shield-check',
        'activity'           => 'bi-activity',
        'zap'                => 'bi-lightning',
        'target'             => 'bi-bullseye',
    ];

    $biClass = $iconMap[$icon] ?? 'bi-circle';
@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $title }}" {{ $attributes->merge(['class' => $classes]) }}>
        <i class="bi {{ $biClass }} text-base"></i>
    </a>
@else
    <button type="button" title="{{ $title }}"
            @if ($onclick) onclick="{{ $onclick }}" @endif
            {{ $attributes->merge(['class' => $classes]) }}>
        <i class="bi {{ $biClass }} text-base"></i>
    </button>
@endif