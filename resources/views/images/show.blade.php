@extends('layouts.app')

@section('title', $image->original_name . ' — LinkPix 3D')

@section('styles')
<style>
    .show-container {
        max-width: 980px;
        margin: 0 auto;
    }

    /* 3D Viewport Header Toolbar */
    .view-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .mode-switch-group {
        display: flex;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 0.35rem;
        border-radius: 16px;
        gap: 0.35rem;
        backdrop-filter: blur(12px);
    }

    .mode-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0.55rem 1.15rem;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        font-family: inherit;
    }

    .mode-btn.active {
        background: var(--primary-gradient);
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }

    /* 3D WebGL Spatial Viewport Stage */
    .spatial-viewport-container {
        position: relative;
        width: 100%;
        height: 520px;
        background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #090d16 100%);
        border: 1px solid rgba(168, 85, 247, 0.3);
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), inset 0 0 30px rgba(99, 102, 241, 0.15);
    }

    #spatial-3d-canvas {
        width: 100%;
        height: 100%;
        display: block;
        cursor: grab;
    }

    #spatial-3d-canvas:active {
        cursor: grabbing;
    }

    .spatial-controls-overlay {
        position: absolute;
        bottom: 1.25rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(9, 13, 22, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 0.4rem 0.85rem;
        backdrop-filter: blur(12px);
        z-index: 20;
    }

    .ctrl-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-main);
        padding: 0.45rem 0.85rem;
        border-radius: 10px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .ctrl-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .ctrl-btn.active {
        background: #818cf8;
        color: #ffffff;
    }

    /* 3D Showcase Card View */
    .showcase-stage {
        display: none;
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid var(--border-card);
        border-radius: 24px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        position: relative;
        overflow: hidden;
        perspective: 1200px;
    }

    .media-preview-container {
        max-width: 100%;
        max-height: 65vh;
        margin: 0 auto;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #000000;
        position: relative;
    }

    .media-preview-container img, .media-preview-container video {
        max-width: 100%;
        max-height: 65vh;
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .meta-card {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid var(--border-card);
        border-radius: 18px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: transform 0.3s ease;
    }

    .meta-card:hover {
        transform: translateY(-2px);
    }

    .meta-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .meta-label {
        font-size: 0.775rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.2rem;
    }

    .meta-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #f3f4f6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .share-bar {
        background: rgba(17, 24, 39, 0.75);
        border: 1px solid var(--border-card);
        border-radius: 20px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .url-box {
        display: flex;
        gap: 0.75rem;
        background: rgba(9, 13, 22, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 14px;
        padding: 0.5rem;
        margin-top: 0.75rem;
    }

    .url-input {
        flex: 1;
        background: transparent;
        border: none;
        color: #f3f4f6;
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        outline: none;
    }
</style>
@endsection

@section('content')

<div class="show-container">

    {{-- Toolbar: View Mode Switcher --}}
    <div class="view-toolbar">
        <div class="mode-switch-group">
            <button type="button" class="mode-btn active" id="btn-mode-spatial" onclick="switchViewMode('spatial')">
                <i data-lucide="box" style="width: 18px; height: 18px;"></i> 3D Spatial Canvas
            </button>
            <button type="button" class="mode-btn" id="btn-mode-showcase" onclick="switchViewMode('showcase')">
                <i data-lucide="sparkles" style="width: 18px; height: 18px;"></i> 3D Showcase Card
            </button>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <span class="badge-3d" style="background: rgba(6, 182, 212, 0.15); border-color: rgba(6, 182, 212, 0.3); color: #22d3ee;">
                <i data-lucide="{{ $image->is_video ? 'film' : 'image' }}" style="width: 14px; height: 14px;"></i>
                {{ strtoupper($image->media_type) }} MEDIA
            </span>
            <a href="{{ route('images.index') }}" class="btn-secondary" style="padding: 0.55rem 1.1rem; font-size: 0.875rem;">
                <i data-lucide="upload" style="width: 16px; height: 16px;"></i> Upload New
            </a>
        </div>
    </div>

    {{-- Mode 1: 3D Spatial WebGL Viewport Stage --}}
    <div class="spatial-viewport-container" id="spatial-stage">
        <canvas id="spatial-3d-canvas"></canvas>

        <div class="spatial-controls-overlay">
            <button type="button" class="ctrl-btn" id="ctrl-spin" onclick="toggle3DSpin()" title="Toggle Auto Rotation">
                <i data-lucide="rotate-cw" style="width: 14px; height: 14px;"></i> <span id="spin-text">Auto-Spin: ON</span>
            </button>
            <button type="button" class="ctrl-btn" onclick="reset3DCamera()" title="Reset 3D View">
                <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Reset Camera
            </button>
            <button type="button" class="ctrl-btn" onclick="toggleSpatialFullscreen()" title="Fullscreen 3D View">
                <i data-lucide="maximize-2" style="width: 14px; height: 14px;"></i> Fullscreen
            </button>
        </div>
    </div>

    {{-- Mode 2: 3D Showcase Card View --}}
    <div class="showcase-stage tilt-3d" id="showcase-stage">
        <div class="media-preview-container">
            @if($image->is_video)
                <video id="media-element" src="{{ $image->storage_url }}" controls autoplay loop playsinline style="width: 100%; height: auto; max-height: 65vh;"></video>
            @else
                <img id="media-element" src="{{ $image->storage_url }}" alt="{{ $image->original_name }}">
            @endif
        </div>
    </div>

    {{-- Media Information Card --}}
    <div class="glass-card" style="margin-top: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 1.85rem; font-weight: 800; color: #f3f4f6; word-break: break-all;">
                    {{ $image->original_name }}
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                    Uploaded {{ $image->created_at->diffForHumans() }} ({{ $image->created_at->format('M d, Y - H:i') }})
                </p>
            </div>
        </div>

        {{-- Meta Stats Grid --}}
        <div class="meta-grid">
            <div class="meta-card">
                <div class="meta-icon">
                    <i data-lucide="eye" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="meta-label">Total Views</div>
                    <div class="meta-value">{{ number_format($image->views) }}</div>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                    <i data-lucide="hard-drive" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="meta-label">File Size</div>
                    <div class="meta-value">{{ $image->formatted_size }}</div>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">
                    <i data-lucide="file-type" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="meta-label">MIME Type</div>
                    <div class="meta-value" style="font-size: 0.9rem;">{{ $image->mime_type }}</div>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">
                    <i data-lucide="key" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="meta-label">Unique Key</div>
                    <div class="meta-value mono-font">{{ $image->unique_key }}</div>
                </div>
            </div>
        </div>

        {{-- Shareable Link Bar --}}
        <div class="share-bar">
            <label style="font-size: 0.9rem; font-weight: 600; color: #e2e8f0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="share-2" style="width: 16px; height: 16px; color: #818cf8;"></i> Shareable 3D Media Link
            </label>
            <div class="url-box">
                <input type="text" id="share-link-input" class="url-input mono-font" value="{{ $image->share_url }}" readonly>
                <button type="button" id="copy-btn" class="btn-primary" onclick="copyShareLink()" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                    <i data-lucide="copy" id="copy-icon" style="width: 16px; height: 16px;"></i>
                    <span id="copy-text">Copy Link</span>
                </button>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    let spatialScene, spatialCamera, spatialRenderer, spatialControls;
    let mediaMesh, isAutoSpinning = true;

    const isVideoMedia = {{ $image->is_video ? 'true' : 'false' }};
    const mediaStorageUrl = "{{ $image->storage_url }}";

    // Initialize 3D Spatial Canvas Stage
    function initSpatial3DStage() {
        const container = document.getElementById('spatial-stage');
        const canvas = document.getElementById('spatial-3d-canvas');
        if (!container || !canvas || typeof THREE === 'undefined') return;

        // Scene, Camera, Renderer
        spatialScene = new THREE.Scene();
        spatialCamera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 0.1, 1000);
        spatialCamera.position.set(0, 0, 320);

        spatialRenderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: true });
        spatialRenderer.setSize(container.clientWidth, container.clientHeight);
        spatialRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Orbit Controls
        if (typeof THREE.OrbitControls !== 'undefined') {
            spatialControls = new THREE.OrbitControls(spatialCamera, spatialRenderer.domElement);
            spatialControls.enableDamping = true;
            spatialControls.dampingFactor = 0.05;
            spatialControls.maxDistance = 600;
            spatialControls.minDistance = 100;
        }

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
        spatialScene.add(ambientLight);

        const pointLight1 = new THREE.PointLight(0xa855f7, 2, 500);
        pointLight1.position.set(200, 200, 200);
        spatialScene.add(pointLight1);

        const pointLight2 = new THREE.PointLight(0x06b6d4, 1.8, 500);
        pointLight2.position.set(-200, -150, 150);
        spatialScene.add(pointLight2);

        // Reflective Ground Grid
        const gridHelper = new THREE.GridHelper(600, 30, 0x818cf8, 0x312e81);
        gridHelper.position.y = -120;
        spatialScene.add(gridHelper);

        // Media Texture Loader (Image / Video Texture)
        let texture;

        if (isVideoMedia) {
            const videoElem = document.getElementById('media-element') || document.createElement('video');
            videoElem.crossOrigin = 'anonymous';
            videoElem.loop = true;
            videoElem.muted = true;
            videoElem.playsInline = true;
            videoElem.src = mediaStorageUrl;
            videoElem.play().catch(() => {});

            texture = new THREE.VideoTexture(videoElem);
            texture.minFilter = THREE.LinearFilter;
            texture.magFilter = THREE.LinearFilter;
            texture.format = THREE.RGBAFormat;
        } else {
            const textureLoader = new THREE.TextureLoader();
            texture = textureLoader.load(mediaStorageUrl, (tex) => {
                // Adjust plane size based on aspect ratio
                if (tex.image && mediaMesh) {
                    const aspect = tex.image.width / tex.image.height;
                    mediaMesh.scale.set(aspect > 1 ? 1.4 : 1.4 * aspect, aspect > 1 ? 1.4 / aspect : 1.4, 1);
                }
            });
        }

        // Create 3D Floating Glass Slab with 3D Depth
        const planeGeo = new THREE.PlaneGeometry(160, 120);
        const planeMat = new THREE.MeshStandardMaterial({
            map: texture,
            side: THREE.DoubleSide,
            roughness: 0.2,
            metalness: 0.1
        });

        mediaMesh = new THREE.Mesh(planeGeo, planeMat);
        spatialScene.add(mediaMesh);

        // Add 3D Glowing Border Frame around plane
        const borderGeo = new THREE.BoxGeometry(164, 124, 6);
        const borderMat = new THREE.MeshPhysicalMaterial({
            color: 0x6366f1,
            transmission: 0.7,
            opacity: 1,
            transparent: true,
            roughness: 0.1,
            ior: 1.5,
            thickness: 5
        });
        const borderMesh = new THREE.Mesh(borderGeo, borderMat);
        borderMesh.position.z = -4;
        mediaMesh.add(borderMesh);

        // Animation Loop
        function renderLoop() {
            requestAnimationFrame(renderLoop);

            if (isAutoSpinning && mediaMesh) {
                mediaMesh.rotation.y += 0.006;
            }

            if (spatialControls) {
                spatialControls.update();
            }

            spatialRenderer.render(spatialScene, spatialCamera);
        }

        renderLoop();

        // Handle Resize
        window.addEventListener('resize', () => {
            if (!container || !spatialRenderer || !spatialCamera) return;
            spatialCamera.aspect = container.clientWidth / container.clientHeight;
            spatialCamera.updateProjectionMatrix();
            spatialRenderer.setSize(container.clientWidth, container.clientHeight);
        });
    }

    // View Mode Switcher
    function switchViewMode(mode) {
        const spatialStage = document.getElementById('spatial-stage');
        const showcaseStage = document.getElementById('showcase-stage');
        const btnSpatial = document.getElementById('btn-mode-spatial');
        const btnShowcase = document.getElementById('btn-mode-showcase');

        if (mode === 'spatial') {
            spatialStage.style.display = 'block';
            showcaseStage.style.display = 'none';
            btnSpatial.classList.add('active');
            btnShowcase.classList.remove('active');
        } else {
            spatialStage.style.display = 'none';
            showcaseStage.style.display = 'block';
            btnShowcase.classList.add('active');
            btnSpatial.classList.remove('active');
        }
    }

    // 3D Controls
    function toggle3DSpin() {
        isAutoSpinning = !isAutoSpinning;
        const spinText = document.getElementById('spin-text');
        const ctrlSpin = document.getElementById('ctrl-spin');

        if (spinText) spinText.textContent = `Auto-Spin: ${isAutoSpinning ? 'ON' : 'OFF'}`;
        if (ctrlSpin) {
            ctrlSpin.classList.toggle('active', isAutoSpinning);
        }
    }

    function reset3DCamera() {
        if (spatialCamera) {
            spatialCamera.position.set(0, 0, 320);
        }
        if (spatialControls) {
            spatialControls.reset();
        }
        if (mediaMesh) {
            mediaMesh.rotation.set(0, 0, 0);
        }
    }

    function toggleSpatialFullscreen() {
        const container = document.getElementById('spatial-stage');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => alert(err.message));
        } else {
            document.exitFullscreen();
        }
    }

    function copyShareLink() {
        const copyInput = document.getElementById('share-link-input');
        const copyBtn = document.getElementById('copy-btn');
        const copyText = document.getElementById('copy-text');
        const copyIcon = document.getElementById('copy-icon');

        navigator.clipboard.writeText(copyInput.value).then(() => {
            copyText.textContent = 'Copied!';
            copyBtn.style.background = '#10b981';
            copyIcon.setAttribute('data-lucide', 'check');
            lucide.createIcons();

            setTimeout(() => {
                copyText.textContent = 'Copy Link';
                copyBtn.style.background = '';
                copyIcon.setAttribute('data-lucide', 'copy');
                lucide.createIcons();
            }, 2500);
        });
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', () => {
        initSpatial3DStage();
    });
</script>
@endsection
