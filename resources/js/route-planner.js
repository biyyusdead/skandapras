// =============================================================
// route-planner.js — CLEAN VERSION
// A* + antar lantai via tangga + tab switcher + dropdown
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
    // UTIL
    // ===========================================================
    function getFloorFromRoom(room) {
        if (!room) return null;
        const f = room.map_floor || room.floor;
        return f != null && f !== '' ? String(f) : null;
    }

    function findNearestStair(roomEntry, stairs) {
        if (!stairs.length) return null;
        let best = null, bestDist = Infinity;
        stairs.forEach(s => {
            const d = Math.hypot(s.x - roomEntry.x, s.z - roomEntry.z);
            if (d < bestDist) { bestDist = d; best = s; }
        });
        return best;
    }

    // ===========================================================
    // GRID A*
    // ===========================================================
    function findRouteBetween(fromRoom, toRoom, api) {
        const step = 0.6;
        const minX = -37.5, maxX = 37.5, minZ = -20.5, maxZ = 20.5;
        const nx = Math.round((maxX - minX) / step) + 1;
        const nz = Math.round((maxZ - minZ) / step) + 1;
        const index = (ix, iz) => iz * nx + ix;
        const point = (ix, iz) => ({ x: minX + ix * step, z: minZ + iz * step });
        const toCell = (x, z) => ({
            ix: Math.max(0, Math.min(nx - 1, Math.round((x - minX) / step))),
            iz: Math.max(0, Math.min(nz - 1, Math.round((z - minZ) / step)))
        });

        const start = toCell(fromRoom.x, fromRoom.z + fromRoom.D / 2 + 0.65);
        const target = toCell(toRoom.x, toRoom.z + toRoom.D / 2 + 0.65);

        const blocked = new Uint8Array(nx * nz);
        const registry = api.roomsRegistry || [];
        const pad = 0.35;

        registry.forEach(o => {
            if (!(o.ht > 0)) return;
            if (Math.abs(o.x - fromRoom.x) < 0.08 && Math.abs(o.z - fromRoom.z) < 0.08) return;
            if (Math.abs(o.x - toRoom.x) < 0.08 && Math.abs(o.z - toRoom.z) < 0.08) return;

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

        const clearArea = (cx, cz, r = 3) => {
            for (let dx = -r; dx <= r; dx++) {
                for (let dz = -r; dz <= r; dz++) {
                    const ix = cx + dx, iz = cz + dz;
                    if (ix >= 0 && ix < nx && iz >= 0 && iz < nz) blocked[index(ix, iz)] = 0;
                }
            }
        };
        clearArea(start.ix, start.iz, 3);
        clearArea(target.ix, target.iz, 3);

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
        let bestFallback = startId;
        let bestH = heuristic(startId);

        const MAX_ITER = 40000;
        while (heap.length && iterations++ < MAX_ITER) {
            const cur = pop();
            if (!cur) break;
            const current = cur[1];
            if (closed[current]) continue;
            if (current === goalId) { found = true; break; }
            closed[current] = 1;

            const h = heuristic(current);
            if (h < bestH) { bestH = h; bestFallback = current; }

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

        const finalId = found ? goalId : bestFallback;
        const path = [];
        let id = finalId;
        while (id !== -1) {
            path.push(point(id % nx, Math.floor(id / nx)));
            if (id === startId) break;
            id = came[id];
        }
        path.reverse();

        const simple = [];
        for (let i = 0; i < path.length; i++) {
            if (i === 0 || i === path.length - 1) { simple.push(path[i]); continue; }
            const a = path[i - 1], b = path[i], c = path[i + 1];
            if (Math.abs((b.x - a.x) * (c.z - b.z) - (b.z - a.z) * (c.x - b.x)) > 0.001) simple.push(b);
        }

        if (simple.length < 2) {
            simple.push({ x: toRoom.x, z: toRoom.z + toRoom.D / 2 + 0.65 });
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
    let retryCount = 0;
    const retry = setInterval(() => {
        retryCount++;
        if (tryFill() || retryCount > 20) clearInterval(retry);
    }, 300);
    window.addEventListener('3d-ready', () => tryFill());
    document.querySelectorAll('.view-toggle-btn').forEach(b => {
        b.addEventListener('click', () => setTimeout(tryFill, 200));
    });

    // ===========================================================
    // CLEAR
    // ===========================================================
    function clearRoute() {
        const api = window.__peta3D;
        if (!api) return;

        if (routeLine) {
            api.scene.remove(routeLine);
            if (routeLine.geometry) routeLine.geometry.dispose();
            if (routeLine.material) {
                if (Array.isArray(routeLine.material)) routeLine.material.forEach(m => m.dispose());
                else routeLine.material.dispose();
            }
            routeLine = null;
        }

        routeMarkers.forEach(m => {
            api.scene.remove(m);
            if (m.geometry) m.geometry.dispose();
            if (m.material) {
                if (Array.isArray(m.material)) m.material.forEach(mm => mm.dispose());
                else m.material.dispose();
            }
        });
        routeMarkers = [];

        if (api._routeAnim) {
            api._routeAnim.active = false;
            api._routeAnim.dots = [];
            api._routeAnim.curve = null;
        }
        if (api._billboardArrows) api._billboardArrows = [];

        if (panel) panel.classList.add('hidden');
    }

    // ===========================================================
    // STAIR LABEL SPRITE
    // ===========================================================
    function makeStairLabel(text, dir) {
        const cv = document.createElement('canvas');
        const dpr = 2;
        cv.width = 512 * dpr;
        cv.height = 200 * dpr;
        const ctx = cv.getContext('2d');
        ctx.scale(dpr, dpr);

        const bgColor = dir === 'up' ? '#16a34a' : '#dc2626';
        const cx = 256, cy = 100;
        const boxW = 340, boxH = 140;
        const r = 30;

        ctx.shadowColor = 'rgba(0,0,0,0.35)';
        ctx.shadowBlur = 24;
        ctx.shadowOffsetY = 8;

        const rx = cx - boxW / 2, ry = cy - boxH / 2;
        ctx.fillStyle = bgColor;
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
        ctx.lineWidth = 6;
        ctx.strokeStyle = '#ffffff';
        ctx.stroke();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 64px system-ui, -apple-system, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(dir === 'up' ? '↑' : '↓', cx - 90, cy + 4);

        ctx.font = 'bold 60px system-ui, -apple-system, sans-serif';
        ctx.fillText(text, cx + 40, cy + 4);

        const tex = new THREE.CanvasTexture(cv);
        tex.minFilter = THREE.LinearFilter;
        tex.magFilter = THREE.LinearFilter;
        tex.needsUpdate = true;

        const mat = new THREE.SpriteMaterial({
            map: tex,
            transparent: true,
            depthTest: false,
            depthWrite: false,
            sizeAttenuation: true,
        });

        const sprite = new THREE.Sprite(mat);
        sprite.scale.set(4.5, 1.75, 1);
        sprite.renderOrder = 1002;
        return sprite;
    }

    // ===========================================================
    // 3D ARROW
    // ===========================================================
    function make3DArrow(dir, color) {
        const group = new THREE.Group();
        const mat = new THREE.MeshBasicMaterial({ color, depthTest: false });
        const sign = dir === 'up' ? 1 : -1;

        const shaft = new THREE.Mesh(
            new THREE.CylinderGeometry(0.12, 0.12, 1.4, 12),
            mat
        );
        shaft.renderOrder = 1002;
        group.add(shaft);

        const head = new THREE.Mesh(
            new THREE.ConeGeometry(0.35, 0.7, 16),
            mat
        );
        head.position.y = sign * 1.0;
        if (dir === 'down') head.rotation.z = Math.PI;
        head.renderOrder = 1002;
        group.add(head);

        group.userData.isBillboard = true;
        return group;
    }

    // ===========================================================
    // DRAW
    // ===========================================================
    function drawRoute(path, staircasePoint = null) {
        const api = window.__peta3D;
        if (!api || !path || path.length < 2) return;

        clearRoute();

        const pts = path.map(p => new THREE.Vector3(p.x, 0.15, p.z));
        const last = pts[pts.length - 1];
        pts.push(new THREE.Vector3(last.x, 0.15, last.z));

        const curve = new THREE.CatmullRomCurve3(pts);
        const tubeGeo = new THREE.TubeGeometry(curve, Math.max(pts.length * 3, 40), 0.09, 8, false);
        const tubeMat = new THREE.MeshBasicMaterial({
            color: 0x00e5ff, depthTest: false, transparent: true, opacity: 0.95,
        });
        routeLine = new THREE.Mesh(tubeGeo, tubeMat);
        routeLine.renderOrder = 999;
        api.scene.add(routeLine);

        const glowMat = new THREE.MeshBasicMaterial({
            color: 0xffffff, depthTest: false, transparent: true, opacity: 0.35,
        });
        const glowGeo = new THREE.TubeGeometry(curve, Math.max(pts.length * 3, 40), 0.16, 8, false);
        const glowLine = new THREE.Mesh(glowGeo, glowMat);
        glowLine.renderOrder = 998;
        api.scene.add(glowLine);
        routeMarkers.push(glowLine);

        const dotGeo = new THREE.SphereGeometry(0.18, 8, 8);
        const dotMat = new THREE.MeshBasicMaterial({ color: 0xffffff, depthTest: false });
        const dots = [];
        for (let i = 0; i < 6; i++) {
            const dot = new THREE.Mesh(dotGeo, dotMat);
            dot.renderOrder = 1000;
            api.scene.add(dot);
            dots.push(dot);
            routeMarkers.push(dot);
        }

        if (!api._routeAnim) api._routeAnim = { dots: [], curve: null, active: false };
        api._routeAnim.dots = dots;
        api._routeAnim.curve = curve;
        api._routeAnim.active = true;

        const startDot = new THREE.Mesh(
            new THREE.SphereGeometry(0.35, 16, 16),
            new THREE.MeshBasicMaterial({ color: 0x22c55e, depthTest: false })
        );
        startDot.position.copy(pts[0]);
        startDot.position.y = 0.4;
        startDot.renderOrder = 1000;
        api.scene.add(startDot);
        routeMarkers.push(startDot);

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

        // Marker khusus tangga
        if (staircasePoint) {
            const dir = staircasePoint.direction || 'up';

            const stairMarker = new THREE.Mesh(
                new THREE.CylinderGeometry(0.55, 0.55, 0.12, 20),
                new THREE.MeshBasicMaterial({ color: 0xfbbf24, depthTest: false, transparent: true, opacity: 0.95 })
            );
            stairMarker.position.set(staircasePoint.x, 0.5, staircasePoint.z);
            stairMarker.renderOrder = 1001;
            api.scene.add(stairMarker);
            routeMarkers.push(stairMarker);

            const sprite = makeStairLabel(dir === 'up' ? 'NAIK' : 'TURUN', dir);
            sprite.position.set(staircasePoint.x, 4.2, staircasePoint.z);
            api.scene.add(sprite);
            routeMarkers.push(sprite);

            const arrowColor = dir === 'up' ? 0x22c55e : 0xef4444;
            const arrow = make3DArrow(dir, arrowColor);
            arrow.position.set(staircasePoint.x, 2.8, staircasePoint.z);
            api.scene.add(arrow);
            routeMarkers.push(arrow);

            if (!api._billboardArrows) api._billboardArrows = [];
            api._billboardArrows.push(arrow);
        }

        const mid = pts[Math.floor(pts.length / 2)];
        api.flyTo(mid.x, mid.z, 2, 45);
    }

    // ===========================================================
    // STEPS PANEL
    // ===========================================================
    function buildSteps(path, fromName, toName, viaStairs, fromFloor, toFloor) {
        let dist = 0;
        for (let i = 1; i < path.length; i++) {
            dist += Math.hypot(path[i].x - path[i - 1].x, path[i].z - path[i - 1].z);
        }
        const meters = Math.round(dist * 2);

        const floorInfo = (fromFloor && toFloor && fromFloor !== toFloor)
            ? `<div class="flex items-center gap-2 text-amber-700 font-semibold">
                   <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                   <span>Lewat:</span><span>Tangga (Lt. ${fromFloor} → Lt. ${toFloor})</span>
               </div>`
            : '';

        return `
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-emerald-700 font-semibold">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>Dari:</span>
                    <span>${fromName}</span>
                </div>
                ${floorInfo}
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
    // EVENT HANDLER — CARI RUTE
    // ===========================================================
    let routeRunning = false;
    btn.addEventListener('click', async () => {
        if (routeRunning) return;
        routeRunning = true;
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.textContent = 'Mencari...';
        try {
            await new Promise(r => setTimeout(r, 0)); // yield ke UI
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

            const fromFloor = getFloorFromRoom(fromEntry.room);
            const toFloor = getFloorFromRoom(toEntry.room);
            const diffFloor = fromFloor && toFloor && fromFloor !== toFloor;

            console.log(`[Route] ${fromEntry.room.name} (Lt.${fromFloor || '?'}) → ${toEntry.room.name} (Lt.${toFloor || '?'})`);

            if (diffFloor) {
                const stairs = api.stairsRegistry || [];
                if (stairs.length === 0) {
                    alert(`Rute antar lantai (Lt. ${fromFloor} → Lt. ${toFloor}) membutuhkan data tangga.\n\nSilakan pilih ruangan di lantai yang sama.`);
                    return;
                }

                const stairsFrom = findNearestStair(fromEntry, stairs);
                const stairsTo = findNearestStair(toEntry, stairs);
                if (!stairsFrom || !stairsTo) { alert('Tangga terdekat tidak ditemukan.'); return; }

                const t0 = performance.now();
                const path1 = findRouteBetween(fromEntry, stairsFrom, api) || [];
                const path2 = findRouteBetween(stairsFrom, toEntry, api) || [];
                const t1 = performance.now();

                let combined = [...path1];
                if (stairsFrom !== stairsTo) {
                    const pathMid = findRouteBetween(stairsFrom, stairsTo, api) || [];
                    combined = combined.concat(pathMid);
                }
                combined = combined.concat(path2);

                console.log(`[Route] Antar-lantai: ${combined.length} titik, ${(t1 - t0).toFixed(1)}ms`);
                if (combined.length < 2) { alert('Rute antar lantai tidak ditemukan.'); return; }

                const fromFloorNum = parseInt(fromFloor, 10) || 1;
                const toFloorNum = parseInt(toFloor, 10) || 1;
                const direction = toFloorNum > fromFloorNum ? 'up' : 'down';
                const stairPointWithDir = { ...stairsFrom, direction };

                drawRoute(combined, stairPointWithDir);

                if (panel && steps) {
                    steps.innerHTML = buildSteps(combined, fromEntry.room.name, toEntry.room.name, true, fromFloor, toFloor);
                    panel.classList.remove('hidden');
                }
                return;
            }

            // Rute satu lantai
            const t0 = performance.now();
            console.time('A* ' + fromEntry.room.name + ' → ' + toEntry.room.name);
            const path = findRouteBetween(fromEntry, toEntry, api);
            console.timeEnd('A* ' + fromEntry.room.name + ' → ' + toEntry.room.name);
            if (path && path.length < 2) {
                console.warn('[Route] Path tidak ditemukan untuk:', fromEntry.room.name, '→', toEntry.room.name);
            }
            const t1 = performance.now();
            console.log(`[Route] A* ${(t1 - t0).toFixed(1)}ms, hasil:`, path ? path.length + ' titik' : 'GAGAL');

            if (!path || path.length < 2) { alert('Rute tidak ditemukan.'); return; }

            drawRoute(path);
            if (panel && steps) {
                steps.innerHTML = buildSteps(path, fromEntry.room.name, toEntry.room.name, false, fromFloor, toFloor);
                panel.classList.remove('hidden');
            }
        } finally {
            routeRunning = false;
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });

    btnClear?.addEventListener('click', clearRoute);

     // ===========================================================
    // ROOMS LIST OVERLAY — daftar ruangan DI DALAM container 3D
    // ===========================================================
    const roomsToggleBtn = document.getElementById('rooms-list-toggle');
    const roomsCloseBtn = document.getElementById('rooms-list-close');
    const roomsPanel = document.getElementById('rooms-list-panel');
    const roomsSearchInput = document.getElementById('rooms-list-search');
    const roomsItemsContainer = document.getElementById('rooms-list-items');

    function renderRoomsList(filter = '') {
        if (!roomsItemsContainer) return;
        const api = window.__peta3D;
        if (!api) return;

        const seen = new Set();
        const rooms = [];
        (api.roomsRegistry || []).forEach(o => {
            if (!o.room || !o.room.id) return;
            if (seen.has(o.room.id)) return;
            seen.add(o.room.id);
            rooms.push({ id: o.room.id, name: o.room.name });
        });
        rooms.sort((a, b) => a.name.localeCompare(b.name));

        const q = filter.toLowerCase().trim();
        const filtered = q ? rooms.filter(r => r.name.toLowerCase().includes(q)) : rooms;

        if (filtered.length === 0) {
            roomsItemsContainer.innerHTML = `<p class="text-[11px] text-surface-400 text-center py-4">Tidak ada ruangan.</p>`;
            return;
        }

        roomsItemsContainer.innerHTML = filtered.map(r => `
            <button type="button"
                    class="room-list-item w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-surface-700 hover:bg-primary-50 hover:text-primary-700 transition-colors"
                    data-room-id="${r.id}">
                ${r.name}
            </button>
        `).join('');

        roomsItemsContainer.querySelectorAll('.room-list-item').forEach(btnItem => {
            btnItem.addEventListener('click', () => {
                const roomId = btnItem.dataset.roomId;
                const api2 = window.__peta3D;
                if (!api2) return;
                const entry = api2.roomsRegistry.find(o => o.room && String(o.room.id) === String(roomId));
                if (!entry) return;

                // Fokus kamera
                api2.flyTo(entry.x, entry.z, 2, 28);

                // Auto-set di dropdown rute
                if (fromSel && !fromSel.value) fromSel.value = roomId;
                else if (toSel) toSel.value = roomId;

                // Buka modal
                setTimeout(() => {
                    if (entry.room) {
                        window.openRoomModal({
                            id: entry.room.id,
                            name: entry.room.name,
                            code: entry.room.code,
                            condition: typeof entry.room.condition === 'object' ? entry.room.condition.value : entry.room.condition,
                            conditionLabel: entry.room.conditionLabel || (typeof entry.room.condition === 'object' ? entry.room.condition.label : 'Tersedia'),
                            location: entry.room.description || '',
                            capacity: entry.room.capacity,
                            health: entry.room.health_score,
                            url: entry.room.detail_url || '#',
                            reportUrl: entry.room.report_url || '#',
                            photo: entry.room.photo_url || '',
                        });
                    }
                }, 400);
            });
        });
    }

    if (roomsToggleBtn && roomsPanel) {
        roomsToggleBtn.addEventListener('click', () => {
            if (roomsPanel.classList.contains('hidden')) {
                roomsPanel.classList.remove('hidden');
                renderRoomsList('');
            } else {
                roomsPanel.classList.add('hidden');
            }
        });
    }

    if (roomsCloseBtn) {
        roomsCloseBtn.addEventListener('click', () => roomsPanel?.classList.add('hidden'));
    }

    if (roomsSearchInput) {
        roomsSearchInput.addEventListener('input', (e) => renderRoomsList(e.target.value));
    }

    // ===========================================================
    // TOGGLE LABEL
    // ===========================================================
    const toggleLabelsBtn = document.getElementById('btn-toggle-labels');
    if (toggleLabelsBtn) {
        let labelsOn = true;
        toggleLabelsBtn.addEventListener('click', () => {
            labelsOn = !labelsOn;
            if (typeof window.toggleTooltips === 'function') window.toggleTooltips(labelsOn);
            toggleLabelsBtn.innerHTML = labelsOn
                ? '<i data-lucide="tag" class="w-3.5 h-3.5"></i> Sembunyikan Label'
                : '<i data-lucide="tag-off" class="w-3.5 h-3.5"></i> Tampilkan Label';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }

    // ===========================================================
    // TOOLTIP COUNT
    // ===========================================================
    window.addEventListener('3d-ready', (e) => {
        const count = e.detail?.count || 0;
        const el = document.getElementById('tooltip-count');
        if (el) el.textContent = count;
    });
});