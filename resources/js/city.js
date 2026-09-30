import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
import { Reflector } from 'three/addons/objects/Reflector.js';

const FOG_COLOR = new THREE.Color('#3a0a4a');
const FOG_DENSITY = 0.0014;
const AVENUE = 13;
const PINK = new THREE.Color(1.6, 0.15, 0.9);
const CYAN = new THREE.Color(0.15, 1.2, 1.6);
const VIOLET = new THREE.Color(0.7, 0.25, 1.6);
const BEACON = new THREE.Color(2.2, 0.1, 0.15);

const sharedGlsl = /* glsl */ `
    uniform vec3 uFogColor;
    uniform float uFogDensity;
    uniform float uTime;
    uniform float uLights;
    uniform float uNeon;
    varying float vDepth;
    vec3 applyFog(vec3 col) {
        float f = 1.0 - exp(-uFogDensity * uFogDensity * vDepth * vDepth);
        return mix(col, uFogColor, clamp(f, 0.0, 1.0));
    }
    float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
`;

const time = { value: 0 };
const flow = { value: 0 };
const lights = { value: 1 };
const neon = { value: 1 };
const beams = { value: 1 };
const skyUniforms = {
    uHorizon: { value: new THREE.Color() },
    uMid: { value: new THREE.Color() },
    uHigh: { value: new THREE.Color() },
    uZenith: { value: new THREE.Color() },
    uStars: { value: 1 },
    uSun: { value: new THREE.Vector3() },
    uSunLow: { value: 0 },
    uMoon: { value: new THREE.Vector3() },
};

const linear = (r, g, b) => new THREE.Color().setRGB(r, g, b);
const night = { horizon: linear(0.12, 0.03, 0.2), mid: linear(0.05, 0.01, 0.14), high: linear(0.02, 0.005, 0.07), zenith: linear(0.005, 0, 0.02), fog: new THREE.Color('#1a0633'), lights: 1.5, neon: 1.15, stars: 1 };
const dawn = { horizon: linear(1, 0.35, 0.12), mid: linear(0.5, 0.1, 0.3), high: linear(0.12, 0.04, 0.3), zenith: linear(0.02, 0.01, 0.1), fog: new THREE.Color('#4a1848'), lights: 1, neon: 0.9, stars: 0.3 };
const morning = { horizon: linear(1, 0.55, 0.6), mid: linear(0.55, 0.25, 0.65), high: linear(0.2, 0.18, 0.6), zenith: linear(0.06, 0.06, 0.3), fog: new THREE.Color('#6a3c80'), lights: 0.55, neon: 0.65, stars: 0 };
const noon = { horizon: linear(1, 0.62, 0.8), mid: linear(0.6, 0.35, 0.85), high: linear(0.25, 0.3, 0.85), zenith: linear(0.08, 0.1, 0.45), fog: new THREE.Color('#7a5098'), lights: 0.45, neon: 0.55, stars: 0 };
const dusk = { horizon: linear(0.95, 0.32, 0.18), mid: linear(0.45, 0.06, 0.4), high: linear(0.08, 0.02, 0.2), zenith: linear(0.02, 0, 0.06), fog: new THREE.Color('#3a0a4a'), lights: 1, neon: 1, stars: 0.5 };
const MOODS = [[0, night], [5, night], [6.5, dawn], [9, morning], [13, noon], [16.5, morning], [19, dusk], [21, night], [24, night]];

function currentHour() {
    const override = Number.parseFloat(new URLSearchParams(location.search).get('hour'));
    if (Number.isFinite(override)) return ((override % 24) + 24) % 24;
    const now = new Date();

    return now.getHours() + now.getMinutes() / 60;
}

function applyMood(hour) {
    const i = MOODS.findIndex(([h]) => h > hour);
    const [h0, a] = MOODS[i - 1];
    const [h1, b] = MOODS[i];
    const t = THREE.MathUtils.smoothstep(hour, h0, h1);

    skyUniforms.uHorizon.value.lerpColors(a.horizon, b.horizon, t);
    skyUniforms.uMid.value.lerpColors(a.mid, b.mid, t);
    skyUniforms.uHigh.value.lerpColors(a.high, b.high, t);
    skyUniforms.uZenith.value.lerpColors(a.zenith, b.zenith, t);
    skyUniforms.uStars.value = THREE.MathUtils.lerp(a.stars, b.stars, t);
    FOG_COLOR.lerpColors(a.fog, b.fog, t);
    lights.value = THREE.MathUtils.lerp(a.lights, b.lights, t);
    neon.value = THREE.MathUtils.lerp(a.neon, b.neon, t);

    const day = (hour - 6) / 14;
    const up = day >= 0 && day <= 1;
    const elevation = up ? Math.sin(day * Math.PI) * 0.4 - 0.04 : -0.5;
    const azimuth = THREE.MathUtils.lerp(-0.32, 0.32, THREE.MathUtils.clamp(day, 0, 1));
    skyUniforms.uSun.value.set(Math.sin(azimuth) * Math.cos(elevation), Math.sin(elevation), -Math.cos(azimuth) * Math.cos(elevation));
    skyUniforms.uSunLow.value = 1 - THREE.MathUtils.smoothstep(elevation, 0, 0.3);
    beams.value = THREE.MathUtils.clamp((neon.value - 0.5) * 1.6, 0.12, 1);

    const late = (((hour - 20) % 24) + 24) % 24 / 10;
    const moonElevation = late <= 1 ? Math.sin(late * Math.PI) * 0.5 - 0.04 : -0.5;
    const moonAzimuth = THREE.MathUtils.lerp(-0.6, 0.6, THREE.MathUtils.clamp(late, 0, 1));
    skyUniforms.uMoon.value.set(Math.sin(moonAzimuth) * Math.cos(moonElevation), Math.sin(moonElevation), -Math.cos(moonAzimuth) * Math.cos(moonElevation));
}

const sharedUniforms = () => ({
    uFogColor: { value: FOG_COLOR },
    uFogDensity: { value: FOG_DENSITY },
    uTime: time,
    uLights: lights,
    uNeon: neon,
});

const worldVertex = /* glsl */ `
    varying vec3 vWorld;
    varying vec3 vNormal;
    varying vec3 vColor;
    varying vec3 vLocal;
    varying float vRadius;
    varying float vDepth;
    void main() {
        mat4 model = modelMatrix;
        #ifdef USE_INSTANCING
            model = model * instanceMatrix;
        #endif
        vColor = vec3(1.0);
        #ifdef USE_INSTANCING_COLOR
            vColor = instanceColor;
        #endif
        vLocal = position;
        vRadius = length(model[0].xyz) * 0.5;
        vec4 world = model * vec4(position, 1.0);
        vWorld = world.xyz;
        vNormal = normalize(mat3(model) * normal);
        vec4 view = viewMatrix * world;
        vDepth = -view.z;
        gl_Position = projectionMatrix * view;
    }
`;

function mulberry32(seed) {
    return () => {
        seed = (seed + 0x6d2b79f5) | 0;
        let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

function layout(rand) {
    const parts = { box: [], round: [], cone: [] };
    const towers = [];
    const lot = 22;

    for (let z = 12; z > -330; z -= lot) {
        for (let x = -440; x <= 440; x += lot) {
            const cx = x + (rand() - 0.5) * 6;
            if (Math.abs(cx) < AVENUE + 8) continue;
            const cz = z + (rand() - 0.5) * 6;
            const near = Math.max(0, 1 - Math.hypot(cx * 0.6, cz) / 260);
            const h = (20 + rand() * 70 + near * 110) * (0.12 + 0.88 * Math.min(1, Math.max(0, (Math.abs(cx) - 20) / 170)) ** 0.8);
            const style = new THREE.Color(rand(), rand(), 0.12 + rand() * 0.22);
            const kind = rand();
            let w = 10 + rand() * 9;
            let d = 10 + rand() * 9;

            if (kind < 0.14) {
                w = d = Math.min(w, d);
                parts.round.push({ x: cx, y: 0, z: cz, w, h, d, style });
                towers.push({ kind: 'round', x: cx, z: cz, w, d, h, top: h });
                continue;
            }

            parts.box.push({ x: cx, y: 0, z: cz, w, h, d, style });
            const tower = { kind: 'box', x: cx, z: cz, w, d, h, top: h, sections: [{ y: 0, w, h, d }] };

            if (kind < 0.45) {
                let y = h;
                let sw = w;
                let sd = d;
                for (let step = 0; step < 1 + Math.floor(rand() * 2); step++) {
                    sw *= 0.65 + rand() * 0.15;
                    sd *= 0.65 + rand() * 0.15;
                    const sh = h * (0.2 + rand() * 0.25);
                    parts.box.push({ x: cx, y, z: cz, w: sw, h: sh, d: sd, style });
                    tower.sections.push({ y, w: sw, h: sh, d: sd });
                    y += sh;
                }
                tower.top = y;
            } else if (kind < 0.58) {
                const ch = Math.min(w, d) * (0.6 + rand() * 0.6);
                parts.cone.push({ x: cx, y: h, z: cz, w, h: ch, d, style });
                tower.top = h + ch;
            }

            if (tower.top > 110 && rand() < 0.45) {
                const sh = 12 + rand() * 26;
                parts.box.push({ x: cx, y: tower.top, z: cz, w: 0.6, h: sh, d: 0.6, style: new THREE.Color(0, 0, 0) });
                tower.spire = tower.top + sh;
            }
            towers.push(tower);
        }
    }

    return { parts, towers };
}

function towerMaterial(round) {
    return new THREE.ShaderMaterial({
        uniforms: sharedUniforms(),
        defines: round ? { ROUND: '' } : {},
        vertexShader: worldVertex,
        fragmentShader: /* glsl */ `
            varying vec3 vWorld;
            varying vec3 vNormal;
            varying vec3 vColor;
            varying vec3 vLocal;
            varying float vRadius;
            ${sharedGlsl}
            void main() {
                float style = floor(vColor.x * 4.0);
                float tint = floor(vColor.y * 4.0);
                float density = vColor.z;

                vec3 base = tint < 1.0 ? vec3(0.16, 0.05, 0.32) : tint < 2.0 ? vec3(0.05, 0.08, 0.36) : tint < 3.0 ? vec3(0.28, 0.04, 0.26) : vec3(0.03, 0.12, 0.25);
                vec3 glow = tint < 1.0 ? vec3(1.0, 0.7, 0.45) : tint < 2.0 ? vec3(0.35, 0.85, 1.0) : tint < 3.0 ? vec3(1.0, 0.4, 0.85) : vec3(0.4, 1.0, 0.9);
                float facing = vNormal.z > 0.5 ? 1.0 : (abs(vNormal.y) > 0.5 ? 0.7 : 0.55);
                vec3 col = mix(base * 0.18, base, 0.25 + clamp(vWorld.y / 220.0, 0.0, 1.0) * 0.75) * facing;

                #ifdef ROUND
                    float u = atan(vLocal.x, vLocal.z) * vRadius;
                #else
                    float u = abs(vNormal.x) > 0.5 ? vWorld.z : vWorld.x;
                #endif
                vec2 size = style < 1.0 ? vec2(0.9, 1.4) : style < 2.0 ? vec2(3.5, 1.6) : style < 3.0 ? vec2(1.6, 5.0) : vec2(2.4, 3.0);
                vec4 pane = style < 1.0 ? vec4(0.2, 0.8, 0.3, 0.75) : style < 2.0 ? vec4(0.0, 1.0, 0.35, 0.7) : style < 3.0 ? vec4(0.4, 0.6, 0.05, 0.95) : vec4(0.12, 0.88, 0.15, 0.85);
                vec2 grid = vec2(u, vWorld.y) / size;
                vec2 fw = fwidth(grid);
                vec2 f = fract(grid);
                vec2 cell = floor(grid);
                float window = step(pane.x, f.x) * step(f.x, pane.y) * step(pane.z, f.y) * step(f.y, pane.w);
                float seed = hash(cell + vColor.xy * 97.0);
                float lit = step(1.0 - density * uLights, hash(cell + floor((uTime + seed * 90.0) / 9.0)));
                float far = smoothstep(0.3, 0.9, max(fw.x, fw.y));
                float coverage = (pane.y - pane.x) * (pane.w - pane.z) * density * uLights;
                float wall = 1.0 - step(0.5, abs(vNormal.y));
                col += glow * mix(window * lit, coverage, far) * wall * 0.6;

                gl_FragColor = vec4(applyFog(col), 1.0);
            }
        `,
    });
}

function instanced(geometry, material, items, place) {
    const mesh = new THREE.InstancedMesh(geometry, material, items.length);
    const matrix = new THREE.Matrix4();
    items.forEach((item, i) => {
        place(matrix, item);
        mesh.setMatrixAt(i, matrix);
        if (item.style || item.color) mesh.setColorAt(i, item.style ?? item.color);
    });

    return mesh;
}

function towerMeshes({ box, round, cone }) {
    const boxMaterial = towerMaterial(false);
    const stack = (m, p) => m.makeScale(p.w, p.h, p.d).setPosition(p.x, p.y + p.h / 2, p.z);
    const coneGeometry = new THREE.ConeGeometry(Math.SQRT1_2, 1, 4, 1).rotateY(Math.PI / 4);

    return [
        instanced(new THREE.BoxGeometry(1, 1, 1), boxMaterial, box, stack),
        instanced(new THREE.CylinderGeometry(0.5, 0.5, 1, 28), towerMaterial(true), round, stack),
        instanced(coneGeometry, boxMaterial, cone, stack),
    ];
}

function neonMaterial() {
    return new THREE.ShaderMaterial({
        uniforms: sharedUniforms(),
        vertexShader: worldVertex,
        fragmentShader: /* glsl */ `
            varying vec3 vWorld;
            varying vec3 vColor;
            ${sharedGlsl}
            void main() {
                float seed = hash(floor(vWorld.xz * 0.5));
                float scan = smoothstep(0.9, 1.0, fract(vWorld.y / 160.0 - uTime * (0.05 + seed * 0.05) + seed));
                float pulse = 0.75 + 0.25 * sin(uTime * (0.6 + seed) + seed * 40.0);
                gl_FragColor = vec4(applyFog(vColor * (pulse + scan * 1.5) * uNeon), 1.0);
            }
        `,
    });
}

function neonMeshes(towers, rand) {
    const strips = [];
    const rings = [];
    const add = (x, y, z, w, h, d, color) => strips.push({ x, y, z, w, h, d, color });

    for (const t of towers) {
        const color = [PINK, CYAN, VIOLET][Math.floor(rand() * 3)];
        const lit = t.z > -40 || rand() < 0.4;

        if (t.kind === 'round') {
            if (lit) rings.push({ x: t.x, y: t.h, z: t.z, w: t.w, color });
            for (let y = t.h * 0.25; y < t.h; y += t.h * 0.25) if (rand() < 0.35) rings.push({ x: t.x, y, z: t.z, w: t.w + 0.2, color });
        } else if (lit) {
            const top = t.sections.at(-1);
            for (const s of t.sections) {
                for (const [sx, sz] of [[1, 1], [-1, 1], [1, -1], [-1, -1]]) {
                    if (rand() < (sz > 0 ? 0.75 : 0.35)) add(t.x + (sx * s.w) / 2, s.y + s.h / 2, t.z + (sz * s.d) / 2, 0.3, s.h, 0.3, color);
                }
            }
            const roof = top.y + top.h;
            add(t.x, roof, t.z + top.d / 2, top.w, 0.3, 0.3, color);
            add(t.x, roof, t.z - top.d / 2, top.w, 0.3, 0.3, color);
            add(t.x + top.w / 2, roof, t.z, 0.3, 0.3, top.d, color);
            add(t.x - top.w / 2, roof, t.z, 0.3, 0.3, top.d, color);
            if (rand() < 0.3) add(t.x, t.h * (0.2 + rand() * 0.6), t.z + t.d / 2 + 0.05, t.w, 0.25, 0.25, color === PINK ? CYAN : PINK);
        }

        if (t.spire) add(t.x, t.spire, t.z, 0.9, 0.9, 0.9, BEACON);
    }

    const material = neonMaterial();
    return [
        instanced(new THREE.BoxGeometry(1, 1, 1), material, strips, (m, s) => m.makeScale(s.w, s.h, s.d).setPosition(s.x, s.y, s.z)),
        instanced(new THREE.TorusGeometry(0.5, 0.012, 6, 40).rotateX(Math.PI / 2), material, rings, (m, r) => m.makeScale(r.w, r.w, r.w).setPosition(r.x, r.y, r.z)),
    ];
}

function terrainMesh() {
    const geometry = new THREE.PlaneGeometry(1000, 240, 200, 48);
    geometry.rotateX(-Math.PI / 2);
    geometry.translate(0, 0, 130);

    return new THREE.Mesh(
        geometry,
        new THREE.ShaderMaterial({
            uniforms: { ...sharedUniforms(), uOffset: flow },
            vertexShader: /* glsl */ `
                uniform float uOffset;
                varying vec3 vWorld;
                varying float vDepth;
                float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
                float noise(vec2 p) {
                    vec2 i = floor(p);
                    vec2 f = fract(p);
                    f = f * f * (3.0 - 2.0 * f);
                    return mix(mix(hash(i), hash(i + vec2(1.0, 0.0)), f.x), mix(hash(i + vec2(0.0, 1.0)), hash(i + vec2(1.0, 1.0)), f.x), f.y);
                }
                void main() {
                    vec3 p = position;
                    vec2 q = vec2(p.x, p.z - uOffset);
                    float n = noise(q * 0.025) * 0.7 + noise(q * 0.07) * 0.3;
                    p.y = n * 28.0 * smoothstep(12.0, 70.0, abs(p.x)) * smoothstep(15.0, 80.0, p.z);
                    vec4 world = modelMatrix * vec4(p, 1.0);
                    vWorld = vec3(world.x, world.y, world.z - uOffset);
                    vec4 view = viewMatrix * world;
                    vDepth = -view.z;
                    gl_Position = projectionMatrix * view;
                }
            `,
            fragmentShader: /* glsl */ `
                varying vec3 vWorld;
                ${sharedGlsl}
                void main() {
                    vec2 cell = vWorld.xz / 7.0;
                    vec2 fw = max(fwidth(cell), vec2(1e-4));
                    vec2 g = abs(fract(cell - 0.5) - 0.5) / fw;
                    float line = 1.0 - min(min(g.x, g.y) * 0.8, 1.0);
                    float far = smoothstep(0.12, 0.45, max(fw.x, fw.y));
                    vec3 col = vec3(0.03, 0.005, 0.07) + vec3(1.5, 0.15, 0.95) * mix(line, 0.14, far) * uNeon;
                    gl_FragColor = vec4(applyFog(col), 1.0);
                }
            `,
        }),
    );
}

function mirrorFloor() {
    const mirror = new Reflector(new THREE.PlaneGeometry(900, 900), { textureWidth: 512, textureHeight: 512, color: 0x55506a });
    mirror.rotation.x = -Math.PI / 2;
    mirror.position.y = -0.5;
    mirror.userData.resize = (w, h) => mirror.getRenderTarget().setSize(w, h);

    return mirror;
}

function skyMesh() {
    const mesh = new THREE.Mesh(
        new THREE.SphereGeometry(1200, 48, 24),
        new THREE.ShaderMaterial({
            side: THREE.BackSide,
            depthWrite: false,
            uniforms: { uTime: time, ...skyUniforms },
            vertexShader: /* glsl */ `
                varying vec3 vDir;
                void main() {
                    vDir = normalize(position);
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
            `,
            fragmentShader: /* glsl */ `
                uniform float uTime;
                uniform vec3 uSun;
                uniform vec3 uMoon;
                uniform float uSunLow;
                uniform float uStars;
                uniform vec3 uHorizon;
                uniform vec3 uMid;
                uniform vec3 uHigh;
                uniform vec3 uZenith;
                varying vec3 vDir;
                float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
                void main() {
                    vec3 d = normalize(vDir);
                    float y = max(d.y, 0.0);
                    vec3 col = mix(uHorizon, uMid, smoothstep(0.0, 0.1, y));
                    col = mix(col, uHigh, smoothstep(0.08, 0.3, y));
                    col = mix(col, uZenith, smoothstep(0.3, 0.7, y));

                    vec2 cell = floor(vec2(atan(d.x, d.z), d.y) * 180.0);
                    col += step(0.996, hash(cell)) * (0.5 + 0.5 * sin(uTime * 2.0 + hash(cell + 3.0) * 60.0)) * smoothstep(0.15, 0.4, y) * uStars;

                    vec3 right = normalize(cross(uSun, vec3(0.0, 1.0, 0.0)));
                    vec3 up = cross(right, uSun);
                    vec2 s = vec2(dot(d, right), dot(d, up)) / 0.3;
                    float r = length(s);
                    if (dot(d, uSun) > 0.0) {
                        col += vec3(1.0, 0.2, 0.55) * exp(-r * 1.6) * 0.28;
                        if (r < 1.0) {
                            float t = clamp(-s.y, 0.0, 1.0);
                            float cut = step(0.0, -s.y) * step(fract(t * 7.0), t * 0.6);
                            vec3 sun = mix(mix(vec3(0.95, 0.55, 0.08), vec3(1.0, 0.38, 0.06), uSunLow), vec3(0.95, 0.06, 0.42), (1.0 - s.y) * 0.5);
                            col = mix(sun, col, cut);
                        }
                    }

                    vec3 mr = normalize(cross(uMoon, vec3(0.0, 1.0, 0.0)));
                    vec2 m = vec2(dot(d, mr), dot(d, cross(mr, uMoon))) / 0.075;
                    if (dot(d, uMoon) > 0.0 && uMoon.y > -0.1) {
                        float mlen = length(m);
                        col += vec3(0.55, 0.45, 1.0) * exp(-mlen * 0.9) * 0.25;
                        if (mlen < 1.0) {
                            float crater = hash(floor(m * 4.0 + 7.0)) * 0.12;
                            col = mix(vec3(0.95, 0.9, 1.05), vec3(0.7, 0.66, 0.9), crater + smoothstep(0.6, 1.0, mlen) * 0.2);
                        }
                    }

                    float slot = floor(uTime / 7.0);
                    float progress = fract(uTime / 7.0) * 7.0;
                    if (progress < 0.9 && hash(vec2(slot, 2.0)) > 0.35 && uStars > 0.2) {
                        vec2 sky = vec2(atan(d.x, -d.z), asin(d.y));
                        vec2 start = vec2((hash(vec2(slot, 0.0)) - 0.5) * 1.4, 0.35 + hash(vec2(slot, 1.0)) * 0.35);
                        vec2 dir = normalize(vec2(hash(vec2(slot, 3.0)) > 0.5 ? 1.0 : -1.0, -0.45));
                        vec2 head = start + dir * progress / 0.9 * 0.35;
                        vec2 tail = head - dir * 0.12;
                        vec2 pa = sky - tail;
                        vec2 ba = head - tail;
                        float h = clamp(dot(pa, ba) / dot(ba, ba), 0.0, 1.0);
                        float streak = smoothstep(0.004, 0.0, length(pa - ba * h)) * h * (1.0 - progress / 0.9);
                        col += vec3(1.2, 1.1, 1.4) * streak * uStars;
                    }
                    gl_FragColor = vec4(col, 1.0);
                }
            `,
        }),
    );
    mesh.renderOrder = -1;

    return mesh;
}

function canvasTexture(width, height, draw) {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    Promise.resolve(draw(canvas.getContext('2d'))).then(() => (texture.needsUpdate = true));

    return texture;
}

async function drawSign(ctx, text, color) {
    try {
        await document.fonts.load('120px Monoton');
    } catch {}
    ctx.font = '120px Monoton, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.shadowColor = color;
    ctx.shadowBlur = 28;
    ctx.fillStyle = '#fff';
    ctx.fillText(text, 512, 130);
    ctx.shadowBlur = 12;
    ctx.strokeStyle = color;
    ctx.lineWidth = 8;
    ctx.strokeRect(20, 20, 984, 220);
}

function signs(towers) {
    const group = new THREE.Group();
    const hosts = towers.filter((t) => t.kind === 'box' && t.z > -40 && Math.abs(t.x) < 140 && t.h > 50);
    [['1982', '#ff3cac'], ['LARAVEL', '#3ef2ff'], ['OPEN SOURCE', '#ff3cac'], ['CATS', '#3ef2ff']].forEach(([text, color], i) => {
        const tower = hosts[Math.floor(((i + 0.5) / 4) * hosts.length)];
        if (!tower) return;
        const sign = new THREE.Mesh(
            new THREE.PlaneGeometry(tower.w * 0.9, tower.w * 0.23),
            new THREE.MeshBasicMaterial({
                map: canvasTexture(1024, 260, (ctx) => drawSign(ctx, text, color)),
                transparent: true,
                depthWrite: false,
                color: new THREE.Color(1.1, 1.1, 1.1),
            }),
        );
        sign.position.set(tower.x, tower.h * 0.55, tower.z + tower.d / 2 + 0.3);
        group.add(sign);
    });

    return group;
}

function trafficMesh(rand, count) {
    const colors = [new THREE.Color(3, 3, 3), new THREE.Color(3, 0.3, 0.45), CYAN];
    const cars = Array.from({ length: count }, () => ({
        x: (rand() * 2 - 1) * 300,
        y: 25 + rand() * 110,
        z: -20 - rand() * 220,
        v: (rand() > 0.5 ? 1 : -1) * (8 + rand() * 16),
    }));
    const mesh = new THREE.InstancedMesh(new THREE.BoxGeometry(3, 0.3, 0.5), new THREE.MeshBasicMaterial(), count);
    cars.forEach((_, i) => mesh.setColorAt(i, colors[i % colors.length]));
    mesh.frustumCulled = false;
    const matrix = new THREE.Matrix4();
    mesh.userData.update = (dt) => {
        cars.forEach((car, i) => {
            car.x += car.v * dt;
            if (Math.abs(car.x) > 300) car.x = -Math.sign(car.x) * 300;
            mesh.setMatrixAt(i, matrix.makeTranslation(car.x, car.y, car.z));
        });
        mesh.instanceMatrix.needsUpdate = true;
    };

    return mesh;
}

function searchlights(towers, rand) {
    const geometry = new THREE.ConeGeometry(26, 420, 24, 1, true).rotateX(Math.PI).translate(0, 210, 0);
    const hosts = towers.filter((t) => t.kind !== 'round' && Math.abs(t.x) > 40 && Math.abs(t.x) < 240 && t.z < -60 && t.z > -220 && t.top < 110).sort((a, b) => b.top - a.top).filter((_, i) => i % 2 === 0).slice(0, 6);

    return hosts.map((t, i) => {
        const beam = new THREE.Mesh(
            geometry,
            new THREE.ShaderMaterial({
                uniforms: { uColor: { value: i % 2 ? CYAN : PINK }, uBeams: beams },
                transparent: true,
                depthWrite: false,
                side: THREE.DoubleSide,
                blending: THREE.AdditiveBlending,
                vertexShader: /* glsl */ `
                    varying float vAlong;
                    varying float vFacing;
                    void main() {
                        vAlong = position.y / 420.0;
                        vec4 view = modelViewMatrix * vec4(position, 1.0);
                        vFacing = abs(dot(normalize(normalMatrix * normal), normalize(-view.xyz)));
                        gl_Position = projectionMatrix * view;
                    }
                `,
                fragmentShader: /* glsl */ `
                    uniform vec3 uColor;
                    uniform float uBeams;
                    varying float vAlong;
                    varying float vFacing;
                    void main() {
                        float fade = pow(1.0 - vAlong, 1.6) * smoothstep(0.0, 0.03, vAlong);
                        gl_FragColor = vec4(uColor * 0.6, fade * (0.25 + 0.75 * pow(vFacing, 1.5)) * 0.35 * uBeams);
                    }
                `,
            }),
        );
        beam.position.set(t.x, t.spire ?? t.top, t.z);
        beam.userData = { tilt: Math.sign(t.x) * (0.15 + rand() * 0.25), speed: 0.12 + rand() * 0.15, phase: rand() * 10 };

        return beam;
    });
}

function car() {
    const body = new THREE.Shape([
        [0, 0.35], [0, 1.1], [0.4, 1.3], [2.6, 1.4], [3.3, 2.15], [5.0, 2.15], [6.3, 1.45], [8, 0.95], [8, 0.35],
    ].map(([x, y]) => new THREE.Vector2(x, y)));
    const geometry = new THREE.ExtrudeGeometry(body, { depth: 3.6, bevelEnabled: false }).rotateY(Math.PI / 2).translate(-1.8, 0, 0);

    const group = new THREE.Group();
    group.add(
        new THREE.Mesh(geometry, new THREE.MeshBasicMaterial({ color: '#0b0620' })),
        new THREE.LineSegments(new THREE.EdgesGeometry(geometry, 20), new THREE.LineBasicMaterial({ color: new THREE.Color(2.2, 0.3, 1.4) })),
    );

    const wheel = new THREE.CylinderGeometry(0.6, 0.6, 0.5, 18).rotateZ(Math.PI / 2);
    const tyre = new THREE.MeshBasicMaterial({ color: '#050210' });
    for (const [x, z] of [[-1.8, -1.5], [1.8, -1.5], [-1.8, -6.5], [1.8, -6.5]]) {
        const mesh = new THREE.Mesh(wheel, tyre);
        mesh.position.set(x, 0.6, z);
        group.add(mesh);
    }

    const lamp = new THREE.MeshBasicMaterial({ color: new THREE.Color(4, 0.15, 0.3) });
    for (const x of [-1.1, 1.1]) {
        const light = new THREE.Mesh(new THREE.BoxGeometry(1.1, 0.2, 0.1), lamp);
        light.position.set(x, 0.95, 0.05);
        group.add(light);
    }

    const glow = (width, length, color, z) => {
        const mesh = new THREE.Mesh(
            new THREE.PlaneGeometry(width, length).rotateX(-Math.PI / 2),
            new THREE.ShaderMaterial({
                uniforms: { uColor: { value: color } },
                transparent: true,
                depthWrite: false,
                blending: THREE.AdditiveBlending,
                vertexShader: 'varying vec2 vUv; void main() { vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }',
                fragmentShader: 'uniform vec3 uColor; varying vec2 vUv; void main() { vec2 p = vUv * 2.0 - 1.0; gl_FragColor = vec4(uColor, pow(max(0.0, 1.0 - dot(p, p)), 2.0) * 0.6); }',
            }),
        );
        mesh.position.set(0, 0.06, z);
        group.add(mesh);
    };
    glow(6, 11, new THREE.Color(1, 0.1, 0.6), -4);
    glow(3.4, 9, new THREE.Color(1, 0.05, 0.1), 4.5);

    group.position.set(0, 0, 215);
    group.userData.update = (t) => {
        group.position.x = Math.sin(t * 0.35) * 1.4;
        group.position.y = Math.sin(t * 9) * 0.03;
        group.rotation.y = Math.cos(t * 0.35) * 0.03;
    };

    return group;
}

function supportsWebGL() {
    try {
        return !!document.createElement('canvas').getContext('webgl2');
    } catch {
        return false;
    }
}

function start(host, still) {
    const canvas = document.createElement('canvas');
    canvas.className = 'city3d';
    canvas.setAttribute('aria-hidden', 'true');
    host.prepend(canvas);

    const renderer = new THREE.WebGLRenderer({ canvas, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, innerWidth < 768 ? 1 : 1.5));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;

    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(FOG_COLOR, FOG_DENSITY);
    scene.fog.color = FOG_COLOR;

    const camera = new THREE.PerspectiveCamera(68, 1, 0.5, 2500);
    camera.rotation.order = 'YXZ';
    camera.position.set(0, 10, 235);

    const rand = mulberry32(1982);
    const { parts, towers } = layout(rand);
    const sky = skyMesh();
    applyMood(currentHour());
    let moodClock = 0;
    const mirror = mirrorFloor();
    const traffic = trafficMesh(rand, 60);
    const spots = searchlights(towers, rand);
    const ride = car();
    scene.add(sky, mirror, terrainMesh(), traffic, ride, signs(towers), ...towerMeshes(parts), ...neonMeshes(towers, rand), ...spots);

    const composer = new EffectComposer(renderer, new THREE.WebGLRenderTarget(1, 1, { type: THREE.HalfFloatType, samples: 4 }));
    const bloom = new UnrealBloomPass(new THREE.Vector2(1, 1), 0.6, 0.4, 0.8);
    composer.addPass(new RenderPass(scene, camera));
    composer.addPass(bloom);
    composer.addPass(new OutputPass());

    const resize = () => {
        const { clientWidth: w, clientHeight: h } = host;
        const ratio = renderer.getPixelRatio();
        renderer.setSize(w, h, false);
        composer.setSize(w, h);
        bloom.resolution.set(w / 2, h / 2);
        mirror.userData.resize((w * ratio) / 2, (h * ratio) / 2);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    };
    new ResizeObserver(resize).observe(host);
    resize();

    const pointer = { x: 0, y: 0 };
    const look = { x: 0, y: 0 };
    addEventListener('pointermove', (e) => {
        pointer.x = (e.clientX / innerWidth) * 2 - 1;
        pointer.y = (e.clientY / innerHeight) * 2 - 1;
    }, { passive: true });

    let visible = true;
    new IntersectionObserver(([entry]) => (visible = entry.isIntersecting)).observe(host);

    const clock = new THREE.Clock();
    renderer.setAnimationLoop(() => {
        const dt = Math.min(clock.getDelta(), 0.1);
        if (!visible) return;

        time.value += dt;
        if ((moodClock += dt) > 1) {
            moodClock = 0;
            applyMood(currentHour());
        }
        flow.value += dt * 14;
        look.x += (pointer.x - look.x) * 0.04;
        look.y += (pointer.y - look.y) * 0.04;
        camera.rotation.set(0.04 - look.y * 0.08 + Math.sin(time.value * 0.15) * 0.01, -look.x * 0.18 + Math.sin(time.value * 0.1) * 0.02, 0);
        sky.position.copy(camera.position);
        traffic.userData.update(dt);
        ride.userData.update(time.value);
        for (const beam of spots) {
            const { tilt, speed, phase } = beam.userData;
            beam.rotation.set(-0.35 + Math.sin(time.value * speed * 0.7 + phase) * 0.15, 0, tilt + Math.sin(time.value * speed + phase) * 0.45);
        }

        composer.render();
        host.classList.add('is-3d');
        if (still) renderer.setAnimationLoop(null);
    });
}

const host = document.querySelector('.drive');
if (host && supportsWebGL()) {
    start(host, matchMedia('(prefers-reduced-motion: reduce)').matches);
}
