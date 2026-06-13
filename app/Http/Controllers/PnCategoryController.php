<?php

namespace App\Http\Controllers;

use App\Services\PnCategoryService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class PnCategoryController extends Controller
{
    use APIResponse;

    public function __construct(private PnCategoryService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $categories = $this->service->getPaginatedCategories($perPage);
            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->service->getCategoryById($id);
            if (!$category) {
                return $this->errorResponse('Category not found', 404);
            }
            return $this->successResponse($category, 'Category retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $category = $this->service->createCategory($validated);
            return $this->successResponse($category, 'Category created successfully', 201);
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
                'name' => 'sometimes|string|max:255',
                'abreviation' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'address' => 'nullable|string',

            ]);

            $updated = $this->service->updateCategory($id, $validated);
            if (!$updated) {
                return $this->errorResponse('Category not found', 404);
            }
            return $this->successResponse(null, 'Category updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteCategory($id);
            if (!$deleted) {
                return $this->errorResponse('Category not found', 404);
            }
            return $this->successResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
