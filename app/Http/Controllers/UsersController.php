<?php

namespace App\Http\Controllers;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

	public function listUsers() {
	
		return response()->json([
			'mockup' => 'yes'
		]);
	}
    //
}
