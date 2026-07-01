<?php

namespace App\Repositories;

use App\Models\ItemAfpos;
use App\Models\ItemAfposCluster;
use Illuminate\Database\Eloquent\Collection;

class ItemAfposRepository
{
    public function all(): Collection
    {
        return ItemAfpos::with(['division','cluster'])->get();
    }

    public function findById(int $id): ?ItemAfpos
    {
        return ItemAfpos::with(['division','cluster'])->find($id);
    }

    public function findByDivision(int $divisionId): Collection
    {
        return ItemAfpos::with(['division','cluster'])->where('division_id', $divisionId)->get();
    }

    public function findByName(string $name): ?ItemAfpos
    {
        return ItemAfpos::with(['division','cluster'])->where('name', $name)->first();
    }

    public function search(string $query): Collection
    {
        return ItemAfpos::with(['division','cluster'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    public function create(array $data): ItemAfpos
    {
        return ItemAfpos::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $item = $this->findById($id);
        if (!$item) {
            return false;
        }
        return $item->update($data);
    }

    public function delete(int $id): bool
    {
        $item = $this->findById($id);
        if (!$item) {
            return false;
        }
        return $item->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return ItemAfpos::with(['division','cluster'])->paginate($perPage);
    }

    public function filterByDivision(int $divisionId, int $perPage = 15)
    {
        return ItemAfpos::with(['division','cluster'])
            ->where('division_id', $divisionId)
            ->paginate($perPage);
    }


    /**
     * Get afpos group from afpos name
     */
    public function getAfposGroup(string $afposData)
    {
        if (empty($afposData)) {
            return null;
        }
        // Uppercase the afpos name for consistency
        $afposName = strtoupper($afposData);

        $afpos = ItemAfposCluster::where('name', $afposName)->first();
        return $afpos?->group ?? null;
    }

}
