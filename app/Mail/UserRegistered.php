<?php

namespace App\Mail;

use App\Constants\UserConstants;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userId = $this->data['userid'];
        $user = User::find($userId);
        $eventEmail = "";
        if (date("Ymd") <= '20211231') {
            $eventEmail = 'user_register_20211031';
        }
        if ($user->role == UserConstants::ROLE_MEMBER) {
            return $this->subject('Chào mừng đến với anyLEARN - Học không giới hạn!')->view('email.' . ($eventEmail != "" ? $eventEmail : 'user_member_register'))->with($this->data);
        }
        return $this->subject('Chào mừng đến với anyLEARN - Học không giới hạn!')->view('email.' . ($eventEmail != "" ? $eventEmail : 'user_register'))->with($this->data);
    }
}
