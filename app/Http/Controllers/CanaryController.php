<?php

namespace App\Http\Controllers;

use App\Models\Canary;
use App\Models\Domain;
use Exception;
use Faker;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CanaryController extends Controller {
    public function __construct() {
    }

    public function listCanaries(Request $req) {
        $me = JWTAuth::user();
        if (isset($me->permissions) && in_array("admin", $me->permissions)) {
            $canaries = Canary::all();
        } else {
            $canaries = Canary::where('assignee', $me->uuid);
        }
        $totalCount = $canaries->count();

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
            $canaries = $canaries->slice((int) $req->input('offset'), (int) $req->input('limit'));
        } else if ($req->has('limit')) {
            $canaries = $canaries->slice(0, (int) $req->input('limit'));
        } else if ($req->has('offset')) {
            return response()->json(['code' => 2, 'message' => 'Invalid range', 'details' => 'Offset cannot be used without limit'], 400);
        }

        return response()->json([
            'count' => $req->has('limit') ? (int) $req->input('limit') : $totalCount,
            'total' => $totalCount,
            'offset' => $req->has('offset') ? (int) $req->input('offset') : 0,
            'canaries' => $canaries,
        ]);

    }

    public function add(Request $req) {
        $faker = Faker\Factory::create();

        if (!$req->has('domain')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No domain supplied'], 400);
        }
        if (!$req->has('site')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No site supplied'], 400);
        }
        if (!$req->has('testing')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Testing vs. production not specified'], 400);
        }
        if (!$req->has('count')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No count supplied'], 400);
        }

        $rules = [
            'domain' => 'required|exists:App\Models\Domain,uuid',
            'site' => 'required|exists:App\Models\Site,uuid',
            'testing' => 'required|boolean',
            'count' => 'required|integer',
        ];

        try {
            $this->validate($req, $rules);
        } catch (Exception $e) {
            $message = "";
            if (array_key_exists('domain', $e->response->original)) {
                $message = $e->response->original['domain'][0];
            } else if (array_key_exists('site', $e->response->original)) {
                $message = $e->response->original['site'][0];
            } else if (array_key_exists('testing', $e->response->original)) {
                $message = $e->response->original['testing'][0];
            } else if (array_key_exists('count', $e->response->original)) {
                $message = $e->response->original['count'][0];
            }
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $message], 400);
        }

        $domain = Domain::where('uuid', $req->input('domain'))->first();

        $response = [];

        for ($i = 0; $i < $req->input('count'); $i++) {
            $username = $faker->userName();
            $email = $username . '@' . $domain->domain;

            $canary = new Canary(['domain' => $req->input('domain'), 'site' => $req->input('site'), 'testing' => $req->input('testing'), 'email' => $email, 'password' => $faker->password(), 'data' => [
                'username' => $username,
            ]]);

            $canary->save();
            array_push($response, $canary);
        }

        return response()->json(['canaries' => $response]);

    }

    public function delete(Request $req, $uuid) {
        $canary = Canary::where('uuid', $uuid)->first();
        if (!empty($canary)) {
            $canary->delete();
        }

        return response(null, 200);
    }

}
