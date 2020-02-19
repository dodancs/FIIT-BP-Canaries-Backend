<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
	public function __construct() {
	}

	public function listUsers(Request $req) {
		$users = User::all();

		return response()->json(['users'=>$users]);
	}

	public function createUser(Request $req) {
		$rules = [
			'login' => 'required|unique:users|max:100',
			'password' => 'required|min:8',
		];

		$this->validate($req, $rules);

		// create new user
		$user = new User($req->only(['login','permissions']));
		$user->password = password_hash($req->password, PASSWORD_DEFAULT);
		$user->save();

		return response($user);
	}

	public function getUser($uuid) {
		$user = User::where('uuid',$uuid)->first();
		if (empty($user))
			return abort(400, "no such user");

		return response($user);
	}

	public function modifyUser(Request $req, $uuid) {
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
                        $user->password = password_hash($req->input('password'),PASSWORD_DEFAULT);

                if ($req->has('permissions'))
                        $user->permissions = $req->input('permissions');

                $user->save();

		return response($user);
	}

	public function deleteUser($uuid) {
		$user = User::where('uuid',$uuid)->first();
		if (empty($user))
			return abort(400, "no such user");

		$user->delete();

		return response(['status'=>"ok"]);
	}
}
