<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class PhaseEightAdminPolishTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure farmer products have adequate stock if orders are created
        $farmer = User::where('role', 'petani')->first();
        if ($farmer) {
            $farmer->products()->update(['stock' => 3000]);
        }
        // Ensure all users are active
        User::query()->update(['is_active' => true]);
    }

    /**
     * 1. Unauthenticated users are redirected to login for all admin routes.
     */
    public function test_unauthenticated_user_cannot_access_admin_portal(): void
    {
        $routes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/commodities',
            '/admin/products',
            '/admin/transactions',
            '/admin/prices',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /**
     * 2. Non-admin users (Petani, Pengepul, Konsumen) get 403 Forbidden when accessing /admin/*.
     */
    public function test_non_admin_users_forbidden_from_admin_portal(): void
    {
        $roles = ['petani', 'pengepul', 'konsumen'];

        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            $this->assertNotNull($user, "User with role {$role} must exist.");

            $response = $this->actingAs($user)->get('/admin/dashboard');
            $response->assertStatus(403);

            $responseUsers = $this->actingAs($user)->get('/admin/users');
            $responseUsers->assertStatus(403);
        }
    }

    /**
     * 3. Public registration cannot register as Admin.
     */
    public function test_public_registration_cannot_register_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'fake_admin_' . uniqid() . '@example.com',
            'phone' => '08999999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
    }

    /**
     * 4. Admin can view dashboard with real platform statistics.
     */
    public function test_admin_can_view_dashboard_with_real_statistics(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Admin user must exist.');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Pusat Komando');
        $response->assertSee('GMV');
        $response->assertSee('Mitra Petani');
        $response->assertSee('Mitra Pengepul');
        $response->assertSee('Konsumen');
    }

    /**
     * 5. Admin can view user management, filter, and view user profile details.
     */
    public function test_admin_can_view_and_manage_users(): void
    {
        $admin = User::where('role', 'admin')->first();
        $farmer = User::where('role', 'petani')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee($farmer->name);

        // Filter by role
        $filterResponse = $this->actingAs($admin)->get('/admin/users?role=petani');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee($farmer->name);

        // View user show
        $detailResponse = $this->actingAs($admin)->get("/admin/users/{$farmer->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Profil Pengguna');
        $detailResponse->assertSee($farmer->email);
    }

    /**
     * 6. Admin can toggle user active status, but cannot toggle their own account.
     */
    public function test_admin_can_toggle_user_status_with_self_protection(): void
    {
        $admin = User::where('role', 'admin')->first();
        $targetUser = User::where('role', 'konsumen')->first();
        $initialStatus = $targetUser->is_active ?? true;

        // Toggle target user
        $response = $this->actingAs($admin)->patch("/admin/users/{$targetUser->id}/toggle-status");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(!$initialStatus, $targetUser->fresh()->is_active);

        // Self-deactivation prevention test
        $selfResponse = $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle-status");
        $selfResponse->assertRedirect();
        $selfResponse->assertSessionHas('error');

        // Restore active status
        $targetUser->update(['is_active' => true]);
    }

    /**
     * 7. Admin can view, add, edit, and toggle commodities.
     */
    public function test_admin_can_manage_master_commodities(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Index
        $response = $this->actingAs($admin)->get('/admin/commodities');
        $response->assertStatus(200);
        $response->assertSee('Master Komoditas Pertanian');

        // Store new commodity
        $newCommodityName = 'Ubi Jalar Ungu ' . uniqid();
        $storeResponse = $this->actingAs($admin)->post('/admin/commodities', [
            'name' => $newCommodityName,
            'category' => 'Palawija',
            'unit' => 'kg',
            'description' => 'Ubi jalar varietas unggul kaya antioksidan.',
        ]);
        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $commodity = Commodity::where('name', $newCommodityName)->first();
        $this->assertNotNull($commodity);
        $this->assertEquals('Palawija', $commodity->category);

        // Update commodity
        $updateResponse = $this->actingAs($admin)->put("/admin/commodities/{$commodity->id}", [
            'name' => $newCommodityName . ' Revisi',
            'category' => 'Hortikultura',
            'unit' => 'kg',
            'description' => 'Deskripsi diperbarui.',
        ]);
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');
        $this->assertEquals($newCommodityName . ' Revisi', $commodity->fresh()->name);

        // Toggle active status
        $toggleResponse = $this->actingAs($admin)->patch("/admin/commodities/{$commodity->id}/toggle-status");
        $toggleResponse->assertRedirect();
        $toggleResponse->assertSessionHas('success');
        $this->assertFalse((bool) $commodity->fresh()->is_active);
    }

    /**
     * 8. Admin can moderate marketplace products (view and toggle status).
     */
    public function test_admin_can_moderate_marketplace_products(): void
    {
        $admin = User::where('role', 'admin')->first();
        $product = Product::first();
        $this->assertNotNull($product, 'At least one product must exist in database.');

        // Index
        $response = $this->actingAs($admin)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Moderasi Produk Marketplace');
        $response->assertSee($product->name);

        // Toggle moderation status
        $originalStatus = $product->status;
        $toggleResponse = $this->actingAs($admin)->patch("/admin/products/{$product->id}/toggle-status");
        $toggleResponse->assertRedirect();
        $toggleResponse->assertSessionHas('success');

        $expectedStatus = ($originalStatus === 'active') ? 'inactive' : 'active';
        $this->assertEquals($expectedStatus, $product->fresh()->status);
    }

    /**
     * 9. Admin can monitor orders/transactions and audit transaction details.
     */
    public function test_admin_can_monitor_and_audit_transactions(): void
    {
        $admin = User::where('role', 'admin')->first();
        $order = Order::first();
        $this->assertNotNull($order, 'At least one order must exist in database.');

        // Index
        $response = $this->actingAs($admin)->get('/admin/transactions');
        $response->assertStatus(200);
        $response->assertSee('Monitoring Transaksi Sistem');
        $response->assertSee($order->order_number);

        // Audit detail / invoice
        $detailResponse = $this->actingAs($admin)->get("/admin/transactions/{$order->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Audit Pesanan #' . $order->order_number);
        $detailResponse->assertSee($order->seller->name);
        $detailResponse->assertSee($order->buyer->name);
    }
}
