<?php

namespace App\Repositories;

use App\Models\PnSubUnit;
use Illuminate\Database\Eloquent\Collection;

class PnSubUnitRepository
{
    public function all(): Collection
    {
        return PnSubUnit::with(['unit', 'category'])->get();
    }

    public function findById(int $id): ?PnSubUnit
    {
        return PnSubUnit::with(['unit', 'category'])->find($id);
    }

    public function findByUnit(int $unitId): Collection
    {
        return PnSubUnit::with(['unit', 'category'])->where('unit_id', $unitId)->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return PnSubUnit::with(['unit', 'category'])->where('category_id', $categoryId)->get();
    }

    public function create(array $data): PnSubUnit
    {
        return PnSubUnit::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $subUnit = $this->findById($id);
        if (!$subUnit) {
            return false;
        }
        return $subUnit->update($data);
    }

    public function delete(int $id): bool
    {
        $subUnit = $this->findById($id);
        if (!$subUnit) {
            return false;
        }
        return $subUnit->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnSubUnit::with(['unit', 'category'])->paginate($perPage);
    }

    public function filterByUnit(int $unitId, int $perPage = 15)
    {
        return PnSubUnit::with(['unit', 'category'])->where('unit_id', $unitId)->paginate($perPage);
    }
}
