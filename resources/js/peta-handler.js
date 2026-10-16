// =============================================================
// peta-handler.js — Port lengkap + tooltip scale by distance
// =============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SCROLL REVEAL & COUNTER
    // ============================================================
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.scroll-reveal').forEach(el => revealObserver.observe(el));

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const counter = entry.target;
            const target = parseInt(counter.dataset.target, 10) || 0;
            const duration = 1500;
            const startTime = performance.now();
            function animate(t) {
                const p = Math.min((t - startTime) / duration, 1);
                const e = 1 - Math.pow(1 - p, 3);
                counter.textContent = Math.floor(e * target).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(animate);
                else counter.textContent = target.toLocaleString('id-ID');
            }
            counter.textContent = '0';
            requestAnimationFrame(animate);
            counterObserver.unobserve(counter);
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));

    // ============================================================
    // 2. VIEW TOGGLE
    // ============================================================
    const viewMap = document.getElementById('view-map');
    const view3D = document.getElementById('view-3d');
    const viewList = document.getElementById('view-list');
    const toggleBtns = document.querySelectorAll('.view-toggle-btn');

    let scene3DInitialized = false;
    let scene3DInitPromise = null;

    function waitForLibs(timeoutMs = 8000) {
        return new Promise((resolve, reject) => {
            const start = performance.now();
            (function check() {
                if (typeof THREE !== 'undefined' && typeof THREE.WebGLRenderer !== 'undefined') return resolve();
                if (performance.now() - start > timeoutMs) return reject(new Error('THREE timeout'));
                requestAnimationFrame(check);
            })();
        });
    }

    async function ensure3DScene() {
        if (scene3DInitialized) { window.trigger3DResize?.(); return; }
        if (scene3DInitPromise) return scene3DInitPromise;
        scene3DInitPromise = (async () => {
            await waitForLibs();
            init3DScene();
            scene3DInitialized = true;
        })();
        try { await scene3DInitPromise; }
        catch (err) {
            console.error('[3D Error]', err);
            scene3DInitPromise = null;
            scene3DInitialized = false;
            const c = document.getElementById('three-container');
            if (c) c.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.7);font-size:14px;padding:20px;text-align:center;">⚠️ Gagal memuat 3D. Refresh halaman.</div>`;
        }
    }

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const view = btn.dataset.view;
            toggleBtns.forEach(b => {
                const a = b.dataset.view === view;
                b.classList.toggle('bg-white', a);
                b.classList.toggle('text-primary-600', a);
                b.classList.toggle('shadow-sm', a);
                b.classList.toggle('text-surface-500', !a);
            });
            if (viewMap) viewMap.classList.toggle('hidden', view !== 'map');
            if (view3D) view3D.classList.toggle('hidden', view !== '3d');
            if (viewList) viewList.classList.toggle('hidden', view !== 'list');

            const searchFilterBar = document.getElementById('search-filter-bar');
            if (searchFilterBar) {
                searchFilterBar.classList.toggle('hidden', view === '3d');
            }

            if (view === '3d') {
                await ensure3DScene();
                requestAnimationFrame(() => window.trigger3DResize?.());
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });

    // ============================================================
    // 3. SEARCH
    // ============================================================
    const searchInput = document.getElementById('room-search');
    const searchClear = document.getElementById('room-search-clear');
    const roomMarkers = document.querySelectorAll('.room-marker');
    const roomCards = document.querySelectorAll('.room-card');
    const noResultMsg = document.getElementById('no-result-message');
    const listEmpty = document.getElementById('list-empty');

    function performSearch(query) {
        const q = (query || '').toLowerCase().trim();
        let vm = 0, vc = 0;
        roomMarkers.forEach(m => {
            const n = (m.dataset.roomName || '').toLowerCase();
            const c = (m.dataset.roomCode || '').toLowerCase();
            const l = (m.dataset.roomLocation || '').toLowerCase();
            const ok = !q || n.includes(q) || c.includes(q) || l.includes(q);
            m.style.display = ok ? '' : 'none';
            if (ok) vm++;
        });
        roomCards.forEach(c => {
            const n = (c.dataset.roomName || '').toLowerCase();
            const co = (c.dataset.roomCode || '').toLowerCase();
            const l = (c.dataset.roomLocation || '').toLowerCase();
            const ok = !q || n.includes(q) || co.includes(q) || l.includes(q);
            c.style.display = ok ? '' : 'none';
            if (ok) vc++;
        });
        if (noResultMsg) noResultMsg.classList.toggle('hidden', vm > 0 || !q);
        if (listEmpty) listEmpty.classList.toggle('hidden', vc > 0 || !q);
        if (searchClear) {
            searchClear.classList.toggle('hidden', !q);
            searchClear.classList.toggle('flex', !!q);
        }
        if (typeof window.filter3DBuildings === 'function') window.filter3DBuildings(q);
    }

    let searchTimeout;
    searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performSearch(e.target.value), 150);
    });
    searchClear?.addEventListener('click', () => {
        if (searchInput) {
            searchInput.value = '';
            performSearch('');
            searchInput.focus();
        }
    });

    // ============================================================
    // 4. PANZOOM 2D
    // ============================================================
    const mapViewport = document.getElementById('map-viewport');
    const mapContent = document.getElementById('map-content');
    if (mapViewport && mapContent && typeof Panzoom !== 'undefined') {
        const pz = Panzoom(mapContent, {
            maxScale: 3, minScale: 0.8, startScale: 1,
            contain: 'outside', cursor: 'grab', step: 0.3,
        });
        mapViewport.addEventListener('wheel', (e) => {
            if (Math.abs(e.deltaY) < 5) return;
            e.preventDefault();
            pz.zoomWithWheel(e);
        }, { passive: false });
        mapViewport.addEventListener('mousedown', () => mapViewport.classList.add('is-dragging'));
        document.addEventListener('mouseup', () => mapViewport.classList.remove('is-dragging'));
        document.getElementById('zoom-in')?.addEventListener('click', () => pz.zoomIn());
        document.getElementById('zoom-out')?.addEventListener('click', () => pz.zoomOut());
        document.getElementById('zoom-reset')?.addEventListener('click', () => pz.reset());
    }

    // ============================================================
    // 5. MODAL
    // ============================================================
    const modal = document.getElementById('room-modal');
    const modalBackdrop = document.getElementById('room-modal-backdrop');
    const modalContent = document.getElementById('room-modal-content');

    window.openRoomModal = function (data) {
        if (!modal) return;
        const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
        set('room-modal-title', data.name || '—');
        set('room-modal-code', data.code || 'RUANG');
        set('room-modal-capacity', data.capacity ? data.capacity + ' org' : '—');
        set('room-modal-health', data.health ? data.health + '/100' : '—');

        const condBadge = document.getElementById('room-modal-condition');
        if (condBadge) {
            const map = {
                baik: 'bg-emerald-500',
                rusak_ringan: 'bg-amber-500',
                rusak_berat: 'bg-red-500',
                dekoratif: 'bg-surface-400',
            };
            const cc = map[data.condition] || 'bg-surface-400';
            condBadge.className = `inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-lg ${cc}`;
            const sp = condBadge.querySelector('span:last-child');
            if (sp) sp.textContent = data.conditionLabel || 'Tersedia';
        }

        const lw = document.getElementById('room-modal-location-wrapper');
        const ls = document.querySelector('#room-modal-location span');
        if (data.location && ls) { ls.textContent = data.location; lw?.classList.remove('hidden'); }
        else lw?.classList.add('hidden');

        const photo = document.getElementById('room-modal-photo');
        if (photo) {
            photo.src = data.photo || '';
            photo.alt = data.name || '';
            photo.onerror = function () {
                this.onerror = null;
                const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="600" height="240" viewBox="0 0 600 240"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#16a34a"/><stop offset="100%" stop-color="#15803d"/></linearGradient></defs><rect width="600" height="240" fill="url(#g)"/><text x="300" y="110" fill="white" font-family="sans-serif" font-size="44" text-anchor="middle">🏫</text><text x="300" y="160" fill="white" font-family="sans-serif" font-size="16" font-weight="bold" text-anchor="middle">${data.name || ''}</text></svg>`;
                this.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
            };
        }

        const dl = document.getElementById('room-modal-detail-link');
        const rl = document.getElementById('room-modal-report-link');
        if (dl) dl.href = data.url || '#';
        if (rl) rl.href = data.reportUrl || '#';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            modalBackdrop?.classList.remove('opacity-0');
            modalBackdrop?.classList.add('opacity-100');
            modalContent?.classList.remove('scale-95', 'opacity-0');
            modalContent?.classList.add('scale-100', 'opacity-100');
            setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 60);
        });
        document.body.style.overflow = 'hidden';
    };

    function closeRoomModal() {
        if (!modal) return;
        modalBackdrop?.classList.remove('opacity-100');
        modalBackdrop?.classList.add('opacity-0');
        modalContent?.classList.remove('scale-100', 'opacity-100');
        modalContent?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 250);
    }

    document.querySelectorAll('.room-marker, .room-card').forEach(el => {
        el.addEventListener('click', () => {
            window.openRoomModal({
                id: el.dataset.roomId,
                name: el.dataset.roomName,
                code: el.dataset.roomCode,
                condition: el.dataset.roomCondition,
                conditionLabel: el.dataset.roomConditionLabel,
                floor: el.dataset.roomFloor,
                location: el.dataset.roomLocation,
                capacity: el.dataset.roomCapacity,
                health: el.dataset.roomHealth,
                url: el.dataset.roomUrl,
                reportUrl: el.dataset.roomReportUrl,
                photo: el.dataset.roomPhoto || '',
            });
        });
    });

    document.getElementById('room-modal-close')?.addEventListener('click', closeRoomModal);
    modalBackdrop?.addEventListener('click', closeRoomModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal?.classList.contains('hidden')) closeRoomModal();
    });

    // ============================================================
    // 6. THREE.JS 3D
    // ============================================================
    function init3DScene() {
        const container = document.getElementById('three-container');
        if (!container) throw new Error('#three-container tidak ditemukan');
        if (typeof THREE === 'undefined') throw new Error('THREE undefined');

        const isLowEnd = (navigator.hardwareConcurrency || 4) <= 4 || window.devicePixelRatio <= 1;

        const S = 0.05, CX = 785, CY = 450;

        const DB_MAPPING = {
            'Lab CAM & CNC': 16, 'L. PPLG': 6, 'Aula RPL': 10,
            'B. Otomotif': 28, 'B. Tekstil': 27, 'B. Mesin': 15,
            'Masjid Al-Amanah': 26, 'Kantin': 5, 'Gudang': 8,
            'R. Perpust': 9, 'R. Multimedia': 2, 'R KS': 12,
            'R. Guru': 11, 'R. TU': 34, 'R. Kesiswaan': 35,
            'Basecamp Organisasi': 21, 'L. CAD CAM': 16,
            'BK': 32, 'R Sarpra': 36, 'R UKS': 40, 'M. Al-Amanah': 26, 
            'Kopsis': 31, 'R. Olah Sampah': 17,
            'R. Kesenian': 7, 'Studio Musik': 20,
            'R 30': 73, 'R 29': 72, 'R 28': 71, 'R 27': 70, 'R 26': 69,
            'R 25': 68, 'R 24': 67, 'R 23': 66, 'R 22': 65, 'R 21': 64,
            'R 20': 63, 'R 19': 62, 'R 18': 61, 'R 17': 60, 'R 16': 59,
            'R 15': 58, 'R 14': 57, 'R 13': 56, 'R 12': 55,
            'R 11': 54, 'R 10': 53, 'R 9': 52, 'R 8': 51, 'R 7': 50,
            'R 6': 49, 'R 5': 48, 'R 4': 47, 'R 3': 46, 'R 2': 3,
            'R KI': 19,
            'R 31': 74, 'R 32': 75, 'R 33': 76, 'R 34': 77, 'R 35': 78, 'R 36': 79,
            'R. Waka': 30,
        };

        const AREA_MAPPING = {
            'Lap. Upacara': 41,
            'Lap. Basket': 37,
            'Lap. Voli': 38,
            'Lap. Utama (Sepak Bola & Atletik)': 42,
        };

        const roomById = new Map();
        const roomsAll = Array.isArray(window.serverRoomsData) ? window.serverRoomsData : [];
        roomsAll.forEach(r => roomById.set(r.id, r));

        const scene = new THREE.Scene();
        const dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        (function () {
            const c = document.createElement('canvas'); c.width = 2; c.height = 256;
            const g = c.getContext('2d');
            const gr = g.createLinearGradient(0, 0, 0, 256);
            gr.addColorStop(0, dark ? '#18283a' : '#4da8e8');
            gr.addColorStop(0.48, dark ? '#263b50' : '#8bd0f5');
            gr.addColorStop(1, dark ? '#34495a' : '#e2f4ff');
            g.fillStyle = gr; g.fillRect(0, 0, 2, 256);
            scene.background = new THREE.CanvasTexture(c);
        })();
        scene.fog = new THREE.Fog(dark ? 0x14202c : 0xc5e4f6, 165, 360);

        let width = container.clientWidth || 800;
        let height = container.clientHeight || 640;
        if (width < 10) width = 800;
        if (height < 10) height = 640;

        const cam = new THREE.PerspectiveCamera(45, width / height, 1, 400);
        const ren = new THREE.WebGLRenderer({ antialias: !isLowEnd, powerPreference: 'high-performance' });
        ren.setPixelRatio(isLowEnd ? 1 : Math.min(window.devicePixelRatio, 1.5));
        ren.setSize(width, height);
        ren.outputEncoding = THREE.sRGBEncoding;
        ren.domElement.style.display = 'block';
        ren.domElement.style.touchAction = 'none';
        container.appendChild(ren.domElement);
        ren.shadowMap.enabled = !isLowEnd;
        ren.shadowMap.type = THREE.PCFShadowMap;
        ren.toneMapping = THREE.ACESFilmicToneMapping;
        ren.toneMappingExposure = 0.90;

        scene.add(new THREE.HemisphereLight(dark ? 0x71869b : 0xd8e7f4, dark ? 0x263a25 : 0x4b6540, 0.54));
        const sun = new THREE.DirectionalLight(0xffedcf, 0.78);
        sun.position.set(-35, 65, 35);
        sun.castShadow = !isLowEnd;
        if (!isLowEnd) {
            sun.shadow.mapSize.set(1024, 1024);
            sun.shadow.radius = 3;
            sun.shadow.bias = -0.0003;
            Object.assign(sun.shadow.camera, { left: -52, right: 52, top: 38, bottom: -38, far: 220 });
        }
        scene.add(sun);
        const fillLight = new THREE.DirectionalLight(0xc6e2ff, 0.16);
        fillLight.position.set(35, 45, -35);
        scene.add(fillLight);

        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(130, 90),
            new THREE.MeshLambertMaterial({ color: dark ? 0x315b2b : 0x4f9f36 })
        );
        ground.receiveShadow = !isLowEnd;
        ground.rotation.x = -Math.PI / 2;
        scene.add(ground);

        const dioBase = new THREE.Mesh(new THREE.BoxGeometry(130.4, 2.5, 90.4), new THREE.MeshLambertMaterial({ color: dark ? 0x29352a : 0x8b6545 }));
        dioBase.position.y = -1.26;
        scene.add(dioBase);
        const rim = new THREE.Mesh(new THREE.BoxGeometry(130.48, 0.16, 90.48), new THREE.MeshLambertMaterial({ color: dark ? 0x315b2b : 0x368b2b }));
        rim.position.y = -0.13;
        scene.add(rim);

        const LM = c => new THREE.MeshLambertMaterial({ color: c });

        // ============================================================
        // TOOLTIP SYSTEM
        // ============================================================
        const labelSprites = [];

        function makeTooltipSprite(text, color = '#16a34a') {
            const cv = document.createElement('canvas');
            const ctx = cv.getContext('2d');

            const dpr = 2;
            const padX = 32;
            cv.width = 512 * dpr;
            cv.height = 160 * dpr;
            ctx.scale(dpr, dpr);

            ctx.font = 'bold 40px system-ui, -apple-system, sans-serif';
            const textW = Math.min(ctx.measureText(text).width, 440);
            const boxW = textW + padX * 2;
            const boxH = 80;
            const cx = 256, cy = 80;

            ctx.shadowColor = 'rgba(0,0,0,0.35)';
            ctx.shadowBlur = 20;
            ctx.shadowOffsetY = 6;

            const rx = cx - boxW / 2, ry = cy - boxH / 2;
            const r = 22;
            ctx.fillStyle = 'rgba(255,255,255,0.97)';
            ctx.beginPath();
            ctx.moveTo(rx + r, ry);
            ctx.lineTo(rx + boxW - r, ry);
            ctx.quadraticCurveTo(rx + boxW, ry, rx + boxW, ry + r);
            ctx.lineTo(rx + boxW, ry + boxH - r);
            ctx.quadraticCurveTo(rx + boxW, ry + boxH, rx + boxW - r, ry + boxH);
            ctx.lineTo(rx + r, ry + boxH);
            ctx.quadraticCurveTo(rx, ry + boxH, rx, ry + boxH - r);
            ctx.lineTo(rx, ry + r);
            ctx.quadraticCurveTo(rx, ry, rx + r, ry);
            ctx.closePath();
            ctx.fill();

            ctx.shadowColor = 'transparent';
            ctx.lineWidth = 4;
            ctx.strokeStyle = color;
            ctx.stroke();

            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 40px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(text, cx, cy + 2, 440);

            const tex = new THREE.CanvasTexture(cv);
            tex.minFilter = THREE.LinearFilter;
            tex.magFilter = THREE.LinearFilter;
            tex.anisotropy = 4;
            tex.needsUpdate = true;

            const mat = new THREE.SpriteMaterial({
                map: tex,
                transparent: true,
                depthTest: false,
                depthWrite: false,
                sizeAttenuation: true,
            });
            const sprite = new THREE.Sprite(mat);
            sprite.renderOrder = 9999;
            return sprite;
        }

        function addTooltip(name, worldX, worldY, worldZ, color) {
            const sprite = makeTooltipSprite(name, color);
            // Base scale: gedung biasa
            const baseScale = { x: 6.5, y: 2.0 };
            sprite.scale.set(baseScale.x, baseScale.y, 1);
            sprite.position.set(worldX, worldY, worldZ);
            sprite.userData = { baseScale, name };
            scene.add(sprite);
            labelSprites.push(sprite);
            return sprite;
        }

        // Wall texture cache
        const wtCache = new Map();
        function getWallCanvas(col, fl) {
            const key = col + '_' + fl;
            if (wtCache.has(key)) return wtCache.get(key);
            const cv = document.createElement('canvas');
            cv.width = 256; cv.height = 256;
            const g = cv.getContext('2d');
            const colHex = '#' + new THREE.Color(col).getHexString();

            g.fillStyle = '#78ad4e'; g.fillRect(0, 0, 256, 256);
            g.strokeStyle = 'rgba(0,0,0,0.04)'; g.lineWidth = 1;
            for (let y = 18; y < 232; y += 14) {
                g.beginPath(); g.moveTo(0, y); g.lineTo(256, y); g.stroke();
            }
            g.fillStyle = '#344454'; g.fillRect(0, 230, 256, 26);
            g.fillStyle = '#e2e8f0'; g.fillRect(0, 227, 256, 3);
            g.fillStyle = '#f8faf7'; g.fillRect(0, 0, 256, 18);
            g.fillStyle = 'rgba(0,0,0,0.14)'; g.fillRect(0, 18, 256, 3);

            if (fl === 1) {
                g.fillStyle = colHex; g.fillRect(0, 21, 256, 10);
                g.fillStyle = 'rgba(255,255,255,0.7)'; g.fillRect(0, 21, 256, 2);
            } else {
                g.fillStyle = colHex; g.fillRect(0, 21, 256, 8);
                g.fillStyle = '#f8faf7'; g.fillRect(0, 120, 256, 16);
                g.fillStyle = colHex; g.fillRect(0, 124, 256, 8);
                g.fillStyle = 'rgba(0,0,0,0.12)'; g.fillRect(0, 136, 256, 3);
            }

            g.fillStyle = '#f8faf7';
            g.fillRect(0, 0, 14, 230); g.fillRect(242, 0, 14, 230);
            g.fillStyle = 'rgba(0,0,0,0.08)';
            g.fillRect(14, 0, 2, 230); g.fillRect(240, 0, 2, 230);

            function drawWindow(cx, y, w, h, isTall) {
                const x = cx - w / 2;
                g.fillStyle = '#1e293b'; g.fillRect(x, y, w, h);
                g.fillStyle = '#f8faf7'; g.fillRect(x - 4, y + h - 2, w + 8, 6);
                g.fillStyle = 'rgba(0,0,0,0.16)'; g.fillRect(x - 4, y + h + 4, w + 8, 3);
                const gw = w - 8, gh = h - 10;
                const gr = g.createLinearGradient(x + 4, y + 4, x + 4, y + 4 + gh);
                gr.addColorStop(0, '#7dd3fc'); gr.addColorStop(1, '#0284c7');
                g.fillStyle = gr; g.fillRect(x + 4, y + 4, gw, gh);
                g.save(); g.beginPath(); g.rect(x + 4, y + 4, gw, gh); g.clip();
                g.fillStyle = 'rgba(255,255,255,0.65)';
                g.beginPath();
                g.moveTo(x + 4, y + 4 + gh * 0.35); g.lineTo(x + 4 + gw * 0.5, y + 4);
                g.lineTo(x + 4 + gw * 0.8, y + 4); g.lineTo(x + 4, y + 4 + gh * 0.65);
                g.closePath(); g.fill();
                g.restore();
                g.fillStyle = '#1e293b';
                g.fillRect(x + 4 + gw / 2 - 1.5, y + 4, 3, gh);
                if (isTall) {
                    g.fillRect(x + 4, y + 4 + gh * 0.33 - 1.5, gw, 3);
                    g.fillRect(x + 4, y + 4 + gh * 0.66 - 1.5, gw, 3);
                } else {
                    g.fillRect(x + 4, y + 4 + gh / 2 - 1.5, gw, 3);
                }
            }

            if (fl === 2) {
                drawWindow(64, 48, 46, 52, false); drawWindow(192, 48, 46, 52, false);
                drawWindow(64, 154, 46, 52, false); drawWindow(192, 154, 46, 52, false);
            } else {
                drawWindow(64, 62, 46, 134, true); drawWindow(192, 62, 46, 134, true);
            }

            wtCache.set(key, cv);
            return cv;
        }

        function wt(col, fl, len) {
            const cv = getWallCanvas(col, fl);
            const t = new THREE.CanvasTexture(cv);
            t.wrapS = t.wrapT = THREE.RepeatWrapping;
            t.repeat.set(Math.max(1, Math.round(len / 2.4)), 1);
            return new THREE.MeshLambertMaterial({ map: t });
        }

        function makePitchedRoof(W, D, rh, roofMat) {
            const g = new THREE.Group();
            const ov = 0.24;
            const w2 = (W + ov * 2) / 2, d2 = (D + ov * 2) / 2;
            const isX = W >= D;
            const rl = isX ? Math.max(0.2, (W + ov * 2) - (D + ov * 2) * 0.75) : Math.max(0.2, (D + ov * 2) - (W + ov * 2) * 0.75);
            const r2 = rl / 2;
            let verts, indices;
            if (isX) {
                verts = new Float32Array([-w2,0,-d2, w2,0,-d2, w2,0,d2, -w2,0,d2, -r2,rh,0, r2,rh,0]);
                indices = [3,2,5, 3,5,4, 1,0,4, 1,4,5, 0,3,4, 2,1,5, 0,1,2, 0,2,3];
            } else {
                verts = new Float32Array([-w2,0,-d2, w2,0,-d2, w2,0,d2, -w2,0,d2, 0,rh,-r2, 0,rh,r2]);
                indices = [2,1,4, 2,4,5, 0,3,5, 0,5,4, 1,0,4, 3,2,5, 0,1,2, 0,2,3];
            }
            const geo = new THREE.BufferGeometry();
            geo.setAttribute('position', new THREE.BufferAttribute(verts, 3));
            geo.setIndex(indices); geo.computeVertexNormals();
            const roofMesh = new THREE.Mesh(geo, roofMat);
            roofMesh.castShadow = !isLowEnd; roofMesh.receiveShadow = true;
            g.add(roofMesh);
            const capGeo = isX ? new THREE.BoxGeometry(rl + 0.12, 0.08, 0.14) : new THREE.BoxGeometry(0.14, 0.08, rl + 0.12);
            const capMesh = new THREE.Mesh(capGeo, LM(0x633b31));
            capMesh.position.y = rh + 0.03;
            g.add(capMesh);
            return g;
        }

        const C = { y: 0xf2b91b, b: 0x4388d8, c: 0xe8dfc8, o: 0xe97924, g: 0x8d969f, m: 0x198a45 };
        const DECORATIVE_COLOR = 0x8a8a8a;

        const pk = [];
        const roomsRegistry = [];
        const stairsRegistry = [];

        function resolveRoom(name) {
            const dbId = DB_MAPPING[name] ?? AREA_MAPPING[name];
            if (dbId == null) return null;
            return roomById.get(dbId) || null;
        }
        function isDecorative(name) {
            return (DB_MAPPING[name] ?? AREA_MAPPING[name]) == null;
        }

        function conditionColor(room) {
            if (!room) return '#94a3b8';
            const cond = typeof room.condition === 'object' ? room.condition.value : room.condition;
            return cond === 'baik' ? '#16a34a' : cond === 'rusak_ringan' ? '#f59e0b' : cond === 'rusak_berat' ? '#ef4444' : '#94a3b8';
        }

        function StairForRow(x, y, w, h, side = 'left') {
            const W = w * S * 0.93 - 0.025, D = h * S * 0.965 - 0.025;
            const baseUpper = 2.14, steps = 8, stepRise = baseUpper / steps;
            const stepRun = 0.22, stairW = Math.min(1.05, W * 0.085);
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);
            const stairMat = LM(0x9a9a94), railMat = LM(0x5d5147);
            const stairX = side === 'right' ? W / 2 - stairW / 2 - 0.06 : -W / 2 + stairW / 2 + 0.06;
            for (let i = 0; i < steps; i++) {
                const step = new THREE.Mesh(new THREE.BoxGeometry(stairW, stepRise * (i + 1), stepRun), stairMat);
                step.position.set(stairX, stepRise * (i + 1) / 2, D / 2 + stepRun * (steps - i - 0.5));
                step.castShadow = step.receiveShadow = !isLowEnd;
                g.add(step);
            }
            [-1, 1].forEach(s => {
                const rail = new THREE.Mesh(new THREE.BoxGeometry(0.06, baseUpper, steps * stepRun), railMat);
                rail.position.set(stairX + s * (stairW / 2 + 0.04), baseUpper / 2, D / 2 + steps * stepRun / 2);
                g.add(rail);
            });
            stairsRegistry.push({ x: g.position.x, z: g.position.z, name: `Tangga`, connectsFloor: [1, 2] });
            scene.add(g);
        }

        function StairAtPoint(x, y, h = 52) {
            const D = h * S * 0.985 - 0.025, baseUpper = 2.14, steps = 8;
            const stepRise = baseUpper / steps, stepRun = 0.22, stairW = 0.82;
            const g = new THREE.Group();
            g.position.set((x - CX) * S, 0, (y + h / 2 - CY) * S);
            const stairMat = LM(0x9a9a94), railMat = LM(0x5d5147);
            for (let i = 0; i < steps; i++) {
                const step = new THREE.Mesh(new THREE.BoxGeometry(stairW, stepRise * (i + 1), stepRun), stairMat);
                step.position.set(0, stepRise * (i + 1) / 2, D / 2 + stepRun * (steps - i - 0.5));
                step.castShadow = step.receiveShadow = !isLowEnd;
                g.add(step);
            }
            [-1, 1].forEach(s => {
                const rail = new THREE.Mesh(new THREE.BoxGeometry(0.06, baseUpper, steps * stepRun), railMat);
                rail.position.set(s * (stairW / 2 + 0.04), baseUpper / 2, D / 2 + steps * stepRun / 2);
                g.add(rail);
            });
            stairsRegistry.push({ x: g.position.x, z: g.position.z, name: 'Tangga', connectsFloor: [1, 2] });
            scene.add(g);
        }

        function B(t, x, y, w, h, k, ht) {
            const dbId = DB_MAPPING[t];
            const deco = (dbId == null);
            const room = deco ? null : (roomById.get(dbId) || null);

            const W = w * S * 0.90 - 0.12, D = h * S * 0.90 - 0.12, fl = ht >= 3.8 ? 2 : 1;
            const col = deco ? DECORATIVE_COLOR : C[k];
            const roofClay = LM(0xa9432c);
            const side = wt(col, fl, D), front = wt(col, fl, W);
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);

            let m, upperFloorMesh = null;
            if (fl === 2) {
                const floorH = ht / 2;
                const floorSide = wt(col, 1, D), floorFront = wt(col, 1, W);
                const slabMat = LM(0xc7c8c4);
                m = new THREE.Mesh(new THREE.BoxGeometry(W, floorH, D), [floorSide, floorSide, slabMat, slabMat, floorFront, floorFront]);
                m.position.y = floorH / 2;
                m.castShadow = m.receiveShadow = !isLowEnd;
                g.add(m);

                upperFloorMesh = new THREE.Mesh(new THREE.BoxGeometry(W, floorH, D), [floorSide, floorSide, slabMat, slabMat, floorFront, floorFront]);
                upperFloorMesh.position.y = floorH + floorH / 2;
                upperFloorMesh.castShadow = upperFloorMesh.receiveShadow = !isLowEnd;
                g.add(upperFloorMesh);

                const floorBand = new THREE.Mesh(new THREE.BoxGeometry(W + 0.10, 0.10, D + 0.10), LM(0xe0d7c2));
                floorBand.position.y = floorH;
                g.add(floorBand);
            } else {
                m = new THREE.Mesh(new THREE.BoxGeometry(W, ht, D), [side, side, roofClay, roofClay, front, front]);
                m.position.y = ht / 2;
                m.castShadow = m.receiveShadow = !isLowEnd;
                g.add(m);
            }

            const sl = new THREE.Mesh(new THREE.BoxGeometry(W + 0.32, 0.16, D + 0.32), LM(0x70443a));
            sl.position.y = ht + 0.08;
            sl.castShadow = !isLowEnd;
            g.add(sl);

            let tooltipY = ht + 1.4;

            if (k === 'c' || (k === 'o' && W > 5)) {
                const gg = new THREE.CylinderGeometry(1, 1, 1, 3);
                gg.rotateZ(Math.PI / 2); gg.rotateX(-Math.PI / 2);
                const r = new THREE.Mesh(gg, LM(k === 'c' ? 0x526477 : 0xa9432c));
                r.scale.set(W + 0.35, 1.0, (D + 0.35) / 1.732);
                r.position.y = ht + 0.65;
                r.castShadow = !isLowEnd;
                g.add(r);
                tooltipY = ht + 1.7;
            } else if (k === 'm') {
                const drum = new THREE.Mesh(new THREE.CylinderGeometry(2.3, 2.3, 0.6, 16), LM(0xfdfbf7));
                drum.position.y = ht + 0.3; drum.castShadow = !isLowEnd; g.add(drum);
                const dm = new THREE.Mesh(new THREE.SphereGeometry(2.3, 24, 16, 0, Math.PI * 2, 0, Math.PI / 2), LM(0x16a34a));
                dm.position.y = ht + 0.6; dm.castShadow = !isLowEnd; g.add(dm);
                const fin = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 0.7, 8), LM(0xf59e0b));
                fin.position.y = ht + 3.0; g.add(fin);
                const fb = new THREE.Mesh(new THREE.SphereGeometry(0.18, 12, 12), LM(0xf59e0b));
                fb.position.y = ht + 3.35; g.add(fb);
                [-1, 1].forEach(s => {
                    const mn = new THREE.Mesh(new THREE.CylinderGeometry(0.28, 0.38, 6.5, 12), LM(0xfdfbf7));
                    mn.position.set(s * (W / 2 - 0.5), 3.25, D / 2 - 0.5);
                    mn.castShadow = !isLowEnd; g.add(mn);
                    const balc = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.44, 0.22, 12), LM(0x16a34a));
                    balc.position.set(mn.position.x, 6.4, mn.position.z); g.add(balc);
                    const tp = new THREE.Mesh(new THREE.ConeGeometry(0.42, 1.1, 12), LM(0x16a34a));
                    tp.position.set(mn.position.x, 7.05, mn.position.z); tp.castShadow = !isLowEnd; g.add(tp);
                    const sp = new THREE.Mesh(new THREE.SphereGeometry(0.09, 8, 8), LM(0xf59e0b));
                    sp.position.set(mn.position.x, 7.65, mn.position.z); g.add(sp);
                });
                tooltipY = ht + 4.5;
            } else {
                const rh = Math.min(0.85, Math.min(W, D) * 0.28);
                const pr = makePitchedRoof(W, D, rh, roofClay);
                pr.position.y = ht + 0.12;
                g.add(pr);
                tooltipY = ht + 1.1;
            }

            if (W < 18 && k !== 'm') {
                const dr = new THREE.Mesh(new THREE.BoxGeometry(0.95, 1.5, 0.08), LM(0x3e2c26));
                dr.position.set(0, 0.75, D / 2 + 0.03); g.add(dr);
                const drF = new THREE.Mesh(new THREE.BoxGeometry(1.05, 1.6, 0.05), LM(0x1e293b));
                drF.position.set(0, 0.8, D / 2 + 0.015); g.add(drF);
                if (W >= 3.2) {
                    const can = new THREE.Mesh(new THREE.BoxGeometry(1.6, 0.14, 0.7), roofClay);
                    can.position.set(0, 1.75, D / 2 + 0.35); can.castShadow = !isLowEnd; g.add(can);
                }
            }

            const roomData = {
                name: t, x: g.position.x, z: g.position.z, W, D, ht,
                room, isDecorative: deco, dbId: dbId ?? null,
                tooltipY,
            };
            m.userData.room = roomData;
            pk.push(m);
            if (upperFloorMesh) {
                upperFloorMesh.userData.room = roomData;
                pk.push(upperFloorMesh);
            }
            roomsRegistry.push(roomData);

            const tipColor = deco ? '#94a3b8' : conditionColor(room);
            const tooltipSprite = addTooltip(t, g.position.x, tooltipY, g.position.z, tipColor);
            tooltipSprite.userData.roomRef = roomData;

            scene.add(g);
        }

        function BStackedClassroomPair(lowerName, upperName, x, y, w, h, lowerK = 'y', upperK = 'b') {
            const W = w * S * 0.93 - 0.025, D = h * S * 0.965 - 0.025;
            const lowerH = 2, slabH = 0.14, upperH = 2, baseUpper = lowerH + slabH;
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);
            const roofClay = LM(0xa9432c);
            const lowerCol = C[lowerK], upperCol = C[upperK];
            const lowerSide = wt(lowerCol, 1, D), lowerFront = wt(lowerCol, 1, W);
            const upperSide = wt(upperCol, 1, D), upperFront = wt(upperCol, 1, W);

            const lowerRoom = {
                name: lowerName, x: g.position.x, z: g.position.z, W, D, ht: lowerH,
                room: resolveRoom(lowerName), isDecorative: isDecorative(lowerName),
            };
            const lower = new THREE.Mesh(new THREE.BoxGeometry(W, lowerH, D), [lowerSide, lowerSide, LM(0xc7c8c4), LM(0xc7c8c4), lowerFront, lowerFront]);
            lower.position.y = lowerH / 2;
            lower.castShadow = lower.receiveShadow = !isLowEnd;
            lower.userData.room = lowerRoom;
            g.add(lower);
            pk.push(lower);
            roomsRegistry.push(lowerRoom);

            const slab = new THREE.Mesh(new THREE.BoxGeometry(W + 0.08, slabH, D + 0.08), LM(0xd6d3d1));
            slab.position.y = lowerH + slabH / 2;
            g.add(slab);

            const upperRoom = {
                name: upperName, x: g.position.x, z: g.position.z, W, D, ht: upperH,
                room: resolveRoom(upperName), isDecorative: isDecorative(upperName),
            };
            const upper = new THREE.Mesh(new THREE.BoxGeometry(W, upperH, D), [upperSide, upperSide, LM(0xc7c8c4), LM(0xc7c8c4), upperFront, upperFront]);
            upper.position.y = baseUpper + upperH / 2;
            upper.castShadow = upper.receiveShadow = !isLowEnd;
            upper.userData.room = upperRoom;
            g.add(upper);
            pk.push(upper);
            roomsRegistry.push(upperRoom);

            const addDoor = (levelY) => {
                const frame = new THREE.Mesh(new THREE.BoxGeometry(1.02, 1.56, 0.07), LM(0x1e293b));
                frame.position.set(0, levelY + 0.78, D / 2 + 0.035);
                g.add(frame);
                const door = new THREE.Mesh(new THREE.BoxGeometry(0.84, 1.38, 0.08), LM(0x6b422f));
                door.position.set(0, levelY + 0.69, D / 2 + 0.08);
                g.add(door);
            };
            addDoor(0);
            addDoor(baseUpper);

            const fascia = new THREE.Mesh(new THREE.BoxGeometry(W + 0.32, 0.16, D + 0.32), LM(0x70443a));
            fascia.position.y = baseUpper + upperH + 0.08;
            g.add(fascia);
            const rh = Math.min(0.85, Math.min(W, D) * 0.28);
            const roof = makePitchedRoof(W, D, rh, roofClay);
            roof.position.y = baseUpper + upperH + 0.12;
            g.add(roof);

            const balconyDepth = 0.85, balconyY = baseUpper + 0.08;
            const balcony = new THREE.Mesh(new THREE.BoxGeometry(W + 0.06, 0.12, balconyDepth), LM(0xb8b8b0));
            balcony.position.set(0, balconyY, D / 2 + balconyDepth / 2);
            balcony.castShadow = !isLowEnd;
            g.add(balcony);

            const railMat = LM(0x5d5147);
            const rail = new THREE.Mesh(new THREE.BoxGeometry(W, 0.07, 0.07), railMat);
            rail.position.set(0, baseUpper + 0.62, D / 2 + balconyDepth - 0.08);
            g.add(rail);
            [-1, 1].forEach(edge => {
                const post = new THREE.Mesh(new THREE.BoxGeometry(0.07, 0.62, 0.07), railMat);
                post.position.set(edge * (W / 2 - 0.06), baseUpper + 0.39, D / 2 + balconyDepth - 0.08);
                g.add(post);
            });

            scene.add(g);

            if (lowerName && lowerName.trim() !== '') {
                const tipL = addTooltip(lowerName, g.position.x, lowerH + 0.8, g.position.z, isDecorative(lowerName) ? '#94a3b8' : conditionColor(lowerRoom.room));
                tipL.userData.roomRef = lowerRoom;
            }
            if (upperName && upperName.trim() !== '') {
                const tipU = addTooltip(upperName, g.position.x, baseUpper + upperH + 1.0, g.position.z, isDecorative(upperName) ? '#94a3b8' : conditionColor(upperRoom.room));
                tipU.userData.roomRef = upperRoom;
            }
        }

        function BUpperWingTwoRooms(nameA, nameB, x, y, w, h, k = 'b') {
            const W = w * S * 0.93 - 0.025, D = h * S * 0.965 - 0.025;
            const slabH = 0.14, baseUpper = 2.14, upperH = 2, halfW = W / 2;
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);
            const slabMat = LM(0xd6d3d1);
            const sideMat = wt(C[k], 1, D), frontMat = wt(C[k], 1, halfW);
            const roofClay = LM(0xa9432c);

            for (const px of [-W / 2 + 0.12, W / 2 - 0.12])
                for (const pz of [-D / 2 + 0.12, D / 2 - 0.12]) {
                    const col = new THREE.Mesh(new THREE.BoxGeometry(0.16, 2, 0.16), LM(0xc7c8c4));
                    col.position.set(px, 1, pz);
                    col.castShadow = !isLowEnd;
                    g.add(col);
                }

            const slab = new THREE.Mesh(new THREE.BoxGeometry(W + 0.08, slabH, D + 0.08), slabMat);
            slab.position.y = 2 + slabH / 2;
            slab.castShadow = !isLowEnd;
            g.add(slab);

            const makeUpper = (name, offset) => {
                const mesh = new THREE.Mesh(new THREE.BoxGeometry(halfW, upperH, D), [sideMat, sideMat, LM(0xc7c8c4), LM(0xc7c8c4), frontMat, frontMat]);
                mesh.position.set(offset, baseUpper + upperH / 2, 0);
                mesh.castShadow = mesh.receiveShadow = !isLowEnd;
                g.add(mesh);
                const room = {
                    name, x: g.position.x + offset, z: g.position.z, W: halfW, D,
                    ht: upperH, room: resolveRoom(name), isDecorative: isDecorative(name),
                };
                mesh.userData.room = room;
                pk.push(mesh);
                roomsRegistry.push(room);
                return room;
            };
            const rA = makeUpper(nameA, -halfW / 2);
            const rB = makeUpper(nameB, halfW / 2);

            const fascia = new THREE.Mesh(new THREE.BoxGeometry(W + 0.32, 0.16, D + 0.32), LM(0x70443a));
            fascia.position.y = baseUpper + upperH + 0.08;
            g.add(fascia);
            const roof = makePitchedRoof(W, D, Math.min(0.85, Math.min(W, D) * 0.28), roofClay);
            roof.position.y = baseUpper + upperH + 0.12;
            g.add(roof);
            scene.add(g);

            const tipY = baseUpper + upperH + 1.0;
            const tipA = addTooltip(nameA, g.position.x - halfW / 2, tipY, g.position.z, isDecorative(nameA) ? '#94a3b8' : conditionColor(rA.room));
            tipA.userData.roomRef = rA;
            const tipB = addTooltip(nameB, g.position.x + halfW / 2, tipY, g.position.z, isDecorative(nameB) ? '#94a3b8' : conditionColor(rB.room));
            tipB.userData.roomRef = rB;
        }

        function P(t, x, y, w, h, col) {
            const W = w * S, D = h * S;
            let groundCol = col;
            const groundY = 0.015;
            if (t === 'Lap. Upacara') groundCol = 0x278447;
            else if (t === 'Tempat Parkir') groundCol = 0x334155;
            else if (t.includes('Basket') || t.includes('Voli') || t.includes('Timur')) groundCol = 0x15803d;
            else if (t.includes('Lap. Utama')) groundCol = 0xb91c1c;

            const m = new THREE.Mesh(new THREE.PlaneGeometry(W, D), new THREE.MeshBasicMaterial({ color: groundCol, side: THREE.DoubleSide }));
            m.rotation.x = -Math.PI / 2;
            m.position.set((x + w / 2 - CX) * S, groundY, (y + h / 2 - CY) * S);
            scene.add(m);

            if (t === 'Tempat Parkir') {
                const stalls = Math.floor(W / 2.2);
                for (let i = 0; i <= stalls; i++) {
                    const sx = m.position.x - W / 2 + 0.8 + i * 2.2;
                    if (sx < m.position.x + W / 2 - 0.5) {
                        const ln = new THREE.Mesh(new THREE.PlaneGeometry(0.08, D * 0.75), new THREE.MeshBasicMaterial({ color: 0xffffff }));
                        ln.rotation.x = -Math.PI / 2;
                        ln.position.set(sx, 0.02, m.position.z);
                        scene.add(ln);
                    }
                }
            }

            if (t.includes('Basket') || t.includes('Voli')) {
                const courtCol = t.includes('Basket') ? 0xc2410c : 0x2563a6;
                const innerCourt = new THREE.Mesh(new THREE.PlaneGeometry(Math.max(1, W - 1.0), Math.max(1, D - 1.0)), LM(courtCol));
                innerCourt.rotation.x = -Math.PI / 2;
                innerCourt.position.set(m.position.x, 0.018, m.position.z);
                scene.add(innerCourt);

                const outline = new THREE.Mesh(
                    new THREE.PlaneGeometry(Math.max(1, W - 1.6), Math.max(1, D - 1.6)),
                    new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true })
                );
                outline.rotation.x = -Math.PI / 2;
                outline.position.set(m.position.x, 0.021, m.position.z);
                scene.add(outline);

                if (t.includes('Basket')) {
                    [-1, 1].forEach(side => {
                        const hp = new THREE.Mesh(new THREE.CylinderGeometry(0.035, 0.035, 1.9, 8), LM(0x475569));
                        hp.position.set(m.position.x + side * (W / 2 - 0.5), 0.95, m.position.z);
                        scene.add(hp);
                        const bb = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.48, 0.65), LM(0xffffff));
                        bb.position.set(m.position.x + side * (W / 2 - 0.52 - side * 0.16), 1.55, m.position.z);
                        scene.add(bb);
                        const rim = new THREE.Mesh(new THREE.TorusGeometry(0.13, 0.025, 8, 16), LM(0xf97316));
                        rim.rotation.x = Math.PI / 2;
                        rim.position.set(m.position.x + side * (W / 2 - 0.75), 1.38, m.position.z);
                        scene.add(rim);
                    });
                } else {
                    const poleX1 = m.position.x - W / 2 + 0.7, poleX2 = m.position.x + W / 2 - 0.7;
                    [poleX1, poleX2].forEach(px => {
                        const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 1.65, 8), LM(0x334155));
                        pole.position.set(px, 0.825, m.position.z);
                        scene.add(pole);
                    });
                    const net = new THREE.Mesh(
                        new THREE.PlaneGeometry(W - 1.4, 0.72),
                        new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true, transparent: true, opacity: 0.8, side: THREE.DoubleSide })
                    );
                    net.position.set(m.position.x, 1.3, m.position.z);
                    scene.add(net);
                }
            }

            if (t.includes('Lap. Utama')) {
                const pW = W - 2.6, pD = D - 2.6;
                const pitch = new THREE.Mesh(new THREE.PlaneGeometry(pW, pD), LM(0x15803d));
                pitch.rotation.x = -Math.PI / 2;
                pitch.position.set(m.position.x, 0.018, m.position.z);
                scene.add(pitch);

                const stripes = 7, sH = pD / stripes;
                for (let i = 0; i < stripes; i += 2) {
                    const st = new THREE.Mesh(new THREE.PlaneGeometry(pW, sH), LM(0x16a34a));
                    st.rotation.x = -Math.PI / 2;
                    st.position.set(m.position.x, 0.019, m.position.z - pD / 2 + sH * (i + 0.5));
                    scene.add(st);
                }

                const lineMat = new THREE.MeshBasicMaterial({ color: 0xffffff });
                const cLine = new THREE.Mesh(new THREE.PlaneGeometry(pW - 0.6, 0.08), lineMat);
                cLine.rotation.x = -Math.PI / 2;
                cLine.position.set(m.position.x, 0.021, m.position.z);
                scene.add(cLine);
                const cCirc = new THREE.Mesh(new THREE.RingGeometry(1.8, 1.9, 32), lineMat);
                cCirc.rotation.x = -Math.PI / 2;
                cCirc.position.set(m.position.x, 0.021, m.position.z);
                scene.add(cCirc);
                const cSpot = new THREE.Mesh(new THREE.CircleGeometry(0.18, 16), lineMat);
                cSpot.rotation.x = -Math.PI / 2;
                cSpot.position.set(m.position.x, 0.022, m.position.z);
                scene.add(cSpot);

                [-1, 1].forEach(dir => {
                    const bZ = m.position.z + dir * (pD / 2 - 1.9);
                    const pBox = new THREE.Mesh(new THREE.PlaneGeometry(pW * 0.5, 3.6), new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true }));
                    pBox.rotation.x = -Math.PI / 2;
                    pBox.position.set(m.position.x, 0.021, bZ);
                    scene.add(pBox);

                    const gZ = m.position.z + dir * (pD / 2 - 0.3);
                    const postG = new THREE.Group();
                    postG.position.set(m.position.x, 0, gZ);
                    [-1.4, 1.4].forEach(px => {
                        const post = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 1.4, 8), LM(0xf8fafc));
                        post.position.set(px, 0.7, 0);
                        post.castShadow = !isLowEnd;
                        postG.add(post);
                    });
                    const cBar = new THREE.Mesh(new THREE.BoxGeometry(2.88, 0.08, 0.08), LM(0xf8fafc));
                    cBar.position.set(0, 1.4, 0);
                    postG.add(cBar);
                    scene.add(postG);
                });
            }

            const room = resolveRoom(t);
            const deco = isDecorative(t);
            const roomData = {
                name: t, x: m.position.x, z: m.position.z, W, D, ht: 0,
                room, isDecorative: deco,
            };
            m.userData.room = roomData;
            pk.push(m);
            roomsRegistry.push(roomData);

            if (t && t.trim() !== '') {
                const tipColor = deco ? '#94a3b8' : conditionColor(room);
                const tipY = 1.8;
                const tooltipSprite = addTooltip(t, m.position.x, tipY, m.position.z, tipColor);
                tooltipSprite.userData.roomRef = roomData;
                tooltipSprite.userData.baseScale = { x: 7.5, y: 2.3 };
                tooltipSprite.scale.set(7.5, 2.3, 1);
            }
        }

        function F(x, y, w, h) {
            const W = w * S, D = h * S;
            const m = new THREE.Mesh(new THREE.PlaneGeometry(W, D), LM(0x667788));
            m.rotation.x = -Math.PI / 2;
            m.position.set((x + w / 2 - CX) * S, 0.012, (y + h / 2 - CY) * S);
            m.receiveShadow = !isLowEnd;
            scene.add(m);
        }

        const treeColors = [0x22c55e, 0x16a34a, 0x15803d, 0x166534, 0x2e7d32, 0x1b5e20];
        function tree(px, py, s) {
            const g = new THREE.Group();
            g.position.set((px - CX) * S, 0, (py - CY) * S);
            const tr = new THREE.Mesh(new THREE.CylinderGeometry(0.14, 0.22, 1.5, 8), LM(0x543621));
            tr.position.y = 0.75; tr.castShadow = !isLowEnd; g.add(tr);
            const tc = treeColors[Math.abs(Math.round(px + py)) % treeColors.length];
            const mf = new THREE.Mesh(new THREE.IcosahedronGeometry(1.2, 1), LM(tc));
            mf.position.y = 2.1; mf.castShadow = !isLowEnd; g.add(mf);
            const p1 = new THREE.Mesh(new THREE.IcosahedronGeometry(0.8, 0), LM(tc));
            p1.position.set(0.4, 2.0, 0.3); p1.castShadow = !isLowEnd; g.add(p1);
            const p2 = new THREE.Mesh(new THREE.IcosahedronGeometry(0.75, 0), LM(tc));
            p2.position.set(-0.4, 1.9, -0.3); p2.castShadow = !isLowEnd; g.add(p2);
            const p3 = new THREE.Mesh(new THREE.IcosahedronGeometry(0.7, 0), LM(tc));
            p3.position.set(-0.15, 2.7, 0.1); p3.castShadow = !isLowEnd; g.add(p3);
            const bush = new THREE.Mesh(new THREE.DodecahedronGeometry(0.38, 0), LM(0x15803d));
            bush.position.set(0.35, 0.1, 0.2); bush.castShadow = !isLowEnd; g.add(bush);
            g.scale.setScalar(s);
            scene.add(g);
        }

        // =====================================================
        // BUILD WORLD
        // =====================================================
        P('', 483, 186, 390, 124, 0x6fb36b);
        P('Lap. Upacara', 570, 532, 298, 112, 0xcfe8c4);
        P('Tempat Parkir', 32, 788, 396, 68, 0xc3d5ee);
        P('Tempat Parkir', 1008, 695, 266, 145, 0xc3d5ee);
        P('Lap. Basket', 1288, 690, 88, 150, 0x9fc3ea);
        P('Lap. Voli', 1384, 690, 70, 150, 0x9fc3ea);
        P('Lap. Utama (Sepak Bola & Atletik)', 1190, 180, 340, 470, 0xb91c1c);

        B('Lab CAM & CNC', 48, 22, 250, 52, 'g', 2.6);
        B('Basecamp Organisasi', 300, 22, 190, 44, 'o', 2);
        BStackedClassroomPair('L. PPLG', 'Aula RPL', 70, 94, 364, 108, 'b', 'o');

        B('B. Otomotif', 70, 210, 362, 90, 'c', 3);
        B('B. Tekstil', 70, 325, 362, 108, 'c', 3);
        B('Masjid Al-Amanah', 68, 460, 360, 210, 'm', 3.2);
        B('B. Mesin', 70, 688, 418, 82, 'c', 3);
        B('R. Kesiswaan', 1458, 690, 78, 60, 'o', 2);

        (function () {
            const x = 480, y = 20, w = 268, h = 42;
            const W = w * S * 0.93 - 0.025, D = h * S * 0.965 - 0.025;
            const lowerH = 2, slabH = 0.14, upperH = 2, baseUpper = lowerH + slabH;
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);
            const roofClay = LM(0xa9432c);
            const lowSide = wt(C.o, 1, D), lowFront = wt(C.o, 1, W / 2);
            const upSide = wt(C.b, 1, D), upFront = wt(C.b, 1, W);

            const kL = { name: 'Kantin', x: g.position.x - W / 4, z: g.position.z, W: W / 2, D, ht: lowerH, room: resolveRoom('Kantin'), isDecorative: isDecorative('Kantin') };
            const kLm = new THREE.Mesh(new THREE.BoxGeometry(W / 2, lowerH, D), [lowSide, lowSide, LM(0xc7c8c4), LM(0xc7c8c4), lowFront, lowFront]);
            kLm.position.set(-W / 4, lowerH / 2, 0);
            kLm.castShadow = kLm.receiveShadow = !isLowEnd;
            kLm.userData.room = kL;
            g.add(kLm); pk.push(kLm); roomsRegistry.push(kL);

            const kR = { name: 'Kantin (sayap)', x: g.position.x + W / 4, z: g.position.z, W: W / 2, D, ht: lowerH, room: null, isDecorative: true };
            const kRm = new THREE.Mesh(new THREE.BoxGeometry(W / 2, lowerH, D), [lowSide, lowSide, LM(0xc7c8c4), LM(0xc7c8c4), lowFront, lowFront]);
            kRm.position.set(W / 4, lowerH / 2, 0);
            kRm.castShadow = kRm.receiveShadow = !isLowEnd;
            kRm.userData.room = kR;
            g.add(kRm); pk.push(kRm); roomsRegistry.push(kR);

            const slab = new THREE.Mesh(new THREE.BoxGeometry(W + 0.08, slabH, D + 0.08), LM(0xd6d3d1));
            slab.position.y = lowerH + slabH / 2;
            g.add(slab);

            const r30 = { name: 'R 30', x: g.position.x, z: g.position.z, W, D, ht: upperH, room: resolveRoom('R 30'), isDecorative: isDecorative('R 30') };
            const up = new THREE.Mesh(new THREE.BoxGeometry(W, upperH, D), [upSide, upSide, LM(0xc7c8c4), LM(0xc7c8c4), upFront, upFront]);
            up.position.y = baseUpper + upperH / 2;
            up.castShadow = up.receiveShadow = !isLowEnd;
            up.userData.room = r30;
            g.add(up); pk.push(up); roomsRegistry.push(r30);

            const fascia = new THREE.Mesh(new THREE.BoxGeometry(W + 0.32, 0.16, D + 0.32), LM(0x70443a));
            fascia.position.y = baseUpper + upperH + 0.08;
            g.add(fascia);
            const roof = makePitchedRoof(W, D, Math.min(0.85, Math.min(W, D) * 0.28), roofClay);
            roof.position.y = baseUpper + upperH + 0.12;
            g.add(roof);

            scene.add(g);

            const tipL = addTooltip('Kantin', g.position.x - W / 4, lowerH + 0.7, g.position.z, isDecorative('Kantin') ? '#94a3b8' : conditionColor(kL.room));
            tipL.userData.roomRef = kL;
            const tipR30 = addTooltip('R 30', g.position.x, baseUpper + upperH + 1.0, g.position.z, isDecorative('R 30') ? '#94a3b8' : conditionColor(r30.room));
            tipR30.userData.roomRef = r30;
        })();

        BUpperWingTwoRooms('R. Kesenian', 'Studio Musik', 748, 20, 100, 42, 'b');

        BStackedClassroomPair('R KI', 'R 29', 480, 127, 90, 52, 'y', 'b');
        BStackedClassroomPair('', 'R 28', 570, 127, 90, 52, 'y', 'b');
        BStackedClassroomPair('R 25', 'R 27', 660, 127, 90, 52, 'y', 'b');
        BStackedClassroomPair('R 24', 'R 26', 750, 127, 90, 52, 'y', 'b');

        const xs = [896, 984, 1070];
        [['R 4', 'R 5', 'R 6', 537, 'y'], ['R 1', 'R 2', 'R 3', 623, 'y']].forEach(r => {
            for (let i = 0; i < 3; i++) {
                B(r[i], xs[i], r[3], 76, 60, r[4], 2);
            }
        });

        BStackedClassroomPair('R 18', 'R 21', 896, 127, 76, 40, 'y', 'b');
        BStackedClassroomPair('R 19', 'R 22', 984, 127, 76, 40, 'y', 'b');
        BStackedClassroomPair('R 20', 'R 23', 1070, 127, 76, 40, 'y', 'b');
        BStackedClassroomPair('R 12', 'R 15', 896, 217, 76, 40, 'y', 'b');
        BStackedClassroomPair('R 13', 'R 16', 984, 217, 76, 40, 'y', 'b');
        BStackedClassroomPair('R 14', 'R 17', 1070, 217, 76, 40, 'y', 'b');

        [['R 9', 910], ['R 10', 998], ['R 11', 1080]].forEach(a => B(a[0], a[1], 345, 86, 60, 'y', 2));
        B('R 7', 982, 436, 84, 60, 'y', 2);
        B('R 8', 1066, 436, 84, 60, 'y', 2);
        B('R 31', 1180, 26, 100, 60, 'y', 2);
        B('R 32', 1282, 26, 100, 60, 'y', 2);
        B('R. Olah Sampah', 1400, 22, 144, 78, 'o', 1.6);

        B('Gudang', 450, 340, 78, 50, 'y', 2);
        B('R Sarpra', 450, 392, 78, 40, 'y', 2);
        B('R UKS', 450, 434, 78, 40, 'y', 2);
        B('Server', 450, 476, 78, 50, 'y', 2);
        B('Kopsis', 450, 556, 78, 46, 'o', 2);
        B('Panel', 450, 602, 78, 46, 'o', 2);

        B('M. Al-Amanah', 552, 340, 110, 64, 'g', 2.4);
        B('L. CAD CAM', 668, 340, 72, 64, 'y', 2);
        B('R. 33', 742, 340, 78, 64, 'y', 2);
        B('BK', 822, 340, 60, 64, 'y', 2);

        BStackedClassroomPair('R. Humas', 'R. Perpust', 552, 436, 95, 52, 'o', 'b');
        BStackedClassroomPair('R. Waka', 'R 34', 664, 436, 84, 52, 'o', 'b');

        (function () {
            const x = 752, y = 436, w = 130, h = 50;
            const W = w * S * 0.93 - 0.025, D = h * S * 0.965 - 0.025;
            const lowerH = 2, slabH = 0.14, upperH = 2, baseUpper = lowerH + slabH;
            const g = new THREE.Group();
            g.position.set((x + w / 2 - CX) * S, 0, (y + h / 2 - CY) * S);
            const roofClay = LM(0xa9432c);
            const lowSide = wt(C.o, 1, D), lowFront = wt(C.o, 1, W);
            const upSide = wt(C.b, 1, D), upFront = wt(C.b, 1, W / 2);

            const lowRoom = {
                name: 'R. Multimedia', x: g.position.x, z: g.position.z, W, D,
                ht: lowerH, room: resolveRoom('R. Multimedia'), isDecorative: isDecorative('R. Multimedia'),
            };
            const lowMesh = new THREE.Mesh(new THREE.BoxGeometry(W, lowerH, D), [lowSide, lowSide, LM(0xc7c8c4), LM(0xc7c8c4), lowFront, lowFront]);
            lowMesh.position.y = lowerH / 2;
            lowMesh.castShadow = lowMesh.receiveShadow = !isLowEnd;
            lowMesh.userData.room = lowRoom;
            g.add(lowMesh);
            pk.push(lowMesh);
            roomsRegistry.push(lowRoom);

            const slab = new THREE.Mesh(new THREE.BoxGeometry(W + 0.08, slabH, D + 0.08), LM(0xd6d3d1));
            slab.position.y = lowerH + slabH / 2;
            g.add(slab);

            const upperW = W / 2;
            const makeUpper = (name, offset) => {
                const mesh = new THREE.Mesh(new THREE.BoxGeometry(upperW, upperH, D), [upSide, upSide, LM(0xc7c8c4), LM(0xc7c8c4), upFront, upFront]);
                mesh.position.set(offset, baseUpper + upperH / 2, 0);
                mesh.castShadow = mesh.receiveShadow = !isLowEnd;
                g.add(mesh);
                const room = {
                    name, x: g.position.x + offset, z: g.position.z, W: upperW, D,
                    ht: upperH, room: resolveRoom(name), isDecorative: isDecorative(name),
                };
                mesh.userData.room = room;
                pk.push(mesh);
                roomsRegistry.push(room);
                return room;
            };
            const r35 = makeUpper('R 35', -upperW / 2);
            const r36 = makeUpper('R 36', upperW / 2);

            const fascia = new THREE.Mesh(new THREE.BoxGeometry(W + 0.32, 0.16, D + 0.32), LM(0x70443a));
            fascia.position.y = baseUpper + upperH + 0.08;
            g.add(fascia);
            const roof = makePitchedRoof(W, D, Math.min(0.85, Math.min(W, D) * 0.28), roofClay);
            roof.position.y = baseUpper + upperH + 0.12;
            g.add(roof);
            scene.add(g);

            const tipY = baseUpper + upperH + 1.0;
            const tip35 = addTooltip('R 35', g.position.x - upperW / 2, tipY, g.position.z, isDecorative('R 35') ? '#94a3b8' : conditionColor(r35.room));
            tip35.userData.roomRef = r35;
            const tip36 = addTooltip('R 36', g.position.x + upperW / 2, tipY, g.position.z, isDecorative('R 36') ? '#94a3b8' : conditionColor(r36.room));
            tip36.userData.roomRef = r36;
            const tipMM = addTooltip('R. Multimedia', g.position.x, lowerH + 0.7, g.position.z, isDecorative('R. Multimedia') ? '#94a3b8' : conditionColor(lowRoom.room));
            tipMM.userData.roomRef = lowRoom;
        })();

        B('LSP', 570, 662, 55, 40, 'o', 2);
        B('R KS', 626, 662, 68, 40, 'o', 2);
        B('R. TU', 570, 704, 122, 64, 'o', 2);
        B('R. Guru', 696, 662, 180, 106, 'o', 2);

        StairForRow(70, 94, 364, 108);
        StairForRow(748, 20, 100, 42, 'right');
        StairForRow(480, 127, 360, 52, 'right');
        StairForRow(896, 127, 250, 40);
        StairForRow(896, 217, 250, 40);
        StairAtPoint(655.5, 436, 52);

        F(540, 320, 370, 370); F(880, 55, 300, 650); F(1180, 110, 370, 50); F(20, 300, 430, 30);
        F(755, 690, 60, 175);

        [[520, 215], [600, 290], [690, 200], [780, 290], [850, 225], [500, 300], [520, 660], [870, 335], [880, 95], [1165, 240], [1165, 380], [1165, 540], [1545, 210], [1545, 330], [1545, 450], [1545, 570], [1545, 650], [440, 28], [60, 860], [480, 760], [1000, 860], [1560, 500]]
            .forEach((p, i) => tree(p[0], p[1], 0.95 + (i % 3) * 0.2));

        // Bendera
        (function () {
            const fx = (720 - CX) * S, fz = (590 - CY) * S;
            const fpBase = new THREE.Mesh(new THREE.CylinderGeometry(0.65, 0.85, 0.22, 16), LM(0xf1f5f9));
            fpBase.position.set(fx, 0.11, fz); scene.add(fpBase);
            const fp = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.07, 6, 12), LM(0xe2e8f0));
            fp.position.set(fx, 3, fz); fp.castShadow = !isLowEnd; scene.add(fp);
            const fpBall = new THREE.Mesh(new THREE.SphereGeometry(0.12, 10, 10), LM(0xf59e0b));
            fpBall.position.set(fx, 6.05, fz); scene.add(fpBall);
            const cv = document.createElement('canvas');
            cv.width = 128; cv.height = 80;
            const g = cv.getContext('2d');
            g.fillStyle = '#e11d48'; g.fillRect(0, 0, 128, 40);
            g.fillStyle = '#ffffff'; g.fillRect(0, 40, 128, 40);
            const tex = new THREE.CanvasTexture(cv);
            const flag = new THREE.Mesh(new THREE.PlaneGeometry(1.4, 0.88), new THREE.MeshLambertMaterial({ map: tex, side: THREE.DoubleSide }));
            flag.position.set(fx + 0.7, 5.5, fz); flag.castShadow = !isLowEnd;
            scene.add(flag);
        })();

        // =====================================================
        // CAMERA & INTERACTION
        // =====================================================
        let th = 0.6, ph = 0.95, rad = 75, auto = false;
        const tgt = new THREE.Vector3();
        const gt = new THREE.Vector3();
        let gr = 75, gp = 0.95, fly = false;
        const def = { th: 0.6, ph: 0.95, rad: 75 };

        function updCam() {
            cam.position.set(
                tgt.x + rad * Math.sin(ph) * Math.sin(th),
                tgt.y + rad * Math.cos(ph),
                tgt.z + rad * Math.sin(ph) * Math.cos(th)
            );
            cam.lookAt(tgt);
        }
        updCam();

        // ============================================================
        // UPDATE TOOLTIP SCALE — TOOLTIP MENGEcil SAAT ZOOM OUT
        // ============================================================
        const BASE_DIST = 75;          // jarak default kamera
        const MIN_SCALE = 0.35;        // batas minimum tooltip
        const MAX_SCALE = 1.35;        // batas maksimum tooltip

        function updateTooltipScale() {
            const ratio = BASE_DIST / Math.max(rad, 1);
            const scale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, ratio));

            labelSprites.forEach(sprite => {
                const bs = sprite.userData.baseScale || { x: 6.5, y: 2.0 };
                sprite.scale.set(bs.x * scale, bs.y * scale, 1);

                // Kalau di-hidden declutter, opacity tetap 0
                if (sprite.userData._hiddenByDeclutter) {
                    sprite.material.opacity = 0;
                } else {
                    sprite.material.opacity = Math.max(0.3, Math.min(1, scale));
                }
            });
        }

                // ============================================================
        // DECLUTTER TOOLTIPS — sembunyikan yang numpuk
        // ============================================================
        let lastDeclutterTime = 0;
        const DECLUTTER_INTERVAL = 150; // ms — jangan tiap frame, berat
        const COLLISION_RADIUS = 110;   // px — radius tabrakan di layar
        const MOBILE_RADIUS = 70;       // px — mobile lebih kecil
        const isMobile = () => window.innerWidth < 768;

        function declutterTooltips() {
            const now = performance.now();
            if (now - lastDeclutterTime < DECLUTTER_INTERVAL) return;
            lastDeclutterTime = now;
            if (!labelSprites.length) return;

            const rect = ren.domElement.getBoundingClientRect();
            const w = rect.width, h = rect.height;
            const radius = isMobile() ? MOBILE_RADIUS : COLLISION_RADIUS;

            const visible = [];
            labelSprites.forEach(sprite => {
                if (!sprite.visible) return;
                const proj = sprite.position.clone().project(cam);
                if (proj.z > 1 || proj.z < -1) return;
                const sx = (proj.x * 0.5 + 0.5) * w;
                const sy = (-proj.y * 0.5 + 0.5) * h;
                if (sx < -50 || sx > w + 50 || sy < -50 || sy > h + 50) return;
                sprite.userData._screenX = sx;
                sprite.userData._screenY = sy;
                sprite.userData._distToCam = cam.position.distanceTo(sprite.position);
                visible.push(sprite);
            });

            visible.sort((a, b) => a.userData._distToCam - b.userData._distToCam);

            const cellSize = radius;
            const grid = new Map();

            visible.forEach(sprite => {
                const cx = Math.floor(sprite.userData._screenX / cellSize);
                const cy = Math.floor(sprite.userData._screenY / cellSize);
                let collides = false;

                for (let dx = -1; dx <= 1 && !collides; dx++) {
                    for (let dy = -1; dy <= 1 && !collides; dy++) {
                        const key = `${cx + dx},${cy + dy}`;
                        const bucket = grid.get(key);
                        if (!bucket) continue;
                        for (const other of bucket) {
                            const d = Math.hypot(
                                sprite.userData._screenX - other.userData._screenX,
                                sprite.userData._screenY - other.userData._screenY
                            );
                            if (d < radius) { collides = true; break; }
                        }
                    }
                }

                if (collides) {
                    sprite.material.opacity = 0;
                    sprite.userData._hiddenByDeclutter = true;
                } else {
                    sprite.userData._hiddenByDeclutter = false;
                    const key = `${cx},${cy}`;
                    if (!grid.has(key)) grid.set(key, []);
                    grid.get(key).push(sprite);
                }
            });
        }

        const el = ren.domElement;
        el.addEventListener('contextmenu', e => e.preventDefault());
        let lastX = 0, lastY = 0, dragging = false, downPos = null;

        el.addEventListener('pointerdown', e => {
            downPos = [e.clientX, e.clientY];
            lastX = e.clientX; lastY = e.clientY;
            dragging = true;
            el.setPointerCapture?.(e.pointerId);
        });

        el.addEventListener('pointermove', e => {
            if (!dragging) return;
            const dx = typeof e.movementX === 'number' ? e.movementX : (e.clientX - lastX);
            const dy = typeof e.movementY === 'number' ? e.movementY : (e.clientY - lastY);
            lastX = e.clientX; lastY = e.clientY;
            if (e.buttons === 1) {
                fly = false;
                th -= dx * 0.006;
                ph = Math.min(1.5, Math.max(0.15, ph - dy * 0.006));
                updCam();
            } else if (e.buttons === 2 || e.shiftKey) {
                fly = false;
                const s = rad * 0.0012;
                tgt.x -= dx * s; tgt.z -= dy * s;
                updCam();
            }
        });

        el.addEventListener('pointerup', e => {
            dragging = false;
            el.releasePointerCapture?.(e.pointerId);
            if (downPos && Math.hypot(e.clientX - downPos[0], e.clientY - downPos[1]) < 6) {
                const rect = el.getBoundingClientRect();
                const mPos = new THREE.Vector2(
                    ((e.clientX - rect.left) / rect.width) * 2 - 1,
                    -((e.clientY - rect.top) / rect.height) * 2 + 1
                );
                const RC = new THREE.Raycaster();
                RC.setFromCamera(mPos, cam);
                const visiblePickables = pk.filter(m => {
                    let n = m;
                    while (n) { if (n.visible === false) return false; n = n.parent; }
                    return true;
                });
                const hit = RC.intersectObjects(visiblePickables)[0];
                if (hit) {
                    const obj = hit.object.userData.room;
                    if (obj) {
                        gt.set(obj.x, obj.ht ? obj.ht * 0.3 : 0, obj.z);
                        gr = 28; gp = 0.8; fly = true;

                        if (obj.isDecorative || !obj.room) {
                            window.openRoomModal({
                                name: obj.name + ' (Dekoratif)',
                                code: '—',
                                condition: 'dekoratif',
                                conditionLabel: 'Dekoratif',
                                location: 'Bangunan ini hanya untuk dekorasi peta 3D.',
                                capacity: '—',
                                health: '—',
                                url: '#', reportUrl: '#', photo: '',
                            });
                        } else {
                            const r = obj.room;
                            window.openRoomModal({
                                id: r.id, name: r.name, code: r.code,
                                condition: typeof r.condition === 'object' ? r.condition.value : r.condition,
                                conditionLabel: r.conditionLabel || (typeof r.condition === 'object' ? r.condition.label : 'Tersedia'),
                                location: r.description || '',
                                capacity: r.capacity, health: r.health_score,
                                url: r.detail_url || '#', reportUrl: r.report_url || '#',
                                photo: r.photo_url || '',
                            });
                        }
                    }
                }
            }
            downPos = null;
        });

        el.addEventListener('pointercancel', () => { dragging = false; downPos = null; });

        el.addEventListener('wheel', e => {
            e.preventDefault();
            fly = false;
            rad = Math.min(160, Math.max(4, rad * (1 + e.deltaY * 0.001)));
            updateTooltipScale();
            updCam();
        }, { passive: false });

        document.getElementById('btn-3d-rotate')?.addEventListener('click', e => {
            auto = !auto;
            e.target.textContent = `Putar otomatis: ${auto ? 'hidup' : 'mati'}`;
        });

        document.getElementById('btn-3d-reset')?.addEventListener('click', () => {
            fly = false;
            th = def.th; ph = def.ph; rad = def.rad;
            tgt.set(0, 0, 0);
            updateTooltipScale();
            updCam();
        });

        window.trigger3DResize = function () {
            if (!container) return;
            const w = container.clientWidth || 800;
            const h = container.clientHeight || 640;
            cam.aspect = w / h;
            cam.updateProjectionMatrix();
            ren.setSize(w, h);
        };
        window.addEventListener('resize', window.trigger3DResize);
        if (window.ResizeObserver) {
            const ro = new ResizeObserver(() => window.trigger3DResize());
            ro.observe(container);
        }

        window.filter3DBuildings = function (q) {
            const query = (q || '').toLowerCase().trim();
            if (!query) {
                pk.forEach(m => m.visible = true);
                labelSprites.forEach(s => s.visible = true);
                return;
            }
            pk.forEach(m => {
                const obj = m.userData.room;
                if (!obj) return;
                const match = (obj.name || '').toLowerCase().includes(query);
                m.visible = match;
            });
            labelSprites.forEach(sprite => {
                const ref = sprite.userData.roomRef;
                if (!ref) return;
                const match = (ref.name || '').toLowerCase().includes(query);
                sprite.visible = match;
            });
        };

        window.toggleTooltips = function (show) {
            labelSprites.forEach(s => s.visible = show);
            return show;
        };

        let rendering = true;
        document.addEventListener('visibilitychange', () => { rendering = !document.hidden; });

        updateTooltipScale();

        function loop() {
            requestAnimationFrame(loop);
            if (!rendering) return;
            if (fly) {
                tgt.lerp(gt, 0.08);
                rad += (gr - rad) * 0.08;
                ph += (gp - ph) * 0.08;
                updateTooltipScale();
            }
            if (auto && !fly) th += 0.003;

            declutterTooltips();

            const arrows = window.__peta3D?._billboardArrows;
            if (arrows && arrows.length) {
                arrows.forEach(a => a.quaternion.copy(cam.quaternion));
            }

            const anim = window.__peta3D?._routeAnim;
            if (anim && anim.active && anim.curve && anim.dots.length) {
                const t = performance.now() * 0.0003;
                anim.dots.forEach((dot, i) => {
                    const prog = (t + i / anim.dots.length) % 1;
                    const pt = anim.curve.getPointAt(prog);
                    dot.position.set(pt.x, pt.y + 0.1, pt.z);
                });
            }

            updCam();
            ren.render(scene, cam);
        }
        loop();

        window.__peta3D = {
            scene, cam, ren, THREE,
            roomsRegistry, S, CX, CY,
            labelSprites,
            stairsRegistry,
            toWorld: (x, z) => ({ x: (x - CX) * S, z: (z - CY) * S }),
            flyTo: (worldX, worldZ, ht = 3, distance = 25) => {
                gt.set(worldX, ht * 0.3, worldZ);
                gr = distance; gp = 0.85; fly = true;
            },
        };

        console.log(`[3D] ${roomsRegistry.length} objek, ${pk.length} pickable, ${labelSprites.length} tooltip.`);
        window.dispatchEvent(new CustomEvent('3d-ready', { detail: { count: roomsRegistry.length } }));
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
});