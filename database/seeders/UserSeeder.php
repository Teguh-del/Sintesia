<?php

namespace Database\Seeders;

use App\Models\CollectorProfile;
use App\Models\ConsumerProfile;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@sintesa.id'],
            [
                'name' => 'Admin SINTESA',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // 2. Petani: Pak Supardi - Tani Makmur
        $farmerUser = User::updateOrCreate(
            ['email' => 'petani@sintesa.id'],
            [
                'name' => 'Pak Supardi',
                'password' => Hash::make('password'),
                'role' => 'petani',
                'phone' => '081298765432',
                'is_active' => true,
            ]
        );

        FarmerProfile::updateOrCreate(
            ['user_id' => $farmerUser->id],
            [
                'farm_name' => 'Kelompok Tani Makmur',
                'farm_area_hectares' => 2.50,
                'primary_commodity' => 'Jagung',
                'address' => 'Desa Pare, Kecamatan Pare, Kabupaten Kediri, Jawa Timur',
                'latitude' => -7.7651000,
                'longitude' => 112.1983000,
            ]
        );

        // 3. Pengepul: CV Hasil Bumi Nusantara
        $collectorUser = User::updateOrCreate(
            ['email' => 'pengepul@sintesa.id'],
            [
                'name' => 'Haji Slamet',
                'password' => Hash::make('password'),
                'role' => 'pengepul',
                'phone' => '081345678901',
                'is_active' => true,
            ]
        );

        CollectorProfile::updateOrCreate(
            ['user_id' => $collectorUser->id],
            [
                'business_name' => 'CV Hasil Bumi Nusantara',
                'business_type' => 'Pengepul Grosir & Distribusi',
                'address' => 'Jl. Pahlawan No. 45, Kediri, Jawa Timur',
                'latitude' => -7.8184500,
                'longitude' => 112.0156000,
            ]
        );

        // 4. Konsumen: Ibu Sari
        $consumerUser = User::updateOrCreate(
            ['email' => 'konsumen@sintesa.id'],
            [
                'name' => 'Ibu Sari Rahayu',
                'password' => Hash::make('password'),
                'role' => 'konsumen',
                'phone' => '081456789012',
                'is_active' => true,
            ]
        );

        ConsumerProfile::updateOrCreate(
            ['user_id' => $consumerUser->id],
            [
                'address' => 'Perumahan Asri Indah Blok C-12, Kediri, Jawa Timur',
                'latitude' => -7.8250000,
                'longitude' => 112.0200000,
            ]
        );
    }
}
