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

            $user = User::with([
                'category',
                'unit',
                'subUnit',
                'office',
                'subOffice',
                'rank'
            ])
            ->where('username', $validated['username'])->first();

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
                    'user' => $user,
                    'token' => $token,
                ],
                'Login successful',
                200
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                $e->errors()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Login failed',
                500,
                ['error' => $e->getMessage()]
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
                    'User not authenticated',
                    401,
                    ['error' => 'User not authenticated']
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
                'Logout failed',
                500,
                ['error' => $e->getMessage()]
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
                    'Not authenticated',
                    401,
                    ['error' => 'User not authenticated']
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
                'Failed to retrieve user',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Change user password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    'Not authenticated',
                    401,
                    ['error' => 'User not authenticated']
                );
            }

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // Check if current password is correct
            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    ['current_password' => ['The current password is incorrect.']]
                );
            }

            // Check if new password is different from current password
            if (Hash::check($validated['new_password'], $user->password)) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    ['new_password' => ['The new password must be different from the current password.']]
                );
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            // Log password change
            $this->loggingService->logPasswordChange($user->username, true, 'Password changed successfully');

            return $this->successResponse(
                [],
                'Password changed successfully',
                200
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                $e->errors()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Password change failed',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}
