<?php

namespace App\Repositories;

use App\Models\ReportFacility;
use App\Repositories\PnUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class ReportFacilityRepository
{
    public function all(): Collection
    {
        return ReportFacility::all();
    }

    public function findById(int $id): ?ReportFacility
    {
        return ReportFacility::find($id);
    }

    public function create(array $data): ReportFacility
    {
        return ReportFacility::create($data);
    }

    public function update(int $id, array $data): ?ReportFacility
    {
        $report = $this->findById($id);
        if (!$report) {
            return null;
        }
        $report->update($data);
        return $report;
    }

    public function delete(int $id): bool
    {
        $report = $this->findById($id);
        if (!$report) {
            return false;
        }
        return $report->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return ReportFacility::paginate($perPage);
    }

    public function filterByUnitId(int $unitId, int $perPage = 15)
    {
        return ReportFacility::where('unit_id', $unitId)->paginate($perPage);
    }

    public function filterBySubUnitId(int $subUnitId, int $perPage = 15)
    {
        return ReportFacility::where('sub_unit_id', $subUnitId)->paginate($perPage);
    }

    public function filterByOfficeId(int $officeId, int $perPage = 15)
    {
        return ReportFacility::where('office_id', $officeId)->paginate($perPage);
    }

    public function filterBySubOfficeId(int $subOfficeId, int $perPage = 15)
    {
        return ReportFacility::where('sub_office_id', $subOfficeId)->paginate($perPage);
    }

    public function filterByMultiple(array $filters, int $perPage = null, $first = false)
    {
        $query = ReportFacility::query();

        if (isset($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }
        if (isset($filters['sub_unit_id'])) {
            $query->where('sub_unit_id', $filters['sub_unit_id']);
        }
        if (isset($filters['office_id'])) {
            $query->where('office_id', $filters['office_id']);
        }
        if (isset($filters['sub_office_id'])) {
            $query->where('sub_office_id', $filters['sub_office_id']);
        }
        if (isset($filters['report_month'])) {
            $query->where('report_month', $filters['report_month']);
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }
        if($first){
            return $query->first();
        }

        return $query->get();
    }

    public function getOrganizationGroupedItems(array $filters)
    {
        $query = ReportFacility::query();

        // Apply filters
        if (isset($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }
        if (isset($filters['sub_unit_id'])) {
            $query->where('sub_unit_id', $filters['sub_unit_id']);
        }
        if (isset($filters['office_id'])) {
            $query->where('office_id', $filters['office_id']);
        }
        if (isset($filters['sub_office_id'])) {
            $query->where('sub_office_id', $filters['sub_office_id']);
        }

        $reports = $query->get();

        if ($reports->isEmpty()) {
            return null;
        }

        // Group by office or sub_office
        $grouped = [];
        foreach ($reports as $report) {
            $groupKey = $report->sub_office_id ?? $report->office_id ?? 'unknown';
            
            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'office_id' => $report->office_id,
                    'sub_office_id' => $report->sub_office_id,
                    'items' => []
                ];
            }
            
            $grouped[$groupKey]['items'][] = $report;
        }

        return [
            'grouped_items' => $grouped
        ];
    }
}
