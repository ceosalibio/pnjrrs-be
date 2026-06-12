<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\APIResponse;
use App\Services\AuthLoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use APIResponse;

    protected AuthLoggingService $loggingService;

    public function __construct(AuthLoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Login user and create token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $validated['username'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                // Log failed login attempt
                $this->loggingService->logLoginAttempt($validated['username'], false, 'Invalid credentials');
                
                throw ValidationException::withMessages([
                    'username' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Create token
            $token = $user->createToken('api_token')->plainTextToken;

            // Log successful login
            $this->loggingService->logLoginAttempt($user->username, true, 'Login successful');

            return $this->successResponse(
                [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'rank' => $user->rank,
                    ],
                    'token' => $token,
                ],
                'Login successful',
                200
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->errors(),
                'Validation failed',
                422
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ['error' => $e->getMessage()],
                'Login failed',
                500
            );
        }
    }

    /**
     * Logout user and revoke token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    ['error' => 'User not authenticated'],
                    'Logout failed',
                    401
                );
            }

            // Revoke token
            $user->currentAccessToken()->delete();

            // Log logout
            $this->loggingService->logLogoutAttempt($user->username, true, 'Logout successful');

            return $this->successResponse(
                [],
                'Logout successful',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ['error' => $e->getMessage()],
                'Logout failed',
                500
            );
        }
    }

    /**
     * Get current authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    ['error' => 'User not authenticated'],
                    'Not authenticated',
                    401
                );
            }

            // Load relationships
            $user->load(['category', 'unit', 'subUnit', 'office', 'subOffice']);

            return $this->successResponse(
                $user,
                'User details retrieved',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ['error' => $e->getMessage()],
                'Failed to retrieve user',
                500
            );
        }
    }
}
