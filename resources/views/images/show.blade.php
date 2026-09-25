@extends('layouts.app')

@section('title', $image->original_name . ' — ImageLK')

@section('styles')
<style>
    .show-container {
        max-width: 980px;
        margin: 0 auto;
    }

    /* Header Toolbar */
    .view-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .toolbar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #f3f4f6;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Media Showcase View */
    .showcase-stage {
        background: #111827;
        border: 1px solid var(--border-card);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        position: relative;
        overflow: hidden;
    }

    .media-preview-container {
        max-width: 100%;
        max-height: 65vh;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
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
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .meta-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(79, 70, 229, 0.15);
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
        background: #111827;
        border: 1px solid var(--border-card);
        border-radius: 14px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .url-box {
        display: flex;
        gap: 0.75rem;
        background: #090d16;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 10px;
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

    /* Mobile, Tablet & iPad Responsive Media Queries */
    @media (max-width: 820px) {
        .showcase-stage {
            padding: 1.25rem;
            border-radius: 14px;
        }

        .meta-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
    }

    @media (max-width: 580px) {
        .view-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .meta-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
        }

        .meta-card {
            padding: 0.85rem;
            gap: 0.6rem;
            border-radius: 10px;
        }

        .meta-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
        }

        .meta-value {
            font-size: 0.9rem;
        }

        .url-box {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.65rem;
        }

        .url-box button {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')

<div class="show-container">

    {{-- Toolbar --}}
    <div class="view-toolbar">
        <div class="toolbar-title">
            <i data-lucide="file-text" style="width: 20px; height: 20px; color: #818cf8;"></i> Media Overview
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <span class="badge-formal" style="background: rgba(8, 145, 178, 0.15); border-color: rgba(8, 145, 178, 0.3); color: #22d3ee;">
                <i data-lucide="{{ $image->is_video ? 'film' : 'image' }}" style="width: 14px; height: 14px;"></i>
                {{ strtoupper($image->media_type) }} MEDIA
            </span>
            <a href="{{ route('images.index') }}" class="btn-secondary" style="padding: 0.55rem 1.1rem; font-size: 0.875rem;">
                <i data-lucide="upload" style="width: 16px; height: 16px;"></i> Upload New
            </a>
        </div>
    </div>

    {{-- Media Showcase View --}}
    <div class="showcase-stage">
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
                <div class="meta-icon" style="background: rgba(124, 58, 237, 0.15); color: #c084fc;">
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
                <div class="meta-icon" style="background: rgba(8, 145, 178, 0.15); color: #22d3ee;">
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
                <i data-lucide="share-2" style="width: 16px; height: 16px; color: #818cf8;"></i> Shareable Media Link
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


