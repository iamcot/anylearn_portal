<?php

namespace App\Http\Middleware;

use App\Models\I18nContent;
use Closure;

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
            }
            \App::setLocale($request->language);
            return redirect()->back();
        } elseif (auth()->user()) {
            \App::setLocale(auth()->user()->language);
        }

        return $next($request);
    }
}