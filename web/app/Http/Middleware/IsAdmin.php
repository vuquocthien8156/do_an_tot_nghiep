<?php

namespace App\Http\Middleware;

use App\Enums\EUserRole;
use Closure;

class IsAdmin {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (auth() && auth()->user()->hasRole(EUserRole::ADMINISTRATOR)) {
			return $next($request);
		}
		return redirect('/');
	}
}
