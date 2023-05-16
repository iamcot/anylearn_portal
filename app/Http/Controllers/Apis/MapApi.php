<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MapApi extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        if ($request->all()) {
            $top = explode(',', $request->get('top'));
            $bot = explode(',', $request->get('bot'));
            
            $data = DB::table('users')
                ->join('user_locations as ul', 'ul.user_id', '=', 'users.id')
                ->where('users.role', 'school')
                ->whereBetween('ul.longitude', [$top[0], $bot[0]])
                ->whereBetween('ul.latitude', [$bot[1], $top[1]])
                ->select(
                    'users.id',
                    'users.name',
                    'users.image',
                    'users.introduce'
                )
                ->get();
                //status

        }
        return response()->json($data);
    }
}


