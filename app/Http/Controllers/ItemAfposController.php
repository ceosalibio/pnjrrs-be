<?php

namespace App\Http\Controllers;

use App\Services\ItemAfposService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class ItemAfposController extends Controller
{
    use APIResponse;

    public function __construct(private ItemAfposService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            if ($search) {
                $items = $this->service->searchItems($search);
                return $this->successResponse($items, 'Item AFPOS search results retrieved successfully');
            } elseif ($request->has('division_id')) {
                if ($request->has('per_page')) {
                    $items = $this->service->getPaginatedItemsByDivision($request->division_id, $request->input('per_page'));
                } else {
                    $items = $this->service->getItemsByDivision($request->division_id);
                }
            } else {
                if ($request->has('per_page')) {
                    $items = $this->service->getPaginatedItems($request->input('per_page'));
                } else {
                    $items = $this->service->getAllItems();
                }
            }

            return $this->successResponse($items, 'Item AFPOS retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $item = $this->service->getItemById($id);
            if (!$item) {
                return $this->errorResponse('Item AFPOS not found', 404);
            }
            return $this->successResponse($item, 'Item AFPOS retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'division_id' => 'required|integer|exists:item_divisions,id',
                'name' => 'required|string|max:255|unique:item_afpos',
                'description' => 'nullable|string|max:500',
                'created_by' => 'nullable|integer|exists:users,id',
            ]);

            $item = $this->service->createItem($validated);
            return $this->successResponse($item, 'Item AFPOS created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'division_id' => 'sometimes|integer|exists:item_divisions,id',
                'name' => 'sometimes|string|max:255|unique:item_afpos,name,' . $id,
                'description' => 'nullable|string|max:500',
                'updated_by' => 'nullable|integer|exists:users,id',
            ]);

            $updated = $this->service->updateItem($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Item AFPOS not found', 404);
            }
            return $this->successResponse(null, 'Item AFPOS updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteItem($id);
            if (!$deleted) {
                return $this->errorResponse('Item AFPOS not found', 404);
            }
            return $this->successResponse(null, 'Item AFPOS deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get items by division
     */
    public function getByDivision($divisionId)
    {
        try {
            $perPage = request()->input('per_page', 15);
            $items = $this->service->getPaginatedItemsByDivision($divisionId, $perPage);
            return $this->successResponse($items, "Item AFPOS for division '{$divisionId}' retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
