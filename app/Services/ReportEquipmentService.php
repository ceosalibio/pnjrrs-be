<?php

namespace App\Services;

use App\Repositories\ReportEquipmentRepository;
use App\Repositories\EquipmentItemRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;
use App\Services\PnSerialService;
use App\Services\ApproverService;

class ReportEquipmentService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;
    private $approverService;
    private $equipmentItemRepository;

    public function __construct(
        ReportEquipmentRepository $repository,
        SettingOrganizationService $organizationService,
        ResultCalculationService $resultCalculationService,
        PnSerialService $pnSerialService,
        ApproverService $approverService,
        EquipmentItemRepository $equipmentItemRepository,
    ) {
        $this->repository = $repository;
        $this->organizationService = $organizationService;
        $this->resultCalculationService = $resultCalculationService;
        $this->pnSerialService = $pnSerialService;
        $this->approverService = $approverService;
        $this->equipmentItemRepository = $equipmentItemRepository;
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
        $approver = $this->approverService->getApproverForReport($result, 'equipment');
        
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
                $approver = $this->approverService->getApproverForReport($currentReport, 'equipment');
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
            
            \Log::info('Equipment Report Fallback Debug', [
                'requestMonth' => $requestMonth,
                'previousMonth' => $previousMonth,
            ]);
            
            if ($previousMonth) {
                $previousMonthFilters = array_merge($unitFilters, ['report_month' => $previousMonth]);
                
                \Log::info('Searching for previous equipment month', [
                    'previousMonth' => $previousMonth,
                    'filters' => $previousMonthFilters,
                ]);
                
                $previousMonthReports = $this->getReportsByFilters($previousMonthFilters, 1);
                
                \Log::info('Previous equipment month search result', [
                    'found' => $previousMonthReports ? $previousMonthReports->count() : 0,
                ]);
                
                if ($previousMonthReports && $previousMonthReports->count() > 0) {
                    $previousReport = $previousMonthReports->first();
                    
                    \Log::info('Copying equipment data from previous month', [
                        'from_month' => $previousReport->report_month,
                        'to_month' => $requestMonth,
                    ]);
                    
                    // Copy previous month's report data to create new month
                    $data['category_id'] = $previousReport->category_id;
                    $data['items'] = $previousReport->items;
                    $data['result'] = $previousReport->result;
                    $data['created_by'] = auth()->user()?->id;
                    
                    $result = $this->repository->create($data);
                    
                    $approver = $this->approverService->getApproverForReport($result, 'equipment');
                    $approverCount = is_array($approver) ? count($approver) : $approver->count();
                    
                    return [
                        'report' => $result,
                        'approver' => $approver,
                        'final_approver' => $approverCount - 1,
                    ];
                }
            }
        }

        // STEP 3: If no previous month, create empty report
        $equipmentData = $this->equipmentItemRepository->filterByMultiple($unitFilters, null, true);
        $data["category_id"] = $equipmentData->category_id;
        $data["items"] = $equipmentData->items;
        $data['created_by'] = auth()->user()?->id;
        $result = $this->repository->create($data);
        
        $approver = $this->approverService->getApproverForReport($result, 'equipment');
        $approverCount = is_array($approver) ? count($approver) : $approver->count();
        
        return [
            'report' => $result,
            'approver' => $approver,
            'final_approver' => $approverCount - 1,
        ];
    }

    public function updateReport(int $id, array $data)
    {
        // Calculate summary from items if items are provided
        if (isset($data['items'])) {
            $find = $this->repository->findById($id);
            $sum = $this->calculateItemsSummary($data['items']);
            $data["required"] = $sum["total_required"];
            $data["actual"] = $sum["total_onhand"];
            $data["result"] = $this->resultCalculationService->calculateEquipmentResults($data['items'], $find->category_id);
        }
        $data['updated_by'] = auth()->user()?->id;
        $result = $this->repository->update($id, $data);
        return $result;
    }

    public function deleteReport(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedReports(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getOrganizationGroupedItems(array $filters)
    {
        return $this->repository->getOrganizationGroupedItems($filters);
    }

    private function getPreviousMonth(string $month): ?string
    {
        $monthParts = explode('/', $month);
        if (count($monthParts) !== 2) {
            return null;
        }

        list($monthStr, $yearStr) = $monthParts;
        $monthNum = (int)$monthStr;
        $yearNum = (int)$yearStr;

        if ($monthNum === 1) {
            $previousMonth = 12;
            $previousYear = $yearNum - 1;
        } else {
            $previousMonth = $monthNum - 1;
            $previousYear = $yearNum;
        }

        return str_pad($previousMonth, 2, '0', STR_PAD_LEFT) . '/' . $previousYear;
    }


    public function calculateItemsSummary(array $items): array
    {
        $totalRequired = 0;
        $totalOnhand = 0;

        foreach ($items as $category) {
            if (!isset($category['divisions'])) {
                continue;
            }

            foreach ($category['divisions'] as $division) {
                if (!isset($division['types'])) {
                    continue;
                }

                foreach ($division['types'] as $type) {
                    if (!isset($type['items']) || !is_array($type['items'])) {
                        continue;
                    }

                    foreach ($type['items'] as $item) {
                        // Sum required field if it exists
                        if (isset($item['required'])) {
                            $totalRequired += (int)$item['required'];
                        }

                        // Sum onhand field if it exists
                        if (isset($item['onhand'])) {
                            $totalOnhand += (int)$item['onhand'];
                        }
                    }
                }
            }
        }

        return [
            'total_required' => $totalRequired,
            'total_onhand' => $totalOnhand,
            'shortage' => $totalRequired - $totalOnhand, // Useful metric
        ];
    }

    public function calculateDivisionsSummary(array $items): array
    {
        $divisionsData = [];

        foreach ($items as $category) {
            if (!isset($category['divisions'])) {
                continue;
            }

            foreach ($category['divisions'] as $division) {
                $divisionId = $division['division_id'] ?? null;
                $divisionName = $division['division_name'] ?? 'Unknown';

                // Initialize division if not already present
                if (!isset($divisionsData[$divisionId])) {
                    $divisionsData[$divisionId] = [
                        'division_id' => $divisionId,
                        'division_name' => $divisionName,
                        'total_required' => 0,
                        'total_onhand' => 0,
                    ];
                }

                if (!isset($division['types'])) {
                    continue;
                }

                foreach ($division['types'] as $type) {
                    if (!isset($type['items']) || !is_array($type['items'])) {
                        continue;
                    }

                    foreach ($type['items'] as $item) {
                        // Sum required field if it exists
                        if (isset($item['required'])) {
                            $divisionsData[$divisionId]['total_required'] += (int)$item['required'];
                        }

                        // Sum onhand field if it exists
                        if (isset($item['onhand'])) {
                            $divisionsData[$divisionId]['total_onhand'] += (int)$item['onhand'];
                        }
                    }
                }
            }
        }

        // Calculate shortage and percentage/rating for each division
        foreach ($divisionsData as &$division) {
            $division['shortage'] = $division['total_required'] - $division['total_onhand'];
            
            // Calculate completion percentage (onhand / required * 100)
            if ($division['total_required'] > 0) {
                $division['completion_percentage'] = round(($division['total_onhand'] / $division['total_required']) * 100, 2);
            } else {
                $division['completion_percentage'] = 0;
            }
            
            // Assign rating based on percentage
            $division['rating'] = $this->getRatingByPercentage($division['completion_percentage']);
        }

        return array_values($divisionsData);
    }

    private function getRatingByPercentage(float $percentage): string
    {
        if ($percentage >= 95) {
            return 'Excellent';
        } elseif ($percentage >= 75) {
            return 'Good';
        } elseif ($percentage >= 50) {
            return 'Fair';
        } elseif ($percentage >= 25) {
            return 'Poor';
        } else {
            return 'Critical';
        }
    }
}
