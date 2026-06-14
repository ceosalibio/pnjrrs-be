<?php

namespace App\Services;
use App\Repositories\PnOfficeRepository;
use App\Repositories\PnSubUnitRepository;
use App\Repositories\PnUnitRepository;
use App\Repositories\PnSubOfficeRepository;
use Illuminate\Database\Eloquent\Collection;

class PnSubOfficeService
{
    private PnUnitRepository $unitRepository;
    private PnSubUnitRepository $subUnitRepository;
    private PnOfficeRepository $officeRepository;
    private PnSubOfficeRepository $repository;
    public function __construct(PnUnitRepository $unitRepository, PnSubUnitRepository $subUnitRepository, PnOfficeRepository $officeRepository, PnSubOfficeRepository $repository)
    {
        $this->unitRepository = $unitRepository;
        $this->subUnitRepository = $subUnitRepository;
        $this->officeRepository = $officeRepository;
        $this->repository = $repository;
    }

    public function getAllSubOffices(): Collection
    {
        return $this->repository->all();
    }

    public function getSubOfficeById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getSubOfficesByOffice(int $officeId): Collection
    {
        return $this->repository->findByOffice($officeId);
    }

    public function getSubOfficesBySubUnit(int $subUnitId): Collection
    {
        return $this->repository->findBySubUnit($subUnitId);
    }

    public function getSubOfficesByUnit(int $unitId): Collection
    {
        return $this->repository->findByUnit($unitId);
    }

    public function getSubOfficesByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function createSubOffice(array $data)
    {
        if (!isset($data["category_id"])) {
            if (isset($data["sub_unit_id"])) {
                $subUnit = $this->subUnitRepository->findById($data["sub_unit_id"]);
                if (!$subUnit) {
                    throw new \Exception("Sub Unit not found");
                }
                $data["category_id"] = $subUnit->category_id;
                $data["unit_id"] = $subUnit->unit_id;
                $data["office_id"] = $subUnit->office_id ?? null;
            } elseif (isset($data["unit_id"])) {
                $unit = $this->unitRepository->findById($data["unit_id"]);
                if (!$unit) {
                    throw new \Exception("Unit not found");
                }
                $data["category_id"] = $unit->category_id;
                $data["office_id"] = $unit->office_id ?? null;
            } elseif (isset($data["office_id"])) {
                $office = $this->officeRepository->findById($data["office_id"]);
                if (!$office) {
                    throw new \Exception("Office not found");
                }
                $data["category_id"] = $office->category_id;
                $data["unit_id"] = $office->unit_id ?? null;
                $data["sub_unit_id"] = $office->sub_unit_id ?? null;
                $data["office_id"] = $office->id;
            } else {
                throw new \Exception("Either sub_unit_id, unit_id, or office_id is required when category ID is not provided");
            }
        }
        return $this->repository->create($data);
    }

    public function updateSubOffice(int $id, array $data): bool
    {
        if (!isset($data["category_id"])) {
            if (isset($data["sub_unit_id"])) {
                $subUnit = $this->subUnitRepository->findById($data["sub_unit_id"]);
                if (!$subUnit) {
                    throw new \Exception("Sub Unit not found");
                }
                $data["category_id"] = $subUnit->category_id;
                $data["unit_id"] = $subUnit->unit_id;
                $data["office_id"] = $subUnit->office_id ?? null;
            } elseif (isset($data["unit_id"])) {
                $unit = $this->unitRepository->findById($data["unit_id"]);
                if (!$unit) {
                    throw new \Exception("Unit not found");
                }
                $data["category_id"] = $unit->category_id;
                $data["office_id"] = $unit->office_id ?? null;
            } elseif (isset($data["office_id"])) {
                $office = $this->officeRepository->findById($data["office_id"]);
                if (!$office) {
                    throw new \Exception("Office not found");
                }
                $data["category_id"] = $office->category_id;
                $data["unit_id"] = $office->unit_id ?? null;
                $data["sub_unit_id"] = $office->sub_unit_id ?? null;
                $data["office_id"] = $office->id;
            } else {
                throw new \Exception("Either sub_unit_id, unit_id, or office_id is required when category ID is not provided");
            }
        }
        return $this->repository->update($id, $data);
    }

    public function deleteSubOffice(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedSubOffices(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getPaginatedSubOfficesByOffice(int $officeId, int $perPage = 15)
    {
        return $this->repository->filterByOffice($officeId, $perPage);
    }
}
