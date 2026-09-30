@extends('layouts.app')

@section('title', 'Dashboard - RZ Store')

@section('content')
    {{-- Hero Section --}}
    <div class="hero-section animate-fade-in-up">
        <span class="hero-badge">Selamat Datang</span>
        <h1 class="hero-title">Temukan Koneksi Tercepat Anda 👋</h1>
        <p class="hero-subtitle">
            Kelola pesanan produk digital Anda, cek status transaksi terbaru, atau nikmati
            belanja instan dari katalog kami.
        </p>
        <div class="hero-actions">
            <div class="hero-status-badges">
                <span class="status-badge">
                    <span class="status-dot green"></span>
                    Status Toko: Aktif
                </span>
                <span class="status-badge">
                    <span class="status-dot green"></span>
                    VPN Server: Online
                </span>
            </div>
            <a href="{{ route('katalog') }}" class="btn-hero">
                Jelajahi Katalog
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12,5 19,12 12,19"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card animate-fade-in-up animate-delay-1">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Pesanan</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up animate-delay-2">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Pesanan Selesai</div>
                <div class="stat-value" style="color: #10B981;">{{ $completedOrders }}</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up animate-delay-3">
            <div class="stat-icon yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Menunggu Pembayaran</div>
                <div class="stat-value" style="color: #F59E0B;">{{ $pendingOrders }}</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up animate-delay-4">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Belanja</div>
                <div class="stat-value">Rp {{ number_format($totalSpend, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- VPN Status Card --}}
    <div class="vpn-status-card animate-fade-in-up">
        <div class="vpn-status-header">
            <div class="vpn-status-title">🔒 Status Server VPN</div>
            <div class="vpn-status-indicator">
                <span class="status-dot green"></span>
                Online
            </div>
        </div>
        <div class="vpn-stats">
            <div class="vpn-stat">
                <div class="vpn-stat-value">3</div>
                <div class="vpn-stat-label">Server Aktif</div>
            </div>
            <div class="vpn-stat">
                <div class="vpn-stat-value">12</div>
                <div class="vpn-stat-label">User Online</div>
            </div>
            <div class="vpn-stat">
                <div class="vpn-stat-value">99.9%</div>
                <div class="vpn-stat-label">Uptime</div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="content-card animate-fade-in-up">
        <div class="content-card-header">
            <h2 class="content-card-title">Riwayat Pesanan Anda</h2>
            <a href="{{ route('pesanan') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Referensi</th>
                        <th>Produk</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $ord)
                    @php $badge = $ord->status_badge; @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1E293B; font-size: 13px;">{{ $ord->created_at->translatedFormat('d M Y') }}</div>
                            <div style="font-size: 11px; color: #94A3B8;">{{ $ord->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-weight: 700; color: #4361EE; background: #EEF2FF; padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                {{ $ord->order_ref }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0F172A; font-size: 13px;">{{ $ord->product_name }}</div>
                            <div style="font-size: 12px; color: #64748B; font-family: monospace;">🎯 {{ $ord->target }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 800; color: #0F172A; font-size: 13px;">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 1px solid {{ $badge['border'] }};">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $badge['color'] }};"></span>
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @if($ord->status === 'pending')
                                    <a href="{{ route('checkout', ['ref' => $ord->order_ref]) }}" class="btn btn-primary btn-sm" style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                        Bayar
                                    </a>
                                @endif
                                <a href="{{ route('pesanan') }}" class="btn btn-outline btn-sm" style="padding: 5px 10px; border-radius: 6px; font-size: 12px;">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:#CBD5E1;margin:0 auto 12px;display:block;">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                </svg>
                                Anda belum melakukan pembelian di toko ini.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
