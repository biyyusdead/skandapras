import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import './loading-state';
import { initLucide, refreshLucide } from './lucide-icons';

Alpine.plugin(collapse);
window.Alpine = Alpine;

// Expose ke global
window.initLucide = initLucide;
window.refreshLucide = refreshLucide;

// ============================================================
// Init Lucide — sekali saja
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    initLucide();
});

document.addEventListener('alpine:initialized', () => {
    initLucide();
});


Alpine.start();