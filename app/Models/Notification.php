<?php

namespace App\Models;

use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use FCM;
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

    public function createNotif($type, $userId, $data)
    {
        $config = config('notification.' . $type);
        $obj = [
            'type' => $type,
            'user_id' => $userId,
            'title' => $config['title'],
            'content' => $this->buildContent($config['template'], $data),
            'route' => $config['route'],
        ];
        if (!empty($config['args']) && !empty($data['args'])) {
            $obj['extra_content'] = $data['args'];
        }
        $newNotif = $this->create($obj);
        if (!$newNotif) {
            return;
        }
        $user = User::find($userId);
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
        ]);
    }

    public function notifNewFriend($userId, $name)
    {
        return $this->createNotif(NotifConstants::NEW_FRIEND, $userId, [
            'friend' => $name,
            'args' => $userId,
        ]);
    }

    public function notifRemindConfirms($itemId)
    {
        $registers = DB::table('order_details')->where('order_details.item_id', $itemId)
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
            ->select('users.id', 'users.name', 'items.title', 'items.date_start', 'items.time_start')
            ->get();
        $now = new DateTime('now');
        $diff = null;
        foreach ($registers as $register) {
            if ($diff == null) {
                $itemDate = new DateTime($register->date_start . ' ' . $register->time_start);
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

    private function buildContent($template, $data)
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
