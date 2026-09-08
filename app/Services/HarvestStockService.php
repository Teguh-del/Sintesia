<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Harvest;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HarvestStockService
{
    /**
     * Record a new harvest and automatically create a corresponding real stock batch.
     * Alur wajib: Hasil Panen -> Stok Riil
     */
    public function recordHarvest(User $farmer, array $data): Harvest
    {
        return DB::transaction(function () use ($farmer, $data) {
            $commodity = Commodity::findOrFail($data['commodity_id']);

            // 1. Create Harvest record
            $harvest = Harvest::create([
                'user_id' => $farmer->id,
                'commodity_id' => $commodity->id,
                'quantity' => $data['quantity'],
                'unit' => $data['unit'] ?? $commodity->unit,
                'harvest_date' => $data['harvest_date'],
                'quality' => $data['quality'],
                'location' => $data['location'],
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Generate unique batch code
            $comCode = strtoupper(substr($commodity->slug, 0, 3));
            $dateCode = date('Ymd', strtotime($data['harvest_date']));
            $randomCode = strtoupper(Str::random(4));
            $batchCode = "STK-{$comCode}-{$dateCode}-{$randomCode}";

            // Ensure uniqueness
            while (Stock::where('batch_code', $batchCode)->exists()) {
                $batchCode = "STK-{$comCode}-{$dateCode}-" . strtoupper(Str::random(4));
            }

            // 3. Determine initial stock status
            $qty = (float) $data['quantity'];
            $status = 'Tersedia';
            if ($qty <= 0) {
                $status = 'Habis';
            } elseif ($qty <= 10) {
                $status = 'Stok Terbatas';
            }

            // 4. Create Stock batch
            Stock::create([
                'user_id' => $farmer->id,
                'commodity_id' => $commodity->id,
                'harvest_id' => $harvest->id,
                'batch_code' => $batchCode,
                'initial_quantity' => $qty,
                'available_quantity' => $qty,
                'ordered_quantity' => 0,
                'sold_quantity' => 0,
                'unit' => $data['unit'] ?? $commodity->unit,
                'quality' => $data['quality'],
                'status' => $status,
            ]);

            return $harvest->load('stock');
        });
    }

    /**
     * Update an existing harvest and adjust the associated stock.
     */
    public function updateHarvest(Harvest $harvest, array $data): Harvest
    {
        return DB::transaction(function () use ($harvest, $data) {
            $oldQty = (float) $harvest->quantity;
            $newQty = (float) $data['quantity'];
            $delta = $newQty - $oldQty;

            $stock = $harvest->stock;

            if ($stock && $delta !== 0.0) {
                $newAvailable = (float) $stock->available_quantity + $delta;
                if ($newAvailable < 0) {
                    throw new \InvalidArgumentException(
                        "Perubahan kuantitas panen tidak dapat diterapkan karena stok yang tersedia saat ini telah dialokasikan untuk pesanan atau terjual."
                    );
                }

                $stock->initial_quantity = $newQty;
                $stock->available_quantity = $newAvailable;
                $stock->quality = $data['quality'];
                $stock->syncStatus();
            }

            $harvest->update([
                'commodity_id' => $data['commodity_id'],
                'quantity' => $newQty,
                'unit' => $data['unit'] ?? $harvest->unit,
                'harvest_date' => $data['harvest_date'],
                'quality' => $data['quality'],
                'location' => $data['location'],
                'notes' => $data['notes'] ?? null,
            ]);

            return $harvest->fresh('stock');
        });
    }

    /**
     * Delete a harvest record and clean up associated stock if safe.
     */
    public function deleteHarvest(Harvest $harvest): void
    {
        DB::transaction(function () use ($harvest) {
            $stock = $harvest->stock;

            if ($stock) {
                if ($stock->ordered_quantity > 0 || $stock->sold_quantity > 0) {
                    throw new \InvalidArgumentException(
                        "Hasil panen ini tidak dapat dihapus karena stoknya sedang dipesan atau telah terjual sebagian."
                    );
                }

                // Check if linked to products
                if ($stock->products()->exists()) {
                    // Dissociate stock from product
                    $stock->products()->update(['stock_id' => null]);
                }

                $stock->delete();
            }

            $harvest->delete();
        });
    }

    /**
     * Adjust physical stock quantity safely (e.g. inventory audit).
     */
    public function adjustStockQuantity(Stock $stock, float $newAvailable, string $reason = ''): Stock
    {
        if ($newAvailable < 0) {
            throw new \InvalidArgumentException("Kuantitas stok tidak boleh bernilai negatif.");
        }

        return DB::transaction(function () use ($stock, $newAvailable) {
            $stock->available_quantity = $newAvailable;
            $stock->syncStatus();
            return $stock->fresh();
        });
    }
}
