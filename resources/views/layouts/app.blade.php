<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RZ Store - Toko Internet & VPN Terpercaya. Beli paket data, pulsa, dan layanan VPN dengan harga terbaik.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RZ Store - Toko Internet & VPN')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    <div class="app-layout">
        {{-- Sidebar Overlay (Mobile) --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            {{-- Logo --}}
            <div class="sidebar-header">
                <div class="sidebar-logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                </div>
                <span class="sidebar-logo-text">RZ STORE</span>
            </div>

            {{-- Navigation: Belanja --}}
            <div class="sidebar-section-label">Belanja</div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            <polyline points="9,22 9,12 15,12 15,22"/>
                        </svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('katalog') }}" class="sidebar-nav-item {{ request()->routeIs('katalog') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                    </span>
                    Katalog Produk
                </a>
                <a href="{{ route('pesanan') }}" class="sidebar-nav-item {{ request()->routeIs('pesanan') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                    </span>
                    Riwayat Pesanan
                </a>
            </nav>

            {{-- Navigation: Panel Admin (Only for Admin) --}}
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="sidebar-section-label" style="margin-top: 20px;">Panel Admin</div>
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.produk') }}" class="sidebar-nav-item {{ request()->routeIs('admin.produk*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </span>
                        Kelola Produk
                    </a>
                    <a href="{{ route('admin.okeconnect') }}" class="sidebar-nav-item {{ request()->routeIs('admin.okeconnect*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 18 22 12 16 6"/>
                                <polyline points="8 6 2 12 8 18"/>
                            </svg>
                        </span>
                        API OkeConnect
                    </a>
                    <a href="{{ route('admin.server') }}" class="sidebar-nav-item {{ request()->routeIs('admin.server*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                                <line x1="6" y1="6" x2="6.01" y2="6"/>
                                <line x1="6" y1="18" x2="6.01" y2="18"/>
                            </svg>
                        </span>
                        Manajemen Server
                    </a>
                </nav>
            @endif

            {{-- User Section (Bottom Left) --}}
            <div class="sidebar-user" style="margin-top: auto; padding: 14px 16px; background: #FAFBFD; border-top: 1px solid #E2E8F0;">
                @auth
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                            <div class="sidebar-user-avatar" style="width: 36px; height: 36px; border-radius: 10px; font-weight: 700; background: {{ Auth::user()->isAdmin() ? 'linear-gradient(135deg, #10B981, #059669)' : 'linear-gradient(135deg, #4361EE, #3A0CA3)' }}; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="sidebar-user-info" style="min-width: 0;">
                                <div class="sidebar-user-name" style="font-weight: 700; color: #0F172A; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ Auth::user()->name }}">
                                    {{ Auth::user()->name }}
                                </div>
                                <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: {{ Auth::user()->isAdmin() ? '#059669' : '#4361EE' }};">
                                    <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ Auth::user()->isAdmin() ? '#10B981' : '#4361EE' }};"></span>
                                    {{ Auth::user()->isAdmin() ? 'Admin' : 'User Biasa' }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" title="Keluar / Logout" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; border: none; color: #EF4444; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: #64748B; display: flex; align-items: center; justify-content: center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 12px; font-weight: 700; color: #1E293B;">Tamu</div>
                                <div style="font-size: 10px; color: #94A3B8;">Belum Masuk</div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; font-weight: 700;">
                            Masuk
                        </a>
                    </div>
                @endauth
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            {{-- Mobile Header --}}
            <div class="mobile-header">
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="mobile-header-logo">RZ STORE</span>
            </div>

            <div class="main-content-inner" style="padding: 0;">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Sidebar Toggle Script --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    </script>

    @yield('scripts')
</body>
</html>
