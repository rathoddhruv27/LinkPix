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

    <style>
        :root {
            --bg-main: #090d16;
            --bg-card: rgba(17, 24, 39, 0.7);
            --border-card: rgba(255, 255, 255, 0.08);
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
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(236, 72, 153, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* Header */
        header {
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-card);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 13, 22, 0.75);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
        }

        .logo-badge {
            background: var(--primary-gradient);
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.25rem;
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Glass Card */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            margin: 2rem 0;
            transition: transform 0.3s ease, border-color 0.3s ease;
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
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            font-family: inherit;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.12);
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
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        /* Footer */
        footer {
            margin-top: auto;
            padding: 2rem 0;
            text-align: center;
            border-top: 1px solid var(--border-card);
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        /* Code & Links */
        .mono-font {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('images.index') }}" class="nav-brand">
                <div class="logo-badge">
                    <i data-lucide="image"></i>
                </div>
                <span class="brand-name">LinkPix</span>
            </a>

            <div>
                <a href="{{ route('images.index') }}" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i> Upload
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
            <p>&copy; {{ date('Y') }} LinkPix — Instant Image Hosting & Sharing platform.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
