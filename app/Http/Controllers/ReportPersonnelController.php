<?php

namespace App\Http\Controllers;

use App\Services\ReportPersonnelService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class ReportPersonnelController extends Controller
{
    use APIResponse;

    public function __construct(private ReportPersonnelService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
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
                $reports = $this->service->getReportsByFilters($filters, $perPage);
            } else {
                $reports = $this->service->getPaginatedReports($perPage);
            }

            return $this->successResponse($reports, 'Reports retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'nullable|integer',
                'unit_id' => 'required|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'report_month' => 'required|string',
            ]);

            $report = $this->service->createReportWithFallback($validated);
            return $this->successResponse($report, 'Report created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $report = $this->service->getReportById($id);
            if (!$report) {
                return $this->errorResponse('Report not found', 404);
            }
            return $this->successResponse($report, 'Report retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'sometimes|integer|exists:pn_categories,id',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'sub_unit_id' => 'sometimes|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'items' => 'nullable|array',
                // 'items.*.description' => 'nullable|string',
                // 'items.*.grade' => 'nullable|string',
                // 'items.*.afpos' => 'nullable|string',
                // 'items.*.required' => 'nullable|string',
                // 'items.*.office' => 'nullable|boolean',
                // 'items.*.officeName' => 'nullable|string',
                'grade_points' => 'nullable|integer',
                'afpos_points' => 'nullable|integer',
                'required' => 'nullable|integer',
                'actual' => 'nullable|integer',
                'report_month' => 'sometimes|string',
                'status' => 'nullable|integer|in:0,1,2,3',
            ]);

            $report = $this->service->updateReport($id, $validated);
            if (!$report) {
                return $this->errorResponse('Report not found', 404);
            }
            return $this->successResponse($report, 'Report updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get organization items grouped by office/sub-office
     * Query params: unit_id, sub_unit_id, office_id, sub_office_id
     */
    public function getItemsGroupedByOffice(Request $request)
    {
        try {
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

            if (empty($filters)) {
                return $this->errorResponse('At least one filter parameter is required (unit_id, sub_unit_id, office_id, or sub_office_id)', 400);
            }

            $groupedData = $this->service->getOrganizationGroupedItems($filters);

            if (!$groupedData) {
                return $this->errorResponse('No organization data found for the given filters', 404);
            }

            return $this->successResponse($groupedData['grouped_items'], 'Items grouped by office retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteReport($id);
            if (!$deleted) {
                return $this->errorResponse('Report not found', 404);
            }
            return $this->successResponse(null, 'Report deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
