<?php

namespace App\Services;

use App\Repositories\PnUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class PnUnitService
{
    public function __construct(private PnUnitRepository $repository)
    {
    }

    public function getAllUnits(): Collection
    {
        return $this->repository->all();
    }

    public function getUnitById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getUnitsByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function createUnit(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateUnit(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteUnit(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedUnits(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getPaginatedUnitsByCategory(int $categoryId, int $perPage = 15)
    {
        return $this->repository->filterByCategory($categoryId, $perPage);
    }
}
