// =============================================================
// route-planner.js — A* grid-based (port dari findCampusRoute)
// =============================================================

document.addEventListener('DOMContentLoaded', function () {
    const fromSel = document.getElementById('route-from');
    const toSel = document.getElementById('route-to');
    const btn = document.getElementById('route-go');
    const btnClear = document.getElementById('route-clear');
    const panel = document.getElementById('route-panel');
    const steps = document.getElementById('route-steps');

    if (!fromSel || !toSel || !btn) {
        console.warn('[Route] Elemen UI tidak ditemukan.');
        return;
    }

    let routeLine = null;
    let routeMarkers = [];
    let dropdownsFilled = false;

    // ===========================================================
    // GRID A* — dari findCampusRoute
    // ===========================================================
    function findRouteBetween(fromRoom, toRoom, api) {
        const step = 0.25;
        const minX = -37.5, maxX = 37.5, minZ = -20.5, maxZ = 20.5;
        const nx = Math.round((maxX - minX) / step) + 1;
        const nz = Math.round((maxZ - minZ) / step) + 1;
        const index = (ix, iz) => iz * nx + ix;
        const point = (ix, iz) => ({ x: minX + ix * step, z: minZ + iz * step });
        const toCell = (x, z) => ({
            ix: Math.max(0, Math.min(nx - 1, Math.round((x - minX) / step))),
            iz: Math.max(0, Math.min(nz - 1, Math.round((z - minZ) / step)))
        });

        // Start & end cell
        const start = toCell(fromRoom.x, fromRoom.z + fromRoom.D / 2 + 0.65);
        const target = toCell(toRoom.x, toRoom.z + toRoom.D / 2 + 0.65);

        // Blocked grid
        const blocked = new Uint8Array(nx * nz);
        const registry = api.roomsRegistry || [];
        registry.forEach(o => {
            if (!(o.ht > 0)) return;
            // Skip gedung start & end (biar bisa masuk)
            if (Math.abs(o.x - fromRoom.x) < 0.08 && Math.abs(o.z - fromRoom.z) < 0.08) return;
            if (Math.abs(o.x - toRoom.x) < 0.08 && Math.abs(o.z - toRoom.z) < 0.08) return;
            const pad = 0.12;
            const x0 = Math.max(0, Math.floor((o.x - o.W / 2 - pad - minX) / step));
            const x1 = Math.min(nx - 1, Math.ceil((o.x + o.W / 2 + pad - minX) / step));
            const z0 = Math.max(0, Math.floor((o.z - o.D / 2 - pad - minZ) / step));
            const z1 = Math.min(nz - 1, Math.ceil((o.z + o.D / 2 + pad - minZ) / step));
            for (let iz = z0; iz <= z1; iz++) {
                for (let ix = x0; ix <= x1; ix++) {
                    blocked[index(ix, iz)] = 1;
                }
            }
        });
        blocked[index(start.ix, start.iz)] = 0;
        blocked[index(target.ix, target.iz)] = 0;

        const total = nx * nz;
        const gScore = new Float32Array(total).fill(Infinity);
        const came = new Int32Array(total).fill(-1);
        const closed = new Uint8Array(total);

        const startId = index(start.ix, start.iz);
        const goalId = index(target.ix, target.iz);
        const heuristic = (id) => {
            const ix = id % nx, iz = Math.floor(id / nx);
            return Math.hypot(ix - target.ix, iz - target.iz);
        };

        // Min-heap sederhana
        const heap = [];
        const push = (item) => {
            heap.push(item);
            let i = heap.length - 1;
            while (i > 0) {
                const p = (i - 1) >> 1;
                if (heap[p][0] <= heap[i][0]) break;
                [heap[p], heap[i]] = [heap[i], heap[p]];
                i = p;
            }
        };
        const pop = () => {
            if (heap.length === 0) return null;
            const top = heap[0];
            const last = heap.pop();
            if (heap.length > 0) {
                heap[0] = last;
                let i = 0;
                const n = heap.length;
                while (true) {
                    const l = i * 2 + 1, r = i * 2 + 2;
                    let s = i;
                    if (l < n && heap[l][0] < heap[s][0]) s = l;
                    if (r < n && heap[r][0] < heap[s][0]) s = r;
                    if (s === i) break;
                    [heap[s], heap[i]] = [heap[i], heap[s]];
                    i = s;
                }
            }
            return top;
        };

        gScore[startId] = 0;
        push([heuristic(startId), startId]);

        const dirs = [
            [-1, 0, 1], [1, 0, 1], [0, -1, 1], [0, 1, 1],
            [-1, -1, 1.414], [1, -1, 1.414], [-1, 1, 1.414], [1, 1, 1.414],
        ];

        let found = false;
        let iterations = 0;

        while (heap.length && iterations++ < total * 3) {
            const cur = pop();
            if (!cur) break;
            const current = cur[1];
            if (closed[current]) continue;
            if (current === goalId) { found = true; break; }
            closed[current] = 1;
            const ix = current % nx, iz = Math.floor(current / nx);

            for (const [dx, dz, cost] of dirs) {
                const xx = ix + dx, zz = iz + dz;
                if (xx < 0 || xx >= nx || zz < 0 || zz >= nz) continue;
                const ni = index(xx, zz);
                if (blocked[ni] || closed[ni]) continue;
                if (dx && dz && (blocked[index(ix + dx, iz)] || blocked[index(ix, iz + dz)])) continue;
                const ng = gScore[current] + cost;
                if (ng < gScore[ni]) {
                    gScore[ni] = ng;
                    came[ni] = current;
                    push([ng + heuristic(ni), ni]);
                }
            }
        }

        if (!found) return null;

        // Reconstruct
        const path = [];
        let id = goalId;
        while (id !== -1) {
            path.push(point(id % nx, Math.floor(id / nx)));
            if (id === startId) break;
            id = came[id];
        }
        path.reverse();

        // Simplify collinear
        const simple = [];
        for (let i = 0; i < path.length; i++) {
            if (i === 0 || i === path.length - 1) { simple.push(path[i]); continue; }
            const a = path[i - 1], b = path[i], c = path[i + 1];
            if (Math.abs((b.x - a.x) * (c.z - b.z) - (b.z - a.z) * (c.x - b.x)) > 0.001) simple.push(b);
        }
        return simple;
    }

    // ===========================================================
    // DROPDOWN
    // ===========================================================
    function getRoomsWith3D() {
        const api = window.__peta3D;
        if (!api) return [];
        const list = api.roomsRegistry || [];
        const seen = new Set();
        const result = [];
        list.forEach(o => {
            if (!o.room || !o.room.id) return;
            if (seen.has(o.room.id)) return;
            seen.add(o.room.id);
            result.push({ id: o.room.id, name: o.room.name });
        });
        result.sort((a, b) => a.name.localeCompare(b.name));
        return result;
    }

    function fillDropdowns() {
        const rooms = getRoomsWith3D();
        if (rooms.length === 0) {
            fromSel.innerHTML = `<option value="">— Belum ada ruangan 3D —</option>`;
            toSel.innerHTML = `<option value="">— Belum ada ruangan 3D —</option>`;
            return false;
        }
        const opts = rooms.map(r => `<option value="${r.id}">${r.name}</option>`).join('');
        fromSel.innerHTML = `<option value="">— Pilih ruangan awal —</option>${opts}`;
        toSel.innerHTML = `<option value="">— Pilih ruangan tujuan —</option>${opts}`;
        return true;
    }

    function tryFill() {
        if (dropdownsFilled) return true;
        if (!window.__peta3D) return false;
        if (fillDropdowns()) {
            dropdownsFilled = true;
            console.log('[Route] Dropdown terisi');
            return true;
        }
        return false;
    }

    tryFill();
    const retry = setInterval(() => { if (tryFill()) clearInterval(retry); }, 300);
    setTimeout(() => clearInterval(retry), 30000);
    window.addEventListener('3d-ready', () => tryFill());
    document.querySelectorAll('.view-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => setTimeout(tryFill, 200));
    });

    // ===========================================================
    // DRAW & CLEAR
    // ===========================================================
    function clearRoute() {
        const api = window.__peta3D;
        if (!api) return;
        if (routeLine) {
            api.scene.remove(routeLine);
            routeLine.geometry.dispose();
            routeLine.material.dispose();
            routeLine = null;
        }
        routeMarkers.forEach(m => {
            api.scene.remove(m);
            if (m.geometry) m.geometry.dispose();
            if (m.material) m.material.dispose();
        });
        routeMarkers = [];
        if (api._routeAnim) {
            api._routeAnim.active = false;
            api._routeAnim.dots = [];
            api._routeAnim.curve = null;
        }
        if (panel) panel.classList.add('hidden');
    }

        function drawRoute(path) {
        const api = window.__peta3D;
        if (!api || !path || path.length < 2) return;

        // Konversi ke Vector3
        const pts = path.map(p => new THREE.Vector3(p.x, 0.15, p.z));

        // Tambah titik end supaya sampai ke gedung
        const last = pts[pts.length - 1];
        pts.push(new THREE.Vector3(last.x, 0.15, last.z));

        // ── Garis utama: TubeGeometry neon cyan ──
        const curve = new THREE.CatmullRomCurve3(pts);
        const tubeGeo = new THREE.TubeGeometry(
            curve,
            Math.max(pts.length * 3, 40),
            0.09,        // radius — tipis tapi keliatan
            8,
            false
        );
        const tubeMat = new THREE.MeshBasicMaterial({
            color: 0x00e5ff,     // cyan neon
            depthTest: false,    // selalu di depan
            transparent: true,
            opacity: 0.95,
        });
        routeLine = new THREE.Mesh(tubeGeo, tubeMat);
        routeLine.renderOrder = 999;
        api.scene.add(routeLine);

        // ── Outline putih (glow) di bawahnya ──
        const glowMat = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            depthTest: false,
            transparent: true,
            opacity: 0.35,
        });
        const glowGeo = new THREE.TubeGeometry(
            curve,
            Math.max(pts.length * 3, 40),
            0.16,        // lebih tebal
            8,
            false
        );
        const glowLine = new THREE.Mesh(glowGeo, glowMat);
        glowLine.renderOrder = 998;
        api.scene.add(glowLine);
        routeMarkers.push(glowLine);

        // ── Animasi dash bergerak (bola-bola kecil sepanjang jalan) ──
        const dotGeo = new THREE.SphereGeometry(0.18, 8, 8);
        const dotMat = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            depthTest: false,
        });
        const dots = [];
        const numDots = 6;
        for (let i = 0; i < numDots; i++) {
            const dot = new THREE.Mesh(dotGeo, dotMat);
            dot.renderOrder = 1000;
            dot.userData.t = i / numDots;  // progress 0..1
            api.scene.add(dot);
            dots.push(dot);
            routeMarkers.push(dot);
        }

        // Simpan di api untuk animasi
        if (!api._routeAnim) {
            api._routeAnim = { dots: [], curve: null, active: false };
        }
        api._routeAnim.dots = dots;
        api._routeAnim.curve = curve;
        api._routeAnim.active = true;

        // ── Marker start & end ──
        const startDot = new THREE.Mesh(
            new THREE.SphereGeometry(0.35, 16, 16),
            new THREE.MeshBasicMaterial({ color: 0x22c55e, depthTest: false })
        );
        startDot.position.copy(pts[0]);
        startDot.position.y = 0.4;
        startDot.renderOrder = 1000;
        api.scene.add(startDot);
        routeMarkers.push(startDot);

        // Ring di start
        const startRing = new THREE.Mesh(
            new THREE.RingGeometry(0.5, 0.65, 32),
            new THREE.MeshBasicMaterial({ color: 0x22c55e, side: THREE.DoubleSide, depthTest: false, transparent: true, opacity: 0.8 })
        );
        startRing.rotation.x = -Math.PI / 2;
        startRing.position.copy(pts[0]);
        startRing.position.y = 0.3;
        startRing.renderOrder = 1000;
        api.scene.add(startRing);
        routeMarkers.push(startRing);

        const endDot = new THREE.Mesh(
            new THREE.SphereGeometry(0.4, 16, 16),
            new THREE.MeshBasicMaterial({ color: 0xef4444, depthTest: false })
        );
        endDot.position.copy(pts[pts.length - 1]);
        endDot.position.y = 0.45;
        endDot.renderOrder = 1000;
        api.scene.add(endDot);
        routeMarkers.push(endDot);

        // Ring di end
        const endRing = new THREE.Mesh(
            new THREE.RingGeometry(0.55, 0.72, 32),
            new THREE.MeshBasicMaterial({ color: 0xef4444, side: THREE.DoubleSide, depthTest: false, transparent: true, opacity: 0.8 })
        );
        endRing.rotation.x = -Math.PI / 2;
        endRing.position.copy(pts[pts.length - 1]);
        endRing.position.y = 0.3;
        endRing.renderOrder = 1000;
        api.scene.add(endRing);
        routeMarkers.push(endRing);

        // Kamera fokus ke tengah
        const mid = pts[Math.floor(pts.length / 2)];
        api.flyTo(mid.x, mid.z, 2, 45);
    }

    function buildSteps(path, fromName, toName) {
        let dist = 0;
        for (let i = 1; i < path.length; i++) {
            dist += Math.hypot(path[i].x - path[i - 1].x, path[i].z - path[i - 1].z);
        }
        const meters = Math.round(dist * 2);
        return `
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-emerald-700 font-semibold">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>Dari:</span>
                    <span>${fromName}</span>
                </div>
                <div class="flex items-center gap-2 text-red-700 font-semibold">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span>Ke:</span>
                    <span>${toName}</span>
                </div>
                <div class="text-surface-600 pt-2 border-t border-surface-200 space-y-1">
                    <div>Jarak perkiraan: <b>${meters} meter</b></div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-6 h-1 rounded-full" style="background:#00e5ff;box-shadow:0 0 6px #00e5ff;"></span>
                        <span>Ikuti garis cyan di peta</span>
                    </div>
                </div>
            </div>
        `;
    }

    // ===========================================================
    // EVENT HANDLER
    // ===========================================================
    btn.addEventListener('click', () => {
        const fromId = fromSel.value;
        const toId = toSel.value;
        if (!fromId || !toId) { alert('Pilih ruangan awal dan tujuan dulu.'); return; }
        if (fromId === toId) { alert('Ruangan awal dan tujuan sama.'); return; }

        const api = window.__peta3D;
        if (!api) { alert('Buka tab Model 3D dulu.'); return; }

        const list = api.roomsRegistry || [];
        const fromEntry = list.find(o => o.room && String(o.room.id) === String(fromId));
        const toEntry = list.find(o => o.room && String(o.room.id) === String(toId));
        if (!fromEntry || !toEntry) { alert('Ruangan tidak ditemukan di 3D.'); return; }

        console.log('[Route] Mencari:', fromEntry.room.name, '→', toEntry.room.name);

        const t0 = performance.now();
        const path = findRouteBetween(fromEntry, toEntry, api);
        const t1 = performance.now();
        console.log(`[Route] A* ${(t1 - t0).toFixed(1)}ms, hasil:`, path ? path.length + ' titik' : 'GAGAL');

        if (!path || path.length < 2) { alert('Rute tidak ditemukan.'); return; }

        drawRoute(path);
        if (panel && steps) {
            steps.innerHTML = buildSteps(path, fromEntry.room.name, toEntry.room.name);
            panel.classList.remove('hidden');
        }
    });

    btnClear?.addEventListener('click', clearRoute);
});