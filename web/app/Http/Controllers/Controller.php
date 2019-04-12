<?php

namespace App\Http\Controllers;

use App\Constant\SessionKey;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function callAction($method, $parameters) {
		$oldTz = Session::get(SessionKey::TIMEZONE);
		if (!isset($oldTz)) {
			Session::put(SessionKey::TIMEZONE, config('app.timezone'));
		} else if (isset($parameters) && isset($parameters[0]) && $parameters[0]->method() === 'POST') {
			$tz = $parameters[0]->headers->get('timezone', null);
			if (!isset($tz)) {
				$tz = array_key_exists('_timezone', $_POST) ? $_POST['_timezone'] : config('app.timezone');
			}
			if ($oldTz !== $tz) {
				Session::put(SessionKey::TIMEZONE, $tz);
			}
		}

		return parent::callAction($method, $parameters);
	}
}
