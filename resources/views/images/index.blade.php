@extends('layouts.app')

@section('title', 'LinkPix — Upload & Share Images')

@section('styles')
<style>
    .upload-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .header-section {
        text-align: center;
        margin-bottom: 2rem;
    }

    .header-section h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(to right, #ffffff, #cbd5e1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .header-section p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Drag & Drop Area */
    .dropzone {
        border: 2px dashed rgba(99, 102, 241, 0.35);
        border-radius: 20px;
        padding: 3rem 2rem;
        text-align: center;
        background: rgba(15, 23, 42, 0.4);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .dropzone:hover, .dropzone.dragover {
        border-color: #a855f7;
        background: rgba(99, 102, 241, 0.08);
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
        transform: translateY(-2px);
    }

    .dropzone-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1.25rem;
        background: rgba(99, 102, 241, 0.12);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        transition: transform 0.3s ease;
    }

    .dropzone:hover .dropzone-icon {
        transform: scale(1.1) rotate(-5deg);
        color: #c084fc;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    /* Preview Card */
    .preview-card {
        display: none;
        margin-top: 1.5rem;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid var(--border-card);
        border-radius: 16px;
        padding: 1.25rem;
        align-items: center;
        gap: 1.25rem;
        animation: fadeIn 0.3s ease-in-out;
    }

    .preview-thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: #000;
    }

    .preview-info {
        flex: 1;
        overflow: hidden;
    }

    .preview-name {
        font-weight: 600;
        color: #f3f4f6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.25rem;
    }

    .preview-meta {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: flex;
        gap: 0.75rem;
    }

    .preview-meta span {
        background: rgba(255, 255, 255, 0.06);
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .btn-remove {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-remove:hover {
        background: rgba(239, 68, 68, 0.3);
    }

    /* Alerts */
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    /* Success Card */
    .success-card {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .success-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #34d399;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .url-box {
        display: flex;
        gap: 0.75rem;
        background: rgba(9, 13, 22, 0.8);
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
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@section('content')

<div class="upload-container">

    {{-- Success State --}}
    @php
        $displayImg = $uploadedImage ?? session('uploaded_image');
    @endphp

    @if(session('success') && $displayImg)
        @php
            $originalName = data_get($displayImg, 'original_name', 'Image');
            $uniqueKey = data_get($displayImg, 'unique_key', '');
            $shareUrl = data_get($displayImg, 'share_url') ?: url('/image/' . $uniqueKey);
        @endphp
        <div class="success-card">
            <div class="success-header">
                <i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>
                <span>Image uploaded successfully!</span>
            </div>

            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Your image <strong>{{ $originalName }}</strong> is live and ready to share!
            </p>

            <div style="margin-top: 1rem;">
                <label style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Your Image Link:</label>
                <div class="url-box">
                    <input type="text" id="shareable-url" class="url-input mono-font" value="{{ $shareUrl }}" readonly>
                    <button type="button" id="copy-btn" class="btn-primary" onclick="copyLink()" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                        <i data-lucide="copy" id="copy-icon" style="width: 16px; height: 16px;"></i>
                        <span id="copy-text">Copy Link</span>
                    </button>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.25rem;">
                <a href="{{ $shareUrl }}" target="_blank" class="btn-secondary" style="flex: 1; justify-content: center;">
                    <i data-lucide="external-link" style="width: 18px; height: 18px;"></i> Open Image Page
                </a>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert-error">
            <i data-lucide="alert-triangle" style="width: 24px; height: 24px; flex-shrink: 0; color: #f87171;"></i>
            <div>
                <strong style="display: block; margin-bottom: 0.25rem;">Upload Failed</strong>
                <ul style="margin-left: 1.25rem; font-size: 0.9rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Upload Form --}}
    <div class="glass-card">
        <div class="header-section">
            <h1>Upload & Share Image</h1>
            <p>Drag and drop your image file here or browse from your device</p>
        </div>

        <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf

            <div class="dropzone" id="dropzone">
                <input type="file" name="image" id="image-file" class="file-input" accept="image/jpeg,image/jpg,image/png,image/webp" required onchange="handleFileSelect(this)">
                
                <div class="dropzone-icon">
                    <i data-lucide="upload-cloud" style="width: 32px; height: 32px;"></i>
                </div>

                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Choose an image file</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Supports JPG, JPEG, PNG and WebP (Max: 10MB)</p>
            </div>

            {{-- Live Image Preview Area --}}
            <div class="preview-card" id="preview-card">
                <img id="preview-img" src="" alt="Preview" class="preview-thumbnail">
                <div class="preview-info">
                    <div class="preview-name" id="preview-filename">filename.jpg</div>
                    <div class="preview-meta">
                        <span id="preview-size">0 KB</span>
                        <span id="preview-type">PNG</span>
                    </div>
                </div>
                <button type="button" class="btn-remove" onclick="resetFileSelection()" title="Remove file">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>

            <div style="margin-top: 2rem; text-align: center;">
                <button type="submit" id="submit-btn" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    <i data-lucide="upload" style="width: 20px; height: 20px;"></i> Start Upload
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
    const previewFilename = document.getElementById('preview-filename');
    const previewSize = document.getElementById('preview-size');
    const previewType = document.getElementById('preview-type');

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

            // Render Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewFilename.textContent = file.name;
                previewSize.textContent = formatBytes(file.size);
                previewType.textContent = file.type.split('/')[1]?.toUpperCase() || 'IMAGE';
                previewCard.style.display = 'flex';
            }
            reader.readAsDataURL(file);
        }
    }

    function resetFileSelection() {
        fileInput.value = '';
        previewImg.src = '';
        previewCard.style.display = 'none';
    }

    function formatBytes(bytes) {
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
