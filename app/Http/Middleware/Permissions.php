<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class Permissions
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $required_perms)
	{
		// everyone is permitted
		if ($required_perms == '')
			return $next($request);

		// make sure this user has permission as required
		$me = JWTAuth::user();
		if (empty($me))
			return abort(401, "authentication required");
		if (!isset($me->permissions))
			return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);

		// normalize input
		$required_perms = explode(';', $required_perms);

		// success, if we have any of the required permissiosn
		foreach ($required_perms as $perm) {
			if (in_array($perm, $me->permissions)) {
				return $next($request);
			}
		}

		return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
	}
}
