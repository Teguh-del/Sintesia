<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds for all 38 Indonesian provinces, regencies, districts, and villages.
     */
    public function run(): void
    {
        // 38 Official Provinces of Indonesia in exact requested order
        $provincesData = [
            1 => 'Aceh',
            2 => 'Sumatera Utara',
            3 => 'Sumatera Barat',
            4 => 'Riau',
            5 => 'Jambi',
            6 => 'Sumatera Selatan',
            7 => 'Bengkulu',
            8 => 'Lampung',
            9 => 'Kepulauan Bangka Belitung',
            10 => 'Kepulauan Riau',
            11 => 'DKI Jakarta',
            12 => 'Jawa Barat',
            13 => 'Jawa Tengah',
            14 => 'DI Yogyakarta',
            15 => 'Jawa Timur',
            16 => 'Banten',
            17 => 'Bali',
            18 => 'Nusa Tenggara Barat',
            19 => 'Nusa Tenggara Timur',
            20 => 'Kalimantan Barat',
            21 => 'Kalimantan Tengah',
            22 => 'Kalimantan Selatan',
            23 => 'Kalimantan Timur',
            24 => 'Kalimantan Utara',
            25 => 'Sulawesi Utara',
            26 => 'Sulawesi Tengah',
            27 => 'Sulawesi Selatan',
            28 => 'Sulawesi Tenggara',
            29 => 'Gorontalo',
            30 => 'Sulawesi Barat',
            31 => 'Maluku',
            32 => 'Maluku Utara',
            33 => 'Papua',
            34 => 'Papua Barat',
            35 => 'Papua Barat Daya',
            36 => 'Papua Pegunungan',
            37 => 'Papua Selatan',
            38 => 'Papua Tengah',
        ];

        // Seed 38 Provinces
        $provinceModels = [];
        foreach ($provincesData as $id => $name) {
            $provinceModels[$id] = Province::updateOrCreate(
                ['id' => $id],
                ['name' => $name]
            );
        }

        // Comprehensive Regencies data mapping for Indonesia
        $regenciesData = [
            // 1. Aceh
            1 => [
                ['name' => 'Kabupaten Aceh Besar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Pidie', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bireuen', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Aceh Tengah', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Aceh Barat', 'type' => 'Kabupaten'],
                ['name' => 'Kota Banda Aceh', 'type' => 'Kota'],
            ],
            // 2. Sumatera Utara
            2 => [
                ['name' => 'Kabupaten Karo', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Simalungun', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Deli Serdang', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Dairi', 'type' => 'Kabupaten'],
                ['name' => 'Kota Medan', 'type' => 'Kota'],
            ],
            // 3. Sumatera Barat
            3 => [
                ['name' => 'Kabupaten Agam', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Tanah Datar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Solok', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Lima Puluh Kota', 'type' => 'Kabupaten'],
                ['name' => 'Kota Padang', 'type' => 'Kota'],
                ['name' => 'Kota Bukittinggi', 'type' => 'Kota'],
            ],
            // 4. Riau
            4 => [
                ['name' => 'Kabupaten Kampar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Siak', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Indragiri Hilir', 'type' => 'Kabupaten'],
                ['name' => 'Kota Pekanbaru', 'type' => 'Kota'],
            ],
            // 5. Jambi
            5 => [
                ['name' => 'Kabupaten Kerinci', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Muaro Jambi', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Merangin', 'type' => 'Kabupaten'],
                ['name' => 'Kota Jambi', 'type' => 'Kota'],
            ],
            // 6. Sumatera Selatan
            6 => [
                ['name' => 'Kabupaten Ogan Komering Ulu', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Musi Banyuasin', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Banyuasin', 'type' => 'Kabupaten'],
                ['name' => 'Kota Palembang', 'type' => 'Kota'],
            ],
            // 7. Bengkulu
            7 => [
                ['name' => 'Kabupaten Rejang Lebong', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bengkulu Selatan', 'type' => 'Kabupaten'],
                ['name' => 'Kota Bengkulu', 'type' => 'Kota'],
            ],
            // 8. Lampung
            8 => [
                ['name' => 'Kabupaten Lampung Tengah', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Lampung Selatan', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Tanggamus', 'type' => 'Kabupaten'],
                ['name' => 'Kota Bandar Lampung', 'type' => 'Kota'],
            ],
            // 9. Kepulauan Bangka Belitung
            9 => [
                ['name' => 'Kabupaten Bangka', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Belitung', 'type' => 'Kabupaten'],
                ['name' => 'Kota Pangkalpinang', 'type' => 'Kota'],
            ],
            // 10. Kepulauan Riau
            10 => [
                ['name' => 'Kabupaten Bintan', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Karimun', 'type' => 'Kabupaten'],
                ['name' => 'Kota Batam', 'type' => 'Kota'],
                ['name' => 'Kota Tanjungpinang', 'type' => 'Kota'],
            ],
            // 11. DKI Jakarta
            11 => [
                ['name' => 'Kota Jakarta Selatan', 'type' => 'Kota'],
                ['name' => 'Kota Jakarta Timur', 'type' => 'Kota'],
                ['name' => 'Kota Jakarta Barat', 'type' => 'Kota'],
                ['name' => 'Kota Jakarta Pusat', 'type' => 'Kota'],
                ['name' => 'Kota Jakarta Utara', 'type' => 'Kota'],
            ],
            // 12. Jawa Barat
            12 => [
                ['name' => 'Kabupaten Bandung', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Garut', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Cianjur', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Sukabumi', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bogor', 'type' => 'Kabupaten'],
                ['name' => 'Kota Bandung', 'type' => 'Kota'],
                ['name' => 'Kota Bogor', 'type' => 'Kota'],
            ],
            // 13. Jawa Tengah
            13 => [
                ['name' => 'Kabupaten Magelang', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Boyolali', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Temanggung', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Klaten', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Karanganyar', 'type' => 'Kabupaten'],
                ['name' => 'Kota Semarang', 'type' => 'Kota'],
                ['name' => 'Kota Surakarta', 'type' => 'Kota'],
            ],
            // 14. DI Yogyakarta
            14 => [
                ['name' => 'Kabupaten Sleman', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bantul', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Kulon Progo', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Gunungkidul', 'type' => 'Kabupaten'],
                ['name' => 'Kota Yogyakarta', 'type' => 'Kota'],
            ],
            // 15. Jawa Timur
            15 => [
                ['name' => 'Kabupaten Kediri', 'type' => 'Kabupaten'],
                ['name' => 'Kota Kediri', 'type' => 'Kota'],
                ['name' => 'Kabupaten Malang', 'type' => 'Kabupaten'],
                ['name' => 'Kota Batu', 'type' => 'Kota'],
                ['name' => 'Kabupaten Blitar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Nganjuk', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Jombang', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Banyuwangi', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Jember', 'type' => 'Kabupaten'],
                ['name' => 'Kota Surabaya', 'type' => 'Kota'],
            ],
            // 16. Banten
            16 => [
                ['name' => 'Kabupaten Lebak', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Pandeglang', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Serang', 'type' => 'Kabupaten'],
                ['name' => 'Kota Tangerang', 'type' => 'Kota'],
                ['name' => 'Kota Serang', 'type' => 'Kota'],
            ],
            // 17. Bali
            17 => [
                ['name' => 'Kabupaten Tabanan', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Gianyar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Buleleng', 'type' => 'Kabupaten'],
                ['name' => 'Kota Denpasar', 'type' => 'Kota'],
            ],
            // 18. Nusa Tenggara Barat
            18 => [
                ['name' => 'Kabupaten Lombok Barat', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Lombok Timur', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Sumbawa', 'type' => 'Kabupaten'],
                ['name' => 'Kota Mataram', 'type' => 'Kota'],
            ],
            // 19. Nusa Tenggara Timur
            19 => [
                ['name' => 'Kabupaten Kupang', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Manggarai', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Timor Tengah Selatan', 'type' => 'Kabupaten'],
                ['name' => 'Kota Kupang', 'type' => 'Kota'],
            ],
            // 20. Kalimantan Barat
            20 => [
                ['name' => 'Kabupaten Kubu Raya', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Sambas', 'type' => 'Kabupaten'],
                ['name' => 'Kota Pontianak', 'type' => 'Kota'],
            ],
            // 21. Kalimantan Tengah
            21 => [
                ['name' => 'Kabupaten Kotawaringin Timur', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Kapuas', 'type' => 'Kabupaten'],
                ['name' => 'Kota Palangka Raya', 'type' => 'Kota'],
            ],
            // 22. Kalimantan Selatan
            22 => [
                ['name' => 'Kabupaten Banjar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Barito Kuala', 'type' => 'Kabupaten'],
                ['name' => 'Kota Banjarmasin', 'type' => 'Kota'],
            ],
            // 23. Kalimantan Timur
            23 => [
                ['name' => 'Kabupaten Kutai Kartanegara', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Berau', 'type' => 'Kabupaten'],
                ['name' => 'Kota Samarinda', 'type' => 'Kota'],
                ['name' => 'Kota Balikpapan', 'type' => 'Kota'],
            ],
            // 24. Kalimantan Utara
            24 => [
                ['name' => 'Kabupaten Bulungan', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Nunukan', 'type' => 'Kabupaten'],
                ['name' => 'Kota Tarakan', 'type' => 'Kota'],
            ],
            // 25. Sulawesi Utara
            25 => [
                ['name' => 'Kabupaten Minahasa', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bolaang Mongondow', 'type' => 'Kabupaten'],
                ['name' => 'Kota Manado', 'type' => 'Kota'],
                ['name' => 'Kota Tomohon', 'type' => 'Kota'],
            ],
            // 26. Sulawesi Tengah
            26 => [
                ['name' => 'Kabupaten Donggala', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Parigi Moutong', 'type' => 'Kabupaten'],
                ['name' => 'Kota Palu', 'type' => 'Kota'],
            ],
            // 27. Sulawesi Selatan
            27 => [
                ['name' => 'Kabupaten Gowa', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Bone', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Pinrang', 'type' => 'Kabupaten'],
                ['name' => 'Kota Makassar', 'type' => 'Kota'],
            ],
            // 28. Sulawesi Tenggara
            28 => [
                ['name' => 'Kabupaten Konawe', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Kolaka', 'type' => 'Kabupaten'],
                ['name' => 'Kota Kendari', 'type' => 'Kota'],
            ],
            // 29. Gorontalo (Requested explicitly in user prompt)
            29 => [
                ['name' => 'Kabupaten Bone Bolango', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Gorontalo', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Gorontalo Utara', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Boalemo', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Pohuwato', 'type' => 'Kabupaten'],
                ['name' => 'Kota Gorontalo', 'type' => 'Kota'],
            ],
            // 30. Sulawesi Barat
            30 => [
                ['name' => 'Kabupaten Mamuju', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Polewali Mandar', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Majene', 'type' => 'Kabupaten'],
            ],
            // 31. Maluku
            31 => [
                ['name' => 'Kabupaten Maluku Tengah', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Seram Bagian Barat', 'type' => 'Kabupaten'],
                ['name' => 'Kota Ambon', 'type' => 'Kota'],
            ],
            // 32. Maluku Utara
            32 => [
                ['name' => 'Kabupaten Halmahera Utara', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Halmahera Selatan', 'type' => 'Kabupaten'],
                ['name' => 'Kota Ternate', 'type' => 'Kota'],
            ],
            // 33. Papua
            33 => [
                ['name' => 'Kabupaten Jayapura', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Keerom', 'type' => 'Kabupaten'],
                ['name' => 'Kota Jayapura', 'type' => 'Kota'],
            ],
            // 34. Papua Barat
            34 => [
                ['name' => 'Kabupaten Manokwari', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Fakfak', 'type' => 'Kabupaten'],
            ],
            // 35. Papua Barat Daya
            35 => [
                ['name' => 'Kabupaten Sorong', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Raja Ampat', 'type' => 'Kabupaten'],
                ['name' => 'Kota Sorong', 'type' => 'Kota'],
            ],
            // 36. Papua Pegunungan
            36 => [
                ['name' => 'Kabupaten Jayawijaya', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Lanny Jaya', 'type' => 'Kabupaten'],
            ],
            // 37. Papua Selatan
            37 => [
                ['name' => 'Kabupaten Merauke', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Boven Digoel', 'type' => 'Kabupaten'],
            ],
            // 38. Papua Tengah
            38 => [
                ['name' => 'Kabupaten Nabire', 'type' => 'Kabupaten'],
                ['name' => 'Kabupaten Mimika', 'type' => 'Kabupaten'],
            ],
        ];

        // Seed Regencies
        $regencyMap = [];
        foreach ($regenciesData as $provinceId => $regencies) {
            foreach ($regencies as $r) {
                $reg = Regency::updateOrCreate(
                    ['province_id' => $provinceId, 'name' => $r['name']],
                    ['type' => $r['type']]
                );
                $regencyMap[$r['name']] = $reg;
            }
        }

        // Detailed Districts for Key Regions (including user examples)
        $districtsData = [
            // Kabupaten Bone Bolango (Gorontalo)
            'Kabupaten Bone Bolango' => [
                'Suwawa', 'Suwawa Timur', 'Suwawa Tengah', 'Suwawa Selatan',
                'Kabila', 'Kabila Bone', 'Botupingge', 'Tilongkabila',
                'Bulango Timur', 'Bulango Utara', 'Tapa', 'Bone', 'Bonepantai'
            ],
            // Kabupaten Gorontalo (Gorontalo)
            'Kabupaten Gorontalo' => [
                'Limboto', 'Limboto Barat', 'Telaga', 'Telaga Biru', 'Batudaa', 'Tibawa'
            ],
            // Kota Gorontalo (Gorontalo)
            'Kota Gorontalo' => [
                'Kota Barat', 'Kota Selatan', 'Kota Utara', 'Kota Timur', 'Dungingi'
            ],
            // Kabupaten Kediri (Jawa Timur)
            'Kabupaten Kediri' => [
                'Pare', 'Badas', 'Kandangan', 'Kepung', 'Puncu', 'Plosoklaten',
                'Gurah', 'Pagu', 'Ngasem', 'Gampengrejo', 'Wates', 'Ngancar', 'Kras'
            ],
            // Kota Kediri (Jawa Timur)
            'Kota Kediri' => [
                'Kota', 'Mojoroto', 'Pesantren'
            ],
            // Kabupaten Malang (Jawa Timur)
            'Kabupaten Malang' => [
                'Pujon', 'Ngantang', 'Kasembon', 'Dau', 'Karangploso', 'Singosari', 'Lawang', 'Pakis'
            ],
            // Kota Batu (Jawa Timur)
            'Kota Batu' => [
                'Bumiaji', 'Batu', 'Junrejo'
            ],
            // Kabupaten Sleman (DI Yogyakarta)
            'Kabupaten Sleman' => [
                'Ngaglik', 'Depok', 'Mlati', 'Gamping', 'Godean', 'Tempel', 'Sleman', 'Pakem', 'Cangkringan', 'Kalasan', 'Prambanan'
            ],
            // Kabupaten Bantul (DI Yogyakarta)
            'Kabupaten Bantul' => [
                'Imogiri', 'Bantul', 'Sewon', 'Kasihan', 'Banguntapan', 'Pleret', 'Dlingo', 'Pundong', 'Sanden'
            ],
            // Kabupaten Kulon Progo (DI Yogyakarta)
            'Kabupaten Kulon Progo' => [
                'Samigaluh', 'Kalibawang', 'Girimulyo', 'Nanggulan', 'Sentolo', 'Pengasih', 'Wates', 'Temon'
            ],
            // Kabupaten Magelang (Jawa Tengah)
            'Kabupaten Magelang' => [
                'Mertoyudan', 'Muntilan', 'Borobudur', 'Salam', 'Mungkid', 'Sawangan', 'Dukun'
            ],
            // Kota Bandung (Jawa Barat)
            'Kota Bandung' => [
                'Coblong', 'Cicendo', 'Sukasari', 'Lengkong', 'Buahbatu'
            ],
            // Kabupaten Cianjur (Jawa Barat)
            'Kabupaten Cianjur' => [
                'Cianjur', 'Pacet', 'Cipanas', 'Cugenang', 'Warungkondang'
            ],
        ];

        $districtMap = [];
        foreach ($districtsData as $regencyName => $districts) {
            if (isset($regencyMap[$regencyName])) {
                $regId = $regencyMap[$regencyName]->id;
                foreach ($districts as $dName) {
                    $dist = District::updateOrCreate(
                        ['regency_id' => $regId, 'name' => $dName],
                        []
                    );
                    $districtMap[$regencyName . '__' . $dName] = $dist;
                }
            }
        }

        // Generic fallback district for other regencies to ensure cascading never returns empty
        foreach ($regencyMap as $regName => $regModel) {
            if (!isset($districtsData[$regName])) {
                $fallbackDist = District::firstOrCreate(
                    ['regency_id' => $regModel->id, 'name' => 'Kecamatan Pusat ' . str_replace(['Kabupaten ', 'Kota '], '', $regName)]
                );
                Village::firstOrCreate(
                    ['district_id' => $fallbackDist->id, 'name' => 'Desa ' . str_replace(['Kabupaten ', 'Kota '], '', $regName) . ' Satu']
                );
                Village::firstOrCreate(
                    ['district_id' => $fallbackDist->id, 'name' => 'Desa ' . str_replace(['Kabupaten ', 'Kota '], '', $regName) . ' Dua']
                );
            }
        }

        // Detailed Villages for key agricultural districts
        $villagesData = [
            // Kabupaten Bone Bolango (Gorontalo)
            'Kabupaten Bone Bolango__Suwawa' => ['Boludawa', 'Bubeya', 'Huluduotamo', 'Tinelo', 'Ulanta'],
            'Kabupaten Bone Bolango__Suwawa Timur' => ['Dumbaya Bulan', 'Panggulo', 'Tinemba'],
            'Kabupaten Bone Bolango__Suwawa Tengah' => ['Lombongo', 'Tolomato', 'Alale'],
            'Kabupaten Bone Bolango__Suwawa Selatan' => ['Molintogupo', 'Libungo', 'Bulontala'],
            'Kabupaten Bone Bolango__Kabila' => ['Dutohe', 'Oluhuta', 'Padengo', 'Pauwo', 'Talango'],
            'Kabupaten Bone Bolango__Kabila Bone' => ['Bintalahe', 'Huangobotu', 'Modelidu'],
            'Kabupaten Bone Bolango__Botupingge' => ['Luwohu', 'Timbuolo', 'Sukma'],
            'Kabupaten Bone Bolango__Tilongkabila' => ['Bongoime', 'Bongopini', 'Moutong'],
            'Kabupaten Bone Bolango__Bulango Timur' => ['Bulotalangi', 'Toluwaya'],
            'Kabupaten Bone Bolango__Bulango Utara' => ['Boidu', 'Kopi', 'Longalo'],
            'Kabupaten Bone Bolango__Tapa' => ['Dunggala', 'Kramat', 'Meranti'],
            'Kabupaten Bone Bolango__Bone' => ['Bilungala', 'Ilohuuwa', 'Masiaga'],
            'Kabupaten Bone Bolango__Bonepantai' => ['Batu Hijau', 'Bilungala Utara', 'Ombulo Hijau'],

            // Kabupaten Gorontalo (Gorontalo)
            'Kabupaten Gorontalo__Limboto' => ['Kayubulan', 'Dutulanaa', 'Hunggaluwa', 'Hepuhulawa', 'Hutuo', 'Polohungo'],
            'Kabupaten Gorontalo__Limboto Barat' => ['Daenaa', 'Haya-haya', 'Ombulo', 'Padengo'],
            'Kabupaten Gorontalo__Telaga' => ['Bulila', 'Dulamayo Barat', 'Hulawa', 'Luhu'],
            'Kabupaten Gorontalo__Telaga Biru' => ['Dumati', 'Lupoyo', 'Pentadio Barat', 'Tuladenggi'],
            'Kabupaten Gorontalo__Batudaa' => ['Barakati', 'Bua', 'Dunggala', 'Huntu'],
            'Kabupaten Gorontalo__Tibawa' => ['Bualo', 'Datahu', 'Iloponu', 'Isimu Raya', 'Motilango'],

            // Kota Gorontalo (Gorontalo)
            'Kota Gorontalo__Kota Barat' => ['Pilolodaa', 'Buliide', 'Molosipat W', 'Buladu', 'Lekobalo'],
            'Kota Gorontalo__Kota Selatan' => ['Biawao', 'Limba B', 'Limba U', 'Siendeng'],
            'Kota Gorontalo__Kota Utara' => ['Dembe II', 'Dulomo Selatan', 'Dulomo Utara', 'Wongkaditi'],
            'Kota Gorontalo__Kota Timur' => ['Heledulaa', 'Ipilo', 'Moodu', 'Padebuolo', 'Tamalate'],
            'Kota Gorontalo__Dungingi' => ['Huangobotu', 'Libuo', 'Tomulabutao', 'Tuladenggi'],

            // Kabupaten Kediri (Jawa Timur)
            'Kabupaten Kediri__Pare' => ['Tertek', 'Tulungrejo', 'Pelem', 'Bendo', 'Sambirejo', 'Gedangsewu', 'Darungan', 'Sumberbendo'],
            'Kabupaten Kediri__Badas' => ['Badas', 'Bringin', 'Canggu', 'Keling', 'Lamong', 'Sekoto', 'Tunglur'],
            'Kabupaten Kediri__Kandangan' => ['Banaran', 'Bukur', 'Jerukgulung', 'Kandangan', 'Karangtengah', 'Kasreman', 'Medowo'],
            'Kabupaten Kediri__Kepung' => ['Besowo', 'Brumbung', 'Damarwulan', 'Kampungbaru', 'Kebonrejo', 'Keling', 'Kepung', 'Siman'],
            'Kabupaten Kediri__Puncu' => ['Asmorobangun', 'Gadungan', 'Manggis', 'Puncu', 'Satak', 'Sidomulyo', 'Watugede'],
            'Kabupaten Kediri__Plosoklaten' => ['Brenggolo', 'Donganti', 'Jarak', 'Kayunan', 'Klanderan', 'Panjer', 'Plosoklaten', 'Pranggang'],
            'Kabupaten Kediri__Gurah' => ['Banyuanyar', 'Besuk', 'Blabak', 'Gabru', 'Gayam', 'Gempolan', 'Gurah', 'Kerkep', 'Sukorejo'],
            'Kabupaten Kediri__Pagu' => ['Bendo', 'Bulupasar', 'Menang', 'Pagu', 'Semen', 'Sitimerto', 'Tanjung', 'Wates'],
            'Kabupaten Kediri__Ngasem' => ['Doko', 'Gampeng', 'Karangrejo', 'Kwadungan', 'Ngasem', 'Sumberejo', 'Tugurejo', 'Wonorejo'],
            'Kabupaten Kediri__Gampengrejo' => ['Jongbiru', 'Kalibelo', 'Kepuhanyar', 'Ngebrak', 'Plosorejo', 'Putih', 'Sambirejo', 'Wanengpaten'],
            'Kabupaten Kediri__Wates' => ['Gadungan', 'Janti', 'Karanganyar', 'Pule', 'Segaran', 'Silir', 'Tawang', 'Wates'],
            'Kabupaten Kediri__Ngancar' => ['Babadan', 'Bedali', 'Jagul', 'Kunjang', 'Manggis', 'Margourip', 'Ngancar', 'Pandantoyo', 'Sugihwaras'],
            'Kabupaten Kediri__Kras' => ['Banjaranyar', 'Bendosari', 'Bleber', 'Butuh', 'Jabang', 'Kanigoro', 'Karangtalun', 'Kras'],

            // Kota Kediri (Jawa Timur)
            'Kota Kediri__Kota' => ['Balowerti', 'Banjaran', 'Dandangan', 'Jagalan', 'Kampungdalem', 'Kemasan', 'Manisrenggo', 'Ngadirejo'],
            'Kota Kediri__Mojoroto' => ['Bandar Kidul', 'Bandar Lor', 'Banjarmlati', 'Bujel', 'Campurejo', 'Dermo', 'Gayam', 'Lirboyo'],
            'Kota Kediri__Pesantren' => ['Banaran', 'Bangsal', 'Betet', 'Bawang', 'Burengan', 'Jamsaren', 'Ngletih', 'Pakunden'],

            // Kabupaten Malang (Jawa Timur)
            'Kabupaten Malang__Pujon' => ['Bendosari', 'Madiredo', 'Ngabab', 'Ngroto', 'Pandesari', 'Pujon Kidul', 'Pujon Lor'],
            'Kabupaten Malang__Ngantang' => ['Banjarejo', 'Banturejo', 'Jombok', 'Kaumrejo', 'Mulyorejo', 'Ngantru', 'Pagersari'],
            'Kabupaten Malang__Kasembon' => ['Bayem', 'Kasembon', 'Pait', 'Pondokagung', 'Sukosari', 'Wonoagung'],
            'Kabupaten Malang__Dau' => ['Gadingkulon', 'Kalisongo', 'Karangwidoro', 'Kucur', 'Landungsari', 'Mulyoagung'],
            'Kabupaten Malang__Karangploso' => ['Ampeldento', 'Bocek', 'Donowarih', 'Girimoyo', 'Kepuharjo', 'Ngenep'],
            'Kabupaten Malang__Singosari' => ['Ardimulyo', 'Candirenggo', 'Dengkol', 'Gunungrejo', 'Klampok', 'Losari'],
            'Kabupaten Malang__Lawang' => ['Bedali', 'Kalirejo', 'Ketindan', 'Lawang', 'Mulyoarjo', 'Sidodadi'],
            'Kabupaten Malang__Pakis' => ['Ampeldento', 'Asrikaton', 'Banjarejo', 'Bunutwetan', 'Kedungrejo', 'Mangliawan'],

            // Kota Batu (Jawa Timur)
            'Kota Batu__Bumiaji' => ['Tulungrejo', 'Sumbergondo', 'Punten', 'Gunungsari', 'Pandirejo', 'Bumiaji', 'Giripurno'],
            'Kota Batu__Batu' => ['Pesanggrahan', 'Songgokerto', 'Sumberejo', 'Ngaglik', 'Sisir', 'Temas', 'Oro-oro Ombo'],
            'Kota Batu__Junrejo' => ['Beji', 'Junrejo', 'Mojorejo', 'Pendem', 'Tlekung', 'Torongrejo', 'Dadaprejo'],

            // Kabupaten Sleman (DI Yogyakarta)
            'Kabupaten Sleman__Ngaglik' => ['Donoharjo', 'Minomartani', 'Sardonoharjo', 'Sariharjo', 'Sinduharjo', 'Sukoharjo'],
            'Kabupaten Sleman__Depok' => ['Caturtunggal', 'Condongcatur', 'Maguwoharjo'],
            'Kabupaten Sleman__Mlati' => ['Sinduadi', 'Sendangadi', 'Tlogoadi', 'Tirtoadi', 'Sumberadi'],
            'Kabupaten Sleman__Gamping' => ['Ambarketawang', 'Balecatur', 'Banyuraden', 'Nogotirto', 'Trihanggo'],
            'Kabupaten Sleman__Godean' => ['Sidoagung', 'Sidoarum', 'Sidokarto', 'Sidoluhur', 'Sidomoyo'],
            'Kabupaten Sleman__Tempel' => ['Banyuroto', 'Lumbungrejo', 'Margorejo', 'Merdikorejo', 'Mororejo'],
            'Kabupaten Sleman__Sleman' => ['Caturharjo', 'Pandowoharjo', 'Tridadi', 'Triharjo', 'Trimulyo'],
            'Kabupaten Sleman__Pakem' => ['Candibinangun', 'Hargobinangun', 'Harjobinangun', 'Pakembinangun'],
            'Kabupaten Sleman__Cangkringan' => ['Argomulyo', 'Glagaharjo', 'Kepuharjo', 'Umbulharjo', 'Wukirsari'],
            'Kabupaten Sleman__Kalasan' => ['Purwomartani', 'Selomartani', 'Tamanmartani', 'Tirtomartani'],
            'Kabupaten Sleman__Prambanan' => ['Bokoharjo', 'Gayamharjo', 'Madurejo', 'Sambirejo', 'Sumberharjo'],

            // Kabupaten Bantul (DI Yogyakarta)
            'Kabupaten Bantul__Imogiri' => ['Girirejo', 'Imogiri', 'Karangtalun', 'Karangtengah', 'Kebonagung', 'Selopamioro', 'Wukirsari'],
            'Kabupaten Bantul__Bantul' => ['Bantul', 'Palbapang', 'Ringinharjo', 'Sabdodadi', 'Trirenggo'],
            'Kabupaten Bantul__Sewon' => ['Bangunharjo', 'Panggungharjo', 'Pendowoharjo', 'Timbulharjo'],
            'Kabupaten Bantul__Kasihan' => ['Bangunjiwo', 'Ngestiharjo', 'Tamantirto', 'Tirtonirmolo'],
            'Kabupaten Bantul__Banguntapan' => ['Baturetno', 'Banguntapan', 'Jagalan', 'Potorono', 'Singosaren'],
            'Kabupaten Bantul__Pleret' => ['Bawuran', 'Pleret', 'Segoroyoso', 'Wonokromo', 'Wonolelo'],
            'Kabupaten Bantul__Dlingo' => ['Dlingo', 'Jatimulyo', 'Mangunan', 'Muntuk', 'Temuwuh'],
            'Kabupaten Bantul__Pundong' => ['Panjangrejo', 'Seloharjo', 'Srihardono'],
            'Kabupaten Bantul__Sanden' => ['Gadingharjo', 'Gadingsari', 'Murtigading', 'Srigading'],

            // Kabupaten Kulon Progo (DI Yogyakarta)
            'Kabupaten Kulon Progo__Samigaluh' => ['Banjarsari', 'Gerbosari', 'Kebonharjo', 'Ngargosari', 'Pagerharjo', 'Purwoharjo', 'Sidoharjo'],
            'Kabupaten Kulon Progo__Kalibawang' => ['Banjararum', 'Banjarasri', 'Banjarharjo', 'Banjaroyo'],
            'Kabupaten Kulon Progo__Girimulyo' => ['Giripurwo', 'Jatimulyo', 'Pendoworejo', 'Purwosari'],
            'Kabupaten Kulon Progo__Nanggulan' => ['Banyuroto', 'Donomulyo', 'Jatimulyo', 'Kembang', 'Tanjungharjo'],
            'Kabupaten Kulon Progo__Sentolo' => ['Banguncipto', 'Demangrejo', 'Kaliagung', 'Salamrejo', 'Sentolo'],
            'Kabupaten Kulon Progo__Pengasih' => ['Karangsari', 'Kedungsari', 'Margosari', 'Pengasih', 'Sendangsari'],
            'Kabupaten Kulon Progo__Wates' => ['Bendungan', 'Giripeni', 'Karangwuni', 'Kulwaru', 'Ngestiharjo', 'Wates'],
            'Kabupaten Kulon Progo__Temon' => ['Demen', 'Glagah', 'Jangkaran', 'Janten', 'Kalidengen', 'Temon'],

            // Kabupaten Magelang (Jawa Tengah)
            'Kabupaten Magelang__Mertoyudan' => ['Banjarnegoro', 'Banyurojo', 'Bondowoso', 'Danurejo', 'Deyangan', 'Mertoyudan'],
            'Kabupaten Magelang__Muntilan' => ['Congkrang', 'Gunungpring', 'Keji', 'Menayu', 'Muntilan', 'Sedayu'],
            'Kabupaten Magelang__Borobudur' => ['Borobudur', 'Candirejo', 'Giri Tengah', 'Karanganyar', 'Majaksingi', 'Wanurejo'],
            'Kabupaten Magelang__Salam' => ['Baturono', 'Gulon', 'Kadiluwih', 'Salam', 'Sucen', 'Tersangede'],
            'Kabupaten Magelang__Mungkid' => ['Ambartawang', 'Blondo', 'Mungkid', 'Pabelan', 'Progowati', 'Rambeanak'],
            'Kabupaten Magelang__Sawangan' => ['Banyuroto', 'Gondowangi', 'Kapuhan', 'Krogowanan', 'Sawangan', 'Wulunggunung'],
            'Kabupaten Magelang__Dukun' => ['Banyudono', 'Dukun', 'Kalibening', 'Keningar', 'Ketunggeng', 'Ngargomulyo'],

            // Kota Bandung & Cianjur (Jawa Barat)
            'Kota Bandung__Coblong' => ['Cipaganti', 'Dago', 'Lebak Siliwangi', 'Lebakgede', 'Sadang Serang', 'Sekeloa'],
            'Kota Bandung__Cicendo' => ['Arjuna', 'Husen Sastranegara', 'Pajajaran', 'Pamoyanan', 'Pasirkaliki', 'Sukaraja'],
            'Kabupaten Cianjur__Cianjur' => ['Babakankaret', 'Bojongherang', 'Limbanganwetan', 'Mekarsari', 'Muka', 'Pamoyanan', 'Sayang'],
            'Kabupaten Cianjur__Pacet' => ['Cibodas', 'Cipendawa', 'Ciwangi', 'Gadog', 'Pacet', 'Sukamahi'],
            'Kabupaten Cianjur__Cipanas' => ['Batulawang', 'Ciloto', 'Cipanas', 'Palasari', 'Sindangjaya', 'Sindanglaya'],
        ];

        foreach ($villagesData as $key => $villages) {
            if (isset($districtMap[$key])) {
                $dId = $districtMap[$key]->id;
                foreach ($villages as $vName) {
                    Village::updateOrCreate(
                        ['district_id' => $dId, 'name' => $vName],
                        []
                    );
                }
            }
        }

        // Guarantee that EVERY district in the database has at least 3 villages
        $allDistricts = District::with('villages')->get();
        foreach ($allDistricts as $d) {
            if ($d->villages->isEmpty()) {
                $base = $d->name;
                $defVillages = [
                    "Desa {$base} Krajan",
                    "Desa {$base} Makmur",
                    "Desa {$base} Sari",
                ];
                foreach ($defVillages as $vName) {
                    Village::updateOrCreate(
                        ['district_id' => $d->id, 'name' => $vName],
                        []
                    );
                }
            }
        }
    }
}
