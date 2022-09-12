<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class Role
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
        
        // if (Auth::check()) {
        //     return redirect('login');
        // }
        if (auth()->user()->role =="admin" || auth()->user()->role =="mod") {
            return $next($request);
        }
        return redirect('me');
    }
}
