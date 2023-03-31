<?php

namespace App\Http\Controllers;

use App\Constants\UserConstants;
use App\Models\SaleActivity;
use App\Models\Spm;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function memberSale(Request $request, $userId)
    {
        $userService = new UserServices();
        $user = Auth::user();

        if ($userId == 1 || !$userService->haveAccess($user->role, 'user.members')) {
            return redirect()->route('user.members')->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $saleUser = User::find($userId);
        if ($user->role == UserConstants::ROLE_SALE && $saleUser->user_id != $user->id && $saleUser->sale_id != $user->id) {
            return redirect()->route('user.members')->with('notify', __('Bạn không có quyền với user này'));
        }
        if ($request->get('action')=='history') {
            $input=$request->all();
            // dd($request->id);
            $memberOrders = DB::table('orders')->where('orders.user_id', $saleUser->id)
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->join('items', 'items.id', '=', 'order_details.item_id')
            ->where('order_details.user_id',$request->id)
            ->select('items.title', 'items.image', 'items.id AS itemId', 'order_details.*')
            ->orderBy('orders.id', 'desc')
            ->paginate(5);
            $this->data['idC'] = $request->id;
        } else{
            $memberOrders = DB::table('orders')->where('orders.user_id', $saleUser->id)
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->join('items', 'items.id', '=', 'order_details.item_id')
            ->select('items.title', 'items.image', 'items.id AS itemId', 'order_details.*')
            ->orderBy('orders.id', 'desc')
            ->paginate(5);
            $this->data['idC'] = null;
        }
        $accountC = DB::table('users')->where('user_id',$userId)->get();
        // dd($memberOrders);
        $this->data['accountC'] = $accountC;
        $this->data['user'] = $user;
        $this->data['memberOrders'] = $memberOrders;
        $this->data['orderStats'] = $userService->orderStats($saleUser->id);

        $this->data['sale'] = $user;
        $this->data['memberProfile'] = $saleUser;
        $this->data['navText'] = $saleUser->name;
        $this->data['lastNote'] = SaleActivity::where('type', SaleActivity::TYPE_NOTE)
            ->where('member_id', $userId)
            ->orderBy('id', 'DESC')
            ->first();
        $this->data['contactHistory'] = SaleActivity::whereIn('type', [SaleActivity::TYPE_CHAT, SaleActivity::TYPE_CALL])
            ->where('member_id', $userId)
            ->where('status', 1)
            ->orderby('id', 'desc')
            ->paginate(5);

        return view('crm.sale', $this->data);
    }

    public function saveNote(Request $request)
    {
        if ($request->get('action') == 'save-note') {
            $data = $request->get('salenote');
            SaleActivity::create([
                'type' => SaleActivity::TYPE_NOTE,
                'sale_id' => Auth::user()->id,
                'member_id' => $data['memberId'],
                'content' => $data['note'],
            ]);
            return redirect()->back()->with('notify', 'Lưu ghi chú thành công.');
        } else return redirect()->back();
    }

    public function saveCall(Request $request)
    {
        if ($request->get('action') == 'save-call') {
            if ($request->get('isajax') == 1) {
                SaleActivity::create([
                    'type' => SaleActivity::TYPE_CALL,
                    'sale_id' => Auth::user()->id,
                    'member_id' => $request->get('member_id'),
                    'logwork' => $request->get('logwork'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'content' => $request->get('uuid'),
                ]);
                echo 'Lưu log hội thoại thành công';
            } else {
                $data = $request->get('salecall');
                SaleActivity::create([
                    'type' => SaleActivity::TYPE_CALL,
                    'sale_id' => Auth::user()->id,
                    'content' => $data['content'],
                    'member_id' => $data['memberId'],
                    'logwork' => $data['logwork'],
                    'created_at' => $data['date'] . ' ' . $data['time'] . ":00"
                ]);
                return redirect()->back()->with('notify', 'Lưu log hội thoại thành công.');
            }
        } else return redirect()->back();
    }

    public function saveChat(Request $request)
    {
        if ($request->get('action') == 'save-chat') {
            $data = $request->get('salechat');
            SaleActivity::create([
                'type' => SaleActivity::TYPE_CHAT,
                'sale_id' => Auth::user()->id,
                'member_id' => $data['memberId'],
                'content' => $data['content'],
                'logwork' => $data['logwork'],
                'created_at' => $data['date'] . ' ' . $data['time'] . ":00"
            ]);
            return redirect()->back()->with('notify', 'Lưu log chat thành công.');
        } else return redirect()->back();
    }

    public function delActivity(Request $request, $id)
    {
        SaleActivity::find($id)->update([
            'status' => 0,
        ]);
        return redirect()->back();
    }

    public function viewActivityContent($id)
    {
        $activity = SaleActivity::find($id);
        $content = nl2br($activity->content);
        echo $content;
    }

    public function anylog(Request $request) {
        $spmM = new Spm();
        $spmM->addSpm($request);
    }
}
