<?php

namespace App\Http\Middleware;

use Closure;

class LocalizationAPIMiddleware
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
        //Check request and set language defaut
        $lang = ($request->get('locale')) ? $request->get('locale') : 'vi';
        //Set laravel localization
        app()->setLocale($lang);
        //Continue request
        return $next($request);
    }
}
