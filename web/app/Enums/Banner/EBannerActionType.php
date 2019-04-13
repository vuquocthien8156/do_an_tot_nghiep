<?php

namespace App\Enums\Banner;


abstract class EBannerActionType {
	const DO_NOTHING = 1;
	const OPEN_WEBSITE = 4;

	public static function isValid($type) {
		if (!is_numeric($type)) {
			return false;
		}
		if (self::DO_NOTHING != $type
			&& self::OPEN_WEBSITE != $type) {
			return false;
		}
		return true;
	}

	public static function valueToName($value) {
		switch ($value) {
			case self::DO_NOTHING:
				return 'Không làm gì cả';
			case self::OPEN_WEBSITE:
				return 'Mở trang web';
		}
		return null;
	}
}