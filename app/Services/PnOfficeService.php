<?php

namespace App\Services;

use App\Repositories\PnOfficeRepository;
use App\Repositories\PnSubUnitRepository;
use App\Repositories\PnUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class PnOfficeService
{
    private PnUnitRepository $unitRepository;
    private PnSubUnitRepository $subUnitRepository;
    private PnOfficeRepository $repository;
    public function __construct(PnUnitRepository $unitRepository, PnSubUnitRepository $subUnitRepository, PnOfficeRepository $repository)
    {
        $this->unitRepository = $unitRepository;
        $this->subUnitRepository = $subUnitRepository;
        $this->repository = $repository;
    }

    public function getAllOffices(): Collection
    {
        return $this->repository->all();
    }

    public function getOfficeById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getOfficesBySubUnit(int $subUnitId): Collection
    {
        return $this->repository->findBySubUnit($subUnitId);
    }

    public function getOfficesByUnit(int $unitId): Collection
    {
        return $this->repository->findByUnit($unitId);
    }

    public function getOfficesByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function createOffice(array $data)
    {
        if (!isset($data["category_id"])) {
            if (isset($data["sub_unit_id"])) {
                $subUnit = $this->subUnitRepository->findById($data["sub_unit_id"]);
                if (!$subUnit) {
                    throw new \Exception("Sub Unit not found");
                }
                $data["category_id"] = $subUnit->category_id;
                $data["unit_id"] = $subUnit->unit_id;
            } elseif (isset($data["unit_id"])) {
                $unit = $this->unitRepository->findById($data["unit_id"]);
                if (!$unit) {
                    throw new \Exception("Unit not found");
                }
                $data["category_id"] = $unit->category_id;
            } else {
                throw new \Exception("Either sub_unit_id or unit_id is required when category ID is not provided");
            }
        }
        return $this->repository->create($data);
    }

    public function updateOffice(int $id, array $data): bool
    {
        if (!isset($data["category_id"])) {
            if (isset($data["sub_unit_id"])) {
                $subUnit = $this->subUnitRepository->findById($data["sub_unit_id"]);
                if (!$subUnit) {
                    throw new \Exception("Sub Unit not found");
                }
                $data["category_id"] = $subUnit->category_id;
                $data["unit_id"] = $subUnit->unit_id;
            } elseif (isset($data["unit_id"])) {
                $unit = $this->unitRepository->findById($data["unit_id"]);
                if (!$unit) {
                    throw new \Exception("Unit not found");
                }
                $data["category_id"] = $unit->category_id;
            } else {
                throw new \Exception("Either sub_unit_id or unit_id is required when category ID is not provided");
            }
        }
        return $this->repository->update($id, $data);
    }

    public function deleteOffice(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedOffices(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getPaginatedOfficesBySubUnit(int $subUnitId, int $perPage = 15)
    {
        return $this->repository->filterBySubUnit($subUnitId, $perPage);
    }
}
