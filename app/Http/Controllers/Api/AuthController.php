<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $userRole = Role::where('slug', 'user')->first();
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
        ]);

        Auth::login($user);

        return response()->json([
            'data' => [
                'user' => $user->load('role'),
                'message' => 'Registration successful.',
            ],
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return response()->json([
                'data' => null,
                'errors' => ['email' => ['The provided credentials are incorrect.']],
                'meta' => null,
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'data' => [
                'user' => Auth::user()->load('role'),
                'message' => 'Login successful.',
            ],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'data' => ['message' => 'Logged out successfully.'],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => $request->user()->load('role'),
            ],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            return response()->json([
                'data' => null,
                'errors' => ['email' => [__($status)]],
                'meta' => null,
            ], 422);
        }

        return response()->json([
            'data' => ['message' => __($status)],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return response()->json([
                'data' => null,
                'errors' => ['email' => [__($status)]],
                'meta' => null,
            ], 422);
        }

        return response()->json([
            'data' => ['message' => __($status)],
            'errors' => null,
            'meta' => null,
        ]);
    }
}
