<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Spm;
use Illuminate\Support\Facades\DB;
use Vanthao03596\HCVN\Models\Province;

class SearchFilterApi extends Controller
{
    public function index(Request $request, $role = 'guest')
    {
        $data['searcheds'] = Spm::where('spmc', 'search')
            ->select(DB::raw('extra, max(created_at) as created_at'))
            ->groupBy('extra')
            ->orderByDesc('created_at')
            ->pluck('extra');

        $data['provinces'] = Province::select('code', 'name')->orderBy('name')->get();
        $data['categories'] = Category::select('id', 'title')->orderBy('title')->get();

        if($role == 'member') {
            $user = $request->get('_user');
            $data['searcheds'] = Spm::where('spmc', 'search')
            ->where('user_id', $user->id)
            ->select(DB::raw('extra, max(created_at) as created_at'))
            ->groupBy('extra')
            ->orderByDesc('created_at')
            ->pluck('extra');
        }

        return response()->json($data);
    }
}
