<?php

namespace App\Services;

use App\Repositories\PnOfficeRepository;
use Illuminate\Database\Eloquent\Collection;

class PnOfficeService
{
    public function __construct(private PnOfficeRepository $repository)
    {
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
        return $this->repository->create($data);
    }

    public function updateOffice(int $id, array $data): bool
    {
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
