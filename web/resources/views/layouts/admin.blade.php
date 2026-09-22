<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control') - Campus Connect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #38bdf8;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --state-recibida: #0284c7;
            --state-recibida-bg: #e0f2fe;
            --state-pendiente: #d97706;
            --state-pendiente-bg: #fef3c7;
            --state-enproceso: #7c3aed;
            --state-enproceso-bg: #ede9fe;
            --state-completada: #059669;
            --state-completada-bg: #d1fae5;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04);
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 270px;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .brand-text h1 {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .brand-text span {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .sidebar-nav {
            padding: 20px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 12px;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
            margin-bottom: 3px;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
            transform: translateX(2px);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.22) 0%, rgba(56, 189, 248, 0.12) 100%);
            color: var(--sidebar-active);
            font-weight: 600;
            border-left: 3px solid var(--sidebar-active);
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            opacity: 0.85;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.6);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .user-info {
            line-height: 1.3;
        }

        .user-info .name {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .user-info .role {
            font-size: 11px;
            color: #38bdf8;
            font-weight: 500;
        }

        /* Main Container */
        .main-wrapper {
            margin-left: 270px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            height: 70px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .topbar-title h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.02em;
        }

        .topbar-title p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(37, 99, 235, 0.2);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Content Area */
        .content {
            padding: 32px;
            flex: 1;
        }

        /* Flash Message */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.2s ease-in-out;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Badges for Request Statuses */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .badge-recibida {
            background-color: var(--state-recibida-bg);
            color: var(--state-recibida);
            border: 1px solid #bae6fd;
        }

        .badge-pendiente {
            background-color: var(--state-pendiente-bg);
            color: var(--state-pendiente);
            border: 1px solid #fde68a;
        }

        .badge-en-proceso, .badge-en_proceso {
            background-color: var(--state-enproceso-bg);
            color: var(--state-enproceso);
            border: 1px solid #ddd6fe;
        }

        .badge-completada {
            background-color: var(--state-completada-bg);
            color: var(--state-completada);
            border: 1px solid #a7f3d0;
        }

        .badge-tipo {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-outline {
            background: white;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Card styles */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card-body {
            padding: 24px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-brand .brand-text, .nav-link span, .nav-section-title, .user-info {
                display: none;
            }
            .main-wrapper {
                margin-left: 70px;
            }
            .topbar {
                padding: 0 16px;
            }
            .content {
                padding: 16px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">CC</div>
            <div class="brand-text">
                <h1>Campus Connect</h1>
                <span>Gestión Universitaria</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Administración</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard (HU1)</span>
            </a>

            <a href="{{ route('admin.solicitudes.index') }}" class="nav-link {{ request()->routeIs('admin.solicitudes.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Solicitudes</span>
            </a>

            <div class="nav-section-title">Filtros Rápidos</div>
            <a href="{{ route('admin.solicitudes.index', ['estado' => 1]) }}" class="nav-link">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--state-recibida); display: inline-block;"></span>
                <span>Recibidas</span>
            </a>
            <a href="{{ route('admin.solicitudes.index', ['estado' => 2]) }}" class="nav-link">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--state-pendiente); display: inline-block;"></span>
                <span>Pendientes</span>
            </a>
            <a href="{{ route('admin.solicitudes.index', ['estado' => 3]) }}" class="nav-link">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--state-enproceso); display: inline-block;"></span>
                <span>En Proceso</span>
            </a>
            <a href="{{ route('admin.solicitudes.index', ['estado' => 4]) }}" class="nav-link">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--state-completada); display: inline-block;"></span>
                <span>Completadas</span>
            </a>
            <a href="{{ route('admin.solicitudes.index', ['responsable' => 'sin_asignar']) }}" class="nav-link">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                <span>Sin Responsable (HU2)</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">AD</div>
                <div class="user-info">
                    <div class="name">Administrador</div>
                    <div class="role">Admin Solicitudes</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-title">
                <h2>@yield('header_title', 'Campus Connect')</h2>
                <p>@yield('header_subtitle', 'Panel Administrativo de Solicitudes y Reclamos')</p>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline btn-sm">
                    Ver Solicitudes
                </a>
                <span class="btn-badge">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
                    Sistema Activo
                </span>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
