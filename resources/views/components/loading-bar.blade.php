<div x-data="loadingBar()" x-init="init()" class="fixed top-0 left-0 right-0 z-[9999] h-0.5 pointer-events-none">
    <div x-show="visible" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="h-full bg-gradient-to-r from-primary-500 via-primary-400 to-primary-600 shadow-[0_0_10px_rgba(108,74,182,0.6)] transition-[width] duration-300 ease-out"
        :style="`width: ${progress}%`"></div>
</div>