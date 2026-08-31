@extends('layouts.app')

@section('title', $image->original_name . ' — LinkPix')

@section('styles')
<style>
    .show-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .image-frame {
        background: #000000;
        border: 1px solid var(--border-card);
        border-radius: 24px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        position: relative;
        overflow: hidden;
    }

    .image-preview {
        max-width: 100%;
        max-height: 70vh;
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .meta-card {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border-card);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .meta-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .meta-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
    }

    .meta-value {
        font-size: 1rem;
        font-weight: 700;
        color: #f3f4f6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .share-bar {
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid var(--border-card);
        border-radius: 20px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .url-box {
        display: flex;
        gap: 0.75rem;
        background: rgba(9, 13, 22, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.12);
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

    {{-- Main Image Frame --}}
    <div class="image-frame">
        <img src="{{ $image->storage_url }}" alt="{{ $image->original_name }}" class="image-preview">
    </div>

    {{-- Image Info Card --}}
    <div class="glass-card" style="margin-top: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #f3f4f6; word-break: break-all;">
                    {{ $image->original_name }}
                </h1>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                    Uploaded {{ $image->created_at->diffForHumans() }} ({{ $image->created_at->format('M d, Y - H:i') }})
                </p>
            </div>

            <a href="{{ route('images.index') }}" class="btn-secondary">
                <i data-lucide="upload" style="width: 18px; height: 18px;"></i> Upload Image
            </a>
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
                <i data-lucide="share-2" style="width: 16px; height: 16px; color: #818cf8;"></i> Shareable Link
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
</script>
@endsection
