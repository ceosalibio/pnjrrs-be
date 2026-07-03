<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;

class UserController extends Controller
{
    use APIResponse;

    public function __construct(private UserService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');

            if ($search) {
                $users = $this->service->searchUsers($search, $perPage);
            } elseif ($request->has('category_id')) {
                $users = $this->service->getPaginatedUsersByCategory($request->category_id, $perPage);
            } elseif ($request->has('unit_id')) {
                $users = $this->service->getPaginatedUsersByUnit($request->unit_id, $perPage);
            } elseif ($request->has('sub_unit_id')) {
                $users = $this->service->getPaginatedUsersBySubUnit($request->sub_unit_id, $perPage);
            } elseif ($request->has('office_id')) {
                $users = $this->service->getPaginatedUsersByOffice($request->office_id, $perPage);
            } elseif ($request->has('sub_office_id')) {
                $users = $this->service->getPaginatedUsersBySubOffice($request->sub_office_id, $perPage);
            } else {
                $users = $this->service->getPaginatedUsers($perPage);
            }

            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->service->getUserById($id);
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:pn_categories,id',
                'unit_id' => 'required|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'rank_id' => 'nullable|integer|exists:item_ranks,id',
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'nullable | integer',
                'approver' => 'nullable | integer',
                'office_role' => 'nullable | integer',
            ]);

            $user = $this->service->createUser($validated);
            // Remove password from response
            $user->makeHidden(['password']);
            return $this->successResponse($user, 'User created successfully', 201);
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
                'category_id' => 'sometimes|integer|exists:pn_categories,id',
                'unit_id' => 'sometimes|integer|exists:pn_units,id',
                'sub_unit_id' => 'nullable|integer|exists:pn_sub_units,id',
                'office_id' => 'nullable|integer|exists:pn_offices,id',
                'sub_office_id' => 'nullable|integer|exists:pn_sub_offices,id',
                'rank_id' => 'nullable|integer|exists:item_ranks,id',
                'name' => 'sometimes|string|max:255',
                'position' => 'required|string|max:255',
                'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
                'password' => 'sometimes|string|min:8',
                'role' => 'nullable | integer',
                'approver' => 'nullable | integer',
                'office_role' => 'nullable | integer',
            ]);

            $updated = $this->service->updateUser($id, $validated);
            if (!$updated) {
                return $this->errorResponse('User not found', 404);
            }
            return $this->successResponse(null, 'User updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->deleteUser($id);
            if (!$deleted) {
                return $this->errorResponse('User not found', 404);
            }
            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get users by rank
     * 
     * @param string $rank
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByRank($rankId)
    {
        try {
            $perPage = request()->input('per_page', 15);
            $users = $this->service->getUsersByRank($rankId);
            return $this->successResponse($users, "Users with rank ID '{$rankId}' retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Search users by name or username
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q');
            if (!$query) {
                return $this->errorResponse('Search query is required', 400);
            }
            $perPage = $request->input('per_page', 15);
            $users = $this->service->searchUsers($query, $perPage);
            return $this->successResponse($users, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getRank()
    {
        try {
            $ranks = $this->service->getAllRanks();
            return $this->successResponse($ranks, 'Ranks retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
