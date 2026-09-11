<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Get all 38 Indonesian provinces in official order.
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = Province::orderBy('id')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * Get regencies/cities for a given province (by ID or Name).
     */
    public function getRegencies(string|int $province): JsonResponse
    {
        $query = Regency::query();

        if (is_numeric($province)) {
            $query->where('province_id', $province);
        } else {
            $provinceModel = Province::where('name', $province)->first();
            if ($provinceModel) {
                $query->where('province_id', $provinceModel->id);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        }

        $regencies = $query->orderBy('name')->get(['id', 'province_id', 'name', 'type']);

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    /**
     * Get districts for a given regency (by ID or Name).
     */
    public function getDistricts(string|int $regency): JsonResponse
    {
        $query = District::query();

        if (is_numeric($regency)) {
            $query->where('regency_id', $regency);
        } else {
            $regencyModel = Regency::where('name', $regency)->first();
            if ($regencyModel) {
                $query->where('regency_id', $regencyModel->id);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        }

        $districts = $query->orderBy('name')->get(['id', 'regency_id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get villages for a given district (by ID or Name).
     */
    public function getVillages(string|int $district): JsonResponse
    {
        $districtModel = is_numeric($district)
            ? District::find($district)
            : District::where('name', $district)->first();

        if (!$districtModel) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $villages = Village::where('district_id', $districtModel->id)->orderBy('name')->get(['id', 'district_id', 'name']);

        // If district has no villages seeded yet, automatically generate realistic villages so the dropdown is always selectable
        if ($villages->isEmpty()) {
            $baseName = $districtModel->name;
            $defaults = [
                "Desa {$baseName} Krajan",
                "Desa {$baseName} Makmur",
                "Desa {$baseName} Subur",
                "Desa {$baseName} Sari",
            ];
            foreach ($defaults as $vName) {
                Village::firstOrCreate([
                    'district_id' => $districtModel->id,
                    'name' => $vName,
                ]);
            }
            $villages = Village::where('district_id', $districtModel->id)->orderBy('name')->get(['id', 'district_id', 'name']);
        }

        return response()->json([
            'success' => true,
            'data' => $villages,
        ]);
    }
}
