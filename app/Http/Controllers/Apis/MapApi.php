<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Spm;
use App\Services\CommonServices;
use Illuminate\Support\Facades\DB;

class MapApi extends Controller
{
    public function index(Request $request)
    {
        // Get school list based on given bounds 
        $data = [];
        if ($request->get('top') && $request->get('bot')) {
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
                    'users.introduce',
                    'ul.longitude',
                    'ul.latitude'
                )
                ->get();
        }

        // Get bounds based on searched school list 
        if ($request->except(['top', 'bot'])) {
            $partnerIds = (new CommonServices)->getSearchResults($request)->pluck('id')->toArray();
            $data = new \stdClass();
            $data->schools = DB::table('users')
                ->join('user_locations as ul', 'ul.user_id', '=', 'users.id')
                ->where('users.role', 'school')
                ->whereIn('users.id', $partnerIds)
                ->select(
                    'users.id',
                    'users.name',
                    'users.image',
                    'users.introduce',
                    'ul.longitude',
                    'ul.latitude'
                )->get();

            $xMax = $xMin = $yMax = $yMin = 0;
            foreach ($data->schools as $key => $partner) {
                if ($key == 0) {
                    $xMin = $partner->longitude;
                    $yMin = $partner->latitude;
                }

                if ($partner->longitude > $xMax) {
                    $xMax = $partner->longitude;
                }

                if ($partner->longitude < $xMin) {
                    $xMin = $partner->longitude;
                }

                if ($partner->latitude > $yMax) {
                    $yMax = $partner->latitude;
                }

                if ($partner->latitude < $yMin) {
                    $yMin = $partner->latitude;
                }
            }

            $margin = 1;
            $data->bounds = [
                'top' => ($xMin - $margin) . ',' . ($yMax + $margin),
                'bot' => ($xMax + $margin) . ',' . ($yMin - $margin),
            ];
        }

        $spm = new Spm();
        $spm->addSpm($request);
        
        return response()->json($data);
    }
}


