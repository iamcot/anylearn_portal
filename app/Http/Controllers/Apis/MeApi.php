<?php

namespace App\Http\Controllers\Apis;

use App\Constants\OrderConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
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
    public function admitStudentAPI(Request $request,$id)
    {
            $user = $request->get('_user');
            $data = OrderDetail::select(
                'items.id as itemId',
                'items.date_start',
                'items.price',
                'items.title',
                'items.short_content',
                'items.image as iimage',
                'users.image as uimage',
                'users.introduce',
                'users.name',
                'users.id as userId',
                'users.phone',
                'users.email',
                'users.address',
                'users.dob',
                'order_details.created_at',
                DB::raw('(SELECT count(*) FROM participations
            WHERE participations.participant_user_id = users.id AND participations.item_id = order_details.item_id
            GROUP BY participations.item_id
            ) AS confirm_count'),
            )
                ->join('users', 'users.id', '=', 'order_details.user_id')
                ->join('items', 'items.id', '=', 'order_details.item_id')
                ->where('order_details.status', OrderConstants::STATUS_DELIVERED)
                ->where('order_details.id', $id)
                ->where('items.user_id', $user->id)
                ->first();

        $userData = [
            'uimage' => $data->uimage,
            'introduce' => $data->introduce,
            'name' => $data->name,
            'userId' => $data->userId,
            'phone' => $data->phone,
            'email' => $data->email,
            'address' => $data->address,
            'dob' => $data->dob,
        ];

        $itemData = [
            'itemId' => $data->itemId,
            'date_start' => $data->date_start,
            'price' => $data->price,
            'title' => $data->title,
            'short_content' => $data->short_content,
            'iimage' => $data->iimage,
            'created_at' => $data->created_at,
            'confirm_count' => $data->confirm_count,
        ];

        return response()->json([
            'user' => $userData,
            'item' => $itemData,
        ]);

    }
}
