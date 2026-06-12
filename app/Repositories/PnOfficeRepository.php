<?php

namespace App\Repositories;

use App\Models\PnOffice;
use Illuminate\Database\Eloquent\Collection;

class PnOfficeRepository
{
    public function all(): Collection
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->get();
    }

    public function findById(int $id): ?PnOffice
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->find($id);
    }

    public function findBySubUnit(int $subUnitId): Collection
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->where('sub_unit_id', $subUnitId)->get();
    }

    public function findByUnit(int $unitId): Collection
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->where('unit_id', $unitId)->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->where('category_id', $categoryId)->get();
    }

    public function create(array $data): PnOffice
    {
        return PnOffice::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $office = $this->findById($id);
        if (!$office) {
            return false;
        }
        return $office->update($data);
    }

    public function delete(int $id): bool
    {
        $office = $this->findById($id);
        if (!$office) {
            return false;
        }
        return $office->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->paginate($perPage);
    }

    public function filterBySubUnit(int $subUnitId, int $perPage = 15)
    {
        return PnOffice::with(['category', 'unit', 'subUnit'])->where('sub_unit_id', $subUnitId)->paginate($perPage);
    }
}
