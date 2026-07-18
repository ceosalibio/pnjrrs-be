<?php

namespace App\Services;

use App\Repositories\ReportPersonnelRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;
use App\Services\PnSerialService;
use App\Services\ApproverService;

class ReportPersonnelService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;
    private $approverService;

    public function __construct(
        ReportPersonnelRepository $repository,
        SettingOrganizationService $organizationService,
        ResultCalculationService $resultCalculationService,
        PnSerialService $pnSerialService,
        ApproverService $approverService
    ) {
        $this->repository = $repository;
        $this->organizationService = $organizationService;
        $this->resultCalculationService = $resultCalculationService;
        $this->pnSerialService = $pnSerialService;
        $this->approverService = $approverService;
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
        
        \Log::info('ReportPersonnelService::createReport - Creating report', [
            'unit_id' => $data['unit_id'] ?? null,
            'report_month' => $data['report_month'] ?? null,
        ]);
        
        $result = $this->repository->create($data);
        
        \Log::info('ReportPersonnelService::createReport - Report created', [
            'report_id' => $result->id ?? null,
        ]);
        
        // Create serial record after report creation
        $this->createSerialForReport($result);
        
        // Fetch approver for the report
        $approver = $this->approverService->getApproverForReport($result,'personnel');
        
        \Log::info('ReportPersonnelService::createReport - Approver fetched', [
            'approver_count' => is_array($approver) ? count($approver) : $approver->count(),
        ]);
        
        // Count approvers (handle both array and Collection)
        $approverCount = is_array($approver) ? count($approver) : $approver->count();
        
        return [
            'report' => $result,
            'approver' => $approver,
            'final_approver' => $approverCount - 1,
        ];
    }

    public function createReportWithFallback(array $data)
    {
        // Build unit filters (without month) for searching organizational units
        $unitFilters = [
            'unit_id' => $data['unit_id'],
        ];
        if (isset($data['sub_unit_id'])) {
            $unitFilters['sub_unit_id'] = $data['sub_unit_id'];
        }
        if (isset($data['office_id'])) {
            $unitFilters['office_id'] = $data['office_id'];
        }
        if (isset($data['sub_office_id'])) {
            $unitFilters['sub_office_id'] = $data['sub_office_id'];
        }

        // Keep requested month in original MM/YYYY format (don't normalize)
        $requestMonth = $data['report_month'] ?? null;

        // STEP 1: Check if a report for the CURRENT/REQUESTED month already exists
        if ($requestMonth) {
            $currentMonthFilters = array_merge($unitFilters, ['report_month' => $requestMonth]);
            $currentMonthReports = $this->getReportsByFilters($currentMonthFilters, 1);
            
            if ($currentMonthReports && $currentMonthReports->count() > 0) {
                $currentReport = $currentMonthReports->first();
                $approver = $this->approverService->getApproverForReport($currentReport,'personnel');
                $approverCount = is_array($approver) ? count($approver) : $approver->count();
                return [
                    'report' => $currentReport,
                    'approver' => $approver,
                    'final_approver' => $approverCount - 1,
                ];
            }
        }

        // STEP 2: Search for previous month report to copy data from
        if ($requestMonth) {
            $previousMonth = $this->getPreviousMonth($requestMonth);
            
            \Log::info('Report Fallback Debug', [
                'requestMonth' => $requestMonth,
                'previousMonth' => $previousMonth,
            ]);
            
            if ($previousMonth) {
                $previousMonthFilters = array_merge($unitFilters, ['report_month' => $previousMonth]);
                
                \Log::info('Searching for previous month', [
                    'previousMonth' => $previousMonth,
                    'filters' => $previousMonthFilters,
                ]);
                
                $previousMonthReports = $this->getReportsByFilters($previousMonthFilters, 1);
                
                \Log::info('Previous month search result', [
                    'found' => $previousMonthReports ? $previousMonthReports->count() : 0,
                ]);
                
                if ($previousMonthReports && $previousMonthReports->count() > 0) {
                    $previousReport = $previousMonthReports->first();
                    
                    \Log::info('Copying data from previous month', [
                        'from_month' => $previousReport->report_month,
                        'to_month' => $requestMonth,
                    ]);
                    
                    // Copy previous month's report data to create new month
                    $data['category_id'] = $previousReport->category_id;
                    $data['items'] = $previousReport->items;
                    $data['result'] = $previousReport->result;
                    $data['grade_points'] = $previousReport->grade_points;
                    $data['afpos_points'] = $previousReport->afpos_points;
                    $data['required'] = $previousReport->required;
                    $data['actual'] = $previousReport->actual;
                    return $this->createReport($data);
                }
            }
        }

        // STEP 3: No existing reports found, use organization data if available
        $organizationData = $this->organizationService->getOrganizationsByFilters($unitFilters, 1);

        if ($organizationData && $organizationData->count() > 0) {
            $organization = $organizationData->first();
            $data['items'] = $organization->items;
            $data['category_id'] = $organization->category_id;
            
            // Calculate aggregate values from items if present
            if ($organization->items) {
                $items = is_array($organization->items) ? $organization->items : json_decode($organization->items, true);
                
                // Sum the 'required' field if it exists and is numeric
                $data['required'] = 0;
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (isset($item['required']) && isset($item['grade']) && is_numeric($item['required'])) {
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
        
      
        
        $result = $this->resultCalculationService->calculatePersonnelResults(
            $gradePoints,
            $afposPoints,
            $actual,
            $required
        );
        
        $data['result'] = $result;
        $data['rating'] = $result['readiness'] ?? 0;
        $data['redcon'] = $result['redcon'] ?? '';
        $this->repository->update($id, $data);
        
        // Refresh and create/update serial record after report update
        $updatedReport = $this->repository->findById($id);
        $this->createSerialForReport($updatedReport);
        // create data in approver table
        $this->approverService->createApprover($updatedReport, 'personnel');
          
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

    /**
     * Normalize month format to YYYY-MM for consistent comparison.
     * Accepts MM/YYYY or YYYY-MM formats.
     * 
     * @param string $month The month in MM/YYYY or YYYY-MM format
     * @return string The normalized month in YYYY-MM format
     */
    private function normalizeMonthFormat($month)
    {
        if (empty($month)) {
            return null;
        }

        // If already in YYYY-MM format, return as is
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $month;
        }

        // Convert MM/YYYY to YYYY-MM
        if (preg_match('/^(\d{2})\/(\d{4})$/', $month, $matches)) {
            return $matches[2] . '-' . $matches[1];
        }

        return null;
    }

    /**
     * Get the previous month in MM/YYYY format.
     * For example: "06/2026" returns "05/2026", "01/2026" returns "12/2025"
     * 
     * @param string $month The current month in MM/YYYY format
     * @return string|null The previous month in MM/YYYY format, or null if invalid
     */
    private function getPreviousMonth($month)
    {
        if (empty($month)) {
            return null;
        }

        // Parse MM/YYYY format
        if (!preg_match('/^(\d{2})\/(\d{4})$/', $month, $matches)) {
            return null;
        }

        $currentMonth = (int) $matches[1];
        $currentYear = (int) $matches[2];

        // Decrement month
        $previousMonth = $currentMonth - 1;
        $previousYear = $currentYear;

        // Handle year boundary (January -> December of previous year)
        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear -= 1;
        }

        // Return in MM/YYYY format with zero-padding
        return str_pad($previousMonth, 2, '0', STR_PAD_LEFT) . '/' . $previousYear;
    }
}

