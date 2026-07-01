<?php

namespace App\Services;

use App\Repositories\SettingOrganizationRepository;
use App\Repositories\PnUnitRepository;
use App\Repositories\PnOfficeRepository;
use App\Repositories\PnSubOfficeRepository;
use App\Repositories\ItemAfposRepository;
use App\Models\ItemGrade;
use Illuminate\Database\Eloquent\Collection;

class SettingOrganizationService
{
    private $unitRepository;
    private $repository;
    private $officeRepository;
    private $subOfficeRepository;
    private $itemAfposRepository;

    public function __construct(
        PnUnitRepository $unitRepository,
        SettingOrganizationRepository $repository,
        PnOfficeRepository $officeRepository,
        PnSubOfficeRepository $subOfficeRepository,
        ItemAfposRepository $itemAfposRepository
    ) {
        $this->unitRepository = $unitRepository;
        $this->repository = $repository;
        $this->officeRepository = $officeRepository;
        $this->subOfficeRepository = $subOfficeRepository;
        $this->itemAfposRepository = $itemAfposRepository;
    }

    public function getAllOrganizations(): Collection
    {
        return $this->repository->all();
    }

    public function getOrganizationsByFilters(array $filters, int $perPage = 15)
    {
        return $this->repository->filterByMultiple($filters, $perPage);
    }

    public function getOrganizationById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createOrganization(array $data)
    {
        if (isset($data['unit_id'])) {
            $category = $this->unitRepository->getCategoryByUnitId($data['unit_id']);
            if ($category) {
                $data['category_id'] = $category->id;
            }
        }

        // Process items array to create offices and sub-offices
        if (isset($data['items']) && is_array($data['items'])) {
            $data['items'] = $this->processItems($data['items'], $data);
        }

        // Group items by office_id and create separate organizations for each office
        if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
            $organizations = $this->createOrganizationsByOffice($data);
            return $organizations;
        }

        $data['created_by'] = auth()->user()?->id;
        return $this->repository->create($data);

    }

    /**
     * Create separate organizations for each unique office in the items
     */
    private function createOrganizationsByOffice(array $baseData): array
    {
        $itemsByOffice = []; // Group items by office_id
        $createdOrganizations = [];

        // Group items by office_id
        foreach ($baseData['items'] as $item) {
            $officeId = $item['office_id'] ?? null;
            $subOfficeId = $item['sub_office_id'] ?? null;
            
            if ($officeId !== null) {
                $key = $officeId . '_' . ($subOfficeId ?? 'null');
                
                if (!isset($itemsByOffice[$key])) {
                    $itemsByOffice[$key] = [
                        'office_id' => $officeId,
                        'sub_office_id' => $subOfficeId,
                        'items' => []
                    ];
                }
                
                $itemsByOffice[$key]['items'][] = $item;
            }
        }

        // Create a separate organization for each unique office
        foreach ($itemsByOffice as $officeGroup) {
            $orgData = $baseData;
            $orgData['office_id'] = $officeGroup['office_id'];
            $orgData['sub_office_id'] = $officeGroup['sub_office_id'];
            $orgData['items'] = $officeGroup['items'];
            $orgData['created_by'] = auth()->user()?->id;

            $createdOrganization = $this->repository->create($orgData);
            $createdOrganizations[] = $createdOrganization;
        }

        return $createdOrganizations;
    }

    /**
     * Filter items for update: only keep items matching the organization's office_id and sub_office_id
     * Does NOT create new offices/sub-offices during update
     */
    private function filterAndPrepareItemsForUpdate(array $items, ?int $officeId, ?int $subOfficeId): array
    {
        if ($officeId === null) {
            return $items;
        }

        $filteredItems = [];
        
        foreach ($items as $item) {
            // If sub_office_id exists, match both office_id and sub_office_id
            if ($subOfficeId !== null) {
                if (isset($item['office_id']) && $item['office_id'] == $officeId &&
                    isset($item['sub_office_id']) && $item['sub_office_id'] == $subOfficeId) {
                    $filteredItems[] = $item;
                }
            } else {
                // If only office_id exists, match office_id only
                if (isset($item['office_id']) && $item['office_id'] == $officeId) {
                    $filteredItems[] = $item;
                }
            }
        }

        return !empty($filteredItems) ? $filteredItems : $items;
    }

    /**
     * Process items array: create offices and sub-offices as needed
     */
    private function processItems(array $items, array $organizationData): array
    {
        $processedItems = [];
        $officeMap = []; // Track created offices by name
        $afposMap = []; // Track created afpos by name
        $currentOfficeId = null;

        foreach ($items as $item) {
            // Handle afpos - create if not exists
            if (!empty($item['afpos'])) {
                $item['item_afpos_id'] = $this->getOrCreateAfpos($item['afpos'], $item['grade'], $afposMap);
                $item['item_afpos_group'] = $this->itemAfposRepository->getAfposGroup($item['afpos']);
            }

            // If this is an office header (office: true), create a PnOffice and PnSubOffice
            if (!empty($item['office']) && $item['office'] === true) {
                $officeAndSubOffice = $this->createOfficeAndSubOffice($item, $organizationData);
                $item['office_id'] = $officeAndSubOffice['office_id'];
                $item['sub_office_id'] = $officeAndSubOffice['sub_office_id'];
                $currentOfficeId = $officeAndSubOffice['office_id'];
                $officeMap[$item['officeName']] = $officeAndSubOffice['office_id'];
            } else {
                // Regular personnel item - assign current office_id
                if ($currentOfficeId !== null) {
                    $item['office_id'] = $currentOfficeId;
                } elseif (isset($item['officeName']) && isset($officeMap[$item['officeName']])) {
                    $item['office_id'] = $officeMap[$item['officeName']];
                } elseif (isset($item['officeName']) && !isset($officeMap[$item['officeName']])) {
                    // Create office if it doesn't exist (when no office payload)
                    $officeId = $this->createOffice($item, $organizationData);
                    $item['office_id'] = $officeId;
                    $officeMap[$item['officeName']] = $officeId;
                }
            }

            $processedItems[] = $item;
        }

        return $processedItems;
    }

    /**
     * Get or create ItemAfpos based on afpos name and grade-division relation
     */
    private function getOrCreateAfpos(string $afposName, string $grade, array &$afposMap): ?int
    {
        // Uppercase the afpos name for consistency
        $afposName = strtoupper($afposName);

        // Don't save if afpos name is "OPEN"
        if ($afposName === 'OPEN RATING' || $afposName === 'OPEN') {
            return null;
        }

        // Check if already processed in this batch
        if (isset($afposMap[$afposName])) {
            return $afposMap[$afposName];
        }

        // Check if afpos exists in database
        $existingAfpos = $this->itemAfposRepository->findByName($afposName);
        if ($existingAfpos) {
            $afposMap[$afposName] = $existingAfpos->id;
            return $existingAfpos->id;
        }

        // If not exist, find division_id using grade
        $divisionId = $this->getDivisionIdByGrade($grade);

        // Create new ItemAfpos
        $afposData = [
            'name' => $afposName,
            'division_id' => $divisionId,
            'created_by' => auth()->user()?->id,
        ];

        $newAfpos = $this->itemAfposRepository->create($afposData);
        $afposMap[$afposName] = $newAfpos->id;

        return $newAfpos->id;
    }

    /**
     * Get division_id from grade name
     */
    private function getDivisionIdByGrade(string $gradeName): ?int
    {
        if (empty($gradeName)) {
            return null;
        }

        $grade = ItemGrade::where('name', $gradeName)->first();
        return $grade?->division_id;
    }

    
    /**
     * Create a PnOffice from item data
     */
    private function createOffice(array $item, array $organizationData): ?int
    {
        $officeData = [
            'name' => $item['officeName'] ?? 'Unnamed Office',
            'unit_id' => $organizationData['unit_id'] ?? null,
            'sub_unit_id' => $organizationData['sub_unit_id'] ?? null,
            'category_id' => $organizationData['category_id'] ?? null,
        ];

        $office = $this->officeRepository->create($officeData);
        return $office->id;
    }

    /**
     * Create PnOffice or PnSubOffice based on whether office_id exists
     * If office_id exists → Only create PnSubOffice
     * If office_id doesn't exist → Create PnOffice only
     */
    private function createOfficeAndSubOffice(array $item, array $organizationData): array
    {
        $officeId = $organizationData['office_id'] ?? null;

        // If office_id exists, only create PnSubOffice
        if ($officeId) {
            $subOfficeData = [
                'name' => $item['officeName'] ?? 'Unnamed Sub-Office',
                'office_id' => $officeId,
                'unit_id' => $organizationData['unit_id'] ?? null,
                'sub_unit_id' => $organizationData['sub_unit_id'] ?? null,
                'category_id' => $organizationData['category_id'] ?? null,
            ];

            $subOffice = $this->subOfficeRepository->create($subOfficeData);

            return [
                'office_id' => $officeId,
                'sub_office_id' => $subOffice->id,
            ];
        }

        // If no office_id, create PnOffice only
        $officeData = [
            'name' => $item['officeName'] ?? 'Unnamed Office',
            'unit_id' => $organizationData['unit_id'] ?? null,
            'sub_unit_id' => $organizationData['sub_unit_id'] ?? null,
            'category_id' => $organizationData['category_id'] ?? null,
        ];

        $office = $this->officeRepository->create($officeData);

        return [
            'office_id' => $office->id,
            'sub_office_id' => null,
        ];
    }

    public function updateOrganization(int $id, array $data)
    {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            return null;
        }

        if (isset($data['unit_id'])) {
            $category = $this->unitRepository->getCategoryByUnitId($data['unit_id']);
            if ($category) {
                $data['category_id'] = $category->id;
            }
        }

        // For updates: just replace items without creating new offices
        // Items are stored as-is without office/office creation logic
        // Only filter items to match the existing organization's office_id and sub_office_id
        if (isset($data['items']) && is_array($data['items'])) {
            $data['items'] = $this->filterAndPrepareItemsForUpdate(
                $data['items'],
                $organization->office_id,
                $organization->sub_office_id
            );
        }

        $data['updated_by'] = auth()->user()?->id;
        return $this->repository->update($id, $data);
        
    }

    public function deleteOrganization(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginatedOrganizations(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }
}
