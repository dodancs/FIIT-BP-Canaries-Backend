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

        if ($req->has('uuid')) {
            $c = Canary::where('uuid', $req->input('uuid'))->first();
            if (empty($c)) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
            }

            if ($c->assignee != $me->uuid && (!isset($me->permissions) || (!in_array("admin", $me->permissions) && !in_array("expert", $me->permissions)))) {
                return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
            }

            return response()->json($c);
        } else if ($req->has('email')) {
            $c = Canary::where('email', $req->input('email'))->first();
            if (empty($c)) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
            }

            if ($c->assignee != $me->uuid && (!isset($me->permissions) || (!in_array("admin", $me->permissions) && !in_array("expert", $me->permissions)))) {
                return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
            }

            return response()->json($c);
        }

        if (isset($me->permissions) && in_array("admin", $me->permissions)) {
            $canaries = Canary::all();
        } else {
            $canaries = Canary::where('assignee', $me->uuid)->get();
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
            if ($canaries->count()) {
                $canaries = $canaries->slice((int) $req->input('offset'), (int) $req->input('limit'));
            }

        } else if ($req->has('limit')) {
            if ($canaries->count()) {
                $canaries = $canaries->slice(0, (int) $req->input('limit'));
            }

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

    public function getParameter(Request $req, $uuid, $parameter, $regen = false) {
        if (!in_array($parameter, array('username', 'firstname', 'lastname', 'birthday', 'sex', 'address', 'phone'))) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Parameter does not exist'], 400);
        }

        $c = Canary::where('uuid', $uuid)->first();
        if (empty($c)) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
        }

        $me = JWTAuth::user();

        if ($c->assignee != $me->uuid && (!isset($me->permissions) || !in_array("admin", $me->permissions))) {
            return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
        }

        $faker = Faker\Factory::create();

        $data = $c->data;

        if (!array_key_exists($parameter, $data) || $regen) {

            if (array_key_exists($parameter, $data)) {
                unset($data[$parameter]);
            }

            switch ($parameter) {
            case 'username':$data[$parameter] = $faker->userName();
                break;
            case 'firstname':$data[$parameter] = $faker->firstName();
                break;
            case 'lastname':$data[$parameter] = $faker->lastName();
                break;
            case 'birthday':$data[$parameter] = $faker->dateTimeThisCentury->format('Y-m-d');
                break;
            case 'sex':$data[$parameter] = $faker->boolean() ? 'female' : 'male';
                break;
            case 'address':$data[$parameter] = [
                    'street' => $faker->streetAddress(),
                    'city' => $faker->city(),
                    'postcode' => $faker->postcode(),
                    'state' => $faker->state(),
                ];
                break;
            case 'phone':$data[$parameter] = $faker->phoneNumber();
                break;
            }
            $c->update(['data' => $data]);
        }

        return response()->json([$parameter => $c->data[$parameter]], 200);

    }

    public function regenParameter(Request $req, $uuid, $parameter) {
        return $this->getParameter($req, $uuid, $parameter, true);
    }

    public function deleteParameter(Request $req, $uuid, $parameter) {
        $c = Canary::where('uuid', $uuid)->first();
        if (empty($c)) {
            return response(null, 200);
        }

        $me = JWTAuth::user();

        if ($c->assignee != $me->uuid && (!isset($me->permissions) || !in_array("admin", $me->permissions))) {
            return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
        }

        $data = $c->data;

        if (array_key_exists($parameter, $data)) {
            unset($data[$parameter]);
            $c->update(['data' => $data]);
        }

        return response(null, 200);
    }

    public function add(Request $req) {
        $faker = Faker\Factory::create();

        $password_strenght = 'random';

        if (!$req->has('domain')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No domain supplied'], 400);
        }

        $site = null;
        if ($req->has('site')) {
            //return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No site supplied'], 400);
            $site = $req->input('site');
            if (empty($req->input('site'))) {
                $site = null;
            }
        }

        if (!$req->has('testing')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Testing vs. production not specified'], 400);
        }
        if (!$req->has('count')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No count supplied'], 400);
        }

        $rules = [
            'domain' => 'required|exists:App\Models\Domain,uuid',
            'site' => 'nullable|exists:App\Models\Site,uuid',
            'testing' => 'required|boolean',
            'count' => 'required|integer',
            'password_strength' => 'in:dictionary,simple,random,strong,trivial',
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
            } else if (array_key_exists('password_strength', $e->response->original)) {
                $message = $e->response->original['password_strength'][0];
            }
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $message], 400);
        }

        if ($req->has('password_strength')) {
            $password_strength = $req->input('password_strength');
        }

        $domain = Domain::where('uuid', $req->input('domain'))->first();

        // Default random function
        $password_generator = function () {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 8; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }

            return $randomString;
        };

        // Setup password generation method
        switch ($password_strength) {
        case "dictionary":
            $password_generator = function () {
                $topPassWords = file(__DIR__ . '/../../../resources/passwords.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
                return $topPassWords[rand(0, count($topPassWords) - 1)];
            };
            break;
        case "simple":
            $password_generator = function () {
                $words = file(__DIR__ . '/../../../resources/words.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                $password = $words[rand(0, count($words) - 1)];
                if (rand(0, 100) <= 70) {$password .= $words[rand(0, count($words) - 1)];}
                $password .= rand(0, 9);
                if (rand(0, 100) <= 50) {$password .= rand(0, 9);}
                if (rand(0, 100) <= 10) {$password .= rand(0, 9);}
                return $password;
            };
            break;
        case "trivial":
            $password_generator = function () {
                $words = file(__DIR__ . '/../../../resources/words.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                $password = $words[rand(0, count($words) - 1)];
                $password .= rand(0, 9);
                if (rand(0, 100) <= 50) {$password .= rand(0, 9);}
                return $password;
            };
            break;
        }

        $response = [];

        for ($i = 0; $i < $req->input('count'); $i++) {
            $username = $faker->userName();
            $email = $username . '@' . $domain->domain;

            $canary = new Canary(['domain' => $req->input('domain'), 'site' => $req->input('site'), 'testing' => $req->input('testing'), 'email' => $email, 'password' => ($password_strength == "strong" ? $faker->password(13, 18) : $password_generator()), 'data' => [
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

    public function update(Request $req, $uuid) {
        $c = Canary::where('uuid', $uuid)->first();
        if (empty($c)) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Canary does not exist'], 400);
        }

        $rules = [
            'site' => 'nullable|exists:App\Models\Site,uuid',
            'testing' => 'boolean',
            'setup' => 'boolean',
            'assignee' => 'nullable|exists:App\Models\User,uuid',
        ];

        try {
            $this->validate($req, $rules);
        } catch (Exception $e) {
            $message = "";
            if (array_key_exists('assignee', $e->response->original)) {
                $message = $e->response->original['assignee'][0];
            } else if (array_key_exists('site', $e->response->original)) {
                $message = $e->response->original['site'][0];
            } else if (array_key_exists('testing', $e->response->original)) {
                $message = $e->response->original['testing'][0];
            } else if (array_key_exists('setup', $e->response->original)) {
                $message = $e->response->original['setup'][0];
            }
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $message], 400);
        }

        $me = JWTAuth::user();

        if ($req->has('assignee') | $req->has('site') | $req->has('testing')) {
            if (!isset($me->permissions) || !in_array("admin", $me->permissions)) {
                return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
            }

            if ($req->has('assignee')) {
                $c->assignee = $req->input('assignee');
                if (empty($req->input('assignee'))) {
                    $c->assignee = null;
                }
            }
            if ($req->has('site')) {
                $c->site = $req->input('site');
                if (empty($req->input('site'))) {
                    $c->site = null;
                }
            }
            if ($req->has('testing')) {
                $c->testing = $req->input('testing');
            }

        }
        if ($req->has('setup')) {
            if ($c->assignee != $me->uuid && (!isset($me->permissions) || !in_array("admin", $me->permissions))) {
                return response()->json(['code' => 1, 'message' => 'Unauthorized'], 401);
            }
            $c->setup = $req->input('setup');
        }

        $c->save();

    }

}
