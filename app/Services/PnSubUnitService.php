<?php

namespace App\Services;

use App\Repositories\PnSubUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class PnSubUnitService
{
    public function __construct(private PnSubUnitRepository $repository)
    {
    }

    public function getAllSubUnits(): Collection
    {
        return $this->repository->all();
    }

    public function getSubUnitById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getSubUnitsByUnit(int $unitId): Collection
    {
        return $this->repository->findByUnit($unitId);
    }

    public function getSubUnitsByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function createSubUnit(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateSubUnit(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSubUnit(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedSubUnits(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getPaginatedSubUnitsByUnit(int $unitId, int $perPage = 15)
    {
        return $this->repository->filterByUnit($unitId, $perPage);
    }
}
