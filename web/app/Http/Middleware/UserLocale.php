<?php

namespace App\Http\Middleware;

use App\Constant\SessionKey;
use App\Enums\ELanguage;
use Closure;

class UserLocale {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		$locale = session(SessionKey::LANG, ELanguage::EN);
		app()->setLocale($locale);
		return $next($request);
	}
}
