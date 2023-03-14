<?php

namespace App\Models;

use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Mail\OrderSuccess;
use App\Services\ItemServices;
use App\Services\SmsServices;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use FCM;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Mail;
use LaravelFCM\Facades\FCM as FacadesFCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Notification extends Model
{
    const PAGESIZE = 20;
    protected $fillable = [
        'user_id', 'title', 'content', 'extra_content',
        'send', 'read', 'route', 'type', 'is_send',
    ];

    public function createNotif($type, $userId, $data, $copy = "", $route = "", $template = "")
    {
        $config = config('notification.' . $type);
        $obj = [
            'type' => $type,
            'user_id' => $userId,
            'title' => $config['title'],
            'content' => $this->buildContent(!empty($template) ? $template : $config['template'], $data),
            'route' => !empty($route) ? $route : $config['route'],
        ];
        if (!empty($config['args']) && !empty($data['args'])) {
            $obj['extra_content'] = $data['args'];
        }
        if (!empty($config['copy']) && !empty($copy)) {
            $obj['extra_content'] = 'copy';
            $obj['route'] = $copy;
        }
        // dd($obj,$userId);
        $newNotif = $this->create($obj);
        if (!$newNotif) {
            return;
        }
        $user = User::find($userId);
        //email

        if (isset($config['email'])) {
            if (!empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new $config['email']($data));
                } catch (\Exception $ex) {
                    Log::error($ex);
                }
            }
        }

        if (isset($data['orderid'])) {
            if (!empty($user->email)) {
                $dataOrder = DB::table('order_details as od')
                ->leftJoin('items as it','it.id','=','od.item_id')
                ->leftJoin('users as us','us.id','=','od.user_id')
                ->leftJoin('item_extras as is','is.item_id','=','od.item_id')
                ->leftJoin('schedules as sc','sc.item_id','=','od.item_id')
                ->where('order_id',$data['orderid'])
                ->select('it.title as item_title','it.price as item_price','us.name as username','is.title as item_extra_title','is.price as item_extra_price','sc.date as schedule_date','sc.time_start as schedule_time','sc.content as schedule_content','it.mailcontent')->get();
                $status = Transaction::where('order_id', $data['orderid'])->where('type', 'order')->first();
                if ($status->status == 1) {
                    Mail::send( "email.order_success",
                        array('name' => $user->name,'data'=>$dataOrder),
                        function ($message) use ($user) {
                            $message->to($user->email, $user->name)->subject('Đăng ký khóa học thành công!');
                        }
                    );
                }
            }
        }

        if (!$user->notif_token) {
            return;
        }
        $rs = $this->firebaseMessage($newNotif, $user->notif_token);
        if ($rs) {
            $this->find($newNotif->id)->update([
                'send' => DB::raw('now()'),
                'is_send' => 1,
            ]);
        }
    }

    public function resendUnsentMessage($userId, $notifToken)
    {
        $notSendNotifs = Notification::where('user_id', $userId)
            ->where('is_send', 0)->get();

        if (!$notifToken) {
            return;
        }
        foreach ($notSendNotifs as $notif) {
            $this->firebaseMessage($notif, $notifToken);
            Notification::find($notif->id)->update([
                'is_send' => 1,
                'send' => DB::raw("now()")
            ]);
        }
    }

    public function firebaseMessage($notifDB, $token)
    {
        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notificationBuilder = new PayloadNotificationBuilder($notifDB->title);
            $notificationBuilder->setBody($notifDB->content)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            if (!empty($notifDB->route)) {
                $obj = [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'screen' => $notifDB->route,
                ];
                if ($notifDB->extra_content) {
                    $obj['args'] = $notifDB->extra_content;
                }
                $dataBuilder->addData($obj);
            }

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $downstreamResponse = FacadesFCM::sendTo($token, $option, $notification, $data);
            return true;
        } catch (\Exception $ex) {
            Log::error($ex);
        }
        return false;
    }

    public function notifNewUser($userId, $name)
    {
        return $this->createNotif(NotifConstants::NEW_USER, $userId, [
            'username' => $name,
            'userid' => $userId,
        ]);
    }

    public function notifNewFriend($userId, $name)
    {
        return $this->createNotif(NotifConstants::NEW_FRIEND, $userId, [
            'friend' => $name,
            'args' => $userId,
        ]);
    }

    public function notifCourseShare($item, $username, $userId)
    {
        return $this->createNotif(NotifConstants::COURSE_SHARE, $userId, [
            'username' => $username,
            'course' => $item->title,
            'args' => $item->id,
        ]);
    }

    public function notifCourseCreated($item)
    {
        $receivers = User::whereIn('role', [UserConstants::ROLE_SALE, UserConstants::ROLE_SALE_CONTENT])->get();
        $itemServ = new ItemServices();
        foreach ($receivers as $user) {
            $this->createNotif(NotifConstants::COURSE_CREATED, $user->id, [
                'name' => $user->name,
                'author' => $item->author,
                'class' => $item->title,
                'price' => number_format($item->price, 0, ',', '.'),
                'orgprice' => number_format($item->org_price, 0, ',', '.'),
                'args' => $item->id,
                'url' => $itemServ->classUrl($item->id),
            ]);
        }
    }

    public function notifRemindConfirms($itemId)
    {
        $registers = DB::table('order_details')->where('order_details.item_id', $itemId)
            ->where('order_details.status', OrderConstants::STATUS_DELIVERED)
            ->join('users', 'users.id', '=', 'order_details.user_id')
            ->join('items', 'items.id', '=', 'order_details.item_id')
            ->select('users.id', 'users.name', 'items.title')
            ->get();
        foreach ($registers as $register) {
            $this->notifRemindConfirm($register->id, $register->name, $register->title);
        }
    }

    public function notifRemindJoin($itemId)
    {
        $registers = DB::table('order_details')->where('order_details.item_id', $itemId)
            ->join('users', 'users.id', '=', 'order_details.user_id')
            ->join('items', 'items.id', '=', 'order_details.item_id')
            // ->select('users.id', 'users.nae', 'items.title', 'items.date_start', 'items.time_start')
            ->select('users.id', 'users.name', 'items.title')
            ->get();
        $now = new DateTime('now');

        $nowInDay = date('Y-m-d');
        $nearestSchedule = Schedule::where('item_id', $itemId)
            ->where('date', '>=', $nowInDay)
            ->first();
        if (!$nearestSchedule) {
            return 'Không tìm thấy lịch học cho khóa ID=' . $itemId;
        }
        $diff = null;
        foreach ($registers as $register) {
            if ($diff == null) {
                $itemDate = new DateTime($nearestSchedule->date . ' ' . $nearestSchedule->time_start);
                $diff = date_diff($now, $itemDate);
            }
            if ($diff->d >= 1) {
                $this->createNotif(NotifConstants::REMIND_COURSE_GOING, $register->id, [
                    'username' => $register->name,
                    'course' => $register->title,
                    'day' => $diff->d,
                ]);
            } elseif ($diff->h >= 1) {
                $this->createNotif(NotifConstants::REMIND_COURSE_GOING_JOIN, $register->id, [
                    'username' => $register->name,
                    'course' => $register->title,
                    'time' => $diff->h . ' giờ',
                ]);
            } elseif ($diff->i >= 1) {
                $this->createNotif(NotifConstants::REMIND_COURSE_GOING_JOIN, $register->id, [
                    'username' => $register->name,
                    'course' => $register->title,
                    'time' => $diff->i . ' phút',
                ]);
            } else {
                $this->createNotif(NotifConstants::REMIND_COURSE_JOIN, $register->id, [
                    'course' => $register->title,
                ]);
            }
        }
        return true;
    }
    public function notifRemindConfirm($userId, $userName, $itemName)
    {
        return $this->createNotif(NotifConstants::REMIND_CONFIRM, $userId, [
            'username' => $userName,
            'course' => $itemName,
        ]);
    }

    public function notifCourseStatus($itemId)
    {
        $itemUpdated = Item::find($itemId);
        $author = User::find($itemUpdated->user_id);
        if ($itemUpdated->status == ItemConstants::STATUS_ACTIVE) {
            try {
                $isExists = $this->where('type', NotifConstants::COURSE_CREATED)
                    ->where('extra_content', $itemId)
                    ->count();
                if ($isExists == 0) {
                    $itemUpdated->author = $author->name;
                    $this->notifCourseCreated($itemUpdated);
                }
            } catch (Exception $ex) {
                Log::error($ex);
            }
            return $this->createNotif(NotifConstants::COURSE_APPROVED, $author->id, [
                'course' => $itemUpdated->title,
            ]);
        } else {
            return $this->createNotif(NotifConstants::COURSE_REJECTED, $author->id, [
                'course' => $itemUpdated->title,
                'username' => $author->name,
            ]);
        }
    }

    public function buildContent($template, $data)
    {
        $keys = [];
        foreach (array_keys($data) as $key) {
            $keys[] = '{' . $key . '}';
        }

        return str_replace(
            $keys,
            array_values($data),
            $template
        );
    }
}
