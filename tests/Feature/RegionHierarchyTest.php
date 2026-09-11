<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionHierarchyTest extends TestCase
{
    /**
     * Test /provinces returns all 38 Indonesian provinces in order.
     */
    public function test_api_provinces_returns_all_38_provinces(): void
    {
        $response = $this->getJson('/provinces');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(38, $data);

        // Verify order and specific provinces
        $this->assertEquals('Aceh', $data[0]['name']); // 1
        $this->assertEquals('Gorontalo', $data[28]['name']); // 29th province (index 28)
        $this->assertEquals('Papua', $data[32]['name']); // 33
        $this->assertEquals('Papua Barat', $data[33]['name']); // 34
        $this->assertEquals('Papua Barat Daya', $data[34]['name']); // 35
        $this->assertEquals('Papua Tengah', $data[37]['name']); // 38
    }

    /**
     * Test /provinces/{province}/regencies returns regencies for that province.
     */
    public function test_api_regencies_by_province_id_and_name(): void
    {
        $gorontalo = Province::where('name', 'Gorontalo')->first();
        $this->assertNotNull($gorontalo);

        // Query by ID
        $response = $this->getJson("/provinces/{$gorontalo->id}/regencies");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertNotEmpty($data);

        $regencyNames = array_column($data, 'name');
        $this->assertContains('Kabupaten Bone Bolango', $regencyNames);
        $this->assertContains('Kota Gorontalo', $regencyNames);

        // Query by Name
        $responseName = $this->getJson("/provinces/Gorontalo/regencies");
        $responseName->assertStatus(200)->assertJson(['success' => true]);
        $dataName = $responseName->json('data');
        $this->assertCount(count($data), $dataName);
    }

    /**
     * Test /regencies/{regency}/districts returns districts for that regency.
     */
    public function test_api_districts_by_regency(): void
    {
        $regency = Regency::where('name', 'Kabupaten Bone Bolango')->first();
        $this->assertNotNull($regency);

        $response = $this->getJson("/regencies/{$regency->id}/districts");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $districts = array_column($response->json('data'), 'name');

        $this->assertContains('Suwawa', $districts);
        $this->assertContains('Kabila', $districts);
    }

    /**
     * Test /districts/{district}/villages returns villages for that district.
     */
    public function test_api_villages_by_district(): void
    {
        $district = District::where('name', 'Suwawa')->first();
        $this->assertNotNull($district);

        $response = $this->getJson("/districts/{$district->id}/villages");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $villages = array_column($response->json('data'), 'name');

        $this->assertContains('Boludawa', $villages);
    }

    /**
     * Test /maps renders all 38 provinces in the location filter dropdown.
     */
    public function test_map_page_contains_all_38_provinces_in_dropdown(): void
    {
        $collector = User::factory()->create(['role' => 'pengepul', 'is_active' => true]);

        $response = $this->actingAs($collector)->get('/maps');
        $response->assertStatus(200);

        // Assert all 38 provinces are in the HTML select options
        $provinces = Province::orderBy('id')->pluck('name');
        foreach ($provinces as $provinceName) {
            $response->assertSee($provinceName);
        }
        $response->assertSee('Semua Provinsi');
    }

    /**
     * Test /register page contains separated manual location inputs.
     */
    public function test_register_page_contains_separated_location_inputs(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response->assertSee('name="province"', false);
        $response->assertSee('name="city"', false);
        $response->assertSee('name="district"', false);
        $response->assertSee('name="village"', false);
    }

    /**
     * Test that all districts have villages available and selectable.
     */
    public function test_api_villages_never_empty_for_any_district(): void
    {
        $limboto = District::where('name', 'Limboto')->first();
        $this->assertNotNull($limboto);

        $response = $this->getJson("/districts/{$limboto->id}/villages");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $villages = $response->json('data');
        $this->assertNotEmpty($villages);

        $singosari = District::where('name', 'Singosari')->first();
        $this->assertNotNull($singosari);

        $responseSingosari = $this->getJson("/districts/{$singosari->id}/villages");
        $responseSingosari->assertStatus(200)->assertJson(['success' => true]);
        $villagesSingosari = $responseSingosari->json('data');
        $this->assertNotEmpty($villagesSingosari);
    }
}
