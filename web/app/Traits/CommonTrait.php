<?php

namespace App\Traits;

trait CommonTrait {
	public function getUserTimezone() {
		return session()->get(\App\Constant\SessionKey::TIMEZONE, config('app.timezone'));
    }
    public function getAuthorizationUser() {
        $getAuthorization = $this->authorizationService->getAuthorizationUser(auth()->id());
        return $getAuthorization;
	}
}