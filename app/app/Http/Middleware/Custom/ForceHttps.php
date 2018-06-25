<?php

namespace App\Http\Middleware\Custom;

use Closure;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && in_array(env('APP_ENV'), ['stage', 'staging', 'preprod', 'preproduction', 'prod', 'production'])) {
            return redirect()->secure('/' . $request->path());
        }

        return $next($request);
    }
}
