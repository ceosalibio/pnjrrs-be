<?php

namespace App\Services;

use App\Repositories\PnCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class PnCategoryService
{
    public function __construct(private PnCategoryRepository $repository)
    {
    }

    public function getAllCategories(): Collection
    {
        return $this->repository->all();
    }

    public function getCategoryById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createCategory(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedCategories(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }
}
