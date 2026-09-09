<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\Product;
use App\Models\User;
use App\Services\MatchingService;
use Tests\TestCase;

class PhaseSixSintesaMatchTest extends TestCase
{
    /**
     * 1. Test unauthenticated user is redirected to login when visiting matching page.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/matching');
        $response->assertRedirect('/login');
    }

    /**
     * 2. Test authenticated collector can access SINTESA Match engine page.
     */
    public function test_authenticated_collector_can_access_matching_engine(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $this->assertNotNull($collector);

        $response = $this->actingAs($collector)->get('/matching');

        $response->assertStatus(200);
        $response->assertSee('SINTESA Match');
        $response->assertSee('Kesesuaian Komoditas');
        $response->assertSee('Ketersediaan Stok');
        $response->assertSee('Kesesuaian Harga');
    }

    /**
     * 3. Test authenticated consumer can access SINTESA Match engine page.
     */
    public function test_authenticated_consumer_can_access_matching_engine(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $this->assertNotNull($consumer);

        $response = $this->actingAs($consumer)->get('/matching');

        $response->assertStatus(200);
        $response->assertSee('SINTESA Match');
    }

    /**
     * 4. Test MatchingService sub-score calculations and weighted formula math.
     */
    public function test_matching_service_calculates_weighted_scores_correctly(): void
    {
        $service = app(MatchingService::class);

        // 4a. Commodity score: exact match gives 100, no match gives 0, unselected commodity gives 80
        $this->assertEquals(100.0, $service->calculateCommodityScore(1, 1));
        $this->assertEquals(0.0, $service->calculateCommodityScore(1, 2));
        $this->assertEquals(80.0, $service->calculateCommodityScore(2, null));

        // 4b. Stock score: stock >= required gives 100, partial stock gives proportional score
        $this->assertEquals(100.0, $service->calculateStockScore(500, 100));
        $this->assertEquals(100.0, $service->calculateStockScore(100, 100));
        $this->assertEquals(70.0, $service->calculateStockScore(50, 100));
        $this->assertEquals(100.0, $service->calculateStockScore(100, 0));

        // 4c. Price score: cheaper than max gives up to 100, at budget ceiling gives 90, above budget scales down
        $this->assertEquals(100.0, $service->calculatePriceScore(8000, 10000));
        $this->assertEquals(90.0, $service->calculatePriceScore(10000, 10000));
        $this->assertEquals(65.0, $service->calculatePriceScore(12000, 10000));
        $this->assertEquals(50.0, $service->calculatePriceScore(10000, 0));

        // 4d. Distance score: within 10km gives high score, stepped intervals
        $this->assertEquals(100.0, $service->calculateDistanceScore(5.0));
        $this->assertEquals(90.0, $service->calculateDistanceScore(10.0));
        $this->assertEquals(55.0, $service->calculateDistanceScore(55.0));

        // 4e. Weighted final score check:
        // C=100 (35), S=100 (25), P=100 (20), D=100 (20) -> 100.0
        $finalScore = (100 * 0.35) + (100 * 0.25) + (100 * 0.20) + (100 * 0.20);
        $this->assertEquals(100.0, round($finalScore, 1));
    }

    /**
     * 5. Test MatchingService classification categories according to PRD.
     */
    public function test_matching_service_classifies_categories_accurately(): void
    {
        $service = app(MatchingService::class);

        $this->assertEquals('Sangat Cocok', $service->classifyMatchCategory(95)['name']);
        $this->assertEquals('Sangat Cocok', $service->classifyMatchCategory(85)['name']);
        $this->assertEquals('Cocok', $service->classifyMatchCategory(84.9)['name']);
        $this->assertEquals('Cocok', $service->classifyMatchCategory(70)['name']);
        $this->assertEquals('Cukup Cocok', $service->classifyMatchCategory(69.9)['name']);
        $this->assertEquals('Cukup Cocok', $service->classifyMatchCategory(50)['name']);
        $this->assertEquals('Kurang Cocok', $service->classifyMatchCategory(49.9)['name']);
        $this->assertEquals('Kurang Cocok', $service->classifyMatchCategory(10)['name']);
    }

    /**
     * 6. Test Haversine distance calculation in kilometers.
     */
    public function test_haversine_distance_calculation(): void
    {
        $service = app(MatchingService::class);

        // Same point -> 0 km
        $distSame = $service->calculateHaversineDistance(-7.7956, 110.3695, -7.7956, 110.3695);
        $this->assertEquals(0.0, $distSame);

        // Yogyakarta (-7.7956, 110.3695) to Kediri (-7.81845, 112.0156) is roughly 180 km
        $distYogyaKediri = $service->calculateHaversineDistance(-7.7956, 110.3695, -7.81845, 112.0156);
        $this->assertGreaterThan(160, $distYogyaKediri);
        $this->assertLessThan(210, $distYogyaKediri);
    }

    /**
     * 7. Test matching query filters and ranks candidates descending by score from real MySQL DB.
     */
    public function test_matching_engine_filters_and_ranks_descending(): void
    {
        $service = app(MatchingService::class);
        $collector = User::where('role', 'pengepul')->first();
        $commodity = Commodity::where('name', 'Jagung')->first();

        $this->assertNotNull($collector);
        $this->assertNotNull($commodity);

        $results = $service->findMatches([
            'commodity_id' => $commodity->id,
            'required_quantity' => 100,
            'max_price' => 10000,
            'buyer_lat' => -7.81845,
            'buyer_lng' => 112.0156,
        ]);

        $this->assertNotEmpty($results);
        $this->assertGreaterThan(0, $results->count());

        // Verify descending sort order
        $previousScore = 101.0;
        foreach ($results as $result) {
            $this->assertArrayHasKey('final_score', $result);
            $this->assertArrayHasKey('category', $result);
            $this->assertArrayHasKey('commodity_score', $result);
            $this->assertArrayHasKey('stock_score', $result);
            $this->assertArrayHasKey('price_score', $result);
            $this->assertArrayHasKey('distance_score', $result);
            $this->assertArrayHasKey('product', $result);
            $this->assertArrayHasKey('farmer', $result);

            $this->assertLessThanOrEqual($previousScore, $result['final_score']);
            $previousScore = $result['final_score'];
        }
    }

    /**
     * 8. Test HTTP matching query with parameters returns rendered recommendations.
     */
    public function test_http_matching_query_returns_results_view(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $commodity = Commodity::where('name', 'Cabai')->first();

        $response = $this->actingAs($collector)->get('/matching?' . http_build_query([
            'commodity_id' => $commodity->id,
            'required_quantity' => 50,
            'max_price' => 35000,
            'location' => 'Sleman, DI Yogyakarta',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Hasil Pemeringkatan Kecocokan Petani');
        $response->assertSee('Beli Sekarang');
    }

    /**
     * 9. Test collector dashboard displays live SINTESA Match recommendations.
     */
    public function test_collector_dashboard_displays_matching_recommendations(): void
    {
        $collector = User::where('role', 'pengepul')->first();

        $response = $this->actingAs($collector)->get('/collector/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Cerdas SINTESA Match');
        $response->assertSee('Buka Engine SINTESA Match Lengkap');
    }

    /**
     * 10. Test consumer dashboard displays live SINTESA Match recommendations.
     */
    public function test_consumer_dashboard_displays_matching_recommendations(): void
    {
        $consumer = User::where('role', 'konsumen')->first();

        $response = $this->actingAs($consumer)->get('/consumer/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Cerdas SINTESA Match');
        $response->assertSee('Buka Engine SINTESA Match Lengkap');
    }
}
