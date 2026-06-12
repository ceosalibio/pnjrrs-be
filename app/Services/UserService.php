<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function getAllUsers(): Collection
    {
        return $this->repository->all();
    }

    public function getUserById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function getUserByUsername(string $username)
    {
        return $this->repository->findByUsername($username);
    }

    public function getUsersByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function getUsersByUnit(int $unitId): Collection
    {
        return $this->repository->findByUnit($unitId);
    }

    public function getUsersBySubUnit(int $subUnitId): Collection
    {
        return $this->repository->findBySubUnit($subUnitId);
    }

    public function getUsersByOffice(int $officeId): Collection
    {
        return $this->repository->findByOffice($officeId);
    }

    public function getUsersBySubOffice(int $subOfficeId): Collection
    {
        return $this->repository->findBySubOffice($subOfficeId);
    }

    public function getUsersByRank(string $rank): Collection
    {
        return $this->repository->findByRank($rank);
    }

    public function createUser(array $data)
    {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->repository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        // Hash password if it's being updated
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->repository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedUsers(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function getPaginatedUsersByCategory(int $categoryId, int $perPage = 15)
    {
        return $this->repository->filterByCategory($categoryId, $perPage);
    }

    public function getPaginatedUsersByUnit(int $unitId, int $perPage = 15)
    {
        return $this->repository->filterByUnit($unitId, $perPage);
    }

    public function getPaginatedUsersBySubUnit(int $subUnitId, int $perPage = 15)
    {
        return $this->repository->filterBySubUnit($subUnitId, $perPage);
    }

    public function getPaginatedUsersByOffice(int $officeId, int $perPage = 15)
    {
        return $this->repository->filterByOffice($officeId, $perPage);
    }

    public function getPaginatedUsersBySubOffice(int $subOfficeId, int $perPage = 15)
    {
        return $this->repository->filterBySubOffice($subOfficeId, $perPage);
    }

    public function searchUsers(string $query, int $perPage = 15)
    {
        return $this->repository->search($query, $perPage);
    }
}
