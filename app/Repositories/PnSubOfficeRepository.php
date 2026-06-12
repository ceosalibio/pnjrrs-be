<?php

namespace App\Repositories;

use App\Models\PnSubOffice;
use Illuminate\Database\Eloquent\Collection;

class PnSubOfficeRepository
{
    public function all(): Collection
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->get();
    }

    public function findById(int $id): ?PnSubOffice
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->find($id);
    }

    public function findByOffice(int $officeId): Collection
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->where('office_id', $officeId)->get();
    }

    public function findBySubUnit(int $subUnitId): Collection
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->where('sub_unit_id', $subUnitId)->get();
    }

    public function findByUnit(int $unitId): Collection
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->where('unit_id', $unitId)->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->where('category_id', $categoryId)->get();
    }

    public function create(array $data): PnSubOffice
    {
        return PnSubOffice::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $subOffice = $this->findById($id);
        if (!$subOffice) {
            return false;
        }
        return $subOffice->update($data);
    }

    public function delete(int $id): bool
    {
        $subOffice = $this->findById($id);
        if (!$subOffice) {
            return false;
        }
        return $subOffice->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->paginate($perPage);
    }

    public function filterByOffice(int $officeId, int $perPage = 15)
    {
        return PnSubOffice::with(['category', 'unit', 'subUnit', 'office'])->where('office_id', $officeId)->paginate($perPage);
    }
}
