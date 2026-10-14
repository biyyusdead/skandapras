{{-- ============================================================ --}}
{{-- Konfigurasi Model 3D --}}
{{-- Dipakai oleh create.blade.php & edit.blade.php --}}
{{-- Variable $room boleh null (create) atau Room instance (edit) --}}
{{-- ============================================================ --}}
@php
    $r = $room ?? null;
    $v = fn ($field, $default = null) => old($field, $r?->{$field} ?? $default);
@endphp

<div class="pt-4 border-t border-surface-100">
    <p class="text-sm font-semibold text-surface-800 mb-1 flex items-center gap-2">
        <i data-lucide="box" class="w-4 h-4 text-primary-600"></i>
        Konfigurasi Model 3D
    </p>
    <p class="text-xs text-surface-500 mb-4">
        Isi koordinat supaya ruangan ini muncul di tab <strong>Model 3D</strong> pada halaman peta publik.
        Kosongkan jika tidak ingin ditampilkan.
    </p>

    {{-- Koordinat X, Y, W, H --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Posisi X
            </label>
            <input type="number" name="model_3d_x"
                   value="{{ $v('model_3d_x') }}"
                   min="0" max="2000" step="0.01"
                   placeholder="Contoh: 480"
                   class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                          focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
            <p class="text-xs text-surface-400 mt-1">Skala sama dengan peta 2D</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Posisi Y
            </label>
            <input type="number" name="model_3d_y"
                   value="{{ $v('model_3d_y') }}"
                   min="0" max="1200" step="0.01"
                   placeholder="Contoh: 186"
                   class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                          focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
            <p class="text-xs text-surface-400 mt-1">0 = atas, 1200 = bawah</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Lebar Bangunan
            </label>
            <input type="number" name="model_3d_w"
                   value="{{ $v('model_3d_w', 90) }}"
                   min="1" max="500" step="0.01"
                   class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                          focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Dalam Bangunan
            </label>
            <input type="number" name="model_3d_h"
                   value="{{ $v('model_3d_h', 52) }}"
                   min="1" max="500" step="0.01"
                   class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                          focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
        </div>
    </div>

    {{-- Tinggi, bentuk atap, warna --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Tinggi (meter)
            </label>
            <input type="number" name="model_3d_height"
                   value="{{ $v('model_3d_height', 3) }}"
                   min="0.5" max="50" step="0.1"
                   class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                          focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
            <p class="text-xs text-surface-400 mt-1">≥ 3.8 = otomatis 2 lantai</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Bentuk Atap
            </label>
            <select name="model_3d_shape"
                    class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                           bg-white cursor-pointer
                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                @foreach ([
                    'pitched'  => 'Atap Limas (default)',
                    'flat'     => 'Datar',
                    'dome'     => 'Kubah / Masjid',
                    'cylinder' => 'Silinder (joglo)',
                ] as $val => $label)
                    <option value="{{ $val }}" @selected($v('model_3d_shape', 'pitched') === $val)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                Warna Dinding
            </label>
            <div class="flex items-center gap-2">
                <input type="color" name="model_3d_color"
                       value="{{ $v('model_3d_color', '#7fa8ee') }}"
                       class="w-14 h-10 rounded-lg border border-surface-300 cursor-pointer shrink-0">
                <input type="text" readonly
                       value="{{ $v('model_3d_color', '#7fa8ee') }}"
                       id="model-3d-color-preview"
                       class="flex-1 px-3 py-2.5 text-xs font-mono border border-surface-200
                              rounded-lg bg-surface-50 text-surface-600">
            </div>
        </div>
    </div>

    {{-- Toggle visibility --}}
    <div class="mt-4 pt-4 border-t border-surface-100">
        <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                      hover:border-primary-400 transition-colors duration-150">
            <input type="hidden" name="model_3d_visible" value="0">
            <input type="checkbox" name="model_3d_visible" value="1"
                   @checked((bool) $v('model_3d_visible', true))
                   class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
            <span class="text-sm text-surface-700">Tampilkan di Model 3D</span>
        </label>
    </div>

    {{-- Helper text --}}
    <div class="mt-4 p-3 bg-primary-50/60 border border-primary-200/60 rounded-lg">
        <p class="text-xs text-primary-800 leading-relaxed">
            <strong>💡 Tips koordinat:</strong> Sistem 3D memakai skala sama dengan peta 2D
            (kanvas 1570 × 900 unit). Contoh: gedung di kiri-atas denah pakai
            <code class="bg-primary-100 px-1 rounded">X=68, Y=22</code>,
            gedung di kanan-bawah pakai <code class="bg-primary-100 px-1 rounded">X=1288, Y=690</code>.
        </p>
    </div>
</div>