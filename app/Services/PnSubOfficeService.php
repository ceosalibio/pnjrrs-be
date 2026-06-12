<?php

namespace App\Services;

use App\Repositories\PnSubOfficeRepository;
use Illuminate\Database\Eloquent\Collection;

class PnSubOfficeService
{
    public function __construct(private PnSubOfficeRepository $repository)
    {
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
        return $this->repository->create($data);
    }

    public function updateSubOffice(int $id, array $data): bool
    {
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
