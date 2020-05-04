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
        $rules = [
            'site' => 'required|unique:sites',
        ];

        $validator = Validator::make($req->all(), $rules, ['unique' => 'The site \':input\' has already been added.']);
        if ($validator->fails()) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
        }

        $site = new Site(['site' => $req->input('site')]);
        $site->save();

        return response()->json($site);

    }

    public function getOne(Request $req, $uuid) {
        $site = Site::where('uuid', $uuid)->first();
        if (!empty($site)) {
            return response()->json([$uuid => $site->site]);
        }

        return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Monitored site does not exist'], 400);
    }

    public function delete(Request $req, $uuid) {
        $site = Site::where('uuid', $uuid)->first();
        if (!empty($site)) {
            $site->delete();
        }

        return response(null, 200);
    }

}
