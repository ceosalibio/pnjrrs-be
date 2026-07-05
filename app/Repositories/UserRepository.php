<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ItemRank;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank',
            'approvers'
        ])->get();
    }

    public function findById(int $id): ?User
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank',
            'approvers'
        ])->find($id);
    }

    public function findByUsername(string $username): ?User
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank'

        ])->where('username', $username)->first();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('category_id', $categoryId)->get();
    }

    public function findByUnit(int $unitId): Collection
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank'
        ])->where('unit_id', $unitId)->get();
    }

    public function findBySubUnit(int $subUnitId): Collection
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('sub_unit_id', $subUnitId)->get();
    }

    public function findByOffice(int $officeId): Collection
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('office_id', $officeId)->get();
    }

    public function findBySubOffice(int $subOfficeId): Collection
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('sub_office_id', $subOfficeId)->get();
    }

    public function findByRank(int $rankId): Collection
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank'
        ])->where('rank_id', $rankId)->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        return $user->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank',
            'approvers'
        ])->paginate($perPage);
    }

    public function filterByCategory(int $categoryId, int $perPage = 15)
    {
        return User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('category_id', $categoryId)->paginate($perPage);
    }

    public function filterByUnit(int $unitId, int $perPage = 15)
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('unit_id', $unitId)->paginate($perPage);
    }

    public function filterBySubUnit(int $subUnitId, int $perPage = 15)
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('sub_unit_id', $subUnitId)->paginate($perPage);
    }

    public function filterByOffice(int $officeId, int $perPage = 15)
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('office_id', $officeId)->paginate($perPage);
    }

    public function filterBySubOffice(int $subOfficeId, int $perPage = 15)
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('sub_office_id', $subOfficeId)->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15)
    {
        return User::with([
           'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',    
            'rank'
        ])->where('name', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    public function getAllRanks() : Collection
    {
        return ItemRank::with(['division','grade'])->get();
    }

// approver model
    public function filterByMultiple(array $filters)
    {
        // Log the incoming filters
        
        $query = User::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
            'rank',
        ]);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }

        if (isset($filters['sub_unit_id'])) {
            $query->where('sub_unit_id', $filters['sub_unit_id']);
        }

        if (isset($filters['office_id'])) {
            $query->where('office_id', $filters['office_id']);
        }

        if (isset($filters['sub_office_id'])) {
            $query->where('sub_office_id', $filters['sub_office_id']);
        }

        // Load approvers with conditional filtering
        if (isset($filters['report_id']) && isset($filters['report_type'])) {
            // Load only approvers matching the report_id and report_type
            $query->with([
                'approvers' => function ($q) use ($filters) {
                    $q->where('report_id', $filters['report_id'])
                        ->where('report_type', $filters['report_type']);
                },
                'approvers.user'
            ]);
        } else {
            
            // Load all approvers
            $query->with([
                'approvers',
                'approvers.user'
            ]);
        }
        
        $query->orderBy('approver');
        
        $results = $query->get();
        \Log::info('UserRepository filterByMultiple - Query results count:', [
            'count' => $results->count(),
            'filters' => $filters
        ]);
        
        return $results;
    }
}

 
