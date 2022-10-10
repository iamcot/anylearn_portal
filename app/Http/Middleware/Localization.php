<?php

namespace App\Http\Middleware;

use App\Models\I18nContent;
use Closure;
use Cookie; 
use Session;
class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('language')) {
            if (!in_array($request->get('language'), I18nContent::$supports)) {
                return $next($request);
            }
            // # save locale 
            // Cookie::queue(Cookie::make(
            //     'language', $locale, 1440 // 24h
            // ));
            if (auth()->user()) {
                auth()->user()->language = $request->language;
                auth()->user()->save();
                \App::setLocale($request->language);
            }else{
                Session::put('locale', $request->language);
                \App::setLocale($request->language);
            }
            return redirect()->back();
        } elseif (auth()->user()) {
            \App::setLocale(auth()->user()->language);
        }else{
            if(Session::get('locale')!= null){
                \App::setLocale(Session::get('locale'));
            }else{
                \App::setLocale('vi');
                Session::put('locale', 'vi');
            }
        }

        return $next($request);
    }
}