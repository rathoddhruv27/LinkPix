<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LinkPix — Free Image Hosting & Sharing')</title>
    <meta name="description" content="Upload and share your images instantly with LinkPix. Fast, secure, and hassle-free image hosting.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Three.js 3D Engine -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <style>
        :root {
            --bg-main: #090d16;
            --bg-card: rgba(15, 23, 42, 0.75);
            --border-card: rgba(255, 255, 255, 0.1);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            --accent-cyan: #06b6d4;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        #bg-3d-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            opacity: 0.7;
        }

        .container {
            max-width: 1060px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        /* Header */
        header {
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border-card);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 13, 22, 0.85);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            transition: transform 0.3s ease;
        }

        .nav-brand:hover {
            transform: scale(1.03);
        }

        .logo-badge {
            background: var(--primary-gradient);
            padding: 0.55rem 0.85rem;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.25rem;
            color: #ffffff;
            box-shadow: 0 6px 24px rgba(99, 102, 241, 0.5), inset 0 0 10px rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transform-style: preserve-3d;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-brand:hover .logo-badge {
            transform: rotateY(15deg) rotateX(-10deg) translateZ(10px);
        }

        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #ffffff, #c084fc, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* 3D Perspective Glass Card */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-card);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.15);
            margin: 2rem 0;
            transform-style: preserve-3d;
            perspective: 1000px;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.4s ease;
        }

        .glass-card:hover {
            border-color: rgba(168, 85, 247, 0.3);
            box-shadow: 0 35px 80px rgba(99, 102, 241, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.25);
        }

        /* 3D Tilt Wrapper */
        .tilt-3d {
            transition: transform 0.15s ease-out;
            transform-style: preserve-3d;
            will-change: transform;
        }

        .badge-3d {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.85rem 1.75rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.6s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(168, 85, 247, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.07);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 0.85rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.25s ease;
            font-family: inherit;
            backdrop-filter: blur(8px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        footer {
            margin-top: auto;
            padding: 2rem 0;
            text-align: center;
            border-top: 1px solid var(--border-card);
            color: var(--text-muted);
            font-size: 0.875rem;
            position: relative;
            z-index: 10;
            background: rgba(9, 13, 22, 0.8);
            backdrop-filter: blur(10px);
        }

        /* Mobile, Tablet & iPad Responsiveness */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            header {
                padding: 0.9rem 0;
            }

            .header-nav-wrap {
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .brand-name {
                font-size: 1.35rem;
            }

            .badge-3d {
                font-size: 0.725rem;
                padding: 0.25rem 0.6rem;
            }

            .glass-card {
                padding: 1.5rem 1.25rem;
                margin: 1.25rem 0;
                border-radius: 20px;
            }

            .btn-primary, .btn-secondary {
                padding: 0.75rem 1.25rem;
                font-size: 0.925rem;
            }

            main {
                padding: 1rem 0;
            }
        }

        @media (max-width: 480px) {
            .header-nav-wrap {
                gap: 0.5rem;
            }

            .logo-badge {
                padding: 0.4rem 0.6rem;
                font-size: 1.1rem;
            }

            .brand-name {
                font-size: 1.2rem;
            }

            .glass-card {
                padding: 1.25rem 1rem;
                border-radius: 18px;
            }
        }

        /* Code & Links */
        .mono-font {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- 3D Canvas Background -->
    <canvas id="bg-3d-canvas"></canvas>

    <header>
        <div class="container header-nav-wrap" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('images.index') }}" class="nav-brand">
                <div class="logo-badge">
                    <i data-lucide="box" style="width: 20px; height: 20px;"></i>
                </div>
                <span class="brand-name">LinkPix 3D</span>
            </a>

            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <span class="badge-3d">
                    <i data-lucide="sparkles" style="width: 13px; height: 13px;"></i> 3D Media
                </span>
                <a href="{{ route('images.index') }}" class="btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                    <i data-lucide="upload-cloud" style="width: 15px; height: 15px;"></i> Upload
                </a>
            </div>
        </div>
    </header>

    <main style="flex: 1; display: flex; align-items: center; padding: 2rem 0;">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} LinkPix 3D — Next-Gen Interactive Video & Image Hosting.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // 3D Background Canvas Initialization using Three.js
        (function init3DBackground() {
            const canvas = document.getElementById('bg-3d-canvas');
            if (!canvas || typeof THREE === 'undefined') return;

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 400;

            const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Create 3D Floating Particles
            const particleCount = 120;
            const geometry = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);
            const colors = new Float32Array(particleCount * 3);

            const color1 = new THREE.Color(0x6366f1);
            const color2 = new THREE.Color(0xc084fc);
            const color3 = new THREE.Color(0x06b6d4);

            for (let i = 0; i < particleCount; i++) {
                positions[i * 3] = (Math.random() - 0.5) * 1200;
                positions[i * 3 + 1] = (Math.random() - 0.5) * 1200;
                positions[i * 3 + 2] = (Math.random() - 0.5) * 800;

                const mixColor = i % 3 === 0 ? color1 : (i % 3 === 1 ? color2 : color3);
                colors[i * 3] = mixColor.r;
                colors[i * 3 + 1] = mixColor.g;
                colors[i * 3 + 2] = mixColor.b;
            }

            geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

            const material = new THREE.PointsMaterial({
                size: 4,
                vertexColors: true,
                transparent: true,
                opacity: 0.65,
                blending: THREE.AdditiveBlending
            });

            const particleSystem = new THREE.Points(geometry, material);
            scene.add(particleSystem);

            // Floating Wireframe 3D Polyhedrons
            const geoMeshGroup = new THREE.Group();
            const icoGeo = new THREE.IcosahedronGeometry(35, 1);
            const icoMat = new THREE.MeshBasicMaterial({ color: 0x818cf8, wireframe: true, transparent: true, opacity: 0.25 });
            const icoMesh = new THREE.Mesh(icoGeo, icoMat);
            icoMesh.position.set(-350, 150, -100);
            geoMeshGroup.add(icoMesh);

            const torusGeo = new THREE.TorusGeometry(30, 8, 12, 30);
            const torusMat = new THREE.MeshBasicMaterial({ color: 0xc084fc, wireframe: true, transparent: true, opacity: 0.2 });
            const torusMesh = new THREE.Mesh(torusGeo, torusMat);
            torusMesh.position.set(380, -120, -150);
            geoMeshGroup.add(torusMesh);

            scene.add(geoMeshGroup);

            // Mouse Move Interaction
            let mouseX = 0, mouseY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX - window.innerWidth / 2) * 0.05;
                mouseY = (e.clientY - window.innerHeight / 2) * 0.05;
            });

            // Resize Event
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });

            // Animation Loop
            function animate() {
                requestAnimationFrame(animate);

                particleSystem.rotation.y += 0.001;
                particleSystem.rotation.x += 0.0005;

                icoMesh.rotation.x += 0.004;
                icoMesh.rotation.y += 0.006;

                torusMesh.rotation.x += 0.005;
                torusMesh.rotation.z += 0.004;

                camera.position.x += (mouseX - camera.position.x) * 0.03;
                camera.position.y += (-mouseY - camera.position.y) * 0.03;
                camera.lookAt(scene.position);

                renderer.render(scene, camera);
            }

            animate();
        })();
    </script>
    @yield('scripts')
</body>
</html>
