<?php

namespace App\Repositories;

use App\Models\PnUnit;
use Illuminate\Database\Eloquent\Collection;

class PnUnitRepository
{
    public function all(): Collection
    {
        return PnUnit::with(['category'])->get();
    }

    public function findById(int $id): ?PnUnit
    {
        return PnUnit::with(['category'])->find($id);
    }

    public function findByCategory(int $categoryId): Collection
    {
        return PnUnit::with(['category'])->where('category_id', $categoryId)->get();
    }

    public function create(array $data): PnUnit
    {
        return PnUnit::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $unit = $this->findById($id);
        if (!$unit) {
            return false;
        }
        return $unit->update($data);
    }

    public function delete(int $id): bool
    {
        $unit = $this->findById($id);
        if (!$unit) {
            return false;
        }
        return $unit->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnUnit::with(['category'])->paginate($perPage);
    }

    public function filterByCategory(int $categoryId, int $perPage = 15)
    {
        return PnUnit::with(['category'])->where('category_id', $categoryId)->paginate($perPage);
    }
}
