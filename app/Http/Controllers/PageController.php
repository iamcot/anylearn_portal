<?php

namespace App\Http\Controllers;

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
        return view('ref');
    }
}
