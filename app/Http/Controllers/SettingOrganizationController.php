<?php

namespace App\Http\Controllers;

use App\Services\SettingOrganizationService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class SettingOrganizationController extends Controller
{
    use APIResponse;

    public function __construct(private SettingOrganizationService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = [];

            // Collect filter parameters
            if ($request->has('unit_id')) {
                $filters['unit_id'] = $request->input('unit_id');
            }
            if ($request->has('sub_unit_id')) {
                $filters['sub_unit_id'] = $request->input('sub_unit_id');
            }
            if ($request->has('office_id')) {
                $filters['office_id'] = $request->input('office_id');
            }

            if ($request->has('sub_office_id')) {
                $filters['sub_office_id'] = $request->input('sub_office_id');
            }

            // If filters exist, use filtered query; otherwise get paginated list
            if (!empty($filters)) {
                $organizations = $this->service->getOrganizationsByFilters($filters, $perPage);
            } else {
                $organizations = $this->service->getPaginatedOrganizations($perPage);
            }

            return $this->successResponse($organizations, 'Organizations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $organization = $this->service->getOrganizationById($id);
            if (!$organization) {
                return $this->errorResponse('Organization not found', 404);
            }
            return $this->successResponse($organization, 'Organization retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'unit_id' => 'required|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'name' => 'nullable|string|max:255',
                'abbreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'items' => 'nullable|array',
                'items.*.description' => 'nullable|string',
                'items.*.grade' => 'nullable|string',
                'items.*.afpos' => 'nullable|string',
                'items.*.required' => 'nullable|string',
                'items.*.office' => 'nullable|boolean',
                'items.*.officeName' => 'nullable|string',
            ]);

            $organization = $this->service->createOrganization($validated);
            
            // Handle both single organization and array of organizations
            $message = is_array($organization) && count($organization) > 1 
                ? count($organization) . ' organizations created successfully (one per office)' 
                : 'Organization created successfully';
            
            return $this->successResponse($organization, $message, 201);
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
                'name' => 'sometimes|string|max:255',
                'abbreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'sub_unit_id' => 'sometimes|integer|exists:pn_sub_units,id',
                'items' => 'sometimes|array',
                'items.*.description' => 'nullable|string',
                'items.*.grade' => 'nullable|string',
                'items.*.afpos' => 'nullable|string',
                'items.*.required' => 'nullable|string',
                'items.*.office' => 'nullable|boolean',
                'items.*.officeName' => 'nullable|string',
            ]);

            $updated = $this->service->updateOrganization($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Organization not found', 404);
            }
            return $this->successResponse($updated, 'Organization updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteOrganization($id);
            if (!$deleted) {
                return $this->errorResponse('Organization not found', 404);
            }
            return $this->successResponse(null, 'Organization deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
