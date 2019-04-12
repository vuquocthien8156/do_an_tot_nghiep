<?php

namespace App\Enums;


abstract class ELanguage {
	const EN = 'en';
	const VI = 'vi';

	public static function isSupportedLanguage(string $lang) {
		return in_array($lang, [self::EN, self::VI]);
	}
}