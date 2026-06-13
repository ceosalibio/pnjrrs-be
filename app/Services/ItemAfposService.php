<?php

namespace App\Services;

use App\Repositories\ItemAfposRepository;
use Illuminate\Database\Eloquent\Collection;

class ItemAfposService
{
    public function __construct(private ItemAfposRepository $repository)
    {
    }

    public function getAllItems(): Collection
    {
        return $this->repository->all();
    }

    public function getItemById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getItemByName(string $name)
    {
        return $this->repository->findByName($name);
    }

    public function getItemsByDivision(int $divisionId): Collection
    {
        return $this->repository->findByDivision($divisionId);
    }

    public function searchItems(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function createItem(array $data)
    {
        $data['name'] = strtoupper($data['name']);
        return $this->repository->create($data);
    }

    public function updateItem(int $id, array $data): bool
    {
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

    public function getPaginatedItemsByDivision(int $divisionId, int $perPage = 15)
    {
        return $this->repository->filterByDivision($divisionId, $perPage);
    }
}
