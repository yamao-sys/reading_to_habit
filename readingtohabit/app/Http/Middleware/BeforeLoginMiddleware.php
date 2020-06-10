<?php

namespace App\Http\Middleware;

use Closure;

class BeforeLoginMiddleware
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
        if (empty($request->session()->get('user_id'))) {
            return $next($request);
        }
        
        return redirect('articles');
    }
}
