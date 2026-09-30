<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesanan_page_displays_synced_orders_from_database(): void
    {
        $order = Order::create([
            'order_ref' => 'RZ-20260930-999',
            'product_name' => 'Telkomsel Data 10 GB',
            'category' => 'Telkomsel',
            'target' => '081234567890',
            'base_price' => 35000,
            'unique_code' => 123,
            'total_amount' => 35123,
            'status' => 'pending',
            'payment_method' => 'QRIS Dinamis (DANA)',
        ]);

        $response = $this->get(route('pesanan'));
        $response->assertStatus(200);
        $response->assertSee('RZ-20260930-999');
        $response->assertSee('Telkomsel Data 10 GB');
        $response->assertSee('081234567890');
        $response->assertSee('35.123');
    }

    public function test_pesanan_page_filters_by_status(): void
    {
        Order::create([
            'order_ref' => 'RZ-PENDING',
            'product_name' => 'Telkomsel 10GB',
            'category' => 'Telkomsel',
            'target' => '081234567890',
            'base_price' => 35000,
            'unique_code' => 111,
            'total_amount' => 35111,
            'status' => 'pending',
        ]);

        Order::create([
            'order_ref' => 'RZ-SUCCESS',
            'product_name' => 'Indosat 25GB',
            'category' => 'Indosat',
            'target' => '085712345678',
            'base_price' => 58000,
            'unique_code' => 0,
            'total_amount' => 58000,
            'status' => 'success',
        ]);

        // Test Filter Pending
        $pendingResponse = $this->get(route('pesanan', ['status' => 'pending']));
        $pendingResponse->assertStatus(200);
        $pendingResponse->assertSee('RZ-PENDING');
        $pendingResponse->assertDontSee('RZ-SUCCESS');

        // Test Filter Completed
        $completedResponse = $this->get(route('pesanan', ['status' => 'completed']));
        $completedResponse->assertStatus(200);
        $completedResponse->assertSee('RZ-SUCCESS');
        $completedResponse->assertDontSee('RZ-PENDING');
    }

    public function test_order_detail_json_endpoint(): void
    {
        $order = Order::create([
            'order_ref' => 'RZ-TEST-DETAIL',
            'product_name' => 'VPN Premium Singapore 1 Bulan',
            'category' => 'VPN Premium',
            'target' => 'user_sg01',
            'base_price' => 20000,
            'unique_code' => 456,
            'total_amount' => 20456,
            'status' => 'completed',
            'serial_number' => 'VLESS://rzstore-sg01@168.110.197.113:443',
        ]);

        $response = $this->getJson(route('pesanan.show', ['ref' => $order->order_ref]));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'order' => [
                'order_ref' => 'RZ-TEST-DETAIL',
                'serial_number' => 'VLESS://rzstore-sg01@168.110.197.113:443',
            ],
            'formatted_total' => 'Rp 20.456',
        ]);
    }

    public function test_dashboard_displays_database_order_metrics(): void
    {
        Order::create([
            'order_ref' => 'RZ-ORD-01',
            'product_name' => 'Telkomsel 10GB',
            'category' => 'Telkomsel',
            'target' => '081234567890',
            'base_price' => 35000,
            'unique_code' => 10,
            'total_amount' => 35010,
            'status' => 'success',
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('RZ-ORD-01');
        $response->assertSee('35.010');
    }

    public function test_order_creation_via_checkout(): void
    {
        $product = Product::create([
            'sku' => 'TSEL10GB',
            'name' => 'Telkomsel Data 10 GB',
            'category' => 'Telkomsel',
            'modal_price' => 31200,
            'sell_price' => 35000,
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Order Test User',
            'email' => 'ordertest@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('checkout', [
            'product_id' => $product->id,
            'target' => '081299998888',
        ]));

        $response->assertStatus(200);
        $response->assertSee('081299998888');
        $response->assertSee('Telkomsel Data 10 GB');

        $this->assertDatabaseHas('orders', [
            'product_name' => 'Telkomsel Data 10 GB',
            'target' => '081299998888',
            'status' => 'pending',
        ]);
    }
}
