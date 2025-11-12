<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::USER,
        ]);

        Auth::login($user);

        return response()->json([
            'data' => [
                'user' => $user,
                'message' => 'Registration successful.',
            ],
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
            'device_name' => ['sometimes','string'],
        ]);

        if (! auth()->attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'data' => null,
                'errors' => ['email' => ['The provided credentials are incorrect.']],
                'meta' => null,
            ], 401);
        }

        $user  = auth()->user();

        // ✅ Sanctum token
        $token = $user->createToken($request->input('device_name', 'api'), ['*'])->plainTextToken;

        return response()->json([
            'data' => [
                'user'    => $user,
                'token'   => $token,
                'message' => 'Login successful.',
            ],
            'errors' => null,
            'meta'   => null,
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
                'user' => $request->user(),
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
