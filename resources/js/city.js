import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
import { Reflector } from 'three/addons/objects/Reflector.js';

const FOG_COLOR = new THREE.Color('#1c0738');
const FOG_DENSITY = 0.003;
const AVENUE = 13;
const PINK = new THREE.Color(1.6, 0.15, 0.9);
const CYAN = new THREE.Color(0.15, 1.2, 1.6);
const VIOLET = new THREE.Color(0.7, 0.25, 1.6);

const sharedGlsl = /* glsl */ `
    uniform vec3 uFogColor;
    uniform float uFogDensity;
    uniform float uTime;
    varying float vDepth;
    vec3 applyFog(vec3 col) {
        float f = 1.0 - exp(-uFogDensity * uFogDensity * vDepth * vDepth);
        return mix(col, uFogColor, clamp(f, 0.0, 1.0));
    }
    float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
`;

const time = { value: 0 };
const sharedUniforms = () => ({
    uFogColor: { value: FOG_COLOR },
    uFogDensity: { value: FOG_DENSITY },
    uTime: time,
});

const worldVertex = /* glsl */ `
    varying vec3 vWorld;
    varying vec3 vNormal;
    varying vec3 vColor;
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
    const towers = [];
    const lot = 22;
    for (let z = 12; z > -330; z -= lot) {
        for (let x = -132; x <= 132; x += lot) {
            const cx = x + (rand() - 0.5) * 6;
            if (Math.abs(cx) < AVENUE + 8) continue;
            const w = 10 + rand() * 9;
            const d = 10 + rand() * 9;
            const near = Math.max(0, 1 - Math.hypot(cx, z) / 260);
            towers.push({ x: cx, z: z + (rand() - 0.5) * 6, w, d, h: 40 + rand() * 120 + near * 140 });
        }
    }
    for (const x of [-48, -30, 30, 48]) {
        towers.push({ x, z: -300 - rand() * 60, w: 14 + rand() * 8, d: 16, h: 140 + rand() * 180 });
    }

    return towers;
}

function towerMesh(towers) {
    const material = new THREE.ShaderMaterial({
        uniforms: sharedUniforms(),
        vertexShader: worldVertex,
        fragmentShader: /* glsl */ `
            varying vec3 vWorld;
            varying vec3 vNormal;
            ${sharedGlsl}
            void main() {
                float height = clamp(vWorld.y / 260.0, 0.0, 1.0);
                float facing = vNormal.z > 0.5 ? 1.0 : (abs(vNormal.x) > 0.5 ? 0.55 : 0.8);
                vec3 col = mix(vec3(0.02, 0.01, 0.07), vec3(0.2, 0.06, 0.36), height) * facing;
                if (abs(vNormal.y) < 0.5) {
                    float u = abs(vNormal.x) > 0.5 ? vWorld.z : vWorld.x;
                    vec2 grid = vec2(u / 0.9, vWorld.y / 1.4);
                    vec2 cell = floor(grid);
                    vec2 f = fract(grid);
                    float window = step(0.2, f.x) * step(f.x, 0.8) * step(0.3, f.y) * step(f.y, 0.78);
                    float seed = hash(cell + floor(vWorld.xz / 11.0) * 7.3);
                    float lit = step(0.84, hash(cell + floor((uTime + seed * 90.0) / 9.0)));
                    vec3 tint = seed > 0.93 ? vec3(1.0, 0.35, 0.85) : (seed > 0.8 ? vec3(0.3, 0.85, 1.0) : vec3(0.75, 0.6, 1.0));
                    col += window * lit * tint * 0.45;
                }
                gl_FragColor = vec4(applyFog(col), 1.0);
            }
        `,
    });

    const mesh = new THREE.InstancedMesh(new THREE.BoxGeometry(1, 1, 1), material, towers.length);
    const matrix = new THREE.Matrix4();
    towers.forEach((t, i) => mesh.setMatrixAt(i, matrix.makeScale(t.w, t.h, t.d).setPosition(t.x, t.h / 2, t.z)));

    return mesh;
}

function neonMesh(towers, rand) {
    const strips = [];
    const add = (x, y, z, sx, sy, sz, color) => strips.push({ x, y, z, sx, sy, sz, color });

    for (const t of towers) {
        const color = [PINK, CYAN, VIOLET][Math.floor(rand() * 3)];
        const front = t.z + t.d / 2;
        const inner = t.x - (Math.sign(t.x) * t.w) / 2;
        if (rand() < 0.7) add(inner, t.h / 2, front, 0.35, t.h, 0.35, color);
        if (rand() < 0.4) add(-inner + 2 * t.x, t.h / 2, front, 0.35, t.h, 0.35, color);
        if (rand() < 0.5) add(t.x, t.h, front, t.w, 0.35, 0.35, color);
        for (let n = rand() * 3; n > 1; n--) add(t.x + (rand() - 0.5) * t.w * 0.7, t.h / 2, front + 0.05, 0.22, t.h, 0.22, color);
        if (rand() < 0.35) add(inner, t.h * (0.2 + rand() * 0.6), t.z, 0.25, 0.25, t.d, color);
    }

    for (const [x, z] of [[-AVENUE - 4, -30], [AVENUE + 5, -75]]) {
        add(x, 110, z, 0.6, 220, 0.6, CYAN);
    }

    const material = new THREE.ShaderMaterial({
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
                gl_FragColor = vec4(applyFog(vColor * (pulse + scan * 1.5)), 1.0);
            }
        `,
    });

    const mesh = new THREE.InstancedMesh(new THREE.BoxGeometry(1, 1, 1), material, strips.length);
    const matrix = new THREE.Matrix4();
    strips.forEach((s, i) => {
        mesh.setMatrixAt(i, matrix.makeScale(s.sx, s.sy, s.sz).setPosition(s.x, s.y, s.z));
        mesh.setColorAt(i, s.color);
    });

    return mesh;
}

function floor(width, height) {
    const group = new THREE.Group();
    const mirror = new Reflector(new THREE.PlaneGeometry(900, 900), {
        textureWidth: width,
        textureHeight: height,
        color: 0x55506a,
    });
    mirror.rotation.x = -Math.PI / 2;

    const grid = new THREE.Mesh(
        new THREE.PlaneGeometry(900, 900),
        new THREE.ShaderMaterial({
            uniforms: sharedUniforms(),
            transparent: true,
            depthWrite: false,
            vertexShader: worldVertex,
            fragmentShader: /* glsl */ `
                varying vec3 vWorld;
                ${sharedGlsl}
                float line(float v, float w) { return 1.0 - smoothstep(0.0, w, abs(v)); }
                void main() {
                    vec2 p = vWorld.xz / 6.0;
                    float g = max(line(fract(p.x) - 0.5, 0.012), line(fract(p.y) - 0.5, 0.012));
                    float edge = line(abs(vWorld.x) - ${AVENUE.toFixed(1)}, 0.1);
                    vec3 col = vec3(1.0, 0.25, 0.75) * g * 0.35 + vec3(0.3, 0.95, 1.0) * edge;
                    float fade = exp(-vDepth * 0.012);
                    gl_FragColor = vec4(col, max(g * 0.5, edge) * fade);
                }
            `,
        }),
    );
    grid.rotation.x = -Math.PI / 2;
    grid.position.y = 0.02;

    group.add(mirror, grid);
    group.userData.resize = (w, h) => mirror.getRenderTarget().setSize(w, h);

    return group;
}

function skyMesh() {
    const mesh = new THREE.Mesh(
        new THREE.SphereGeometry(700, 32, 16),
        new THREE.ShaderMaterial({
            side: THREE.BackSide,
            depthWrite: false,
            uniforms: { uTime: time },
            vertexShader: /* glsl */ `
                varying vec3 vDir;
                void main() {
                    vDir = normalize(position);
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
            `,
            fragmentShader: /* glsl */ `
                uniform float uTime;
                varying vec3 vDir;
                float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
                void main() {
                    float y = max(vDir.y, 0.0);
                    vec3 col = mix(vec3(0.55, 0.1, 0.5), vec3(0.18, 0.05, 0.42), smoothstep(0.0, 0.25, y));
                    col = mix(col, vec3(0.04, 0.01, 0.14), smoothstep(0.2, 0.8, y));
                    vec2 cell = floor(vec2(atan(vDir.x, vDir.z), vDir.y) * 180.0);
                    float star = step(0.996, hash(cell));
                    col += star * (0.5 + 0.5 * sin(uTime * 2.0 + hash(cell + 3.0) * 60.0)) * smoothstep(0.15, 0.4, y);
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

function drawPalm(ctx) {
    ctx.lineCap = 'round';
    ctx.shadowColor = '#3ef2ff';
    ctx.shadowBlur = 16;
    ctx.strokeStyle = '#c9fbff';
    ctx.lineWidth = 7;
    ctx.beginPath();
    ctx.moveTo(118, 512);
    ctx.quadraticCurveTo(110, 320, 140, 170);
    ctx.stroke();
    ctx.lineWidth = 6;
    for (const [ex, ey, cx, cy] of [
        [10, 230, 60, 130], [30, 120, 80, 90], [140, 40, 120, 90], [240, 110, 200, 90],
        [250, 230, 210, 140], [70, 300, 90, 190], [200, 300, 190, 190],
    ]) {
        ctx.beginPath();
        ctx.moveTo(140, 170);
        ctx.quadraticCurveTo(cx, cy, ex, ey);
        ctx.stroke();
    }
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

function glowPlane(texture, width, height) {
    return new THREE.Mesh(
        new THREE.PlaneGeometry(width, height),
        new THREE.MeshBasicMaterial({ map: texture, transparent: true, depthWrite: false, color: new THREE.Color(1.1, 1.1, 1.1) }),
    );
}

function decorations(towers, rand) {
    const group = new THREE.Group();
    const palm = canvasTexture(256, 512, drawPalm);
    for (let z = -12; z > -150; z -= 16 + rand() * 8) {
        for (const side of [-1, 1]) {
            const mesh = glowPlane(palm, 7, 14);
            mesh.position.set(side * (AVENUE - 1.5), 7, z - rand() * 6);
            mesh.scale.x = side;
            group.add(mesh);
        }
    }

    const hosts = towers.filter((t) => t.z < -15 && t.z > -140 && Math.abs(t.x) < 45).sort((a, b) => b.z - a.z);
    [['1982', '#ff3cac'], ['LARAVEL', '#3ef2ff'], ['OPEN SOURCE', '#ff3cac'], ['CATS', '#3ef2ff']].forEach(([text, color], i) => {
        const tower = hosts[(i * 3) % hosts.length];
        if (!tower) return;
        const sign = glowPlane(canvasTexture(1024, 260, (ctx) => drawSign(ctx, text, color)), tower.w * 0.9, tower.w * 0.23);
        sign.position.set(tower.x, 18 + i * 9, tower.z + tower.d / 2 + 0.3);
        group.add(sign);
    });

    return group;
}

function trafficMesh(rand, count) {
    const colors = [new THREE.Color(3, 3, 3), new THREE.Color(3, 0.3, 0.45), CYAN];
    const cars = Array.from({ length: count }, () => ({
        x: (rand() * 2 - 1) * 140,
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
            if (Math.abs(car.x) > 140) car.x = -Math.sign(car.x) * 140;
            mesh.setMatrixAt(i, matrix.makeTranslation(car.x, car.y, car.z));
        });
        mesh.instanceMatrix.needsUpdate = true;
    };

    return mesh;
}

function supportsWebGL() {
    try {
        return !!document.createElement('canvas').getContext('webgl2');
    } catch {
        return false;
    }
}

function start(host) {
    const canvas = document.createElement('canvas');
    canvas.className = 'city3d';
    canvas.setAttribute('aria-hidden', 'true');
    host.prepend(canvas);

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, innerWidth < 768 ? 1 : 1.5));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;

    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(FOG_COLOR, FOG_DENSITY);

    const camera = new THREE.PerspectiveCamera(68, 1, 0.5, 1500);
    camera.rotation.order = 'YXZ';
    camera.position.set(0, 2.2, 14);

    const rand = mulberry32(1982);
    const towers = layout(rand);
    const ground = floor(512, 512);
    const traffic = trafficMesh(rand, 40);
    scene.add(skyMesh(), towerMesh(towers), neonMesh(towers, rand), decorations(towers, rand), ground, traffic);

    const composer = new EffectComposer(renderer);
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
        ground.userData.resize((w * ratio) / 2, (h * ratio) / 2);
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
        look.x += (pointer.x - look.x) * 0.04;
        look.y += (pointer.y - look.y) * 0.04;
        camera.rotation.set(0.26 - look.y * 0.08 + Math.sin(time.value * 0.15) * 0.01, -look.x * 0.18 + Math.sin(time.value * 0.1) * 0.02, 0);
        traffic.userData.update(dt);

        composer.render();
        host.classList.add('is-3d');
    });
}

const host = document.querySelector('.drive');
if (host && supportsWebGL() && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    start(host);
}
