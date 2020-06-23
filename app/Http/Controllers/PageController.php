<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PageController extends Controller
{

    public function home()
    {
        return view('home');
    }

    public function ref(Request $request, $code = "")
    {
        $data = [];
        if (empty($code)) {
            return redirect('/');
        }
        $refUser = User::where('refcode', $code)->first();
        if (!$refUser) {
            return redirect('/');
        }
        if ($request->get('has-account')) {
            $data['isReg'] = true;
        }
        $data['user'] = $refUser;
        return view('ref', $data);
    }
}
