<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

class FakeController extends Controller
{
    public function __construct()
    {
    }

    public function login(Request $req)
    {
        if ($req->has('admin')) return response()->json([
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
            'token_type' => 'bearer',
            'expires' => 3600,
            'uuid' => '5757ecea-24e5-44b1-b92f-65c2fd0458ac',
            'permissions' => ['admin'],
            'canaries' => []
        ]);
        return response()->json([
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
            'token_type' => 'bearer',
            'expires' => 3600,
            'uuid' => '6c94847a-1a74-4143-b411-f059e1ca2445',
            'permissions' => ['worker'],
            'canaries' => ['731a4675-60c1-48f5-82fc-459f4237e154']
        ]);
    }

    public function logout(Request $req)
    {
        return response(null, 200);
    }

    public function users(Request $req)
    {

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
                        [
                            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
                            'token_type' => 'bearer',
                            'expires' => 3600,
                            'uuid' => '6c94847a-1a74-4143-b411-f059e1ca2445',
                            'permissions' => ['worker'],
                            'canaries' => ['731a4675-60c1-48f5-82fc-459f4237e154']
                        ]
                    ]
                ]);
            } else {
                return response()->json([
                    'count' => 1,
                    'total' => 2,
                    'offset' => 0,
                    'users' => [
                        [
                            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
                            'token_type' => 'bearer',
                            'expires' => 3600,
                            'uuid' => '5757ecea-24e5-44b1-b92f-65c2fd0458ac',
                            'permissions' => ['admin'],
                            'canaries' => []
                        ]
                    ]
                ]);
            }
        } else if ($req->has('limit') && ((int) $req->input('limit') == 1)) {
            return response()->json([
                'count' => 1,
                'total' => 2,
                'offset' => 0,
                'users' => [
                    [
                        'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
                        'token_type' => 'bearer',
                        'expires' => 3600,
                        'uuid' => '5757ecea-24e5-44b1-b92f-65c2fd0458ac',
                        'permissions' => ['admin'],
                        'canaries' => []
                    ]
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
                [
                    'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
                    'token_type' => 'bearer',
                    'expires' => 3600,
                    'uuid' => '5757ecea-24e5-44b1-b92f-65c2fd0458ac',
                    'permissions' => ['admin'],
                    'canaries' => []
                ],
                [
                    'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvdjFcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTgzMDA4OTI1LCJleHAiOjE1ODMwMTI1MjUsIm5iZiI6MTU4MzAwODkyNSwianRpIjoiQUdGVGlYRFVkMWZaaHRGSCIsInN1YiI6IjlkOGFiMmQxLTUxYTctNDg5Ny1hY2Q4LWI1NmQ0MTAxZjQ5MyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.c1sw6yFBFx36QqqEd9EXwlGziYQ5k6_AL4wgWJBOsDY',
                    'token_type' => 'bearer',
                    'expires' => 3600,
                    'uuid' => '6c94847a-1a74-4143-b411-f059e1ca2445',
                    'permissions' => ['worker'],
                    'canaries' => ['731a4675-60c1-48f5-82fc-459f4237e154']
                ]
            ]
        ]);
    }
}
