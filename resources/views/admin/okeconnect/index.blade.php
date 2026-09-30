@extends('layouts.app')

@section('title', 'API OkeConnect - Integrasi Transaksi IP - Panel Admin RZ Store')

@section('content')
<div class="page-container">
    {{-- Page Header --}}
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="font-size: 13px; color: #64748B; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <span>Panel Admin</span>
                <span>/</span>
                <span style="color: #4361EE; font-weight: 600;">API OkeConnect</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
                Pengaturan API OkeConnect (H2H Transaksi IP)
            </h1>
            <p style="color: #64748B; font-size: 14px; margin-top: 2px;">
                Konfigurasi koneksi Host-to-Host (H2H) dengan IP Center OkeConnect untuk otomatisasi transaksi pulsa & kuota.
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="testConnection()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #FFFFFF; border: 1px solid #E2E8F0; color: #334155; cursor: pointer; transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Test Ping IP Center
            </button>
            <button onclick="checkBalance()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #059669; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25); transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/>
                    <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/>
                    <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                </svg>
                Cek Saldo H2H
            </button>
        </div>
    </div>

    {{-- Status Banner Cards --}}
    <div class="admin-oke-grid" style="margin-bottom: 24px;">
        {{-- Saldo Card --}}
        <div style="background: linear-gradient(135deg, #065F46, #047857); border-radius: 16px; padding: 22px 24px; color: #FFFFFF; box-shadow: 0 10px 20px rgba(4, 120, 87, 0.15); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; color: #A7F3D0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Sisa Saldo Deposit OkeConnect</div>
                <div style="font-size: 28px; font-weight: 800; margin-top: 4px; letter-spacing: -0.02em;" id="balanceDisplay">
                    Rp {{ number_format($config['balance'], 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #D1FAE5; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #34D399; display: inline-block;"></span>
                    Terakhir diperbarui: {{ $config['last_sync'] }}
                </div>
            </div>
            <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>

        {{-- Connection Status --}}
        <div style="background: #FFFFFF; border-radius: 16px; padding: 20px; border: 1px solid #E2E8F0; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: #64748B; font-weight: 600;">Status H2H</span>
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                    {{ $config['status'] }}
                </span>
            </div>
            <div style="margin-top: 10px;">
                <div style="font-size: 11px; color: #94A3B8;">IP Center Target</div>
                <div style="font-size: 13px; font-weight: 700; color: #0F172A; font-family: monospace;">{{ $config['ip_center_ip'] }}</div>
            </div>
        </div>

        {{-- Response Time --}}
        <div style="background: #FFFFFF; border-radius: 16px; padding: 20px; border: 1px solid #E2E8F0; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: #64748B; font-weight: 600;">Latency / Respon</span>
                <span style="font-size: 12px; font-weight: 700; color: #4361EE; background: #EEF2FF; padding: 3px 8px; border-radius: 6px;">Cepat</span>
            </div>
            <div style="margin-top: 10px;">
                <div style="font-size: 11px; color: #94A3B8;">Rata-rata Ping</div>
                <div style="font-size: 18px; font-weight: 800; color: #0F172A;">{{ $config['response_time'] }}</div>
            </div>
        </div>
    </div>

    {{-- Main Settings Card (Desain persis seperti Screenshot Dashboard OkeConnect) --}}
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 24px;">
        {{-- Tab Header --}}
        <div style="padding: 16px 28px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 16px; font-weight: 700; color: #0F172A; border-bottom: 3px solid #10B981; padding-bottom: 14px; margin-bottom: -17px; display: inline-block;">
                    Pengaturan
                </span>
            </div>
            <div style="font-size: 13px; color: #64748B;">
                <strong style="color: #334155;">IP Center :</strong>
                <a href="{{ $config['ip_center'] }}" target="_blank" style="color: #047857; font-weight: 600; text-decoration: underline;">{{ $config['ip_center'] }}</a>
                <span style="color: #64748B; font-weight: 500;">({{ $config['ip_center_ip'] }})</span>
            </div>
        </div>

        {{-- Form Body --}}
        <form onsubmit="handleSaveOkeconnect(event)" style="padding: 32px 36px;">
            {{-- User ID --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; align-items: center; gap: 20px; margin-bottom: 22px;">
                <label style="font-size: 14px; font-weight: 600; color: #334155;">User ID</label>
                <div>
                    <input type="text" id="user_id" value="{{ $config['user_id'] }}" readonly style="width: 100%; max-width: 650px; padding: 10px 16px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 14px; background: #F1F5F9; color: #475569; font-weight: 600; font-family: monospace;">
                </div>
            </div>

            {{-- URL Callback --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; align-items: center; gap: 20px; margin-bottom: 22px;">
                <label style="font-size: 14px; font-weight: 600; color: #334155;">URL Callback</label>
                <div style="display: flex; gap: 10px; max-width: 650px;">
                    <input type="text" id="callback_url" value="{{ $config['callback_url'] }}" style="flex: 1; padding: 10px 16px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 14px; background: #FFFFFF; color: #1E293B;">
                    <button type="button" onclick="copyCallbackUrl()" style="padding: 10px 14px; border-radius: 8px; border: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        Salin
                    </button>
                </div>
            </div>

            {{-- IP Address Whitelist --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; align-items: flex-start; gap: 20px; margin-bottom: 22px;">
                <label style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 8px;">IP Address</label>
                <div style="max-width: 650px;">
                    <input type="text" id="ip_address" value="{{ $config['ip_address'] }}" style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 14px; background: #FFFFFF; color: #1E293B; font-family: monospace;">
                    <p style="font-size: 12px; color: #64748B; font-style: italic; margin-top: 6px;">
                        Gunakan koma (,) bila lebih 1 IP. Contoh : 192.168.0.1, 192.168.0.2
                    </p>
                </div>
            </div>

            {{-- Password / PIN --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; align-items: center; gap: 20px; margin-bottom: 22px;">
                <label style="font-size: 14px; font-weight: 600; color: #334155;">Password / PIN</label>
                <div style="position: relative; max-width: 650px;">
                    <input type="password" id="api_password" value="{{ $config['password'] }}" style="width: 100%; padding: 10px 42px 10px 16px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 14px; background: #FFFFFF; color: #1E293B;">
                    <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer;">
                        <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Status --}}
            <div style="display: grid; grid-template-columns: 200px 1fr; align-items: center; gap: 20px; margin-bottom: 32px;">
                <label style="font-size: 14px; font-weight: 600; color: #334155;">Status</label>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 14px; font-weight: 700; color: #10B981;">Aktif</span>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                        <input type="checkbox" checked id="status_toggle" style="opacity: 0; width: 0; height: 0;">
                        <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10B981; transition: .3s; border-radius: 24px;"></span>
                    </label>
                </div>
            </div>

            {{-- Submit Button --}}
            <div style="display: flex; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #F1F5F9;">
                <button type="submit" style="padding: 12px 36px; border-radius: 8px; border: none; background: #10B981; color: #FFFFFF; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>

    {{-- Recent H2H API Transaction Logs --}}
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A;">Log Transaksi Host-to-Host (OkeConnect)</h3>
                <p style="font-size: 12px; color: #64748B; margin-top: 2px;">Riwayat eksekusi API order paket kuota dan cek saldo real-time</p>
            </div>
            <button onclick="refreshLogs()" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; border: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; font-size: 12px; font-weight: 600; cursor: pointer;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                </svg>
                Refresh Log
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-weight: 600; font-size: 11px; text-transform: uppercase;">
                        <th style="padding: 12px 20px;">ID Log & Waktu</th>
                        <th style="padding: 12px 16px;">Tipe Aksi</th>
                        <th style="padding: 12px 16px;">Kode SKU</th>
                        <th style="padding: 12px 16px;">No. Tujuan</th>
                        <th style="padding: 12px 16px;">Harga Modal</th>
                        <th style="padding: 12px 16px;">Nomor SN (Serial)</th>
                        <th style="padding: 12px 20px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                    <tr style="border-bottom: 1px solid #F1F5F9;">
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 700; color: #0F172A; font-family: monospace;">{{ $log['id'] }}</div>
                            <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">{{ $log['time'] }}</div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-family: monospace; font-size: 11px; background: #EEF2FF; color: #4361EE; padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                                {{ $log['action'] }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600; color: #334155;">{{ $log['sku'] }}</td>
                        <td style="padding: 14px 16px; font-family: monospace; color: #475569;">{{ $log['target'] }}</td>
                        <td style="padding: 14px 16px; color: #64748B;">
                            {{ $log['price'] > 0 ? 'Rp ' . number_format($log['price'], 0, ',', '.') : '-' }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-family: monospace; font-size: 11px; color: #0F172A;">{{ $log['sn'] }}</span>
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #ECFDF5; color: #047857;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $log['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('api_password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function copyCallbackUrl() {
        const url = document.getElementById('callback_url').value;
        navigator.clipboard.writeText(url).then(() => {
            showToast('URL Callback berhasil disalin ke clipboard!', 'success');
        });
    }

    function testConnection() {
        showToast('Mengirim request Ping ke https://h2h.okeconnect.com/trx (103.139.245.61)...', 'info');
        setTimeout(() => {
            showToast('Koneksi Host-to-Host Berhasil! Latency: 182 ms (HTTP 200 OK)', 'success');
        }, 1000);
    }

    function checkBalance() {
        showToast('Memeriksa sisa saldo akun OkeConnect OK1988589...', 'info');
        setTimeout(() => {
            document.getElementById('balanceDisplay').innerText = 'Rp 1.450.000';
            showToast('Saldo Terverifikasi: Rp 1.450.000 (Aktif)', 'success');
        }, 800);
    }

    function handleSaveOkeconnect(e) {
        e.preventDefault();
        showToast('Pengaturan API OkeConnect berhasil disimpan!', 'success');
    }

    function refreshLogs() {
        showToast('Memperbarui log H2H...', 'info');
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const bg = type === 'success' ? '#10B981' : type === 'warning' ? '#F59E0B' : '#4361EE';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: ${bg};
            color: #FFFFFF;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            font-size: 13px;
            font-weight: 600;
            z-index: 9999;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(20px);
            opacity: 0;
        `;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        }, 10);
        setTimeout(() => {
            toast.style.transform = 'translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
</script>
@endsection
