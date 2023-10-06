<?php namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtpServices
{
    const SERVIVCE_ZALO = 'zalo';
    const SERVIVCE_SMS = 'sms';
    const NOTIFICATION_TITLE = 'otp';

    public function genOtp($phone, $service = self::SERVIVCE_ZALO)
    {
        $otp = mt_rand(100000, 999999);
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new \Exception("User không tồn tại.");
        }
        $data = [
            'type' => $service,
            'title' => self::NOTIFICATION_TITLE,
            'user_id' => $user->id,
            'content' => $otp,
            'route' => $otp,
        ];
        // $data['send'] = date('Y-m-d H:i:s');
        Notification::where('user_id', $user->id)
            ->where('type', $service)
            ->where('title', self::NOTIFICATION_TITLE)
            ->whereNull('read')
            ->update([
                'read' => DB::raw('NOW()')
            ]);
        $newNotif = Notification::create($data);
        return [
            'notification_id' => $newNotif->id,
            'otp' => $otp
        ];
    }

    public function verifyOTP($phone, $otp, $service = self::SERVIVCE_ZALO, $setRead = true)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new \Exception("User không tồn tại.");
        }
        $haveNotif = Notification::where('type', $service)
            ->where('user_id', $user->id)
            ->whereNull('read')
            ->where('title', self::NOTIFICATION_TITLE)
            ->where('route', $otp)
            ->orderby('id', 'desc')
            ->first();
        if (!$haveNotif) {
            Log::debug($phone,$otp,$user->id);
            throw new \Exception("OTP không đúng");
        }
        if ($setRead) {
            Notification::find($haveNotif->id)->update([
                'read' => DB::raw('NOW()')
            ]);
        }

        return true;
    }
}
