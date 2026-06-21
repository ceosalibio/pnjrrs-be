<?php

namespace App\Repositories;

use App\Models\ReportPersonnel;
use Illuminate\Database\Eloquent\Collection;

class ReportPersonnelRepository
{
    public function all(): Collection
    {
        return ReportPersonnel::all();
    }

    public function findById(int $id): ?ReportPersonnel
    {
        return ReportPersonnel::find($id);
    }

    public function create(array $data): ReportPersonnel
    {
        return ReportPersonnel::create($data);
    }

    public function update(int $id, array $data): ?ReportPersonnel
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
        return ReportPersonnel::paginate($perPage);
    }

    public function filterByUnitId(int $unitId, int $perPage = 15)
    {
        return ReportPersonnel::where('unit_id', $unitId)->paginate($perPage);
    }

    public function filterBySubUnitId(int $subUnitId, int $perPage = 15)
    {
        return ReportPersonnel::where('sub_unit_id', $subUnitId)->paginate($perPage);
    }

    public function filterByOfficeId(int $officeId, int $perPage = 15)
    {
        return ReportPersonnel::where('office_id', $officeId)->paginate($perPage);
    }

    public function filterBySubOfficeId(int $subOfficeId, int $perPage = 15)
    {
        return ReportPersonnel::where('sub_office_id', $subOfficeId)->paginate($perPage);
    }

    public function filterByMultiple(array $filters, int $perPage = 15)
    {
        $query = ReportPersonnel::query();

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

        $result = $query->paginate($perPage);

        // Fallback: if office_id filter returns no results, try sub_unit_id
        // if ($result->isEmpty() && isset($filters['office_id'])) {
        //     $fallbackQuery = ReportPersonnel::query();
        //     if (isset($filters['unit_id'])) {
        //         $fallbackQuery->where('unit_id', $filters['unit_id']);
        //     }
        //     if (isset($filters['sub_unit_id'])) {
        //         $fallbackQuery->where('sub_unit_id', $filters['sub_unit_id']);
        //     }
        //     $result = $fallbackQuery->paginate($perPage);
        // }

        // // Fallback: if sub_office_id filter returns no results, try office_id
        // if ($result->isEmpty() && isset($filters['sub_office_id'])) {
        //     $fallbackQuery = ReportPersonnel::query();
        //     if (isset($filters['unit_id'])) {
        //         $fallbackQuery->where('unit_id', $filters['unit_id']);
        //     }
        //     if (isset($filters['office_id'])) {
        //         $fallbackQuery->where('office_id', $filters['office_id']);
        //     }
        //     $result = $fallbackQuery->paginate($perPage);
        // }

        return $result;
    }
}
