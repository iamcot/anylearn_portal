<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ItemServices;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{

    public function home()
    {
        return view('home', $this->data);
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
        if ($request->get('has-account') || Auth::user()) {
            $data['isReg'] = true;
        }
        $data['user'] = $refUser;
        $data['newUser'] = Auth::user();
        return view('ref', $data);
    }

    public function pdp(Request $request, $itemId)
    {
        $itemService = new ItemServices();
        $user = null;
        try {
            $data = $itemService->pdpData($itemId, $user);
            // dd($data);
            return view('pdp.index', ['data' => $data]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
