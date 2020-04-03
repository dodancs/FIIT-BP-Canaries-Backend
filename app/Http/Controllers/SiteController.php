<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller {
    public function __construct() {
    }

    public function get(Request $req) {
        $sites = Site::all();
        $totalCount = $sites->count();

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
            if ($sites->count()) {
                $sites = $sites->slice((int) $req->input('offset'), (int) $req->input('limit'));
            }

        } else if ($req->has('limit')) {
            if ($sites->count()) {
                $sites = $sites->slice(0, (int) $req->input('limit'));
            }

        } else if ($req->has('offset')) {
            return response()->json(['code' => 2, 'message' => 'Invalid range', 'details' => 'Offset cannot be used without limit'], 400);
        }

        return response()->json([
            'count' => $req->has('limit') ? (int) $req->input('limit') : $totalCount,
            'total' => $totalCount,
            'offset' => $req->has('offset') ? (int) $req->input('offset') : 0,
            'sites' => $sites,
        ]);

    }

    public function add(Request $req) {
        if (!$req->has('sites')) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'No sites supplied'], 400);
        }

        $rules = [
            'site' => 'required|unique:sites',
        ];

        foreach ($req->input('sites') as $s) {
            $validator = Validator::make(['site' => $s], $rules);
            if ($validator->fails()) {
                return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Site \'' . $s . '\' already exists'], 400);
            }
        }

        $response = [];

        foreach ($req->input('sites') as $s) {
            $site = new Site(['site' => $s]);
            $site->save();
            array_push($response, $site);
        }

        return response()->json(['sites' => $response]);

    }

    public function delete(Request $req, $uuid) {
        $site = Site::where('uuid', $uuid)->first();
        if (!empty($site)) {
            $site->delete();
        }

        return response(null, 200);
    }

}
