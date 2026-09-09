<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\CommodityPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PhaseSevenMapPriceSeeder extends Seeder
{
    /**
     * Seed 35 days of realistic daily market price history for core commodities across regional centers.
     */
    public function run(): void
    {
        $commodities = Commodity::all()->keyBy('name');

        $scenarios = [
            'Jagung' => [
                'base_price' => 7600,
                'variance' => 150,
                'trend_factor' => 5, // slow steady upward
                'unit' => 'kg',
                'markets' => ['Pasar Induk Pare, Kediri', 'Pasar Hewan & Pakan Bantul'],
            ],
            'Cabai' => [
                'base_price' => 26000,
                'variance' => 800,
                'trend_factor' => 80, // rising price due to seasonality
                'unit' => 'kg',
                'markets' => ['Pasar Beringharjo, Sleman/Yogya', 'Pasar Induk Pare, Kediri', 'Pasar Karangploso, Malang'],
            ],
            'Tomat' => [
                'base_price' => 11500,
                'variance' => 400,
                'trend_factor' => -40, // slight downward due to harvest peak
                'unit' => 'kg',
                'markets' => ['Pasar Karangploso, Malang', 'Pasar Sleman, DI Yogyakarta'],
            ],
            'Kelapa' => [
                'base_price' => 5800,
                'variance' => 100,
                'trend_factor' => 0, // very stable
                'unit' => 'butir',
                'markets' => ['Pasar Wates, Kulon Progo', 'Pasar Banyuwangi'],
            ],
            'Padi' => [
                'base_price' => 14200,
                'variance' => 150,
                'trend_factor' => 10, // stable organic rice
                'unit' => 'kg',
                'markets' => ['Pasar Beringharjo, DI Yogyakarta', 'Pasar Muntilan, Magelang'],
            ],
        ];

        $today = Carbon::today();
        $daysCount = 35;

        foreach ($scenarios as $commodityName => $config) {
            $commodity = $commodities->get($commodityName);
            if (!$commodity) {
                continue;
            }

            foreach ($config['markets'] as $marketLocation) {
                for ($i = $daysCount; $i >= 0; $i--) {
                    $date = (clone $today)->subDays($i);

                    // Daily calculated price with variance and trend
                    $offsetDays = $daysCount - $i;
                    $trendAddition = $offsetDays * $config['trend_factor'];
                    $sinFluctuation = sin($offsetDays * 0.5) * $config['variance'];
                    $computedPrice = round($config['base_price'] + $trendAddition + $sinFluctuation, -2); // round to nearest 100

                    CommodityPrice::updateOrCreate(
                        [
                            'commodity_id' => $commodity->id,
                            'location' => $marketLocation,
                            'recorded_date' => $date->format('Y-m-d'),
                        ],
                        [
                            'price' => max(1000, $computedPrice),
                            'unit' => $config['unit'],
                            'source' => 'Survei Pasar SINTESA',
                            'notes' => "Pemantauan berkala harga {$commodity->name} di {$marketLocation}.",
                        ]
                    );
                }
            }
        }
    }
}
