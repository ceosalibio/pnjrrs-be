<?php

namespace App\Http\Controllers;

use App\Services\PnSubOfficeService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnSubOfficeController extends Controller
{
    use APIResponse;

    public function __construct(private PnSubOfficeService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            if ($request->has('office_id')) {
                $subOffices = $this->service->getPaginatedSubOfficesByOffice($request->office_id, $perPage);
            } else {
                $subOffices = $this->service->getPaginatedSubOffices($perPage);
            }
            
            return $this->successResponse($subOffices, 'Sub-offices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $subOffice = $this->service->getSubOfficeById($id);
            if (!$subOffice) {
                return $this->errorResponse('Sub-office not found', 404);
            }
            return $this->successResponse($subOffice, 'Sub-office retrieved successfully');
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
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'name' => 'required|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',

            ]);

            $subOffice = $this->service->createSubOffice($validated);
            return $this->successResponse($subOffice, 'Sub-office created successfully', 201);
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
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'name' => 'sometimes|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',

            ]);

            $updated = $this->service->updateSubOffice($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Sub-office not found', 404);
            }
            return $this->successResponse(null, 'Sub-office updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteSubOffice($id);
            if (!$deleted) {
                return $this->errorResponse('Sub-office not found', 404);
            }
            return $this->successResponse(null, 'Sub-office deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
