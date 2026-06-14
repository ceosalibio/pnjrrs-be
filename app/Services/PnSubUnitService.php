<?php

namespace App\Services;

use App\Repositories\PnSubUnitRepository;
use App\Repositories\PnUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class PnSubUnitService
{
    private PnUnitRepository $unitRepository;
    private PnSubUnitRepository $repository;
    public function __construct(PnUnitRepository $unitRepository, PnSubUnitRepository $repository)
    {
        $this->unitRepository = $unitRepository;
        $this->repository = $repository;
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
       if (!isset($data["category_id"])) {
            if (!isset($data["unit_id"])) {
                throw new \Exception("Unit ID is required when category ID is not provided");
            }
            $unit = $this->unitRepository->findById($data["unit_id"]);
            if (!$unit) {
                throw new \Exception("Unit not found");
            }
            $data["category_id"] = $unit->category_id;
        }
        return $this->repository->create($data);
    }

    public function updateSubUnit(int $id, array $data): bool
    {
        if (isset($data["unit_id"]) && !isset($data["category_id"])) {
            $unit = $this->unitRepository->findById($data["unit_id"]);
            if (!$unit) {
                throw new \Exception("Unit not found");
            }
            $data["category_id"] = $unit->category_id;
        }
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
