<?php

namespace App\Http\Controllers;

use App\Services\PnSubUnitService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnSubUnitController extends Controller
{
    use APIResponse;

    public function __construct(private PnSubUnitService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            if ($request->has('unit_id')) {
                $subUnits = $this->service->getPaginatedSubUnitsByUnit($request->unit_id, $perPage);
            } else {
                $subUnits = $this->service->getPaginatedSubUnits($perPage);
            }
            
            return $this->successResponse($subUnits, 'Sub-units retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $subUnit = $this->service->getSubUnitById($id);
            if (!$subUnit) {
                return $this->errorResponse('Sub-unit not found', 404);
            }
            return $this->successResponse($subUnit, 'Sub-unit retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:pn_categories,id',
                'unit_id' => 'required|integer|exists:pn_units,id',
                'name' => 'required|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
            ]);

            $subUnit = $this->service->createSubUnit($validated);
            return $this->successResponse($subUnit, 'Sub-unit created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'sometimes|integer|exists:pn_categories,id',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'name' => 'sometimes|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
            ]);

            $updated = $this->service->updateSubUnit($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Sub-unit not found', 404);
            }
            return $this->successResponse(null, 'Sub-unit updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteSubUnit($id);
            if (!$deleted) {
                return $this->errorResponse('Sub-unit not found', 404);
            }
            return $this->successResponse(null, 'Sub-unit deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
