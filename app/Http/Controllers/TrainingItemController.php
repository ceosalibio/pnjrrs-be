<?php

namespace App\Http\Controllers;

use App\Services\TrainingItemService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class TrainingItemController extends Controller
{
    use APIResponse;

    public function __construct(private TrainingItemService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $hasPerPage = $request->has('per_page');
            $perPage = $request->input('per_page', 15);
            $filters = [];

            // Collect filter parameters
            if ($request->has('category_id')) {
                $filters['category_id'] = $request->input('category_id');
            }
            if ($request->has('unit_id')) {
                $filters['unit_id'] = $request->input('unit_id');
            }
            if ($request->has('sub_unit_id')) {
                $filters['sub_unit_id'] = $request->input('sub_unit_id');
            }
            if ($request->has('office_id')) {
                $filters['office_id'] = $request->input('office_id');
            }
            if ($request->has('sub_office_id')) {
                $filters['sub_office_id'] = $request->input('sub_office_id');
            }
            if ($request->has('year')) {
                $filters['year'] = $request->input('year');
            }

            // If search query is provided
            if ($search) {
                $items = $this->service->searchItems($search);
                return $this->successResponse($items, 'Training items search results retrieved successfully');
            }

            // If filters exist, use filtered query
            if (!empty($filters)) {
                if ($hasPerPage) {
                    $items = $this->service->getItemsByFilters($filters, $perPage);
                } else {
                    $items = $this->service->getItemsByFiltersAll($filters);
                }
            } else {
                // No filters
                if ($hasPerPage) {
                    $items = $this->service->getPaginatedItems($perPage);
                } else {
                    $items = $this->service->getAllItems();
                }
            }

            return $this->successResponse($items, 'Training items retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:pn_categories,id',
                'unit_id' => 'required|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'items' => 'nullable|array',
                'year' => 'nullable|integer|digits:4',
            ]);

            $item = $this->service->createItem($validated);
            return $this->successResponse($item, 'Training item created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $item = $this->service->getItemById($id);
            if (!$item) {
                return $this->errorResponse('Training item not found', 404);
            }
            return $this->successResponse($item, 'Training item retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'sometimes|integer|exists:pn_categories,id',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'items' => 'nullable|array',
                'year' => 'nullable|integer|digits:4',
            ]);

            $updated = $this->service->updateItem($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Training item not found', 404);
            }
            return $this->successResponse(null, 'Training item updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteItem($id);
            if (!$deleted) {
                return $this->errorResponse('Training item not found', 404);
            }
            return $this->successResponse(null, 'Training item deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get training items by unit
     */
    public function getByUnit($unitId)
    {
        try {
            $perPage = request()->input('per_page', 15);
            $items = $this->service->getItemsByUnitId($unitId, $perPage);
            return $this->successResponse($items, "Training items for unit '{$unitId}' retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get training items by year
     */
    public function getByYear($year)
    {
        try {
            $perPage = request()->input('per_page', 15);
            $items = $this->service->getItemsByYear($year, $perPage);
            return $this->successResponse($items, "Training items for year '{$year}' retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
