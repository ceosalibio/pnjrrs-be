<?php

namespace App\Repositories;

use App\Models\PnCategory;
use Illuminate\Database\Eloquent\Collection;

class PnCategoryRepository
{
    public function all(): Collection
    {
        return PnCategory::all();
    }

    public function findById(int $id): ?PnCategory
    {
        return PnCategory::find($id);
    }

    public function create(array $data): PnCategory
    {
        return PnCategory::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->findById($id);
        if (!$category) {
            return false;
        }
        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnCategory::paginate($perPage);
    }
}
