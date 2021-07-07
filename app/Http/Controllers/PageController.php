<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Models\Configuration;
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
        $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP_WEB)->first();
        if (!empty($lastConfig)) {
            $homePopup = json_decode($lastConfig->value, true);
            if ($homePopup['status'] == 1) {
                $this->data['popup'] = $homePopup;
            }
        }
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

    public function paymentHelp() {
        $data['bank'] = config('bank');
        return view('checkout.paymenthelp', $data);
    }
}
