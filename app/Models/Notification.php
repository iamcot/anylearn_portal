<?php

namespace App\Models;

use App\Constants\NotifConstants;
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
