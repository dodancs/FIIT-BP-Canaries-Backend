<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class Permissions {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $required_perms) {
		//  everyone is permitted
		if ($required_perms == '')
			return $next($request);

		// make sure this user has permission as required
		$me = JWTAuth::user();
		if (empty($me))
			return abort(401, "authentication required");
		if (!isset($me->permissions))
			return abort(403, "forbidden");

		// normalize input
		$required_perms = explode(';', $required_perms);

		// success, if we have any of the required permissiosn
		foreach ($required_perms as $perm) {
			if (array_key_exists($perm, $me->permissions) && $me->permissions[$perm] == 1) {
				return $next($request);
			}
		}

		return abort(403, "forbidden");
	}
}
