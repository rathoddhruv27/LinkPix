<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ImageLK — Free Image Hosting & Sharing')</title>
    <meta name="description" content="Upload and share your images instantly with ImageLK. Fast, secure, and hassle-free image hosting.">

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
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --border-card: rgba(255, 255, 255, 0.1);
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-cyan: #0891b2;
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
            position: sticky;
            top: 0;
            z-index: 50;
            background: #0d1322;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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
            padding: 0.55rem 0.85rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.25rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }

        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        /* Glass / Formal Card */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin: 2rem 0;
        }

        .badge-formal {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(79, 70, 229, 0.15);
            border: 1px solid rgba(79, 70, 229, 0.3);
            color: #a5b4fc;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.85rem 1.75rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
            font-family: inherit;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.07);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 0.85rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.25);
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
            background: #0d1322;
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

            .badge-formal {
                font-size: 0.725rem;
                padding: 0.25rem 0.6rem;
            }

            .glass-card {
                padding: 1.5rem 1.25rem;
                margin: 1.25rem 0;
                border-radius: 14px;
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
                border-radius: 12px;
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
    <header>
        <div class="container header-nav-wrap" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('images.index') }}" class="nav-brand">
                <div class="logo-badge">
                    <i data-lucide="image" style="width: 20px; height: 20px;"></i>
                </div>
                <span class="brand-name">ImageLK</span>
            </a>

            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <span class="badge-formal">
                    <i data-lucide="shield-check" style="width: 13px; height: 13px;"></i> Media Portal
                </span>
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
            <p>&copy; {{ date('Y') }} ImageLK </p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>

