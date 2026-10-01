import * as THREE from 'three';

export function createGlider() {
    const glider = new THREE.Group();
    glider.scale.setScalar(1.35);
    const hull = new THREE.MeshBasicMaterial({ color: '#655078' });
    const body = new THREE.Mesh(new THREE.CapsuleGeometry(0.65, 3.8, 3, 6), hull);
    body.rotation.z = Math.PI / 2;
    const wings = new THREE.Mesh(new THREE.BoxGeometry(2.4, 0.25, 5), hull);
    const cockpit = new THREE.Mesh(new THREE.SphereGeometry(0.75, 8, 6), new THREE.MeshBasicMaterial({ color: new THREE.Color(0.2, 1.1, 1.4) }));
    cockpit.scale.set(1.5, 0.45, 0.65);
    cockpit.position.set(0.6, 0.6, 0);
    glider.add(body, wings, cockpit);

    const lampGeometry = new THREE.SphereGeometry(0.3, 6, 4);
    for (const side of [-1, 1]) {
        const lamp = new THREE.Mesh(lampGeometry, new THREE.MeshBasicMaterial({ color: side < 0 ? new THREE.Color(2, 0.12, 0.3) : new THREE.Color(0.2, 1.7, 2) }));
        lamp.position.set(-0.3, 0, side * 2.5);
        glider.add(lamp);
    }

    const trail = new THREE.Mesh(new THREE.PlaneGeometry(14, 1.8), new THREE.ShaderMaterial({
        transparent: true,
        depthWrite: false,
        side: THREE.DoubleSide,
        blending: THREE.AdditiveBlending,
        vertexShader: /* glsl */ `
            varying vec2 vUv;
            void main() {
                vUv = uv;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: /* glsl */ `
            varying vec2 vUv;
            void main() {
                float fade = vUv.x * vUv.x * (1.0 - smoothstep(0.0, 0.5, abs(vUv.y - 0.5)));
                gl_FragColor = vec4(0.3, 1.2, 1.8, fade * 0.3);
            }
        `,
    }));
    trail.position.x = -9;
    glider.add(trail);

    glider.visible = false;
    glider.userData.update = (elapsed) => {
        const progress = (elapsed % 38 - 5) / 14;
        glider.visible = progress >= 0 && progress < 1;
        if (!glider.visible) return;

        const direction = Math.floor(elapsed / 38) % 2 === 0 ? 1 : -1;
        glider.position.set(direction * THREE.MathUtils.lerp(-260, 260, progress), 120 + Math.sin(progress * Math.PI) * 8, -65);
        glider.rotation.set(0, direction > 0 ? 0 : Math.PI, Math.cos(progress * Math.PI) * 0.06);
    };

    return glider;
}
