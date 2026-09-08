<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\Harvest;
use App\Models\Stock;
use App\Models\User;
use Tests\TestCase;

class PhaseThreeHarvestStockTest extends TestCase
{
    /**
     * Test farmer can access harvest index page and view stats.
     */
    public function test_farmer_can_view_harvests_index(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $this->assertNotNull($farmer);

        $response = $this->actingAs($farmer)->get('/farmer/harvests');

        $response->assertStatus(200);
        $response->assertSee('Pencatatan Hasil Panen');
        $response->assertSee('Total Hasil Panen');
        $response->assertSee('Catat Panen Baru');
    }

    /**
     * Test recording a new harvest automatically creates a real stock batch.
     * Alur wajib: Hasil Panen -> Stok Riil
     */
    public function test_farmer_can_record_harvest_and_automatic_stock_created(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::where('slug', 'jagung')->first();
        $this->assertNotNull($farmer);
        $this->assertNotNull($commodity);

        $harvestData = [
            'commodity_id' => $commodity->id,
            'quantity' => 1250,
            'unit' => 'kg',
            'harvest_date' => now()->format('Y-m-d'),
            'quality' => 'Grade A (Super)',
            'location' => 'Lahan Uji Petani, Pujon Malang',
            'notes' => 'Panen jagung segar untuk pengujian integrasi otomatis.',
        ];

        $response = $this->actingAs($farmer)->post('/farmer/harvests', $harvestData);

        $response->assertRedirect('/farmer/harvests');

        // Assert harvest exists
        $this->assertDatabaseHas('harvests', [
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'quantity' => 1250,
            'quality' => 'Grade A (Super)',
        ]);

        $harvest = Harvest::where('notes', 'Panen jagung segar untuk pengujian integrasi otomatis.')->first();
        $this->assertNotNull($harvest);

        // Assert stock batch exists and linked to this harvest
        $this->assertDatabaseHas('stocks', [
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'harvest_id' => $harvest->id,
            'available_quantity' => 1250,
            'initial_quantity' => 1250,
            'status' => 'Tersedia',
        ]);
    }

    /**
     * Test farmer can view real stocks management.
     */
    public function test_farmer_can_view_stocks_index(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $this->assertNotNull($farmer);

        $response = $this->actingAs($farmer)->get('/farmer/stocks');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Stok Riil');
        $response->assertSee('Stok Tersedia');
        $response->assertSee('Sedang Dipesan');
        $response->assertSee('Total Terjual');
    }

    /**
     * Test farmer can view stock detail page.
     */
    public function test_farmer_can_view_stock_detail(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $stock = Stock::where('user_id', $farmer->id)->first();
        $this->assertNotNull($stock);

        $response = $this->actingAs($farmer)->get('/farmer/stocks/' . $stock->id);

        $response->assertStatus(200);
        $response->assertSee($stock->batch_code);
        $response->assertSee('Sumber Hasil Panen');
        $response->assertSee('Produk Marketplace');
    }

    /**
     * Test farmer can safely adjust physical stock quantity.
     */
    public function test_farmer_can_adjust_stock_quantity(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $stock = Stock::where('user_id', $farmer->id)->first();
        $this->assertNotNull($stock);

        $response = $this->actingAs($farmer)->patch('/farmer/stocks/' . $stock->id . '/adjust', [
            'available_quantity' => 450,
            'reason' => 'Penyusutan alami saat penyimpanan gudang',
        ]);

        $response->assertStatus(302);
        $stock->refresh();

        $this->assertEquals(450, (float) $stock->available_quantity);
    }

    /**
     * Test stock quantity cannot be negative.
     */
    public function test_stock_cannot_be_negative(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $stock = Stock::where('user_id', $farmer->id)->first();
        $this->assertNotNull($stock);

        $response = $this->actingAs($farmer)->patch('/farmer/stocks/' . $stock->id . '/adjust', [
            'available_quantity' => -10,
            'reason' => 'Invalid negative input',
        ]);

        $response->assertSessionHasErrors('available_quantity');
    }

    /**
     * Test stock status transitions correctly based on PRD rules.
     */
    public function test_stock_status_transitions(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $stock = Stock::where('user_id', $farmer->id)->first();
        $this->assertNotNull($stock);

        // Tersedia (> 10)
        $stock->available_quantity = 50;
        $stock->syncStatus();
        $this->assertEquals('Tersedia', $stock->status);

        // Stok Terbatas (<= 10 and > 0)
        $stock->available_quantity = 8;
        $stock->syncStatus();
        $this->assertEquals('Stok Terbatas', $stock->status);

        // Habis (<= 0)
        $stock->available_quantity = 0;
        $stock->syncStatus();
        $this->assertEquals('Habis', $stock->status);
    }

    /**
     * Test stock reservation and release lifecycle methods.
     */
    public function test_stock_reservation_and_release_lifecycle(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $stock = Stock::create([
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'batch_code' => 'STK-TEST-' . time(),
            'initial_quantity' => 100,
            'available_quantity' => 100,
            'ordered_quantity' => 0,
            'sold_quantity' => 0,
            'unit' => 'kg',
            'quality' => 'Grade A',
            'status' => 'Tersedia',
        ]);

        // Reserve 30 kg
        $stock->reserve(30);
        $this->assertEquals(70, (float) $stock->available_quantity);
        $this->assertEquals(30, (float) $stock->ordered_quantity);

        // Release 10 kg back
        $stock->release(10);
        $this->assertEquals(80, (float) $stock->available_quantity);
        $this->assertEquals(20, (float) $stock->ordered_quantity);

        // Complete sale for remaining 20 kg
        $stock->completeSale(20);
        $this->assertEquals(80, (float) $stock->available_quantity);
        $this->assertEquals(0, (float) $stock->ordered_quantity);
        $this->assertEquals(20, (float) $stock->sold_quantity);
    }

    /**
     * Test non-farmer cannot access farmer harvest or stock management.
     */
    public function test_non_farmer_cannot_access_harvest_or_stock(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $this->assertNotNull($collector);

        $harvestResponse = $this->actingAs($collector)->get('/farmer/harvests');
        $this->assertTrue(in_array($harvestResponse->status(), [302, 403]));

        $stockResponse = $this->actingAs($collector)->get('/farmer/stocks');
        $this->assertTrue(in_array($stockResponse->status(), [302, 403]));
    }
}
