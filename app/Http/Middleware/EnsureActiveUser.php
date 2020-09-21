<?php

namespace App\Http\Middleware;

use Closure;

class EnsureActiveUser
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
        if($request->user()->status === 'inactive'){
            abort(403, 'This account is Inactive.');
        }
        else if($request->user()->status === 'suspended'){
            abort(403, 'This account is temporarily suspended.');
        }

        return $next($request);
    }
}
