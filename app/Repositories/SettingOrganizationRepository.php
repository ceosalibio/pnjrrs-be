<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

class SettingOrganizationRepository
{
    public function all(): Collection
    {
        return Organization::all();
    }

    public function findById(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(int $id, array $data)
    {
        $organization = $this->findById($id);
        if (!$organization) {
            return null;
        }
        $organization->update($data);
        return $organization; // Return the updated model
    }

    public function delete(int $id): bool
    {
        $organization = $this->findById($id);
        if (!$organization) {
            return false;
        }
        return $organization->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return Organization::paginate($perPage);
    }

    public function filterByUnitId(int $unitId, int $perPage = 15)
    {
        return Organization::where('unit_id', $unitId)->paginate($perPage);
    }

    public function filterBySubUnitId(int $subUnitId, int $perPage = 15)
    {
        return Organization::where('sub_unit_id', $subUnitId)->paginate($perPage);
    }

    public function filterByOfficeId(int $officeId, int $perPage = 15)
    {
        return Organization::where('office_id', $officeId)->paginate($perPage);
    }

    public function filterByMultiple(array $filters, int $perPage = 15)
    {
        $query = Organization::query();

        if (isset($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }
        if (isset($filters['sub_unit_id'])) {
            $query->where('sub_unit_id', $filters['sub_unit_id']);
        }
        if (isset($filters['office_id'])) {
            $query->where('office_id', $filters['office_id']);
        }

        return $query->paginate($perPage);
    }
}
