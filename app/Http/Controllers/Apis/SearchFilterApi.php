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
        $data['lastSearch'] = Spm::where('spmc', 'search')
            ->whereNotNull('extra')
            ->where('ip', $request->ip())
            ->select(DB::raw('extra, max(created_at) as created_at'))
            ->groupBy('extra')
            ->orderByDesc('created_at')
            ->take(6)
            ->pluck('extra');

        $data['provinces'] = Province::whereIn('name', ['Hà Nội', 'Hồ Chí Minh'])
            ->select('name as label', 'code as value')
            ->orderBy('name')
            ->get();

        $data['categories'] = Category::select('id', 'title')
            ->orderBy('title')
            ->get();

        if ($request->get('_user')) {  

            $data['lastSearch'] = Spm::where('spmc', 'search')
                ->whereNotNull('extra')
                ->where('user_id', $request->get('_user')->id)
                ->select(DB::raw('extra, max(created_at) as created_at'))
                ->groupBy('extra')
                ->orderByDesc('created_at')
                ->take(6)
                ->pluck('extra');
        }

        return response()->json($data);
    }
}
