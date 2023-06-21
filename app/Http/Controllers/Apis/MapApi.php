<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Spm;
use App\Models\UserLocation;
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
        if ($request->except(['top', 'bot'])) {$data = new \stdClass();
            $data    = new \stdClass();
            $schools = (new CommonServices)->getSearchResults($request, true)->get();

            $mapBounds = [];
            foreach ($schools as $val) {
                $school = $val;
                $school->locations = UserLocation::whereIn('id', explode(',', $val->locations))
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->get();
                $data->schools[] = $school;
                $mapBounds = array_merge($mapBounds, $school->locations->toArray()); 
            }

            $xMax = $xMin = $yMax = $yMin = 0;
            foreach ($mapBounds as $key => $val) {
                $longitude = $val['longitude'];
                $latitude  = $val['latitude'];

                if ($key == 0) {
                    $xMin = $longitude;
                    $yMin = $latitude;
                }

                if ($longitude > $xMax) {
                    $xMax = $longitude;
                }

                if ($longitude < $xMin) {
                    $xMin = $longitude;
                }

                if ($latitude > $yMax) {
                    $yMax = $latitude;
                }

                if ($latitude < $yMin) {
                    $yMin = $latitude;
                }
            }

            $margin = 2;
            $data->mapBounds = [
                'top' => ($xMin - $margin) . ',' . ($yMax + $margin),
                'bot' => ($xMax + $margin) . ',' . ($yMin - $margin),
            ];
        }

        $spm = new Spm();
        $spm->addSpm($request);
        
        return response()->json($data);
    }
}


