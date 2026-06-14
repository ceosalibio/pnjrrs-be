<?php

namespace App\Http\Controllers;

use App\Services\PnOfficeService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnOfficeController extends Controller
{
    use APIResponse;

    public function __construct(private PnOfficeService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            if ($request->has('sub_unit_id')) {
                if ($request->has('per_page')) {
                    $perPage = $request->input('per_page');
                    $offices = $this->service->getPaginatedOfficesBySubUnit($request->sub_unit_id, $perPage);
                } else {
                    $offices = $this->service->getOfficesBySubUnit($request->sub_unit_id);
                }
            } else {
                if ($request->has('per_page')) {
                    $perPage = $request->input('per_page');
                    $offices = $this->service->getPaginatedOffices($perPage);
                } else {
                    $offices = $this->service->getAllOffices();
                }
            }
            
            return $this->successResponse($offices, 'Offices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $office = $this->service->getOfficeById($id);
            if (!$office) {
                return $this->errorResponse('Office not found', 404);
            }
            return $this->successResponse($office, 'Office retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'nullable|integer|exists:pn_categories,id',
                'unit_id' => 'nullable|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'name' => 'required|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',

            ]);

            $office = $this->service->createOffice($validated);
            return $this->successResponse($office, 'Office created successfully', 201);
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
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'name' => 'sometimes|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',

            ]);

            $updated = $this->service->updateOffice($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Office not found', 404);
            }
            return $this->successResponse(null, 'Office updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteOffice($id);
            if (!$deleted) {
                return $this->errorResponse('Office not found', 404);
            }
            return $this->successResponse(null, 'Office deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
