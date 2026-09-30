<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\QrisService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with users, products, and sample orders.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@rzstore.com'],
            [
                'name' => 'Admin RZ Store',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Seed Regular User
        $user = User::updateOrCreate(
            ['email' => 'user@rzstore.com'],
            [
                'name' => 'Pelanggan RZ',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        // 3. Seed 2 Products
        $p1 = Product::updateOrCreate(
            ['sku' => 'TSEL10GB'],
            [
                'name' => 'Telkomsel Data 10 GB (30 Hari)',
                'category' => 'Telkomsel',
                'description' => '10 GB Kuota Utama 24 Jam Semua Jaringan + 2 GB Chat',
                'active_period' => '30 Hari',
                'modal_price' => 31200,
                'sell_price' => 35000,
                'margin' => 3800,
                'status' => 'active',
                'sales_count' => 12,
            ]
        );

        $p2 = Product::updateOrCreate(
            ['sku' => 'VPN-PREM-1M'],
            [
                'name' => 'VPN Premium SG (WireGuard / V2Ray)',
                'category' => 'VPN Premium',
                'description' => 'Akun VPN Pribadi Server Singapore VPS Ubuntu 24.04 (Low Ping / High Speed 1 Gbps)',
                'active_period' => '30 Hari',
                'modal_price' => 0,
                'sell_price' => 20000,
                'margin' => 20000,
                'status' => 'active',
                'sales_count' => 28,
            ]
        );

        // Seed 2 Sample Orders
        Order::updateOrCreate(
            ['order_ref' => 'RZ-20260930-001'],
            [
                'user_id' => $user->id,
                'product_id' => $p1->id,
                'product_name' => $p1->name,
                'category' => $p1->category,
                'sku' => $p1->sku,
                'target' => '081234567890',
                'notes' => 'Nomor utama keluarga',
                'base_price' => $p1->sell_price,
                'unique_code' => 142,
                'total_amount' => $p1->sell_price + 142,
                'qris_payload' => QrisService::makeDynamic($p1->sell_price + 142),
                'status' => 'success',
                'payment_method' => 'QRIS DANA Bisnis',
                'serial_number' => 'SN2026093000189345091',
                'paid_at' => now()->subHours(2),
                'completed_at' => now()->subHours(2)->addMinutes(1),
            ]
        );

        Order::updateOrCreate(
            ['order_ref' => 'RZ-20260930-002'],
            [
                'user_id' => $user->id,
                'product_id' => $p2->id,
                'product_name' => $p2->name,
                'category' => $p2->category,
                'sku' => $p2->sku,
                'target' => 'rz_vpn_user',
                'notes' => 'Tolong aktifkan WireGuard Singapore',
                'base_price' => $p2->sell_price,
                'unique_code' => 218,
                'total_amount' => $p2->sell_price + 218,
                'qris_payload' => QrisService::makeDynamic($p2->sell_price + 218),
                'status' => 'pending',
                'payment_method' => 'QRIS DANA Bisnis',
                'serial_number' => null,
                'paid_at' => null,
                'completed_at' => null,
            ]
        );
    }
}
