<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $this->validate($request, [
            'username' => 'required|max:100',
            'password' => 'required',
        ]);

        if (!$token = JWTAuth::attempt($request->only('username', 'password'))) {
            return response()->json(['error' => ['message' => 'invalid credentials', 'status_code' => 401]], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires' => config('jwt.refresh_ttl'),
            'uuid' => JWTAuth::user()->uuid,
            'permissions' => JWTAuth::user()->permissions,
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response()->json(['message' => 'success']);
    }

    public function refresh(Request $req)
    {
        $token = JWTAuth::refresh();
        return response()->json(['token' => $token]);
    }

    public function me()
    {
        $me = JWTAuth::user();
        $token = JWTAuth::payload();
        return response()->json(['me' => $me, 'token' => $token]);
    }
}
