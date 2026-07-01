<?php

namespace App\Services;

use App\Repositories\TrainingItemRepository;

class TrainingItemService
{
    private $repository;

    public function __construct(TrainingItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllItems()
    {
        return $this->repository->all();
    }

    public function getItemsByFilters(array $filters, int $perPage = 15)
    {
        return $this->repository->filterByMultiple($filters, $perPage);
    }

    public function getItemById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createItem(array $data)
    {
        $data['year'] = now()->year;
        $data['created_by'] = auth()->user()?->id;
        return $this->repository->create($data);
    }

    public function updateItem(int $id, array $data)
    {
        $data['updated_by'] = auth()->user()?->id;
        return $this->repository->update($id, $data);
    }

    public function deleteItem(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedItems(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function searchItems(string $query)
    {
        return $this->repository->search($query);
    }

    public function getItemsByUnitId(int $unitId, int $perPage = 15)
    {
        return $this->repository->getByUnitId($unitId, $perPage);
    }

    public function getItemsByYear(int $year, int $perPage = 15)
    {
        return $this->repository->getByYear($year, $perPage);
    }

    public function getItemsByFiltersAll(array $filters)
    {
        return $this->repository->filterByMultipleAll($filters);
    }
}
