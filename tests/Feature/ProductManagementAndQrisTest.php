<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\QrisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementAndQrisTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test QRIS static to dynamic conversion and CRC16 checksum calculation.
     */
    public function test_qris_service_calculates_valid_crc16_and_dynamic_payload(): void
    {
        $staticPayload = QrisService::DEFAULT_STATIC_QRIS;
        $expectedCrc = substr($staticPayload, -4);

        // Verify static CRC calculation matches original Dana string
        $calculatedCrc = QrisService::calculateCrc16(substr($staticPayload, 0, -4));
        $this->assertEquals($expectedCrc, $calculatedCrc);

        // Generate dynamic QRIS with nominal 35.123
        $amount = 35123;
        $dynamicQris = QrisService::makeDynamic($amount);

        // Assertions for EMVCo specifications
        $this->assertStringContainsString('010212', $dynamicQris); // Dynamic Point of Initiation
        $this->assertStringContainsString('540535123', $dynamicQris); // Tag 54 with amount 35123
        $this->assertStringContainsString('5802ID', $dynamicQris);
        $this->assertStringContainsString('5908rz store', $dynamicQris);

        // Verify dynamic QRIS CRC is valid
        $dynamicPayloadWithoutCrc = substr($dynamicQris, 0, -4);
        $dynamicExpectedCrc = substr($dynamicQris, -4);
        $this->assertEquals($dynamicExpectedCrc, QrisService::calculateCrc16($dynamicPayloadWithoutCrc));

        // Test SVG Generation
        $svg = QrisService::generateQrSvg($dynamicQris, 200);
        $this->assertStringContainsString('<svg', $svg);
    }

    /**
     * Test Admin can view products.
     */
    public function test_admin_can_view_product_list(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Product::create([
            'sku' => 'TSEL10GB',
            'name' => 'Telkomsel Data 10 GB',
            'category' => 'Telkomsel',
            'modal_price' => 31200,
            'sell_price' => 35000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.produk'));
        $response->assertStatus(200);
        $response->assertSee('TSEL10GB');
        $response->assertSee('Telkomsel Data 10 GB');
    }

    /**
     * Test Admin can create a new product.
     */
    public function test_admin_can_create_product(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test2@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $payload = [
            'sku' => 'ISAT25GB',
            'name' => 'Indosat Freedom 25 GB',
            'category' => 'Indosat',
            'description' => '25 GB 24 Jam Full Kuota Utama',
            'active_period' => '30 Hari',
            'modal_price' => 52000,
            'sell_price' => 58000,
            'status' => 'active',
        ];

        $response = $this->actingAs($admin)->postJson(route('admin.produk.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'sku' => 'ISAT25GB',
            'name' => 'Indosat Freedom 25 GB',
            'modal_price' => 52000,
            'sell_price' => 58000,
            'margin' => 6000,
        ]);
    }

    /**
     * Test Admin can update a product.
     */
    public function test_admin_can_update_product(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test3@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $product = Product::create([
            'sku' => 'XL10GB',
            'name' => 'XL Xtra 10 GB',
            'category' => 'XL Axiata',
            'modal_price' => 28000,
            'sell_price' => 32000,
            'status' => 'active',
        ]);

        $updatePayload = [
            'sku' => 'XL10GB',
            'name' => 'XL Xtra Combo 10 GB Pro',
            'category' => 'XL Axiata',
            'modal_price' => 29000,
            'sell_price' => 34000,
            'description' => 'Updated desc',
        ];

        $response = $this->actingAs($admin)->putJson(route('admin.produk.update', $product), $updatePayload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'XL Xtra Combo 10 GB Pro',
            'modal_price' => 29000,
            'sell_price' => 34000,
            'margin' => 5000,
        ]);
    }

    /**
     * Test Admin can toggle product status.
     */
    public function test_admin_can_toggle_product_status(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test4@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $product = Product::create([
            'sku' => 'VPN-01',
            'name' => 'VPN SG Account',
            'category' => 'VPN Premium',
            'modal_price' => 0,
            'sell_price' => 20000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.produk.toggle', $product));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'inactive',
        ]);

        $this->assertEquals('inactive', $product->fresh()->status);
    }

    /**
     * Test Admin can delete a product.
     */
    public function test_admin_can_delete_product(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test5@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $product = Product::create([
            'sku' => 'TRI5GB',
            'name' => 'Tri Happy 5 GB',
            'category' => 'Tri',
            'modal_price' => 15000,
            'sell_price' => 18000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->deleteJson(route('admin.produk.destroy', $product));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * Test Checkout page displays dynamic QRIS with unique code.
     */
    public function test_checkout_page_renders_dynamic_qris(): void
    {
        $user = User::create([
            'name' => 'User Buyer',
            'email' => 'buyer_test@rzstore.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $product = Product::create([
            'sku' => 'TSEL10GB',
            'name' => 'Telkomsel Data 10 GB',
            'category' => 'Telkomsel',
            'modal_price' => 31200,
            'sell_price' => 35000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('checkout', ['product_id' => $product->id, 'code' => 123]));
        $response->assertStatus(200);
        $response->assertSee('35.123');
        $response->assertSee('Telkomsel Data 10 GB');
        $response->assertSee('<svg', false);
    }
}
