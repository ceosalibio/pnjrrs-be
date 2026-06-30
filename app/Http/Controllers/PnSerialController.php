<?php

namespace App\Http\Controllers;

use App\Services\PnSerialService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnSerialController extends Controller
{
    use APIResponse;

    public function __construct(private PnSerialService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
                $serials = $this->service->getPaginatedSerials($perPage);
            } else {
                $serials = $this->service->getAllSerials();
            }
            return $this->successResponse($serials, 'Serials retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $serial = $this->service->getSerialById($id);
            if (!$serial) {
                return $this->errorResponse('Serial not found', 404);
            }
            return $this->successResponse($serial, 'Serial retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'personnel_report_id' => 'required|integer|exists:report_personnel,id',
                'category_id' => 'required|integer|exists:pn_categories,id',
                'unit_id' => 'required|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'rank_id' => 'nullable|integer|exists:item_ranks,id',
                'serial' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'report_month' => 'required|string|max:7',
            ]);

            $serial = $this->service->createSerial($validated);
            return $this->successResponse($serial, 'Serial created successfully', 201);
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
                'personnel_report_id' => 'sometimes|integer|exists:report_personnel,id',
                'category_id' => 'sometimes|integer|exists:pn_categories,id',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'rank_id' => 'nullable|integer|exists:item_ranks,id',
                'serial' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'report_month' => 'sometimes|string|max:7',
            ]);

            $updated = $this->service->updateSerial($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Serial not found', 404);
            }

            $serial = $this->service->getSerialById($id);
            return $this->successResponse($serial, 'Serial updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteSerial($id);
            if (!$deleted) {
                return $this->errorResponse('Serial not found', 404);
            }
            return $this->successResponse(null, 'Serial deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getByReportMonth(Request $request)
    {
        try {
            $reportMonth = $request->query('report_month');
            if (!$reportMonth) {
                return $this->errorResponse('report_month parameter is required', 400);
            }

            $serials = $this->service->getSerialsByReportMonth($reportMonth);
            return $this->successResponse($serials, 'Serials retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getByPersonnelReport($personnelReportId)
    {
        try {
            $serials = $this->service->getSerialsByPersonnelReportId($personnelReportId);
            return $this->successResponse($serials, 'Serials retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
