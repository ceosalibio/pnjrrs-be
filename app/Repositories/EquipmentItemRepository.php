<?php

namespace App\Repositories;

use App\Models\EquipmentItem;
use App\Models\EquipmentList;
use Illuminate\Database\Eloquent\Collection;

class EquipmentItemRepository
{
    public function all(): Collection
    {
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])->get();
    }

    public function findById(int $id): ?EquipmentItem
    {
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])->find($id);
    }

    public function search(string $query): Collection
    {
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])
            ->where('items', 'like', "%{$query}%")
            ->get();
    }

    public function create(array $data): EquipmentItem
    {
        return EquipmentItem::create($data);
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
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])->paginate($perPage);
    }

    public function filterByMultiple(array $filters, int $perPage = 15)
    {
        $query = EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
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

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->paginate($perPage);
    }

    public function filterByMultipleAll(array $filters)
    {
        $query = EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
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

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->get();
    }

    public function getByUnitId(int $unitId, int $perPage = 15)
    {
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])
            ->where('unit_id', $unitId)
            ->paginate($perPage);
    }

    public function getByYear(int $year, int $perPage = 15)
    {
        return EquipmentItem::with([
            'category',
            'unit',
            'subUnit',
            'office',
            'subOffice',
        ])
            ->where('year', $year)
            ->paginate($perPage);
    }

    public function getTemplate()
    {
        return EquipmentList::with([
            'category',
            'division',
            'type',
        ])->get();
    }
}
