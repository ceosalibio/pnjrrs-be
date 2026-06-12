<?php

namespace App\Http\Controllers;

use App\Services\PnUnitService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnUnitController extends Controller
{
    use APIResponse;

    public function __construct(private PnUnitService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            if ($request->has('category_id')) {
                $units = $this->service->getPaginatedUnitsByCategory($request->category_id, $perPage);
            } else {
                $units = $this->service->getPaginatedUnits($perPage);
            }
            
            return $this->successResponse($units, 'Units retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $unit = $this->service->getUnitById($id);
            if (!$unit) {
                return $this->errorResponse('Unit not found', 404);
            }
            return $this->successResponse($unit, 'Unit retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:pn_categories,id',
                'name' => 'required|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
            ]);

            $unit = $this->service->createUnit($validated);
            return $this->successResponse($unit, 'Unit created successfully', 201);
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
                'name' => 'sometimes|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
            ]);

            $updated = $this->service->updateUnit($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Unit not found', 404);
            }
            return $this->successResponse(null, 'Unit updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteUnit($id);
            if (!$deleted) {
                return $this->errorResponse('Unit not found', 404);
            }
            return $this->successResponse(null, 'Unit deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
