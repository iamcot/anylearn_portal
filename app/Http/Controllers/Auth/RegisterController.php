<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function registerRefPage(Request $request, $code)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));

        $data = [];
        if (empty($code)) {
            return redirect('/');
        }
        
        // Redirect to the previous page
        if (session()->has('urlPrevious')) {
            Auth::login($user);
            return redirect()->to(session('urlPrevious'));
        }

        $refUser = User::where('refcode', $code)->first();
        if (!$refUser) {
            return redirect('/');
        }
        
        $data['user'] = $refUser;
        $data['newUser'] = $user;
        $data['isReg'] = true;
        $data['sale_id'] = $request->get('s');

        $data['role'] = $request->get('r');
        if ($data['role'] == 'member') {
            return view('register.member', $data);
        } else if ($data['role'] == 'school') {
            return view('register.school', $data);
        } else if ($data['role'] == 'teacher') {
            return view('register.teacher', $data);
        }
        return view('register.index', $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user = new User();
        return $user->validateMember($data);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = new User();
        return $user->createNewMember($data);
    }

    public function redirectPath()
    {
        $userM = new User();
        return $userM->redirectToUpdateDocs();
    }
}
