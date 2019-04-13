<?php

namespace App\Services;


use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseService {
	public static function firebase() {
		$serviceAccount = ServiceAccount::fromJsonFile(config('app.google_service_account_json'));
		$firebase = (new Factory)
			->withServiceAccount($serviceAccount)
			->withDatabaseUri(config('app.firebase.databaseURL'))
			->create();
		return $firebase;
    }

	public static function database() {
		return self::firebase()->getDatabase();
	}

	public static function messaging() {
		return self::firebase()->getMessaging();
	}

	public static function auth() {
		return self::firebase()->getAuth();
	}

	public static function storage() {
		return self::firebase()->getStorage();
	}

	public static function remoteConfig() {
		return self::firebase()->getRemoteConfig();
	}
}