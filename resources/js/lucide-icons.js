import { createIcons, icons } from 'lucide';

// Auto-whitelist: semua icon Lucide
// Alasan: whitelist manual sering ketinggalan icon baru
// Trade-off: bundle +~150KB, tapi di-cache browser

let initialized = false;

export function initLucide(force = false) {
    if (!force && initialized) return;
    try {
        createIcons({ icons });
        initialized = true;
    } catch (e) {
        console.warn('[Lucide] init error:', e);
    }
}

export function refreshLucide() {
    initialized = false;
    initLucide(true);
}

window.initLucide = initLucide;
window.refreshLucide = refreshLucide;