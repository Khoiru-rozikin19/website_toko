<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('RZ STORE');
        $response->assertSee('Kata Sandi');
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_guest_can_register_new_user_account(): void
    {
        $payload = [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register.post'), $payload);
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
        ]);

        $this->assertAuthenticated();
    }

    public function test_admin_can_login_and_access_admin_panel(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@rzstore.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'admin@rzstore.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        // Access Admin Panel
        $adminResponse = $this->actingAs($admin)->get(route('admin.produk'));
        $adminResponse->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::create([
            'name' => 'User Biasa',
            'email' => 'user@rzstore.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('admin.produk'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get(route('admin.produk'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_checkout_without_login(): void
    {
        $response = $this->get(route('checkout'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_checkout(): void
    {
        $user = User::create([
            'name' => 'Pembeli Kuota',
            'email' => 'buyer@rzstore.com',
            'password' => Hash::make('secret123'),
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

        $response = $this->actingAs($user)->get(route('checkout', [
            'product_id' => $product->id,
            'target' => '081234567899',
        ]));

        $response->assertStatus(200);
        $response->assertSee('081234567899');
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'target' => '081234567899',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@rzstore.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
