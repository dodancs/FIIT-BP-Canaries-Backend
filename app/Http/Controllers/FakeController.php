<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

class FakeController extends Controller
{

    private $admin = [
        'uuid' => '5757ecea-24e5-44b1-b92f-65c2fd0458ac',
        'permissions' => ['admin'],
        'canaries' => [],
        'created_at' => '2020-02-19 08:46:28',
        'updated_at' => '2020-02-19 08:46:28'
    ];
    private $admin_token = [
        'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
        'token_type' => 'bearer',
        'expires' => 3600,
    ];
    private $worker = [
        'uuid' => '6c94847a-1a74-4143-b411-f059e1ca2445',
        'permissions' => ['worker'],
        'canaries' => ['731a4675-60c1-48f5-82fc-459f4237e154'],
        'created_at' => '2020-02-19 11:33:01',
        'updated_at' => '2020-02-19 11:33:01'
    ];
    private $worker_token = [
        'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
        'token_type' => 'bearer',
        'expires' => 3600,
    ];

    public function __construct()
    {
    }

    public function login(Request $req)
    {
        if ($req->has('username') && $req->has('password')) {
            if (!strcmp($req->input('username'), 'admin') && !strcmp($req->input('password'), 'admin')) {
                return response()->json(array_merge($this->admin_token, $this->admin));
            }
            if (!strcmp($req->input('username'), 'worker') && !strcmp($req->input('password'), 'worker')) {
                return response()->json(array_merge($this->worker_token, $this->worker));
            }

            return response()->json(['code' => 0, 'message' => 'Bad request', 'details' => 'Invalid credentials'], 400);
        } else {
            return response()->json(['code' => 0, 'message' => 'Bad request', 'details' => 'Credentials not supplied'], 400);
        }
    }

    public function logout(Request $req)
    {
        return response(null, 200);
    }

    public function users(Request $req, $uuid = null)
    {
        if ($uuid && !strcmp($this->admin['uuid'], $uuid)) {
            return response()->json($this->admin);
        }
        if ($uuid && !strcmp($this->worker['uuid'], $uuid)) {
            return response()->json($this->worker);
        }
        if ($uuid) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'User does not exist'], 400);
        }

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
            if ((int) $req->input('offset') + (int) $req->input('limit') > 2) {
                return response()->json(['code' => 2, 'message' => 'Invalid range'], 400);
            }
            if (((int) $req->input('limit') == 1) && ((int) $req->input('offset') == 1)) {
                return response()->json([
                    'count' => 1,
                    'total' => 2,
                    'offset' => 1,
                    'users' => [
                        $this->worker
                    ]
                ]);
            } else {
                return response()->json([
                    'count' => 1,
                    'total' => 2,
                    'offset' => 0,
                    'users' => [
                        $this->admin
                    ]
                ]);
            }
        } else if ($req->has('limit') && ((int) $req->input('limit') == 1)) {
            return response()->json([
                'count' => 1,
                'total' => 2,
                'offset' => 0,
                'users' => [
                    $this->admin
                ]
            ]);
        } else if ($req->has('offset')) {
            return response()->json(['code' => 2, 'message' => 'Invalid range', 'details' => 'Offset cannot be used without limit'], 400);
        }

        return response()->json([
            'count' => 2,
            'total' => 2,
            'offset' => 0,
            'users' => [
                $this->admin,
                $this->worker
            ]
        ]);
    }
}
