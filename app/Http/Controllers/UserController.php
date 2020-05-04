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
            if ($users->count()) {
                $users = $users->slice((int) $req->input('offset'), (int) $req->input('limit'));
            }

        } else if ($req->has('limit')) {
            if ($users->count()) {
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
            'permissions.*' => 'required|in:admin,worker,expert',
        ];

        if (!$req->has('permissions') || count($req->input('permissions')) == 0) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No permissions supplied!'], 400);
        }

        $validator = Validator::make($req->all(), $rules, ['unique' => 'The username \':input\' has already been taken.']);
        if ($validator->fails()) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
        }

        if ($req->has('canaries')) {
            $validator = Validator::make($req->all(), ['canaries.*' => 'nullable|exists:App\Models\Canary,uuid']);
            if ($validator->fails()) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
            }
        }

        $me = JWTAuth::user();

        if ($req->has('canaries')) {
            foreach ($req->input('canaries') as $c) {
                $can = Canary::where('uuid', $c)->first();
                if (!empty($can) && ($can->assignee != null && $can->assignee != "")) {
                    return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary \'' . $c . '\' already has assignee'], 400);
                } else if (empty($can)) {
                    return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
                }
            }
        }

        $user = new User(['username' => $req->input('username')]);
        $user->password = Hash::make($req->input('password'));
        $user->permissions = $req->input('permissions');
        $user->updated_by = $me->uuid;
        $user->save();

        if ($req->has('canaries')) {
            foreach ($req->input('canaries') as $c) {
                $can = Canary::where('uuid', $c)->first();
                if (!empty($can)) {
                    $can->assignee = $user->uuid;
                    $can->save();
                }
            }
        }

        return response()->json(array_merge($user->toArray(), ['canaries' => $req->input('canaries') == null ? [] : $req->input('canaries')]));
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

        $rules = [
            'username' => 'unique:users|max:100',
            'password' => 'min:8',
            'permissions.*' => 'in:admin,worker,expert',
            'canaries.*' => 'nullable|exists:App\Models\Canary,uuid',
        ];

        $validator = Validator::make($req->all(), $rules, ['unique' => 'The username \':input\' has already been taken.']);
        if ($validator->fails()) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
        }

        if ($req->has('username')) {
            $user->username = $req->input('username');
        }

        if ($req->has('password')) {
            $user->password = Hash::make($req->input('password'));
        }

        if ($req->has('permissions')) {
            $user->permissions = $req->input('permissions');
        }

        if ($req->has('canaries')) {
            foreach ($req->input('canaries') as $c) {
                $can = Canary::where('uuid', $c)->first();
                if (!empty($can) && $can->assignee != null && $can->assignee != "" && $can->assignee != $user->uuid) {
                    return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary \'' . $c . '\' already has assignee (\'' . $can->assignee . '\')'], 400);
                } else if (empty($can)) {
                    return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
                }
            }

            // unassign all canaries for that user
            $canaries = Canary::where('assignee', $user->uuid)->get();
            foreach ($canaries as $c) {
                $c->assignee = null;
                $c->save();
            }

            foreach ($req->input('canaries') as $c) {
                $can = Canary::where('uuid', $c)->first();
                $can->assignee = $user->uuid;
                $can->save();
            }
        }

        $me = JWTAuth::user();
        $user->updated_by = $me->uuid;

        $user->save();

        return response(null, 200);
    }

    public function deleteUser(Request $req, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (!empty($user)) {
            // unassign all canaries for that user
            $canaries = Canary::where('assignee', $user->uuid)->get();
            foreach ($canaries as $c) {
                $c->assignee = null;
                $c->save();
            }
            $user->delete();
        }

        return response(null, 200);
    }

}
