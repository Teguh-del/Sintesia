<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class PhaseTwoMarketplaceTest extends TestCase
{
    /**
     * Test public marketplace catalog renders successfully.
     */
    public function test_marketplace_catalog_renders_successfully(): void
    {
        $response = $this->get('/marketplace');

        $response->assertStatus(200);
        $response->assertSee('Marketplace Pertanian');
        $latestProduct = Product::where('status', 'active')->latest()->first();
        if ($latestProduct) {
            $response->assertSee($latestProduct->name);
        }
    }

    /**
     * Test filtering marketplace products by commodity slug.
     */
    public function test_marketplace_filters_by_commodity(): void
    {
        $response = $this->get('/marketplace?commodity=cabai');

        $response->assertStatus(200);
        $response->assertSee('Cabai Rawit Merah Segar');
    }

    /**
     * Test searching marketplace products by keyword.
     */
    public function test_marketplace_searches_by_keyword(): void
    {
        $response = $this->get('/marketplace?search=Beras');

        $response->assertStatus(200);
        $response->assertSee('Beras Pandan Wangi');
    }

    /**
     * Test marketplace detail page displays correct information and calculator.
     */
    public function test_marketplace_product_detail_renders(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->get('/marketplace/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Spesifikasi Komoditas');
        $response->assertSee('Profil Petani Produsen');
        $response->assertSee('Estimasi Subtotal');
    }

    /**
     * Test farmer can access product management page.
     */
    public function test_farmer_can_access_products_management(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $this->assertNotNull($farmer);

        $response = $this->actingAs($farmer)->get('/farmer/products');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Produk Marketplace');
        $response->assertSee('Tambah Produk Baru');
    }

    /**
     * Test non-farmer role (pengepul) is forbidden or redirected from farmer product management.
     */
    public function test_non_farmer_cannot_access_farmer_products(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $this->assertNotNull($collector);

        $response = $this->actingAs($collector)->get('/farmer/products');

        // RoleMiddleware redirects or denies
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    /**
     * Test farmer can create a new product with validation.
     */
    public function test_farmer_can_create_new_product(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::where('slug', 'tomat')->first();

        $this->assertNotNull($farmer);
        $this->assertNotNull($commodity);

        $productData = [
            'name' => 'Tomat Mawar Organik Segar',
            'commodity_id' => $commodity->id,
            'price' => 14000,
            'stock' => 300,
            'unit' => 'kg',
            'min_order' => 5,
            'quality' => 'Grade A (Super)',
            'location' => 'Kec. Pujon, Kab. Malang',
            'status' => 'active',
            'allow_negotiation' => 1,
            'description' => 'Tomat mawar segar kualitas ekspor.',
        ];

        $response = $this->actingAs($farmer)->post('/farmer/products', $productData);

        $response->assertRedirect('/farmer/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Tomat Mawar Organik Segar',
            'price' => 14000,
            'stock' => 300,
        ]);
    }

    /**
     * Test farmer can toggle status of their product.
     */
    public function test_farmer_can_toggle_product_status(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->first();
        $this->assertNotNull($product);

        $initialStatus = $product->status;

        $response = $this->actingAs($farmer)->patch('/farmer/products/' . $product->id . '/toggle');

        $response->assertStatus(302);
        $product->refresh();

        $this->assertNotEquals($initialStatus, $product->status);
    }
}
