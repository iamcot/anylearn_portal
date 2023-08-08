<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DashboardServices;
use Illuminate\Support\Facades\DB;

class MeApi extends Controller
{
    public function index(Request $request)
    {
        $dashServ = new DashboardServices();
        $user = $request->get('_user');
        $dashServ->init(@request('dateF') ?? date('Y-m-d', strtotime('-30 days')), @request('dateT') ?? date('Y-m-d'));

        $query = DB::table('order_details')
        ->whereNotNull('created_at');

        $input = $request->all();

        if ($request->input('filter')) {
            if ($input['dateF']) {
                $query = $query->where('created_at', '>=', $input['dateF']);
            }
            if ($input['dateT']) {
                $query = $query->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($input['dateT'])));
            }
        } else {
            $query = $query->where('created_at', '>=', date('Y-m-d', strtotime('-30 days')));
        }

        $results = $query->selectRaw('DATE(created_at) AS day, sum(order_details.unit_price) AS num')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get();

        $chartDataset = [
            'labels' => [],
            'data' => []
        ];

        foreach ($results as $row) {
            $chartDataset['labels'][] = date('d/m', strtotime($row->day));
            $chartDataset['data'][] = $row->num;
        }

        return response()->json([
            'totalRevenue' => $dashServ->gmvpartnerAPI(true,$user),
            'revenueInPeriod' => $dashServ->gmvpartnerAPI(false,$user),
            'totalStudents' => $dashServ->userCountpanertAPI(true,$user),
            'studentsInPeriod' => $dashServ->userCountpanertAPI(false,$user),
            'chartDataset' => $chartDataset
        ]);
    }
}
