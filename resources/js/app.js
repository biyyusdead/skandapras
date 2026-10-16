import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import './loading-state';
import './ui-helpers';
import { initLucide, refreshLucide } from './lucide-icons';
import { registerReportModal } from './pengaduan';
import { registerChatbot } from './public';

// 1. Plugin dulu
Alpine.plugin(collapse);

// 2. Register component function SEBELUM Alpine.start()
registerReportModal();
registerChatbot();

// 3. Expose ke window
window.Alpine = Alpine;
window.initLucide = initLucide;
window.refreshLucide = refreshLucide;

// 4. BARU start Alpine
Alpine.start();

// 5. Init Lucide setelah semua siap
document.addEventListener('DOMContentLoaded', () => initLucide());
document.addEventListener('alpine:initialized', () => initLucide());