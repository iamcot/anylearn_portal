<?php

namespace App\Http\Controllers\Apis;

use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\UserLocation;
use App\Services\DashboardServices;
use App\Services\UserServices;
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
        $query = $query->where('created_at', '>=', date('Y-m-d', strtotime('-30 days')));
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
            'totalRevenue' => $dashServ->gmvpartnerAPI(true, $user),
            'revenueInPeriod' => $dashServ->gmvpartnerAPI(false, $user),
            'totalStudents' => $dashServ->userCountpanertAPI(true, $user),
            'studentsInPeriod' => $dashServ->userCountpanertAPI(false, $user),
            'chartDataset' => $chartDataset
        ]);
    }
    public function admitStudentAPI(Request $request, $id)
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
        if (!$data) {
            return response()->json(['message' => 'Invalid request'], 400);
        }
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
    public function getChildAccountsAPI(Request $request)
    {
        $userService = new UserServices();
        $user = $request->get('_user');
        $id = $user->id;

        $childuser = User::where('user_id', $id)->where('is_child', 1)->get();

        $orderStats = $userService->orderStats($user->id);

        $responseData = [
            'orderStats' => $orderStats,
            'childuser' => $childuser,
            'user' => $user
        ];

        return response()->json($responseData);
    }

    public function childAccountAPI(Request $request, $id = null)
    {
        $parent = $request->get('_user');

        if (!$parent) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $username = $request->get('username');
        $dob = $request->get('dob');
        $sex = $request->get('sex');
        $introduce = $request->get('introduce');

        $userC = User::find($id);

        if (!$userC) {
            $input = [
                'name' => $username,
                'dob' => $dob,
                'sex' => $sex,
                'introduce' => $introduce
            ];

            $userChild = new User();
            $data = $userChild->createChild($parent, $input);

            if ($data) {
                return response()->json(['message' => 'New child account created successfully'], 200);
            }
        } else {
            $userC->name = $username;
            $userC->dob = $dob;
            $userC->sex = $sex;
            $userC->introduce = $introduce;
            $userC->save();

            if ($userC) {
                return response()->json(['message' => 'Child account updated successfully'], 200);
            }
        }
        return response()->json(['message' => 'Invalid request'], 400);
    }
    public function meWork(Request $request)
    {
        $data = DB::table('item_activities as ia')
        ->join('items as i', 'i.id', '=', 'ia.item_id')
        ->join('users as u', 'u.id', '=', 'ia.user_id')
        ->where('ia.user_id', auth()->user()->id)
            ->select('ia.*', 'i.title', 'u.name')
            ->get();

        $responseData = [
            'data' => $data,
        ];

        return response()->json($responseData, 200);
    }
    // Assuming this code is part of a controller
    public function locationList(Request $request)
    {
        $userService = new UserServices();

        $user = $request->get('_user');
        $userLocationId = $user->id;
        if ($request->get('user_id')) {
            $userLocationId = $request->get('user_id');
        }
        $locations = UserLocation::where('user_id', $userLocationId)->paginate();
        $partners = [];

        if ($userService->isMod()) {
            $partners = User::whereIn('role', [UserConstants::ROLE_SCHOOL, UserConstants::ROLE_TEACHER])
                ->where('status', 1)
                ->select('id', 'name')
                ->get();
        }

        $responseData = [
            'locations' => $locations,
            'partners' => $partners,
        ];

        return response()->json($responseData, 200);
    }

}
