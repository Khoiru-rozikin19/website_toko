@extends('layouts.app')

@section('title', 'Checkout - RZ Store')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">Checkout Pembayaran</h1>
        <p class="page-subtitle">Selesaikan pembayaran Anda menggunakan QRIS</p>
    </div>

    <div class="checkout-grid">
        {{-- Left: Order Details --}}
        <div class="checkout-form-section">
            {{-- Order Summary Card --}}
            <div class="content-card">
                <div class="content-card-header">
                    <h2 class="content-card-title">📋 Detail Pesanan</h2>
                    <span class="badge badge-warning">Menunggu Pembayaran</span>
                </div>
                <div class="content-card-body">
                    <div class="order-detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Nomor Referensi</div>
                            <div class="detail-value" style="color:#4361EE;font-family:monospace;" id="checkoutRef">{{ $orderRef }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tanggal</div>
                            <div class="detail-value" id="checkoutDate">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Produk</div>
                            <div class="detail-value" id="checkoutProduct">{{ $productName }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Nomor Tujuan / Akun</div>
                            <div class="detail-value" id="checkoutTarget">{{ $target }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Instructions --}}
            <div class="content-card">
                <div class="content-card-header">
                    <h2 class="content-card-title">📖 Cara Pembayaran</h2>
                </div>
                <div class="content-card-body">
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div style="display:flex;gap:14px;align-items:flex-start;">
                            <div style="width:28px;height:28px;background:#EEF2FF;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#4361EE;flex-shrink:0;">1</div>
                            <div>
                                <div style="font-weight:600;color:#1E293B;font-size:14px;margin-bottom:2px;">Buka Aplikasi E-Wallet / M-Banking</div>
                                <div style="color:#64748B;font-size:13px;">Buka DANA, GoPay, OVO, ShopeePay, BCA, Mandiri, BRImo atau bank lain</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:14px;align-items:flex-start;">
                            <div style="width:28px;height:28px;background:#EEF2FF;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#4361EE;flex-shrink:0;">2</div>
                            <div>
                                <div style="font-weight:600;color:#1E293B;font-size:14px;margin-bottom:2px;">Scan QR Code Dinamis</div>
                                <div style="color:#64748B;font-size:13px;">Nominal Rp {{ number_format($totalAmount, 0, ',', '.') }} sudah terisi otomatis (termasuk kode unik)</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:14px;align-items:flex-start;">
                            <div style="width:28px;height:28px;background:#EEF2FF;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#4361EE;flex-shrink:0;">3</div>
                            <div>
                                <div style="font-weight:600;color:#1E293B;font-size:14px;margin-bottom:2px;">Konfirmasi Pembayaran</div>
                                <div style="color:#64748B;font-size:13px;">Bayar persis sesuai nominal agar notifikasi DANA Bisnis langsung memproses pesanan otomatis.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Support Card --}}
            <div class="content-card" style="border-color:#DBEAFE;">
                <div class="content-card-body" style="display:flex;align-items:center;gap:16px;">
                    <div style="width:48px;height:48px;background:#EEF2FF;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">💬</div>
                    <div>
                        <div style="font-weight:600;color:#1E293B;font-size:14px;margin-bottom:2px;">Butuh Bantuan Pembayaran?</div>
                        <div style="color:#64748B;font-size:13px;">Hubungi Admin RZ Store jika nominal transfer tidak sesuai</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: QRIS Payment --}}
        <div class="qris-card">
            <div class="qris-header">
                <div class="qris-title">Scan QRIS Dinamis</div>
                <div class="qris-subtitle">Mendukung semua e-wallet & mobile banking</div>
            </div>

            {{-- Timer --}}
            <div class="qris-timer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                </svg>
                <span>Sisa waktu: <strong id="timerDisplay">15:00</strong></span>
            </div>

            {{-- QR Code Display --}}
            <div class="qris-code-wrapper" style="display:flex;justify-content:center;align-items:center;padding:16px;background:#FFFFFF;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.06);">
                {!! $qrSvg !!}
            </div>

            {{-- Amount --}}
            <div class="qris-amount" id="qrisAmount" style="margin-top:12px;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            <div class="qris-merchant">RZ Store • Dana Bisnis</div>
            <div style="font-size:12px;color:#059669;background:#ECFDF5;padding:4px 10px;border-radius:6px;margin:8px auto;display:inline-block;font-weight:600;">
                Kode Unik: +Rp {{ $uniqueCode }}
            </div>

            {{-- Divider --}}
            <div class="qris-divider"></div>

            {{-- Order Summary --}}
            <div class="order-summary-item">
                <span class="label">Harga Paket</span>
                <span class="value">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
            </div>
            <div class="order-summary-item">
                <span class="label">Kode Unik Verifikasi</span>
                <span class="value" style="color:#059669;font-weight:700;">+Rp {{ $uniqueCode }}</span>
            </div>
            <div class="order-summary-total">
                <span class="label">Total Transfer</span>
                <span class="value" id="summaryTotal" style="color:#4361EE;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            {{-- Supported Payments --}}
            <div style="margin-top:20px;display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#F1F5F9;border-radius:8px;font-size:11px;font-weight:600;color:#64748B;">
                    <span style="font-size:14px;">💳</span> Dana
                </div>
                <div style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#F1F5F9;border-radius:8px;font-size:11px;font-weight:600;color:#64748B;">
                    <span style="font-size:14px;">💳</span> GoPay
                </div>
                <div style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#F1F5F9;border-radius:8px;font-size:11px;font-weight:600;color:#64748B;">
                    <span style="font-size:14px;">💳</span> OVO
                </div>
                <div style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#F1F5F9;border-radius:8px;font-size:11px;font-weight:600;color:#64748B;">
                    <span style="font-size:14px;">💳</span> ShopeePay
                </div>
            </div>

            {{-- Check Payment Button --}}
            <button class="btn btn-primary w-full" style="margin-top:20px;width:100%;justify-content:center;" onclick="checkPaymentStatus()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
                Cek Status Pembayaran
            </button>
        </div>
    </div>
@endsection

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush

@section('scripts')

<script>
    // ============================================
    // QRIS ENCODER — Static → Dynamic
    // ============================================

    /**
     * Parse EMV QRIS TLV format into tag-value pairs.
     */
    function parseTLV(data) {
        const result = [];
        let i = 0;
        while (i < data.length) {
            const tag = data.substring(i, i + 2);
            i += 2;
            const len = parseInt(data.substring(i, i + 2), 10);
            i += 2;
            const value = data.substring(i, i + len);
            i += len;
            result.push({ tag, len, value });
        }
        return result;
    }

    /**
     * Calculate CRC16-CCITT checksum for QRIS.
     */
    function crc16CCITT(data) {
        let crc = 0xFFFF;
        for (let i = 0; i < data.length; i++) {
            crc ^= data.charCodeAt(i) << 8;
            for (let j = 0; j < 8; j++) {
                if (crc & 0x8000) {
                    crc = (crc << 1) ^ 0x1021;
                } else {
                    crc <<= 1;
                }
                crc &= 0xFFFF;
            }
        }
        return crc.toString(16).toUpperCase().padStart(4, '0');
    }

    /**
     * Build a TLV string from tag, value.
     */
    function buildTLV(tag, value) {
        return tag + String(value.length).padStart(2, '0') + value;
    }

    /**
     * Convert static QRIS to dynamic QRIS with a specified amount.
     * Changes tag 01 (Point of Initiation) from 11 (static) to 12 (dynamic)
     * and inserts tag 54 (Transaction Amount) with the given amount.
     */
    function staticToDynamic(staticQris, amount) {
        // Remove the CRC (last 4 chars after tag 63)
        const withoutCRC = staticQris.substring(0, staticQris.length - 4);
        const tags = parseTLV(withoutCRC);

        let output = '';
        let inserted54 = false;

        for (const t of tags) {
            if (t.tag === '01') {
                // Change from 11 (static) to 12 (dynamic)
                output += buildTLV('01', '12');
            } else if (t.tag === '63') {
                // Skip old CRC, will recalculate
                continue;
            } else {
                output += buildTLV(t.tag, t.value);
            }

            // Insert tag 54 (amount) after tag 53 (currency)
            if (t.tag === '53' && !inserted54) {
                const amountStr = String(amount);
                output += buildTLV('54', amountStr);
                inserted54 = true;
            }
        }

        // Add CRC tag (63) placeholder and calculate
        output += '6304';
        const crc = crc16CCITT(output);
        output += crc;

        return output;
    }

    // ============================================
    // PAGE INITIALIZATION
    // ============================================

    // Static QRIS string from Dana Bisnis
    const STATIC_QRIS = '00020101021126570011ID.DANA.WWW011893600915302634402802090263440280303UMI51440014ID.CO.QRIS.WWW0215ID10265391682640303UMI5204481453033605802ID5908rz store6015Kab. Ogan Komer610532159630485BE';

    // Parse URL params
    const params = new URLSearchParams(window.location.search);
    const productName = params.get('product') || 'Produk Digital';
    const price = parseInt(params.get('price') || '0', 10);
    const targetNumber = params.get('target') || '-';
    const notes = params.get('notes') || '';

    // Generate reference number
    const now = new Date();
    const refNum = 'RZ-' + now.getFullYear() +
        String(now.getMonth() + 1).padStart(2, '0') +
        String(now.getDate()).padStart(2, '0') + '-' +
        String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');

    // Populate page
    document.getElementById('checkoutRef').textContent = refNum;
    document.getElementById('checkoutDate').textContent = now.toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
    document.getElementById('checkoutProduct').textContent = productName;
    document.getElementById('checkoutTarget').textContent = targetNumber;
    document.getElementById('qrisAmount').textContent = 'Rp ' + price.toLocaleString('id-ID');
    document.getElementById('summaryProduct').textContent = productName;
    document.getElementById('summaryTarget').textContent = targetNumber;
    document.getElementById('summaryTotal').textContent = 'Rp ' + price.toLocaleString('id-ID');

    // Generate Dynamic QRIS with the order amount
    const qrisData = price > 0 ? staticToDynamic(STATIC_QRIS, price) : STATIC_QRIS;

    new QRCode(document.getElementById('qrCodeContainer'), {
        text: qrisData,
        width: 240,
        height: 240,
        colorDark: '#1E293B',
        colorLight: '#F8FAFC',
        correctLevel: QRCode.CorrectLevel.M
    });

    // ============================================
    // COUNTDOWN TIMER (15 minutes)
    // ============================================
    let timeLeft = 15 * 60; // 15 minutes in seconds

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timerDisplay').textContent =
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (timeLeft <= 0) {
            document.getElementById('timerDisplay').textContent = 'Expired';
            document.querySelector('.qris-timer').style.background = '#FEE2E2';
            document.querySelector('.qris-timer').style.color = '#DC2626';
            return;
        }

        if (timeLeft <= 60) {
            document.querySelector('.qris-timer').style.background = '#FEE2E2';
            document.querySelector('.qris-timer').style.color = '#DC2626';
        }

        timeLeft--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();

    // ============================================
    // PAYMENT STATUS CHECK (placeholder)
    // ============================================
    function checkPaymentStatus() {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
            </svg>
            Mengecek...
        `;
        btn.disabled = true;

        // Simulate checking (will be connected to backend later)
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showToast('Pembayaran belum diterima. Silakan scan QRIS dan bayar terlebih dahulu.', 'warning');
        }, 2000);
    }

    // Toast notification
    function showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const icons = {
            success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
            error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) toast.remove();
        }, 5000);
    }
</script>

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
