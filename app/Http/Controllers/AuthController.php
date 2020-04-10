<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Canary;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller {

    public function login(Request $request) {

        $this->validate($request, [
            'username' => 'required|max:100',
            'password' => 'required',
        ]);

        if (!$token = JWTAuth::attempt($request->only('username', 'password'))) {
            return response()->json(['code' => 0, 'message' => 'Bad request', 'details' => 'Invalid credentials'], 400);
        }

        $me = JWTAuth::user();

        $canaries = Canary::where('assignee', $me->uuid)->pluck('uuid')->toArray();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires' => config('jwt.refresh_ttl'),
            'uuid' => $me->uuid,
            'permissions' => $me->permissions,
            'canaries' => $canaries,
        ]);
    }

    public function logout() {
        JWTAuth::invalidate();
        return response()->json(['message' => 'success']);
    }

    public function refresh(Request $req) {
        $token = JWTAuth::refresh();
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires' => config('jwt.refresh_ttl'),
        ]);
    }

    public function me() {
        $me = JWTAuth::user();
        $token = JWTAuth::payload();
        return response()->json(['me' => $me, 'token' => $token]);
    }
}
