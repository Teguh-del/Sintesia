<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneAuthTest extends TestCase
{
    /**
     * Test landing page renders successfully with commodities.
     */
    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SINTESA');
        $response->assertSee('Memperluas Akses Pasar');
    }

    /**
     * Test login page renders successfully.
     */
    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke SINTESA');
        $response->assertSee('petani@sintesa.id');
    }

    /**
     * Test register page renders successfully with roles.
     */
    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Petani');
        $response->assertSee('Pengepul');
        $response->assertSee('Konsumen');
    }

    /**
     * Test unauthenticated access to dashboards redirects to login.
     */
    public function test_unauthenticated_dashboards_redirect_to_login(): void
    {
        $this->get('/farmer/dashboard')->assertRedirect('/login');
        $this->get('/collector/dashboard')->assertRedirect('/login');
        $this->get('/consumer/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    /**
     * Test valid login redirects to role dashboard.
     */
    public function test_farmer_can_login_and_access_dashboard(): void
    {
        $user = User::where('email', 'petani@sintesa.id')->first();
        $this->assertNotNull($user);

        $response = $this->post('/login', [
            'email' => 'petani@sintesa.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/farmer/dashboard');
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->actingAs($user)->get('/farmer/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Pak Supardi');
    }

    /**
     * Test role boundary protection (Petani cannot access Admin dashboard).
     */
    public function test_role_boundary_enforcement(): void
    {
        $farmer = User::where('email', 'petani@sintesa.id')->first();

        $response = $this->actingAs($farmer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test public registration creates user and profile.
     */
    public function test_public_registration_farmer(): void
    {
        $email = 'testpetani_' . time() . '@sintesa.id';

        $response = $this->post('/register', [
            'name' => 'Petani Uji Coba',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'petani',
            'farm_name' => 'Lahan Uji Coba',
            'farm_area_hectares' => 3.5,
            'primary_commodity' => 'Cabai',
            'address' => 'Kediri',
        ]);

        $response->assertRedirect('/farmer/dashboard');
        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'petani']);
        $this->assertDatabaseHas('farmer_profiles', ['farm_name' => 'Lahan Uji Coba']);
    }

    /**
     * Test public registration with separated location fields (desa, kecamatan, kabupaten, provinsi).
     */
    public function test_public_registration_with_separated_location_fields(): void
    {
        $email = 'petani_detail_' . time() . '@sintesa.id';

        $response = $this->post('/register', [
            'name' => 'Petani Pare',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'petani',
            'farm_name' => 'Kebun Pare Sejahtera',
            'farm_area_hectares' => 2.0,
            'primary_commodity' => 'Jagung',
            'province' => 'Jawa Timur',
            'city' => 'Kabupaten Kediri',
            'district' => 'Pare',
            'village' => 'Tertek',
            'detail_address' => 'RT 01 / RW 02',
        ]);

        $response->assertRedirect('/farmer/dashboard');
        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'petani']);
        $this->assertDatabaseHas('farmer_profiles', [
            'farm_name' => 'Kebun Pare Sejahtera',
            'province' => 'Jawa Timur',
            'city' => 'Kabupaten Kediri',
            'district' => 'Pare',
            'village' => 'Tertek',
        ]);
    }

    /**
     * Test admin role cannot be injected via public registration.
     */
    public function test_admin_role_cannot_be_registered_publicly(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@sintesa.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', ['email' => 'hacker@sintesa.id']);
    }
}
