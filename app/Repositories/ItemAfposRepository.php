<?php

namespace App\Repositories;

use App\Models\ItemAfpos;
use Illuminate\Database\Eloquent\Collection;

class ItemAfposRepository
{
    public function all(): Collection
    {
        return ItemAfpos::with('division')->get();
    }

    public function findById(int $id): ?ItemAfpos
    {
        return ItemAfpos::with('division')->find($id);
    }

    public function findByDivision(int $divisionId): Collection
    {
        return ItemAfpos::with('division')->where('division_id', $divisionId)->get();
    }

    public function findByName(string $name): ?ItemAfpos
    {
        return ItemAfpos::with('division')->where('name', $name)->first();
    }

    public function search(string $query): Collection
    {
        return ItemAfpos::with('division')
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
        return ItemAfpos::with('division')->paginate($perPage);
    }

    public function filterByDivision(int $divisionId, int $perPage = 15)
    {
        return ItemAfpos::with('division')
            ->where('division_id', $divisionId)
            ->paginate($perPage);
    }
}
