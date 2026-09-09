<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\CommodityPrice;
use App\Models\User;
use App\Services\MapService;
use App\Services\PriceAnalyticsService;
use Tests\TestCase;

class PhaseSevenMapsPriceAnalyticsTest extends TestCase
{
    /**
     * 1. Test unauthenticated users are redirected to login for map and prices.
     */
    public function test_unauthenticated_user_cannot_access_maps_or_prices(): void
    {
        $responseMap = $this->get('/maps');
        $responseMap->assertRedirect('/login');

        $responsePrice = $this->get('/prices');
        $responsePrice->assertRedirect('/login');
    }

    /**
     * 2. Test authenticated buyers (collector & consumer) can view agricultural map.
     */
    public function test_authenticated_buyers_can_view_agricultural_map(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $this->assertNotNull($collector);

        $response = $this->actingAs($collector)->get('/maps');
        $response->assertStatus(200);
        $response->assertSee('Peta Sebaran Petani');
        $response->assertSee('Titik Lahan');
        $response->assertSee('agricultural-map');

        $consumer = User::where('role', 'konsumen')->first();
        $this->assertNotNull($consumer);

        $responseConsumer = $this->actingAs($consumer)->get('/maps');
        $responseConsumer->assertStatus(200);
        $responseConsumer->assertSee('Peta Sebaran Petani');
    }

    /**
     * 3. Test map markers JSON endpoint returns valid geospatial data from MySQL.
     */
    public function test_maps_markers_api_returns_valid_geospatial_json(): void
    {
        $collector = User::where('role', 'pengepul')->first();

        $response = $this->actingAs($collector)->getJson('/api/maps/markers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'count',
            'summary' => [
                'total_farmers',
                'total_commodities',
                'total_stock',
            ],
            'markers' => [
                '*' => [
                    'id',
                    'name',
                    'farm_name',
                    'primary_commodity',
                    'address',
                    'latitude',
                    'longitude',
                    'products',
                    'total_stock',
                ]
            ]
        ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['count']);

        // Verify latitude and longitude are valid numeric coordinates
        $firstMarker = $data['markers'][0];
        $this->assertIsNumeric($firstMarker['latitude']);
        $this->assertIsNumeric($firstMarker['longitude']);
        $this->assertLessThan(0, $firstMarker['latitude']); // Indonesia southern hemisphere
        $this->assertGreaterThan(100, $firstMarker['longitude']); // Indonesia eastern longitude
    }

    /**
     * 4. Test filtering map markers by commodity.
     */
    public function test_map_filters_by_commodity(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $commodity = Commodity::where('name', 'Cabai')->first();
        $this->assertNotNull($commodity);

        $response = $this->actingAs($collector)->getJson('/api/maps/markers?commodity_id=' . $commodity->id);

        $response->assertStatus(200);
        $data = $response->json();

        // Every returned farmer must have active products matching the filtered commodity
        foreach ($data['markers'] as $marker) {
            $hasMatchingProduct = collect($marker['products'])->contains(function ($prod) use ($commodity) {
                return $prod['commodity'] === $commodity->name;
            });
            $this->assertTrue($hasMatchingProduct);
        }
    }

    /**
     * 5. Test authenticated user can access price analytics dashboard with Chart.js.
     */
    public function test_authenticated_user_can_view_price_analytics_dashboard(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $this->assertNotNull($farmer);

        $response = $this->actingAs($farmer)->get('/prices');

        $response->assertStatus(200);
        $response->assertSee('Tren Harga Komoditas Pertanian');
        $response->assertSee('Harga Terkini');
        $response->assertSee('Harga Tertinggi');
        $response->assertSee('Harga Terendah');
        $response->assertSee('commodityPriceChart');
        $response->assertSee('Riwayat Catatan Harga Pasar');
    }

    /**
     * 6. Test PriceAnalyticsService accurately computes KPI and trend direction.
     */
    public function test_price_analytics_service_calculates_kpi_and_trend_accurately(): void
    {
        $service = app(PriceAnalyticsService::class);
        $commodity = Commodity::where('name', 'Jagung')->first();
        $this->assertNotNull($commodity);

        $analytics = $service->getAnalytics($commodity->id, 30);

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('latest_price', $analytics);
        $this->assertArrayHasKey('highest_price', $analytics);
        $this->assertArrayHasKey('lowest_price', $analytics);
        $this->assertArrayHasKey('average_price', $analytics);
        $this->assertArrayHasKey('change_nominal', $analytics);
        $this->assertArrayHasKey('change_percentage', $analytics);
        $this->assertArrayHasKey('trend', $analytics);
        $this->assertArrayHasKey('chart_labels', $analytics);
        $this->assertArrayHasKey('chart_prices', $analytics);

        $this->assertGreaterThan(0, $analytics['latest_price']);
        $this->assertGreaterThanOrEqual($analytics['lowest_price'], $analytics['highest_price']);
        $this->assertContains($analytics['trend'], ['Naik', 'Turun', 'Stabil']);
        $this->assertNotEmpty($analytics['chart_labels']);
        $this->assertNotEmpty($analytics['chart_prices']);
        $this->assertEquals(count($analytics['chart_labels']), count($analytics['chart_prices']));
    }

    /**
     * 7. Test Chart Data API endpoint returns time series.
     */
    public function test_chart_data_api_returns_valid_time_series(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $commodity = Commodity::where('name', 'Cabai')->first();

        $response = $this->actingAs($consumer)->getJson('/api/prices/chart-data?commodity_id=' . $commodity->id . '&days=30');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'labels',
            'prices',
            'latest_price',
            'highest_price',
            'lowest_price',
            'average_price',
            'change_nominal',
            'change_percentage',
            'trend',
            'commodity_name',
            'unit',
        ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals('Cabai', $data['commodity_name']);
        $this->assertIsArray($data['labels']);
        $this->assertIsArray($data['prices']);
    }

    /**
     * 8. Test Admin can manage (CRUD) commodity market prices.
     */
    public function test_admin_can_manage_commodity_market_prices(): void
    {
        $admin = User::where('role', 'admin')->first();
        $commodity = Commodity::where('name', 'Tomat')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($commodity);

        // 8a. Admin can view price management index
        $indexResponse = $this->actingAs($admin)->get('/admin/prices');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Kelola Data Harga Komoditas');

        // 8b. Admin can store a new market price entry
        $storeResponse = $this->actingAs($admin)->post('/admin/prices', [
            'commodity_id' => $commodity->id,
            'price' => 12500,
            'unit' => 'kg',
            'location' => 'Pasar Induk Giwangan, Sleman',
            'recorded_date' => now()->format('Y-m-d'),
            'source' => 'Survei Langsung Petugas Lapangan',
            'notes' => 'Uji coba pencatatan harga pasar Admin Phase 7.',
        ]);

        $storeResponse->assertRedirect('/admin/prices');
        $this->assertDatabaseHas('commodity_prices', [
            'commodity_id' => $commodity->id,
            'price' => 12500,
            'location' => 'Pasar Induk Giwangan, Sleman',
        ]);

        $createdPrice = CommodityPrice::where('location', 'Pasar Induk Giwangan, Sleman')->latest()->first();
        $this->assertNotNull($createdPrice);

        // 8c. Admin can update the price
        $updateResponse = $this->actingAs($admin)->put("/admin/prices/{$createdPrice->id}", [
            'commodity_id' => $commodity->id,
            'price' => 13000,
            'unit' => 'kg',
            'location' => 'Pasar Induk Giwangan, Sleman (Revisi)',
            'recorded_date' => now()->format('Y-m-d'),
            'source' => 'Survei Langsung Petugas Lapangan',
            'notes' => 'Revisi harga pasar naik sedikit.',
        ]);

        $updateResponse->assertRedirect('/admin/prices');
        $this->assertDatabaseHas('commodity_prices', [
            'id' => $createdPrice->id,
            'price' => 13000,
            'location' => 'Pasar Induk Giwangan, Sleman (Revisi)',
        ]);

        // 8d. Admin can delete the price
        $deleteResponse = $this->actingAs($admin)->delete("/admin/prices/{$createdPrice->id}");
        $deleteResponse->assertRedirect('/admin/prices');
        $this->assertDatabaseMissing('commodity_prices', [
            'id' => $createdPrice->id,
        ]);
    }

    /**
     * 9. Test non-admin cannot access Admin price management (403 Forbidden).
     */
    public function test_non_admin_cannot_access_admin_price_management(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $collector = User::where('role', 'pengepul')->first();
        $consumer = User::where('role', 'konsumen')->first();

        // Farmer forbidden
        $responseFarmer = $this->actingAs($farmer)->get('/admin/prices');
        $responseFarmer->assertStatus(403);

        // Collector forbidden
        $responseCollector = $this->actingAs($collector)->get('/admin/prices');
        $responseCollector->assertStatus(403);

        // Consumer forbidden
        $responseConsumer = $this->actingAs($consumer)->get('/admin/prices');
        $responseConsumer->assertStatus(403);
    }
}
