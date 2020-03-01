<?php

namespace App\Http\Controllers;

use App\Models\Canary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller {
    public function __construct() {
    }

    public function listUsers(Request $req) {

        $users = User::all();

        if (($req->has('limit') && !is_numeric($req->input('limit'))) || ($req->has('offset') && !is_numeric($req->input('offset')))) {
            return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
        }

        if ($req->has('limit') && ((int) $req->input('limit') < 1)) {
            return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
        }

        if ($req->has('offset') && ((int) $req->input('offset') < 0)) {
            return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
        }

        if ($req->has('limit') && $req->has('offset')) {
            if ((int) $req->input('offset') + (int) $req->input('limit') > $users->count()) {
                return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
            }
            $users = $users->slice((int) $req->input('offset'), (int) $req->input('limit'));
        } else if ($req->has('limit')) {
            $users = $users->slice(0, (int) $req->input('limit'));
        } else if ($req->has('offset')) {
            return response()->json(['code' => 2, 'message' => 'Invalid range', 'details' => 'Offset cannot be used without limit'], 400);
        }

        $response = [];
        foreach ($users as $u) {
            $canaries = Canary::where('assignee', $u->uuid)->pluck('uuid')->toArray();
            array_push($response, array_merge(
                $u->toArray(),
                [
                    'canaries' => $canaries,
                ]
            ));
        }

        return response()->json(['users' => $response]);
    }

    public function createUsers(Request $req) {
        $rules = [
            'username' => 'required|unique:users|max:100',
            'password' => 'required|min:8',
        ];

        if (!$req->has('users')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No users supplied'], 400);
        }

        foreach ($req->input('users') as $u) {
            $validator = Validator::make($u, $rules);
            if ($validator->fails()) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
            }
        }

        $response = [];

        foreach ($req->input('users') as $u) {
            $user = new User(['username' => $u['username']]);
            $user->password = Hash::make($u['password']);
            $user->permissions = $u['permissions'];
            $user->save();
            array_push($response, $user);
        }

        return response()->json(['users' => $response]);
    }

    public function getUser(Request $req, $uuid) {
        $me = JWTAuth::user();
        if (strcmp($me->uuid, $uuid) && (!isset($me->permissions) || !in_array("admin", $me->permissions))) {
            return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
        }

        $u = User::where('uuid', $uuid)->first();
        if (empty($u)) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'User does not exist'], 400);
        }

        $canaries = Canary::where('assignee', $u->uuid)->pluck('uuid')->toArray();

        return response()->json(array_merge(
            $u->toArray(),
            [
                'canaries' => $canaries,
            ]
        ));
    }

    public function modifyUser(Request $req, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (empty($user)) {
            return abort(400, "no such user");
        }

        $rules = [
            'login' => 'unique:users|max:100',
            'password' => 'min:8',
        ];
        $this->validate($req, $rules);

        if ($req->has('login')) {
            $user->login = $req->input('login');
        }

        if ($req->has('password')) {
            $user->password = password_hash($req->input('password'), PASSWORD_DEFAULT);
        }

        if ($req->has('permissions')) {
            $user->permissions = $req->input('permissions');
        }

        $user->save();

        return response($user);
    }

    public function deleteUser(Request $req, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (empty($user)) {
            return abort(400, "no such user");
        }

        $user->delete();

        return response(['status' => "ok"]);
    }

    public function refresh(Request $req) {

    }
}
