<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\SlackOrg;
use Illuminate\Http\Response;

class IsOrgOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            return abort(404);
        }

        $user = Auth::user();
        $org = SlackOrg::where('user_id', $user->id)
            ->where('id', $request->id)
            ->first();

        if ($org == NULL)
            return abort(404);

        return $next($request);
    }
}
