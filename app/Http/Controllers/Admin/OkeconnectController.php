<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OkeconnectController extends Controller
{
    /**
     * Display the OkeConnect API integration settings page.
     */
    public function index(Request $request): View
    {
        $config = [
            'ip_center' => 'https://h2h.okeconnect.com/trx',
            'ip_center_ip' => '103.139.245.61',
            'user_id' => 'OK1988589',
            'callback_url' => url('/api/callback/okeconnect'),
            'ip_address' => '168.110.197.113',
            'password' => 'secret_pin_123',
            'status' => 'Aktif',
            'balance' => 1450000,
            'last_sync' => '30 Sep 2026, 13:15:02 WIB',
            'response_time' => '182 ms',
        ];

        $recentLogs = [
            [
                'id' => 'LOG-9921',
                'time' => '30 Sep 2026, 13:10:45',
                'action' => 'ORDER_TRX',
                'sku' => 'TSEL10GB',
                'target' => '081234567890',
                'status' => 'SUCCESS',
                'sn' => 'SN2026093000192837192',
                'price' => 31200,
                'message' => 'Transaksi Berhasil',
            ],
            [
                'id' => 'LOG-9920',
                'time' => '30 Sep 2026, 12:45:12',
                'action' => 'ORDER_TRX',
                'sku' => 'ISAT15GB',
                'target' => '085711223344',
                'status' => 'SUCCESS',
                'sn' => 'SN2026093000191823901',
                'price' => 39000,
                'message' => 'Transaksi Berhasil',
            ],
            [
                'id' => 'LOG-9919',
                'time' => '30 Sep 2026, 11:20:00',
                'action' => 'CHECK_BALANCE',
                'sku' => '-',
                'target' => '-',
                'status' => 'SUCCESS',
                'sn' => '-',
                'price' => 0,
                'message' => 'Sisa Saldo: Rp 1.450.000',
            ],
            [
                'id' => 'LOG-9918',
                'time' => '30 Sep 2026, 09:12:30',
                'action' => 'ORDER_TRX',
                'sku' => 'XLXTRA20',
                'target' => '087812993811',
                'status' => 'SUCCESS',
                'sn' => 'SN2026093000189912093',
                'price' => 48500,
                'message' => 'Transaksi Berhasil',
            ],
        ];

        return view('admin.okeconnect.index', compact('config', 'recentLogs'));
    }
}
