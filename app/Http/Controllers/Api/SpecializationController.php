<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorCategoryResource;
use App\Models\DoctorCategory;
use App\Traits\ApiResponds;
use Illuminate\Http\JsonResponse;

class SpecializationController extends Controller
{
    use ApiResponds;

    /**
     * GET /api/specializations
     *
     * Return all active categories ordered by name, with doctors_count.
     */
    public function index(): JsonResponse
    {
        $categories = DoctorCategory::active()
            ->withCount(['doctors' => fn ($q) => $q->where('is_active', 1)])
            ->orderBy('name')
            ->get();

        return $this->success(
            DoctorCategoryResource::collection($categories),
            'Specializations retrieved successfully'
        );
    }
}
