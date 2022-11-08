<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class Spm extends Model
{
    protected $table = 'spms';

    protected $fillable = [
        'spm_key', 'session_id', 'day', 'user_id', 'spma', 'spmb', 'spmc', 'spmd', 'spm_pre',
        'p_url', 'p_ref', 'p_title', 'p_meta_desc', 'p_meta_robots', 'p_canonical', 'p_lang',
        'os', 'ip', 'country', 'browser', 'screen', 'user_type', 'logfrom',
        'extra', 'event',
    ];

    public function addSpm(Request $request)
    {
        $spmKey = $request->get('spm');
        $spms = explode(".", $spmKey);
        if (count($spms) < 4) {
            return false;
        }
        try {
            $data = [
                'spm_key' => $spmKey,
                'event' => $request->get('event', 'pageview'),
                'day' => date('Y-m-d'),
                'session_id' => $request->get('sid',  session()->getId()),
                'user_id' => $request->get('usid'),
                'spma' => $spms[0],
                'spmb' => $spms[1],
                'spmc' => $spms[2],
                'spmd' => $spms[3],
                'spm_pre' => $request->get('spm_pre'),
                'p_url' => URL::full(),
                'p_ref' => $request->server('HTTP_REFERER'),
                'p_title' => $request->get('p_title'),
                'p_meta_desc' => $request->get('p_meta_desc'),
                'p_meta_canonical' => URL::current(),
                'p_lang' => $request->get('p_lang', App::getLocale()),
                'os' => $request->get('os'),
                'country' => $request->get('country'),
                'screen' => $request->get('screen'),
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
            ];
            // get country from IP
            Spm::create($data);
        } catch (\Exception $ex) {
            Log::error($ex);
            return false;
        }

        return true;
    }
}
