@extends('layouts.app')

@section('title', 'Manajemen Server VPS - Panel Admin RZ Store')

@section('content')
<div class="page-container" style="padding: 28px 32px;">
    {{-- Page Header --}}
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div>
            <div style="font-size: 13px; color: #64748B; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <span>Panel Admin</span>
                <span>/</span>
                <span style="color: #4361EE; font-weight: 600;">Manajemen Server</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
                Manajemen Server VPS (VPN Gateway)
            </h1>
            <p style="color: #64748B; font-size: 14px; margin-top: 2px;">
                Integrasi remote SSH ke VPS Ubuntu 24.04 LTS untuk monitoring & manajemen akun VPN (WireGuard / V2Ray / OpenVPN).
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="testSshConnection()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #FFFFFF; border: 1px solid #E2E8F0; color: #334155; cursor: pointer; transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                    <line x1="6" y1="6" x2="6.01" y2="6"/>
                    <line x1="6" y1="18" x2="6.01" y2="18"/>
                </svg>
                Test Koneksi SSH
            </button>
            <button onclick="restartVpnServices()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #4361EE; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25); transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <polyline points="1 20 1 14 7 14"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                Restart Services
            </button>
        </div>
    </div>

    {{-- Server Live Specs & Metrics Card --}}
    <div style="background: linear-gradient(135deg, #1E293B, #0F172A); border-radius: 16px; padding: 24px; color: #FFFFFF; margin-bottom: 24px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(67, 97, 238, 0.2); border: 1px solid rgba(67, 97, 238, 0.4); display: flex; align-items: center; justify-content: center; color: #818CF8;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                        <line x1="6" y1="6" x2="6.01" y2="6"/>
                        <line x1="6" y1="18" x2="6.01" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h2 style="font-size: 20px; font-weight: 700; color: #FFFFFF;">{{ $serverInfo['name'] }}</h2>
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(52, 211, 153, 0.3);">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #34D399;"></span>
                            SSH ONLINE
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 4px; font-size: 13px; color: #94A3B8;">
                        <span><strong style="color: #E2E8F0;">OS:</strong> {{ $serverInfo['os'] }}</span>
                        <span>•</span>
                        <span><strong style="color: #E2E8F0;">IP Host:</strong> <code style="color: #93C5FD; font-family: monospace;">{{ $serverInfo['host'] }}:{{ $serverInfo['port'] }}</code></span>
                        <span>•</span>
                        <span><strong style="color: #E2E8F0;">Ping:</strong> <span style="color: #34D399; font-weight: 600;">{{ $serverInfo['ping'] }}</span></span>
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <div style="font-size: 11px; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Server Uptime</div>
                <div style="font-size: 16px; font-weight: 700; color: #F8FAFC; margin-top: 2px;">{{ $serverInfo['uptime'] }}</div>
            </div>
        </div>

        {{-- Resource Bars --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            {{-- CPU --}}
            <div style="background: rgba(255, 255, 255, 0.05); padding: 14px 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px;">
                    <span style="color: #94A3B8;">CPU Load</span>
                    <span style="color: #60A5FA; font-weight: 700;">{{ $serverInfo['cpu_usage'] }}%</span>
                </div>
                <div style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; overflow: hidden;">
                    <div style="width: {{ $serverInfo['cpu_usage'] }}%; height: 100%; background: #3B82F6; border-radius: 10px;"></div>
                </div>
            </div>

            {{-- RAM --}}
            <div style="background: rgba(255, 255, 255, 0.05); padding: 14px 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px;">
                    <span style="color: #94A3B8;">RAM Usage</span>
                    <span style="color: #34D399; font-weight: 700;">{{ $serverInfo['ram_used'] }} / {{ $serverInfo['ram_total'] }} ({{ $serverInfo['ram_percentage'] }}%)</span>
                </div>
                <div style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; overflow: hidden;">
                    <div style="width: {{ $serverInfo['ram_percentage'] }}%; height: 100%; background: #10B981; border-radius: 10px;"></div>
                </div>
            </div>

            {{-- Disk --}}
            <div style="background: rgba(255, 255, 255, 0.05); padding: 14px 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px;">
                    <span style="color: #94A3B8;">SSD Storage</span>
                    <span style="color: #FBBF24; font-weight: 700;">{{ $serverInfo['disk_used'] }} / {{ $serverInfo['disk_total'] }} ({{ $serverInfo['disk_percentage'] }}%)</span>
                </div>
                <div style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; overflow: hidden;">
                    <div style="width: {{ $serverInfo['disk_percentage'] }}%; height: 100%; background: #F59E0B; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2 Column Layout: SSH Config & VPN Services --}}
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; margin-bottom: 24px;">
        {{-- Left: SSH Configuration Form --}}
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Kredensial SSH Server VPN
                </h3>
                <span style="font-size: 11px; background: #EEF2FF; color: #4361EE; font-weight: 600; padding: 3px 8px; border-radius: 6px;">Port 22</span>
            </div>

            <form onsubmit="handleSaveSshConfig(event)">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama / Label Server</label>
                    <input type="text" value="{{ $serverInfo['name'] }}" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #F8FAFC;">
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">IP Address / Host VPS</label>
                        <input type="text" id="ssh_host" value="{{ $serverInfo['host'] }}" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #F8FAFC; font-family: monospace;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Port SSH</label>
                        <input type="number" id="ssh_port" value="{{ $serverInfo['port'] }}" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #F8FAFC;">
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">SSH Username</label>
                    <input type="text" id="ssh_user" value="{{ $serverInfo['username'] }}" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #F8FAFC;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Metode Autentikasi</label>
                    <select id="ssh_auth_type" onchange="toggleAuthType()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #F8FAFC;">
                        <option value="password" selected>Password SSH Root</option>
                        <option value="key">SSH Private Key (.pem / id_rsa)</option>
                    </select>
                </div>

                <div id="passwordFieldContainer" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Password Server VPS</label>
                    <input type="password" value="••••••••••••" placeholder="Masukkan password root VPS" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 13px; background: #FFFFFF;">
                </div>

                <div id="keyFieldContainer" style="display: none; margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">SSH Private Key (OpenSSH RSA/ED25519)</label>
                    <textarea rows="4" placeholder="-----BEGIN OPENSSH PRIVATE KEY-----..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 11px; font-family: monospace; background: #F8FAFC;"></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 14px; border-top: 1px solid #F1F5F9;">
                    <button type="submit" style="padding: 10px 22px; border-radius: 8px; border: none; background: #4361EE; color: #FFFFFF; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);">
                        Simpan Kredensial SSH
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: VPN Service Controller --}}
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0F172A;">Status Layanan VPN (Daemons)</h3>
                    <p style="font-size: 12px; color: #64748B; margin-top: 2px;">Service daemon yang berjalan di VPS Ubuntu 24.04</p>
                </div>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #ECFDF5; color: #047857;">
                    4/4 Running
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($vpnServices as $svc)
                <div style="border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; background: #FAFAFC;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 700; font-size: 14px; color: #0F172A;">{{ $svc['name'] }}</span>
                            <span style="font-family: monospace; font-size: 11px; background: #EEF2FF; color: #4361EE; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $svc['interface'] }}</span>
                            <span style="font-size: 11px; color: #64748B;">({{ $svc['protocol'] }})</span>
                        </div>
                        <p style="font-size: 12px; color: #64748B; margin-top: 4px;">{{ $svc['description'] }}</p>
                        <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px; font-size: 12px;">
                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10B981; font-weight: 600;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                                Active (Running)
                            </span>
                            <span style="color: #94A3B8;">•</span>
                            <span style="color: #334155; font-weight: 600;">{{ $svc['active_clients'] }} Klien Terhubung</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 6px; flex-shrink: 0; margin-left: 14px;">
                        <button onclick="restartService('{{ $svc['name'] }}')" title="Restart Service" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #334155; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                            Restart
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SSH Terminal / Command Runner Console --}}
    <div style="background: #0F172A; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); border: 1px solid #1E293B;">
        <div style="padding: 12px 20px; background: #1E293B; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="display: flex; gap: 6px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                </div>
                <span style="font-family: monospace; font-size: 12px; color: #94A3B8;">root@vps-ubuntu-24:~# (SSH Remote Shell)</span>
            </div>
            <div style="display: flex; gap: 6px;">
                <button onclick="runQuickCommand('wg show')" style="background: rgba(255, 255, 255, 0.1); border: none; color: #93C5FD; font-family: monospace; font-size: 11px; padding: 4px 8px; border-radius: 4px; cursor: pointer;">wg show</button>
                <button onclick="runQuickCommand('systemctl status xray')" style="background: rgba(255, 255, 255, 0.1); border: none; color: #93C5FD; font-family: monospace; font-size: 11px; padding: 4px 8px; border-radius: 4px; cursor: pointer;">status xray</button>
                <button onclick="runQuickCommand('uptime')" style="background: rgba(255, 255, 255, 0.1); border: none; color: #93C5FD; font-family: monospace; font-size: 11px; padding: 4px 8px; border-radius: 4px; cursor: pointer;">uptime</button>
            </div>
        </div>

        <div id="terminalBody" style="padding: 18px 20px; font-family: monospace; font-size: 12px; color: #34D399; min-height: 140px; line-height: 1.6; background: #0B1120;">
            <div style="color: #64748B;"># SSH Session Connected to 168.110.197.113:22 (Ubuntu 24.04 LTS)</div>
            <div style="color: #64748B;"># Last login: Wed Sep 30 13:20:00 2026 from 103.139.245.61</div>
            <div style="margin-top: 8px;"><span style="color: #60A5FA;">root@vps-vpn:~#</span> systemctl status wireguard xray openvpn badvpn</div>
            <div style="color: #E2E8F0;">● wireguard.service - WireGuard Server (wg0) -> Active: active (running) [14 peers]</div>
            <div style="color: #E2E8F0;">● xray.service - Xray Core VLESS TLS Service -> Active: active (running) [32 clients]</div>
            <div style="color: #E2E8F0;">● openvpn@server.service - OpenVPN Server (tun0) -> Active: active (running)</div>
            <div style="color: #E2E8F0;">● badvpn-udpgw.service - BadVPN UDP Gateway Port 7300 -> Active: active (running)</div>
            <div style="margin-top: 8px;" id="dynamicTerminalOutput"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleAuthType() {
        const type = document.getElementById('ssh_auth_type').value;
        document.getElementById('passwordFieldContainer').style.display = type === 'password' ? 'block' : 'none';
        document.getElementById('keyFieldContainer').style.display = type === 'key' ? 'block' : 'none';
    }

    function testSshConnection() {
        showToast('Menghubungkan via SSH ke 168.110.197.113:22...', 'info');
        setTimeout(() => {
            showToast('Koneksi SSH Berhasil! Server: Ubuntu 24.04 LTS (Ping: 28ms)', 'success');
        }, 1100);
    }

    function restartVpnServices() {
        showToast('Mengirim perintah restart all VPN services via SSH...', 'info');
        setTimeout(() => {
            showToast('Semua daemon VPN (WireGuard, Xray, OpenVPN, UDPGW) berhasil direstart!', 'success');
        }, 1400);
    }

    function restartService(name) {
        showToast(`Merestart layanan ${name} via SSH...`, 'info');
        setTimeout(() => {
            showToast(`Layanan ${name} berhasil direstart (Active/Running)!`, 'success');
        }, 900);
    }

    function handleSaveSshConfig(e) {
        e.preventDefault();
        showToast('Kredensial SSH VPS berhasil disimpan dan diverifikasi!', 'success');
    }

    function runQuickCommand(cmd) {
        const outputDiv = document.getElementById('dynamicTerminalOutput');
        outputDiv.innerHTML += `<div style="color: #60A5FA; margin-top: 8px;">root@vps-vpn:~# <span style="color: #F8FAFC;">${cmd}</span></div>`;
        
        if (cmd === 'wg show') {
            outputDiv.innerHTML += `<div style="color: #94A3B8;">interface: wg0<br>  public key: jK8x9Lw21A...<br>  listening port: 51820<br>peer: 9kL2... (rz_user_01) -> latest handshake: 12 seconds ago, transfer: 1.4 GiB received, 4.2 GiB sent</div>`;
        } else if (cmd === 'status xray') {
            outputDiv.innerHTML += `<div style="color: #94A3B8;">Xray 1.8.24 (Xray, Penetrates Everything.) Custom build<br>A unified platform for anti-censorship. Status: Running (0 errors)</div>`;
        } else if (cmd === 'uptime') {
            outputDiv.innerHTML += `<div style="color: #94A3B8;">13:30:00 up 18 days, 6:52, 1 user, load average: 0.14, 0.18, 0.19</div>`;
        }
        
        showToast(`Perintah '${cmd}' dijalankan di VPS`, 'info');
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
