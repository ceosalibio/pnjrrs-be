<?php

namespace App\Services;

use App\Repositories\ReportPersonnelRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;
use App\Services\PnSerialService;

class ReportPersonnelService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;

    public function __construct(
        ReportPersonnelRepository $repository,
        SettingOrganizationService $organizationService,
        ResultCalculationService $resultCalculationService,
        PnSerialService $pnSerialService
    ) {
        $this->repository = $repository;
        $this->organizationService = $organizationService;
        $this->resultCalculationService = $resultCalculationService;
        $this->pnSerialService = $pnSerialService;
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
        // Validate serial numbers in items are unique within the report month
        if (isset($data['items']) && is_array($data['items']) && isset($data['report_month'])) {
            $this->validateSerialNumbersUniqueness($data['items'], $data['report_month']);
        }

        $data['created_by'] = auth()->user()?->id;
        $result = $this->repository->create($data);
        
        // Create serial record after report creation
        $this->createSerialForReport($result);
        
        return $result;
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

        // Validate serial numbers in items are unique within the report month
        if (isset($data['items']) && is_array($data['items'])) {
            $reportMonth = $data['report_month'] ?? $report->report_month;
            $this->validateSerialNumbersUniqueness($data['items'], $reportMonth, $id);
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
  
        $this->repository->update($id, $data);
        
        // Refresh and create/update serial record after report update
        $updatedReport = $this->repository->findById($id);
        $this->createSerialForReport($updatedReport);
        
        return $updatedReport;
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

    /**
     * Validate that all serial numbers in items are unique within the same report month.
     * 
     * @param array $items The items array containing serial numbers
     * @param string $reportMonth The report month to check against
     * @param int|null $excludePersonnelReportId The personnel report ID to exclude (for updates)
     * @throws \Exception If a serial number already exists in the same month
     */
    private function validateSerialNumbersUniqueness(array $items, string $reportMonth, ?int $excludePersonnelReportId = null)
    {
        foreach ($items as $item) {
            // Check if item has a 'serial' field
            if (isset($item['serial']) && !empty($item['serial'])) {
                $serialNumber = $item['serial'];
                
                // Check if this serial number already exists in the same month (excluding current report if updating)
                if ($this->pnSerialService->isSerialNumberExistsInMonth($serialNumber, $reportMonth, $excludePersonnelReportId)) {
                    throw new \Exception('The serial number already exists for the selected month.');
                }
            }
        }
    }

    /**
     * Create serial records for a report based on items in the report.
     * 
     * @param object $report The ReportPersonnel instance
     */
    private function createSerialForReport($report)
    {
        if (!$report || !$report->items) {
            return;
        }

        $items = is_array($report->items) ? $report->items : json_decode($report->items, true);
        
        if (!is_array($items)) {
            return;
        }

        // Create a serial record for each item in the report
        foreach ($items as $item) {
            if (isset($item['serial']) && !empty($item['serial'])) {
                $serialData = [
                    'personnel_report_id' => $report->id,
                    'category_id' => $report->category_id,
                    'unit_id' => $report->unit_id,
                    'sub_unit_id' => $report->sub_unit_id ?? null,
                    'office_id' => $report->office_id ?? null,
                    'sub_office_id' => $report->sub_office_id ?? null,
                    'rank_id' => $item['rank_id'] ?? null,
                    'serial' => $item['serial'],
                    'name' => $item['name'] ?? null,
                    'report_month' => $report->report_month,
                ];

                // Check if serial already exists and update or create
                $existingSerial = \App\Models\PnSerial::where('personnel_report_id', $report->id)
                    ->where('serial', $item['serial'])
                    ->first();

                if ($existingSerial) {
                    $existingSerial->update($serialData);
                } else {
                    $this->pnSerialService->createSerial($serialData);
                }
            }
        }
    }
}

