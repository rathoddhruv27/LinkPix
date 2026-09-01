@extends('layouts.app')

@section('title', 'LinkPix 3D — Upload & Share Images & Videos')

@section('styles')
<style>
    .upload-container {
        max-width: 720px;
        margin: 0 auto;
        perspective: 1200px;
    }

    .header-section {
        text-align: center;
        margin-bottom: 2.25rem;
    }

    .header-section h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.6rem;
        background: linear-gradient(to right, #ffffff, #c084fc, #38bdf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.02em;
    }

    .header-section p {
        color: var(--text-muted);
        font-size: 1.15rem;
    }

    /* Drag & Drop Area with 3D Depth */
    .dropzone {
        border: 2px dashed rgba(168, 85, 247, 0.4);
        border-radius: 20px;
        padding: 3.5rem 2rem;
        text-align: center;
        background: rgba(15, 23, 42, 0.5);
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
    }

    .dropzone:hover, .dropzone.dragover {
        border-color: #38bdf8;
        background: rgba(99, 102, 241, 0.12);
        box-shadow: 0 15px 40px rgba(168, 85, 247, 0.3), inset 0 0 20px rgba(99, 102, 241, 0.2);
        transform: translateY(-4px) translateZ(15px);
    }

    .dropzone-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.25rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c084fc;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .dropzone:hover .dropzone-icon {
        transform: scale(1.15) rotate(-6deg) translateZ(25px);
        color: #38bdf8;
        box-shadow: 0 12px 30px rgba(56, 189, 248, 0.4);
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 5;
    }

    /* Preview Card with 3D Depth */
    .preview-card {
        display: none;
        margin-top: 1.75rem;
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 1.25rem;
        align-items: center;
        gap: 1.25rem;
        animation: fadeIn 0.35s ease-in-out;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .preview-media-box {
        width: 90px;
        height: 90px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: #000;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .preview-media-box img, .preview-media-box video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-info {
        flex: 1;
        overflow: hidden;
    }

    .preview-name {
        font-weight: 700;
        color: #f3f4f6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.35rem;
        font-size: 1.05rem;
    }

    .preview-meta {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .preview-meta span {
        background: rgba(255, 255, 255, 0.08);
        padding: 0.2rem 0.65rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .btn-remove {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        width: 38px;
        height: 38px;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .btn-remove:hover {
        background: rgba(239, 68, 68, 0.3);
        transform: scale(1.1);
    }

    /* Alerts */
    .alert-error {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #fca5a5;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    /* Success Card */
    .success-card {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 22px;
        padding: 2rem;
        margin-bottom: 2rem;
        animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
    }

    .success-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #34d399;
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }

    .url-box {
        display: flex;
        gap: 0.75rem;
        background: rgba(9, 13, 22, 0.85);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 14px;
        padding: 0.5rem;
        margin: 1rem 0;
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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Mobile, Tablet & iPad Responsive Media Queries */
    @media (max-width: 768px) {
        .header-section {
            margin-bottom: 1.75rem;
        }

        .header-section h1 {
            font-size: 2.15rem;
        }

        .header-section p {
            font-size: 1rem;
        }

        .dropzone {
            padding: 2.5rem 1.25rem;
            border-radius: 16px;
        }

        .dropzone-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
        }

        .dropzone h3 {
            font-size: 1.2rem !important;
        }

        .success-card {
            padding: 1.5rem 1.25rem;
            border-radius: 18px;
        }
    }

    @media (max-width: 520px) {
        .header-section h1 {
            font-size: 1.7rem;
        }

        .header-section p {
            font-size: 0.9rem;
        }

        .dropzone {
            padding: 2rem 1rem;
        }

        .dropzone-icon {
            width: 52px;
            height: 52px;
        }

        .url-box {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.65rem;
        }

        .url-box button {
            width: 100%;
        }

        .preview-card {
            flex-direction: row;
            gap: 0.85rem;
            padding: 1rem;
        }

        .preview-media-box {
            width: 70px;
            height: 70px;
        }

        .preview-name {
            font-size: 0.95rem;
        }

        .preview-meta {
            font-size: 0.775rem;
            gap: 0.35rem;
        }
    }
</style>
@endsection

@section('content')

<div class="upload-container">

    {{-- Success State --}}
    @php
        $displayImg = $uploadedImage ?? session('uploaded_image');
    @endphp

    @if(session('success') && $displayImg)
        @php
            $originalName = data_get($displayImg, 'original_name', 'Media');
            $uniqueKey = data_get($displayImg, 'unique_key', '');
            $isVideo = data_get($displayImg, 'is_video', false);
            $shareUrl = data_get($displayImg, 'share_url') ?: url('/image/' . $uniqueKey);
        @endphp
        <div class="success-card">
            <div class="success-header">
                <i data-lucide="check-circle-2" style="width: 28px; height: 28px;"></i>
                <span>{{ $isVideo ? 'Video' : 'Image' }} Uploaded Successfully!</span>
            </div>

            <p style="color: var(--text-muted); font-size: 1rem;">
                Your {{ $isVideo ? 'video' : 'image' }} <strong>{{ $originalName }}</strong> is now ready for 3D viewing and sharing!
            </p>

            <div style="margin-top: 1rem;">
                <label style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Your Shareable 3D Media Link:</label>
                <div class="url-box">
                    <input type="text" id="shareable-url" class="url-input mono-font" value="{{ $shareUrl }}" readonly>
                    <button type="button" id="copy-btn" class="btn-primary" onclick="copyLink()" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                        <i data-lucide="copy" id="copy-icon" style="width: 16px; height: 16px;"></i>
                        <span id="copy-text">Copy Link</span>
                    </button>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.25rem;">
                <a href="{{ $shareUrl }}" class="btn-primary" style="flex: 1; justify-content: center;">
                    <i data-lucide="box" style="width: 18px; height: 18px;"></i> View in 3D Spatial Player
                </a>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert-error">
            <i data-lucide="alert-triangle" style="width: 26px; height: 26px; flex-shrink: 0; color: #f87171;"></i>
            <div>
                <strong style="display: block; margin-bottom: 0.35rem; font-size: 1.05rem;">Upload Failed</strong>
                <ul style="margin-left: 1.25rem; font-size: 0.925rem; line-height: 1.5;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main 3D Upload Form --}}
    <div class="glass-card tilt-3d" id="tilt-card">
        <div class="header-section">
            <h1>Upload & Share Media</h1>
            <p>Drag and drop your image or video file here or browse from your device</p>
        </div>

        <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf

            <div class="dropzone" id="dropzone">
                <input type="file" name="image" id="image-file" class="file-input" accept="image/*,video/*,.jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.ogg,.mov" required onchange="handleFileSelect(this)">
                
                <div class="dropzone-icon">
                    <i data-lucide="upload-cloud" style="width: 36px; height: 36px;"></i>
                </div>

                <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem; color: #f3f4f6;">Choose an Image or Video File</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Supports JPG, PNG, WebP, GIF, MP4, WebM & MOV (Max: 50MB)
                </p>
            </div>

            {{-- Live Media Preview Area --}}
            <div class="preview-card" id="preview-card">
                <div class="preview-media-box" id="preview-media-box">
                    <img id="preview-img" src="" alt="Preview" style="display: none;">
                    <video id="preview-video" src="" muted loop style="display: none;"></video>
                </div>
                <div class="preview-info">
                    <div class="preview-name" id="preview-filename">filename.ext</div>
                    <div class="preview-meta">
                        <span id="preview-size"><i data-lucide="hard-drive" style="width: 12px; height: 12px;"></i> 0 KB</span>
                        <span id="preview-type"><i data-lucide="file" style="width: 12px; height: 12px;"></i> MEDIA</span>
                        <span id="preview-dimension" style="display: none;"><i data-lucide="maximize" style="width: 12px; height: 12px;"></i> --</span>
                    </div>
                </div>
                <button type="button" class="btn-remove" onclick="resetFileSelection()" title="Remove file">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <div style="margin-top: 2.25rem; text-align: center;">
                <button type="submit" id="submit-btn" class="btn-primary" style="width: 100%; padding: 1.1rem; font-size: 1.15rem;">
                    <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i> Start 3D Media Upload
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('image-file');
    const previewCard = document.getElementById('preview-card');
    const previewImg = document.getElementById('preview-img');
    const previewVideo = document.getElementById('preview-video');
    const previewFilename = document.getElementById('preview-filename');
    const previewSize = document.getElementById('preview-size');
    const previewType = document.getElementById('preview-type');
    const previewDimension = document.getElementById('preview-dimension');
    const tiltCard = document.getElementById('tilt-card');

    // 3D Card Tilt Interaction
    if (tiltCard) {
        tiltCard.addEventListener('mousemove', (e) => {
            const rect = tiltCard.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            const rotateX = (-y / rect.height) * 8;
            const rotateY = (x / rect.width) * 8;

            tiltCard.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        tiltCard.addEventListener('mouseleave', () => {
            tiltCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });
    }

    // Drag and Drop styling
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        }, false);
    });

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const isVideo = file.type.startsWith('video/') || ['mp4','webm','ogg','mov'].includes(file.name.split('.').pop().toLowerCase());

            previewFilename.textContent = file.name;
            previewSize.innerHTML = `<i data-lucide="hard-drive" style="width: 12px; height: 12px;"></i> ${formatBytes(file.size)}`;
            previewType.innerHTML = `<i data-lucide="${isVideo ? 'film' : 'image'}" style="width: 12px; height: 12px;"></i> ${isVideo ? 'VIDEO' : 'IMAGE'} (${file.name.split('.').pop().toUpperCase()})`;

            const reader = new FileReader();

            if (isVideo) {
                previewImg.style.display = 'none';
                previewVideo.style.display = 'block';

                reader.onload = function(e) {
                    previewVideo.src = e.target.result;
                    previewVideo.play().catch(() => {});
                    previewCard.style.display = 'flex';
                    lucide.createIcons();
                };
            } else {
                previewVideo.style.display = 'none';
                previewVideo.pause();
                previewImg.style.display = 'block';

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewCard.style.display = 'flex';
                    lucide.createIcons();
                };
            }

            reader.readAsDataURL(file);
        }
    }

    function resetFileSelection() {
        fileInput.value = '';
        previewImg.src = '';
        previewVideo.src = '';
        previewVideo.pause();
        previewCard.style.display = 'none';
    }

    function formatBytes(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' Bytes';
    }

    function copyLink() {
        const copyInput = document.getElementById('shareable-url');
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
</script>
@endsection
