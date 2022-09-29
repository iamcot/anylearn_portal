<?php

namespace App\Http\Middleware;

use App\models\I18nContent;
use Closure;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

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
            $path = $request->path();
            // $locale = $request->get('language'); 
            // # save locale 
            // Cookie::queue(Cookie::make(
            //     'language', $locale, 1440 // 24h
            // ));
            if (auth()->user()) {
                auth()->user()->language = $request->language;
                auth()->user()->save();
            }
            \App::setLocale($request->language);
            return redirect($path);
        } elseif (auth()->user()) {
            \App::setLocale(auth()->user()->language);
        }

        return $next($request);
    }
}
