<?php

namespace App\Console\Commands;

use App\Mail\UserRegistered;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test send an email';

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
        $user = User::find(1);
        print("Start Send Email To User $user->name");
        Mail::to("info.anylearn@gmail.com")->send(new UserRegistered($user));
        print("\nEmail Sent!!!");
    }
}
