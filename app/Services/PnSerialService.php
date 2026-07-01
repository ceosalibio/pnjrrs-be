<?php

namespace App\Services;

use App\Repositories\PnSerialRepository;
use Illuminate\Database\Eloquent\Collection;

class PnSerialService
{
    public function __construct(private PnSerialRepository $repository)
    {
    }

    public function getAllSerials(): Collection
    {
        return $this->repository->all();
    }

    public function getSerialById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createSerial(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateSerial(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSerial(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedSerials(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getSerialsByReportMonth(string $reportMonth)
    {
        return $this->repository->findByReportMonth($reportMonth);
    }

    public function getSerialsByPersonnelReportId(int $personnelReportId)
    {
        return $this->repository->findByPersonnelReportId($personnelReportId);
    }

    public function isSerialExistingInReportMonthButDifferentPersonnelReport(string $reportMonth, int $personnelReportId): bool
    {
        $serials = $this->repository->findByReportMonth($reportMonth);
        
        foreach ($serials as $serial) {
            if ($serial->personnel_report_id !== $personnelReportId) {
                return true;
            }
        }
        
        return false;
    }

    public function isSerialNumberExistsInMonth(string $serialNumber, string $reportMonth, ?int $excludePersonnelReportId = null): bool
    {
        $query = \App\Models\PnSerial::where('serial', $serialNumber)
            ->where('report_month', $reportMonth);
        
        if ($excludePersonnelReportId !== null) {
            $query->where('personnel_report_id', '!=', $excludePersonnelReportId);
        }
        
        return $query->exists();
    }
}
