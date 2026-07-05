<?php

namespace App\Repositories;

use App\Models\ReportTraining;
use Illuminate\Database\Eloquent\Collection;

class ReportTrainingRepository
{
    public function all(): Collection
    {
        return ReportTraining::all();
    }

    public function findById(int $id): ?ReportTraining
    {
        return ReportTraining::find($id);
    }

    public function create(array $data): ReportTraining
    {
        return ReportTraining::create($data);
    }

    public function update(int $id, array $data): ?ReportTraining
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
        return ReportTraining::paginate($perPage);
    }

    public function filterByUnitId(int $unitId, int $perPage = 15)
    {
        return ReportTraining::where('unit_id', $unitId)->paginate($perPage);
    }

    public function filterBySubUnitId(int $subUnitId, int $perPage = 15)
    {
        return ReportTraining::where('sub_unit_id', $subUnitId)->paginate($perPage);
    }

    public function filterByOfficeId(int $officeId, int $perPage = 15)
    {
        return ReportTraining::where('office_id', $officeId)->paginate($perPage);
    }

    public function filterBySubOfficeId(int $subOfficeId, int $perPage = 15)
    {
        return ReportTraining::where('sub_office_id', $subOfficeId)->paginate($perPage);
    }

    public function filterByMultiple(array $filters, int $perPage = null)
    {
        $query = ReportTraining::query();

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

        // Return all results if perPage is null or 0, otherwise paginate
        if (!$perPage) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }
}
