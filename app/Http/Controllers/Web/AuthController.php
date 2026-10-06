<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\LoginRequest;

use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        $request->session()->regenerate();

        return $this->successResponse(Auth::user(), 'Logged in successfully.', 200);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successResponse(null);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse($request->user());
    }
}