<?php

namespace App\Http\Controllers\Auth;

use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'phone';
    }

    protected function authenticated(Request $request, $user)
    {
        if (auth()->user()->status == UserConstants::STATUS_INACTIVE) {
            $name = auth()->user()->name;
            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect()->intended('/inactive')->with('name', $name);
        }
        $userM = new User();
        return redirect($userM->redirectToUpdateDocs());
    }
}
