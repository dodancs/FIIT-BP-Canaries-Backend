<?php

namespace App\Http\Controllers;

use App\Models\Canary;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller {
    public function __construct() {
    }

    public function listUsers(Request $req) {

        $users = User::all();
        $totalCount = $users->count();

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
            if ((int) $req->input('offset') + (int) $req->input('limit') > $totalCount) {
                return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
            }
            if ($users->isNotEmpty()) {
                $users = $users->slice((int) $req->input('offset'), (int) $req->input('limit'));
            }

        } else if ($req->has('limit')) {
            if ($users->isNotEmpty()) {
                $users = $users->slice(0, (int) $req->input('limit'));
            }

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

        return response()->json([
            'count' => $req->has('limit') ? (int) $req->input('limit') : $totalCount,
            'total' => $totalCount,
            'offset' => $req->has('offset') ? (int) $req->input('offset') : 0,
            'users' => $response,
        ]);
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
            $validator = Validator::make($u, $rules, ['unique' => 'The username \':input\' has already been taken.']);
            if ($validator->fails()) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
            }
        }

        $me = JWTAuth::user();

        $response = [];

        foreach ($req->input('users') as $u) {
            $user = new User(['username' => $u['username']]);
            $user->password = Hash::make($u['password']);
            $user->permissions = $u['permissions'];
            $user->updated_by = $me->uuid;
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
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'User does not exist'], 400);
        }

        if ($req->has('username')) {
            $rules = [
                'username' => 'required|unique:users|max:100',
            ];
            try {
                $this->validate($req, $rules);
            } catch (Exception $e) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Username did not validate'], 400);
            }
            $user->username = $req->input('username');
        }

        if ($req->has('password')) {
            $rules = [
                'password' => 'required|min:8',
            ];
            try {
                $this->validate($req, $rules);
            } catch (Exception $e) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Password did not validate'], 400);
            }
            $user->password = Hash::make($req->input('password'));
        }

        if ($req->has('permissions')) {
            $permissions = $user->permissions; // old permissions
            $perms = []; // permissions to remove
            $perms_add = [];
            foreach ($req->input('permissions') as $perm => $val) {
                if (!$val) {
                    array_push($perms, $perm);
                } else {
                    array_push($perms_add, $perm);
                }
            }
            $user->permissions = array_merge(array_diff($permissions, $perms), $perms_add);
        }

        $me = JWTAuth::user();
        $user->updated_by = $me->uuid;

        $user->save();

        return response(null, 200);
    }

    public function deleteUser(Request $req, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (!empty($user)) {
            $user->delete();
        }

        return response(null, 200);
    }

}
