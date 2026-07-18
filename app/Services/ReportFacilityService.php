<?php

namespace App\Services;

use App\Builders\FacilityInspectionReportBuilder;
use App\Repositories\ReportFacilityRepository;
use App\Services\SettingOrganizationService;
use App\Services\ResultCalculationService;
use App\Services\ApproverService;
use App\Repositories\PnUnitRepository;

class ReportFacilityService
{
    private $repository;
    private $organizationService;
    private $resultCalculationService;
    private $approverService;
    private $unitRepository;

    public function __construct(
        ReportFacilityRepository $repository,
        SettingOrganizationService $organizationService,
        ResultCalculationService $resultCalculationService,
        ApproverService $approverService,
        PnUnitRepository $unitRepository
    ) {
        $this->repository = $repository;
        $this->organizationService = $organizationService;
        $this->resultCalculationService = $resultCalculationService;
        $this->approverService = $approverService;
        $this->unitRepository = $unitRepository;
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
        $approver = $this->approverService->getApproverForReport($result, 'facility');
        
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
                $approver = $this->approverService->getApproverForReport($currentReport, 'facility');
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
            
            \Log::info('Facility Report Fallback Debug', [
                'requestMonth' => $requestMonth,
                'previousMonth' => $previousMonth,
            ]);
            
            if ($previousMonth) {
                $previousMonthFilters = array_merge($unitFilters, ['report_month' => $previousMonth]);
                
                \Log::info('Searching for previous facility month', [
                    'previousMonth' => $previousMonth,
                    'filters' => $previousMonthFilters,
                ]);
                
                $previousMonthReports = $this->getReportsByFilters($previousMonthFilters, 1);
                
                \Log::info('Previous facility month search result', [
                    'found' => $previousMonthReports ? $previousMonthReports->count() : 0,
                ]);
                
                if ($previousMonthReports && $previousMonthReports->count() > 0) {
                    $previousReport = $previousMonthReports->first();
                    
                    \Log::info('Copying facility data from previous month', [
                        'from_month' => $previousReport->report_month,
                        'to_month' => $requestMonth,
                    ]);
                    
                    // Copy previous month's report data to create new month
                    $data['category_id'] = $previousReport->category_id;
                    $data['items'] = $previousReport->items;
                    $data['result'] = $previousReport->result;
                    $data['created_by'] = auth()->user()?->id;
                    
                    $result = $this->repository->create($data);
                    
                    $approver = $this->approverService->getApproverForReport($result, 'facility');
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
        if (isset($data['unit_id'])) {
            $category = $this->unitRepository->getCategoryByUnitId($data['unit_id']);
            if ($category) {
                $data['category_id'] = $category->id;
            }
        }
        $data['created_by'] = auth()->user()?->id;
        $data["items"] = FacilityInspectionReportBuilder::buildFacilitiesItems($data['unit_id']);
        $result = $this->repository->create($data);
        $approver = $this->approverService->getApproverForReport($result, 'facility');
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
            $response = $this->resultCalculationService->calculateFacilities($data['items'], $find->unit_id);
            $data["result"] = $response;
            $data["rating"] = $response["overall_readiness"] ?? 0;
            $data["redcon"] = $response["redcon"] ?? '';
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
        $totalActual = 0;

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

                        // Sum actual field if it exists
                        if (isset($item['actual'])) {
                            $totalActual += (int)$item['actual'];
                        }
                    }
                }
            }
        }

        return [
            'total_required' => $totalRequired,
            'total_actual' => $totalActual,
            'shortage' => $totalRequired - $totalActual,
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
                        'total_actual' => 0,
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

                        // Sum actual field if it exists
                        if (isset($item['actual'])) {
                            $divisionsData[$divisionId]['total_actual'] += (int)$item['actual'];
                        }
                    }
                }
            }
        }

        // Calculate shortage and percentage/rating for each division
        foreach ($divisionsData as &$division) {
            $division['shortage'] = $division['total_required'] - $division['total_actual'];
            
            // Calculate completion percentage (actual / required * 100)
            if ($division['total_required'] > 0) {
                $division['completion_percentage'] = round(($division['total_actual'] / $division['total_required']) * 100, 2);
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
