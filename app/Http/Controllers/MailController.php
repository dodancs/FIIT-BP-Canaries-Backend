<?php

namespace App\Http\Controllers;

use App\Models\Canary;
use App\Models\Mail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MailController extends Controller {
    public function __construct() {
    }

    public function get(Request $req, $uuid) {
        $me = JWTAuth::user();

        $c = Canary::where('uuid', $uuid)->first();

        if (empty($c)) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
        }

        if ($c->assignee != $me->uuid && (!isset($me->permissions) || !in_array("admin", $me->permissions))) {
            return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
        }
        $mail = Mail::where('canary', $uuid)->get();

        return response()->json([
            'emails' => $mail,
        ]);

    }

}