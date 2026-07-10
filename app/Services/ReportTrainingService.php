<?php

namespace App\Services;

use App\Repositories\ReportTrainingRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;
use App\Services\PnSerialService;
use App\Services\ApproverService;


class ReportTrainingService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;
    private $approverService;

    public function __construct(
        ReportTrainingRepository $repository,
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

    public function getReportsByFilters(array $filters, int $perPage = null)
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
        $result = $this->repository->create($data);
        
      
        // Fetch approver for the report
        $approver = $this->approverService->getApproverForReport($result, 'training');
        
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
                $approver = $this->approverService->getApproverForReport($currentReport, 'training');
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
            
            \Log::info('Training Report Fallback Debug', [
                'requestMonth' => $requestMonth,
                'previousMonth' => $previousMonth,
            ]);
            
            if ($previousMonth) {
                $previousMonthFilters = array_merge($unitFilters, ['report_month' => $previousMonth]);
                
                \Log::info('Searching for previous training month', [
                    'previousMonth' => $previousMonth,
                    'filters' => $previousMonthFilters,
                ]);
                
                $previousMonthReports = $this->getReportsByFilters($previousMonthFilters, 1);
                
                \Log::info('Previous training month search result', [
                    'found' => $previousMonthReports ? $previousMonthReports->count() : 0,
                ]);
                
                if ($previousMonthReports && $previousMonthReports->count() > 0) {
                    $previousReport = $previousMonthReports->first();
                    
                    \Log::info('Copying training data from previous month', [
                        'from_month' => $previousReport->report_month,
                        'to_month' => $requestMonth,
                    ]);
                    
                    // Copy previous month's report data to create new month
                    $data['category_id'] = $previousReport->category_id;
                    $data['items'] = $previousReport->items;
                    $data['result'] = $previousReport->result;
                    
                    // Calculate accumulative required based on quarter from requested month
                    $month = null;
                    $monthParts = explode('/', $requestMonth);
                    if (count($monthParts) === 2) {
                        list($monthStr, $yearStr) = $monthParts;
                        $month = (int)$monthStr;
                    }
                    
                    if ($month && $previousReport->items) {
                        $quarter = $this->getQuarterFromMonth($month);
                        $requiredSum = $this->sumRequiredByQuarter($previousReport->items, $quarter);
                        $data['required'] = $requiredSum;
                    } else {
                        $data['required'] = $previousReport->required;
                    }
                    
                    $data['actual'] = $previousReport->actual;
                    return $this->createReport($data);
                }
            }
        }

        // STEP 3: No existing reports found, use TrainingItem data if available
        // Extract year and month from report_month (MM/YYYY format)
        $year = null;
        $month = null;
        if ($requestMonth) {
            $monthParts = explode('/', $requestMonth);
            if (count($monthParts) === 2) {
                list($month, $year) = $monthParts;
                $month = (int)$month;
                $year = (int)$year;
            }
        }

        // Try to get training items for the unit and year
        $trainingItems = null;
        if ($year && isset($data['unit_id'])) {
            $trainingItems = \App\Models\TrainingItem::where('unit_id', $data['unit_id'])
                ->where('year', $year)
                ->first();
        }

        if ($trainingItems) {
            $data['category_id'] = $trainingItems->category_id;
            $data['items'] = $trainingItems->items;
            
            // Calculate required based on quarter if items exist
            if ($trainingItems->items && $month) {
                $quarter = $this->getQuarterFromMonth($month);
                $requiredSum = $this->sumRequiredByQuarter($trainingItems->items, $quarter);
                $data['required'] = $requiredSum;
            } else {
                $data['required'] = $trainingItems->required ?? 0;
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

        // Extract month from report_month to calculate quarter for summing actual
        $month = null;
        $reportMonth = $data['report_month'] ?? $report->report_month;
        
        if ($reportMonth) {
            $monthParts = explode('/', $reportMonth);
            if (count($monthParts) === 2) {
                list($monthStr, $yearStr) = $monthParts;
                $month = (int)$monthStr;
            }
        }

        // Use provided items or fall back to existing report items
        $items = isset($data['items']) ? $data['items'] : $report->items;

        // Auto-mark items as complete only if new items are being provided
        if (!empty($data["status"])) {
            foreach ($items as $key => $value) {
                // Auto-mark item as complete if report is approved and item has actual value but no status or status is false
                if (!empty($items[$key]["actual"]) && (!isset($items[$key]["status"]) || $items[$key]["status"] === false)) {
                    \Log::info('Auto-marking training item as complete', [
                        'report_id' => $id,
                        'item_key' => $key,
                        'report_status' => $data["status"],
                        'item_actual' => $items[$key]["actual"],
                        'item_status_before' => $items[$key]["status"] ?? 'not set',
                    ]);
                    $items[$key]["status"] = true;
                    $items[$key]["status_date"] = now();
                }
            }
            // return;

            // Kung ginagamit pa ang $data['items'] sa ibang parte ng code pagkatapos nito,
            // i-sync natin para consistent
            $data['items'] = $items;
        }

        // Calculate sum of 'actual' based on quarter if items exist and month is available
        if ($month && $items) {
            $quarter = $this->getQuarterFromMonth($month);
            $actualSum = $this->sumActualByQuarter($items, $quarter);
            $data['actual'] = $actualSum;
        }

        $data['updated_by'] = auth()->user()?->id;
        $requiredValue = $data['required'] ?? $report->required ?? 0;
        $data['result'] = $this->resultCalculationService->calculateTrainingResults($data['actual'] ?? 0, $requiredValue);
        $this->repository->update($id, $data);
        
        // Refresh and create/update serial record after report update
        
        $updatedReport = $this->repository->findById($id);
        // create data in approver table
        $this->approverService->createApprover($updatedReport, 'training');
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
     * Get organization data grouped by office (for initial form population)
     */
    public function getOrganizationGroupedItems(array $filters)
    {
        return $this->organizationService->getOrganizationGroupedByOffice($filters);
    }

    /**
     * Validate that all serial numbers in items are unique within the same report month.
     * 
     * @param array $items The items array containing serial numbers
     * @param string $reportMonth The report month to check against
     * @param int|null $excludeTrainingReportId The training report ID to exclude (for updates)
     * @throws \Exception If a serial number already exists in the same month
     */
    private function validateSerialNumbersUniqueness(array $items, string $reportMonth, ?int $excludeTrainingReportId = null)
    {
        foreach ($items as $item) {
            // Check if item has a 'serial' field
            if (isset($item['serial']) && !empty($item['serial'])) {
                $serialNumber = $item['serial'];
                
                // Check if this serial number already exists in the same month (excluding current report if updating)
                if ($this->pnSerialService->isSerialNumberExistsInMonth($serialNumber, $reportMonth, $excludeTrainingReportId)) {
                    throw new \Exception('The serial number already exists for the selected month.');
                }
            }
        }
    }

    /**
     * Convert month number (1-12) to quarter (1-4)
     * January-March = Quarter 1
     * April-June = Quarter 2
     * July-September = Quarter 3
     * October-December = Quarter 4
     */
    private function getQuarterFromMonth(int $month): int
    {
        if ($month >= 1 && $month <= 3) {
            return 1;
        } elseif ($month >= 4 && $month <= 6) {
            return 2;
        } elseif ($month >= 7 && $month <= 9) {
            return 3;
        } else {
            return 4;
        }
    }

    /**
     * Sum the 'required' field from items that match a specific quarter and all previous quarters (accumulative)
     * 
     * @param array|string $items The items array or JSON string
     * @param int $quarter The quarter to filter by (1-4)
     * @return int The sum of required values for the matching quarter and all previous quarters
     */
    private function sumRequiredByQuarter($items, int $quarter): int
    {
        // Convert to array if JSON string
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items)) {
            return 0;
        }

        $total = 0;
        foreach ($items as $item) {
            // Check if item has quarter and required fields
            if (isset($item['quarter']) && isset($item['required'])) {
                $itemQuarter = (int)$item['quarter'];
                // Include items from quarter 1 up to and including the specified quarter (accumulative)
                if ($itemQuarter >= 1 && $itemQuarter <= $quarter && is_numeric($item['required'])) {
                    $total += (int)$item['required'];
                }
            }
        }

        return $total;
    }

    /**
     * Sum the 'actual' field from items that match a specific quarter and all previous quarters (accumulative)
     * 
     * @param array|string $items The items array or JSON string
     * @param int $quarter The quarter to filter by (1-4)
     * @return int The sum of actual values for the matching quarter and all previous quarters
     */
    private function sumActualByQuarter($items, int $quarter): int
    {
        // Convert to array if JSON string
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items)) {
            return 0;
        }

        $total = 0;
        foreach ($items as $item) {
            // Check if item has quarter and actual fields
            if (isset($item['quarter']) && isset($item['actual'])) {
                $itemQuarter = (int)$item['quarter'];
                // Include items from quarter 1 up to and including the specified quarter (accumulative)
                if ($itemQuarter >= 1 && $itemQuarter <= $quarter && is_numeric($item['actual'])) {
                    $total += (int)$item['actual'];
                }
            }
        }

        return $total;
    }

   
    /**
     * Get previous month in MM/YYYY format
     */
    private function getPreviousMonth(string $currentMonth): ?string
    {
        try {
            // Parse MM/YYYY format
            $monthParts = explode('/', $currentMonth);
            if (count($monthParts) !== 2) {
                return null;
            }
            list($month, $year) = $monthParts;
            $month = (int)$month;
            $year = (int)$year;

            // Go back one month
            if ($month == 1) {
                $month = 12;
                $year--;
            } else {
                $month--;
            }

            // Format back to MM/YYYY
            return str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . $year;
        } catch (\Exception $e) {
            \Log::warning('Failed to calculate previous month', ['currentMonth' => $currentMonth]);
            return null;
        }
    }
}
