<?php

namespace App\Services;

use App\Repositories\ReportPersonnelRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;

class ReportPersonnelService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;

    public function __construct(
        ReportPersonnelRepository $repository,
        SettingOrganizationService $organizationService,
        ResultCalculationService $resultCalculationService
    ) {
        $this->repository = $repository;
        $this->organizationService = $organizationService;
        $this->resultCalculationService = $resultCalculationService;
    }

    public function getAllReports()
    {
        return $this->repository->all();
    }

    public function getReportsByFilters(array $filters, int $perPage = 15)
    {
        return $this->repository->filterByMultiple($filters, $perPage);
    }

    public function getReportById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createReport(array $data)
    {
        $data['created_by'] = auth()->user()?->id;
        return $this->repository->create($data);
    }

    public function createReportWithFallback(array $data)
    {
        // Build filters WITHOUT report_month to get any existing report for this unit/office
        $filtersWithoutMonth = [
            'unit_id' => $data['unit_id'],
        ];
        if (isset($data['sub_unit_id'])) {
            $filtersWithoutMonth['sub_unit_id'] = $data['sub_unit_id'];
        }
        if (isset($data['office_id'])) {
            $filtersWithoutMonth['office_id'] = $data['office_id'];
        }
        if (isset($data['sub_office_id'])) {
            $filtersWithoutMonth['sub_office_id'] = $data['sub_office_id'];
        }

        // Check if ANY report exists for these filters (regardless of month)
        $existingReport = $this->getReportsByFilters($filtersWithoutMonth, 1);

        if ($existingReport && $existingReport->count() > 0) {
            $lastReport = $existingReport->first();
            
            // If same report_month exists, don't create - return existing
            if (isset($data['report_month']) && $lastReport->report_month === $data['report_month']) {
                return $lastReport;
            }
            
            // Different report_month - copy previous report data and create new
            $data['category_id'] = $lastReport->category_id;
            $data['items'] = $lastReport->items;
            $data['result'] = $lastReport->result;
            $data['grade_points'] = $lastReport->grade_points;
            $data['afpos_points'] = $lastReport->afpos_points;
            $data['required'] = $lastReport->required;
            $data['actual'] = $lastReport->actual;
        } else {
            // No existing report - get organization data and create new
            $organizationData = $this->organizationService->getOrganizationsByFilters($filtersWithoutMonth, 1);

            if ($organizationData && $organizationData->count() > 0) {
                // Extract items from organization (JSON) and aggregate values
                $organization = $organizationData->first();
                $data['items'] = $organization->items;
                $data['category_id'] = $organization->category_id; // Set category_id to null or default value
                // Calculate aggregate values from items if present
                if ($organization->items) {
                    $items = is_array($organization->items) ? $organization->items : json_decode($organization->items, true);
                    
                    // Sum the 'required' field if it exists and is numeric, otherwise count items
                    $data['required'] = 0;
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (isset($item['required']) && isset($item['grade']) && is_numeric($item['required']) ) {
                                $data['required'] += $item['required'];
                            }
                        }
                    }
                    
                    $data['actual'] = 0;
                    $data['grade_points'] = 0;
                    $data['afpos_points'] = 0;
                }
            } else {
                // No existing data found, create empty report
                $data['items'] = null;
                $data['result'] = null;
                $data['grade_points'] = 0;
                $data['afpos_points'] = 0;
                $data['required'] = 0;
                $data['actual'] = 0;
            }
        }

        return $this->createReport($data);
    }

    public function updateReport(int $id, array $data)
    {
        $report = $this->repository->findById($id);
        if (!$report) {
            return null;
        }

        $data['updated_by'] = auth()->user()?->id;
        
        // Calculate result ratings using current or updated values
        $gradePoints = $data['grade_points'] ?? $report->grade_points ?? 0;
        $afposPoints = $data['afpos_points'] ?? $report->afpos_points ?? 0;
        $actual = $data['actual'] ?? $report->actual ?? 0;
        $required = $data['required'] ?? $report->required ?? 0;
        
        $data['result'] = $this->resultCalculationService->calculatePersonnelResults(
            $gradePoints,
            $afposPoints,
            $actual,
            $required
        );
        
        return $this->repository->update($id, $data);
    }

    public function deleteReport(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedReports(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getReportsByUnitId(int $unitId, int $perPage = 15)
    {
        return $this->repository->filterByUnitId($unitId, $perPage);
    }

    public function getReportsBySubUnitId(int $subUnitId, int $perPage = 15)
    {
        return $this->repository->filterBySubUnitId($subUnitId, $perPage);
    }

    public function getReportsByOfficeId(int $officeId, int $perPage = 15)
    {
        return $this->repository->filterByOfficeId($officeId, $perPage);
    }

    public function getReportsBySubOfficeId(int $subOfficeId, int $perPage = 15)
    {
        return $this->repository->filterBySubOfficeId($subOfficeId, $perPage);
    }
}
