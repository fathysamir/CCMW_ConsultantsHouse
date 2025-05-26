<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (! Auth::check()) {

            return redirect('/login');

        }
        if (count(auth()->user()->roles) == 0) {
            Auth::logout();

            return redirect('/login');
        }
        if (! is_null(auth()->user()->roles)) {
            if (auth()->user()->roles->first()->name != 'Super Admin' && auth()->user()->roles->first()->name != 'User') {
                Auth::logout();

                return redirect('/login')->withErrors(['msg' => 'Please verify that your information is correct']);
            }
        }

        return $next($request);

    }
}
