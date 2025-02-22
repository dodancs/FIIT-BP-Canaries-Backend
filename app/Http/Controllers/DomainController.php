<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller {
    public function __construct() {
    }

    public function get(Request $req) {
        $domains = Domain::all();
        $totalCount = $domains->count();

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
            if ($domains->count()) {
                $domains = $domains->slice((int) $req->input('offset'), (int) $req->input('limit'));
            }

        } else if ($req->has('limit')) {
            if ($domains->count()) {
                $domains = $domains->slice(0, (int) $req->input('limit'));
            }

        } else if ($req->has('offset')) {
            return response()->json(['code' => 2, 'message' => 'Invalid range', 'details' => 'Offset cannot be used without limit'], 400);
        }

        return response()->json([
            'count' => $req->has('limit') ? (int) $req->input('limit') : $totalCount,
            'total' => $totalCount,
            'offset' => $req->has('offset') ? (int) $req->input('offset') : 0,
            'domains' => $domains,
        ]);

    }

    public function add(Request $req) {
        $rules = [
            'domain' => 'required|unique:domains',
        ];

        $validator = Validator::make($req->all(), $rules, ['unique' => 'The domain \':input\' has already been added.']);
        if ($validator->fails()) {
            return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => $validator->errors()->first()], 400);
        }

        $domain = new Domain(['domain' => $req->input('domain')]);
        $domain->save();

        return response()->json($domain);

    }

    public function getOne(Request $req, $uuid) {
        $domain = Domain::where('uuid', $uuid)->first();
        if (!empty($domain)) {
            return response()->json([$uuid => $domain->domain]);
        }

        return response()->json(['code' => 2, 'message' => 'Bad request', 'details' => 'Domain does not exist'], 400);
    }

    public function delete(Request $req, $uuid) {
        $domain = Domain::where('uuid', $uuid)->first();
        if (!empty($domain)) {
            $domain->delete();
        }

        return response(null, 200);
    }

}
