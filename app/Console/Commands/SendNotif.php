<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class SendNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firenotif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notif via firebase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notSendNotifs = Notification::where('is_send', 0)->get();
   
        $notifM = new Notification();
        foreach ($notSendNotifs as $notif) {
            $user = User::find($notif->user_id);
            if (!$user->notif_token) {
                continue;
            }
            $notifM->firebaseMessage($notif, $user->notif_token);
            Notification::find($notif->id)->update([
                'is_send' => 1,
                'send' => DB::raw("now()")
            ]);
            print("Send notif to $user->id , token $user->notif_token \n");
        }
    }
}
