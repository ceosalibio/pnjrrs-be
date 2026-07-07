<?php

namespace App\Services;

use App\Repositories\EquipmentItemRepository;
use App\Repositories\PnUnitRepository;

class EquipmentItemService
{
    private $repository;

    public function __construct(
        EquipmentItemRepository $repository,
        PnUnitRepository $unitRepository

        )
    {
        $this->repository = $repository;
        $this->unitRepository = $unitRepository;

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
        if (isset($data['unit_id'])) {
            $category = $this->unitRepository->getCategoryByUnitId($data['unit_id']);
            if ($category) {
                $data['category_id'] = $category->id;
            }
        }
        $data['year'] = now()->year;
        $data['created_by'] = auth()->user()?->id;
        return $this->repository->create($data);
    }

    public function updateItem(int $id, array $data)
    {
        if (isset($data['unit_id'])) {
            $category = $this->unitRepository->getCategoryByUnitId($data['unit_id']);
            if ($category) {
                $data['category_id'] = $category->id;
            }
        }
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

    public function getTemplate()
    {
        return $this->repository->getTemplate();
    }

    public function getTemplateGrouped()
    {
        $items = $this->repository->getTemplate();
        
        // Group by category -> division -> type
        $grouped = [];
        
        foreach ($items as $item) {
            $categoryName = $item->category->name;
            $divisionName = $item->division->name;
            $typeName = $item->type->name;
            
            // Initialize category if not exists
            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = [
                    'category_id' => $item->category->id,
                    'category_name' => $categoryName,
                    'divisions' => []
                ];
            }
            
            // Initialize division if not exists
            if (!isset($grouped[$categoryName]['divisions'][$divisionName])) {
                $grouped[$categoryName]['divisions'][$divisionName] = [
                    'division_id' => $item->division->id,
                    'division_name' => $divisionName,
                    'types' => []
                ];
            }
            
            // Initialize type if not exists
            if (!isset($grouped[$categoryName]['divisions'][$divisionName]['types'][$typeName])) {
                $grouped[$categoryName]['divisions'][$divisionName]['types'][$typeName] = [
                    'type_id' => $item->type->id,
                    'type_name' => $typeName,
                    'items' => []
                ];
            }
            
            // Add the item
            $grouped[$categoryName]['divisions'][$divisionName]['types'][$typeName]['items'][] = [
                'id' => $item->id,
                'name' => $item->name,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        }
        
        // Convert to array and format divisions and types as arrays instead of associative arrays
        $result = [];
        foreach ($grouped as $category) {
            $divisions = [];
            foreach ($category['divisions'] as $division) {
                $types = [];
                foreach ($division['types'] as $type) {
                    $types[] = $type;
                }
                $division['types'] = $types;
                $divisions[] = $division;
            }
            $category['divisions'] = $divisions;
            $result[] = $category;
        }
        
        return $result;
    }
}
