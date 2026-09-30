@extends('layouts.app')

@section('title', 'Riwayat Pesanan - RZ Store')

@section('content')
    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <h1 class="page-title">Riwayat Pesanan</h1>
        <p class="page-subtitle">Lacak status pembayaran QRIS, nomor serial voucher, dan akun VPN Anda</p>
    </div>

    {{-- Stats Widgets --}}
    <div class="stats-grid" style="margin-bottom: 20px;">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Pesanan</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Pesanan Selesai</div>
                <div class="stat-value" style="color: #10B981;">{{ $completedOrders }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <div>
                <div class="stat-label">Menunggu Pembayaran</div>
                <div class="stat-value" style="color: #F59E0B;">{{ $pendingOrders }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

    {{-- Filter & Search Bar --}}
    <div class="content-card" style="margin-bottom: 16px;">
        <div style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="max-width: 340px; flex: 1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="search-input" placeholder="Cari ref, produk, atau nomor tujuan..." id="orderSearchInput" onkeyup="searchOrders()">
            </div>

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('pesanan') }}" class="btn {{ empty($statusFilter) || $statusFilter === 'all' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="border-radius: 20px; padding: 6px 14px;">
                    Semua ({{ $totalOrders }})
                </a>
                <a href="{{ route('pesanan', ['status' => 'pending']) }}" class="btn {{ $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="border-radius: 20px; padding: 6px 14px;">
                    Menunggu ({{ $pendingOrders }})
                </a>
                <a href="{{ route('pesanan', ['status' => 'completed']) }}" class="btn {{ $statusFilter === 'completed' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="border-radius: 20px; padding: 6px 14px;">
                    Selesai ({{ $completedOrders }})
                </a>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="content-card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Nomor Referensi</th>
                        <th>Produk & Tujuan</th>
                        <th>Total Transfer</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @forelse($orders as $ord)
                    @php $badge = $ord->status_badge; @endphp
                    <tr class="order-row" data-search="{{ strtolower($ord->order_ref . ' ' . $ord->product_name . ' ' . $ord->target . ' ' . $ord->category) }}">
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
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="provider-icon {{ $ord->category === 'VPN Premium' ? 'vpn' : strtolower($ord->category) }}" style="width: 34px; height: 34px; font-size: 11px; border-radius: 10px; flex-shrink: 0; font-weight: 800;">
                                    {{ substr($ord->category, 0, 2) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #0F172A; font-size: 13px;">{{ $ord->product_name }}</div>
                                    <div style="font-size: 12px; color: #64748B; margin-top: 1px; font-family: monospace;">
                                        🎯 {{ $ord->target }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 800; color: #0F172A; font-size: 14px;">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </div>
                            @if($ord->unique_code > 0)
                                <div style="font-size: 11px; color: #10B981; font-weight: 600;">(Kode: +{{ $ord->unique_code }})</div>
                            @endif
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
                                    <a href="{{ route('checkout', ['ref' => $ord->order_ref]) }}" class="btn btn-primary btn-sm" style="padding: 6px 14px; border-radius: 8px; font-weight: 700;">
                                        Bayar QRIS
                                    </a>
                                @endif
                                <button class="btn btn-outline btn-sm" onclick="showOrderDetail('{{ $ord->order_ref }}')" style="padding: 6px 12px; border-radius: 8px;">
                                    Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-empty" style="padding: 48px 24px; text-align: center;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📋</div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Belum Ada Riwayat Pesanan</h3>
                            <p style="font-size: 13px; color: #64748B; margin-bottom: 16px;">Silakan pilih paket kuota atau VPN di katalog untuk mulai berbelanja.</p>
                            <a href="{{ route('katalog') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                                Jelajahi Katalog Sekarang →
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal-overlay" id="orderDetailModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
        <div class="modal-content" style="background: #FFFFFF; border-radius: 20px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(226, 232, 240, 0.8); overflow: hidden;">
            <div style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #F1F5F9; background: #FFFFFF;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EEF2FF; color: #4361EE; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0;">Detail Transaksi</h3>
                        <div id="detailRefNumber" style="font-size: 12px; font-family: monospace; color: #4361EE; font-weight: 700;">-</div>
                    </div>
                </div>
                <button onclick="closeDetailModal()" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; border: none; color: #64748B; font-size: 20px; cursor: pointer;">&times;</button>
            </div>

            <div style="padding: 24px; background: #FFFFFF;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px dashed #E2E8F0;">
                    <span style="font-size: 13px; color: #64748B;">Status Pembayaran</span>
                    <span id="detailBadgeContainer"></span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Tanggal Transaksi</span>
                        <span id="detailDate" style="font-weight: 600; color: #0F172A;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Produk</span>
                        <span id="detailProduct" style="font-weight: 700; color: #0F172A; text-align: right;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Nomor Tujuan / Akun</span>
                        <span id="detailTarget" style="font-family: monospace; font-weight: 700; color: #0F172A;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Metode Bayar</span>
                        <span style="font-weight: 600; color: #0F172A;">QRIS Dinamis (DANA Bisnis)</span>
                    </div>
                    <div id="detailSnRow" style="display: none; justify-content: space-between;">
                        <span style="color: #64748B;">Nomor Serial (SN)</span>
                        <span id="detailSn" style="font-family: monospace; font-weight: 700; color: #059669;">-</span>
                    </div>
                </div>

                <div style="background: #F8FAFC; border-radius: 12px; padding: 14px 16px; margin-top: 18px; border: 1px solid #E2E8F0;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span style="color: #64748B;">Harga Produk</span>
                        <span id="detailBasePrice" style="color: #334155; font-weight: 600;">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                        <span style="color: #64748B;">Kode Unik Transaksi</span>
                        <span id="detailUniqueCode" style="color: #10B981; font-weight: 700;">+Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 800; color: #0F172A; padding-top: 8px; border-top: 1px solid #E2E8F0;">
                        <span>Total Bayar</span>
                        <span id="detailTotalAmount" style="color: #4361EE;">Rp 0</span>
                    </div>
                </div>
            </div>

            <div style="padding: 16px 24px; background: #FAFBFD; border-top: 1px solid #F1F5F9; display: flex; justify-content: flex-end; gap: 10px;">
                <button onclick="closeDetailModal()" class="btn btn-secondary" style="padding: 10px 18px; border-radius: 10px;">Tutup</button>
                <a id="detailPayLink" href="#" class="btn btn-primary" style="display: none; padding: 10px 20px; border-radius: 10px; font-weight: 700;">
                    Bayar Sekarang →
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function searchOrders() {
        const query = document.getElementById('orderSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.order-row');
        rows.forEach(row => {
            const text = row.dataset.search || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function showOrderDetail(ref) {
        fetch(`/pesanan/${ref}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Data pesanan tidak ditemukan');
                    return;
                }
                const ord = data.order;
                const badge = data.badge;

                document.getElementById('detailRefNumber').innerText = ord.order_ref;
                document.getElementById('detailDate').innerText = data.formatted_date;
                document.getElementById('detailProduct').innerText = ord.product_name;
                document.getElementById('detailTarget').innerText = ord.target;
                document.getElementById('detailBasePrice').innerText = data.formatted_base;
                document.getElementById('detailUniqueCode').innerText = '+Rp ' + ord.unique_code;
                document.getElementById('detailTotalAmount').innerText = data.formatted_total;

                document.getElementById('detailBadgeContainer').innerHTML = `
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: ${badge.bg}; color: ${badge.color}; border: 1px solid ${badge.border};">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: ${badge.color};"></span>
                        ${badge.label}
                    </span>
                `;

                const snRow = document.getElementById('detailSnRow');
                if (ord.serial_number) {
                    snRow.style.display = 'flex';
                    document.getElementById('detailSn').innerText = ord.serial_number;
                } else {
                    snRow.style.display = 'none';
                }

                const payLink = document.getElementById('detailPayLink');
                if (ord.status === 'pending') {
                    payLink.style.display = 'inline-flex';
                    payLink.href = data.checkout_url;
                } else {
                    payLink.style.display = 'none';
                }

                const modal = document.getElementById('orderDetailModal');
                modal.style.display = 'flex';
            })
            .catch(() => {
                alert('Terjadi kesalahan saat memuat detail pesanan');
            });
    }

    function closeDetailModal() {
        document.getElementById('orderDetailModal').style.display = 'none';
    }

    document.getElementById('orderDetailModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDetailModal();
    });
</script>
@endsection
