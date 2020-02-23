<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
	public function __construct()
	{
	}

	public function listUsers(Request $req)
	{

		$users = User::all();

		if (($req->has('limit') && !is_numeric($req->input('limit'))) || ($req->has('offset') && !is_numeric($req->input('offset')))) {
			return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
		}

		if ($req->has('limit') && ((int) $req->input('limit') == 0)) {
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

		$response = array();

		foreach ($users as $u) {
			array_push($response, [
				'uuid' => $u->uuid,
				'username' => $u->username,
				'permissions' => $u->permissions,
			]);
		}

		return response()->json(['users' => $response]);
	}

	public function createUser(Request $req)
	{
		$rules = [
			'login' => 'required|unique:users|max:100',
			'password' => 'required|min:8',
		];

		$this->validate($req, $rules);

		// create new user
		$user = new User($req->only(['login', 'permissions']));
		$user->password = password_hash($req->password, PASSWORD_DEFAULT);
		$user->save();

		return response($user);
	}

	public function getUser($uuid)
	{
		$user = User::where('uuid', $uuid)->first();
		if (empty($user))
			return abort(400, "no such user");

		return response($user);
	}

	public function modifyUser(Request $req, $uuid)
	{
		$user = User::where('uuid', $uuid)->first();
		if (empty($user))
			return abort(400, "no such user");

		$rules = [
			'login' => 'unique:users|max:100',
			'password' => 'min:8'
		];
		$this->validate($req, $rules);

		if ($req->has('login'))
			$user->login = $req->input('login');

		if ($req->has('password'))
			$user->password = password_hash($req->input('password'), PASSWORD_DEFAULT);

		if ($req->has('permissions'))
			$user->permissions = $req->input('permissions');

		$user->save();

		return response($user);
	}

	public function deleteUser($uuid)
	{
		$user = User::where('uuid', $uuid)->first();
		if (empty($user))
			return abort(400, "no such user");

		$user->delete();

		return response(['status' => "ok"]);
	}
}
