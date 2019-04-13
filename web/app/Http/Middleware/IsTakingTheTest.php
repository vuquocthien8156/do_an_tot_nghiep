<?php

namespace App\Http\Middleware;

use App\Enums\EUserRole;
use Closure;
use App\Constant\SessionKey;
use Carbon\Carbon;

class IsTakingTheTest {


	public function __construct() {

	}

	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        return $next($request);
    }
}
