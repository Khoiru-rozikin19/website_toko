<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerController extends Controller
{
    /**
     * Display the VPS Server (VPN) management page.
     */
    public function index(Request $request): View
    {
        $serverInfo = [
            'name' => 'VPN SG-01 (Production)',
            'os' => 'Ubuntu 24.04 LTS (Noble Numbat)',
            'kernel' => 'Linux 6.8.0-40-generic x86_64',
            'host' => '168.110.197.113',
            'port' => 22,
            'username' => 'root',
            'auth_type' => 'password', // 'password' or 'key'
            'status' => 'online',
            'ping' => '28 ms',
            'uptime' => '18 Hari, 6 Jam, 42 Menit',
            'cpu_usage' => 14,
            'ram_used' => '1.2 GB',
            'ram_total' => '4.0 GB',
            'ram_percentage' => 30,
            'disk_used' => '18.4 GB',
            'disk_total' => '80 GB',
            'disk_percentage' => 23,
            'bandwidth_tx' => '248.5 GB',
            'bandwidth_rx' => '312.8 GB',
            'last_checked' => '30 Sep 2026, 13:20:00 WIB',
        ];

        $vpnServices = [
            [
                'name' => 'WireGuard Server',
                'interface' => 'wg0',
                'protocol' => 'UDP / 51820',
                'status' => 'running',
                'active_clients' => 14,
                'description' => 'Fast, modern & secure VPN protocol dengan enkripsi canggih.',
            ],
            [
                'name' => 'Xray / V2Ray Core (VLESS)',
                'interface' => 'xray.service',
                'protocol' => 'TCP/TLS / 443',
                'status' => 'running',
                'active_clients' => 32,
                'description' => 'Bypass DPI & internet positif dengan WebSocket + TLS CDN support.',
            ],
            [
                'name' => 'OpenVPN Server',
                'interface' => 'tun0',
                'protocol' => 'UDP / 1194',
                'status' => 'running',
                'active_clients' => 8,
                'description' => 'Standard enterprise SSL VPN compatibility untuk berbagai OS.',
            ],
            [
                'name' => 'BadVPN UDPGW',
                'interface' => 'badvpn-udpgw',
                'protocol' => 'Port 7300',
                'status' => 'running',
                'active_clients' => 54,
                'description' => 'UDP Forwarding untuk voice chat, Discord, dan gaming online.',
            ],
        ];

        $sshLogs = [
            [
                'time' => '13:20:01',
                'user' => 'system',
                'command' => 'systemctl status wireguard xray badvpn',
                'output' => 'All services active (running). 0 failed units.',
            ],
            [
                'time' => '13:00:00',
                'user' => 'cron',
                'command' => 'wg show wg0 transfer',
                'output' => 'Peer sync completed. Total peers: 14 active.',
            ],
            [
                'time' => '12:15:33',
                'user' => 'admin',
                'command' => 'uptime',
                'output' => '13:15:33 up 18 days, 6:42, 1 user, load average: 0.14, 0.20, 0.18',
            ],
        ];

        return view('admin.server.index', compact('serverInfo', 'vpnServices', 'sshLogs'));
    }
}
