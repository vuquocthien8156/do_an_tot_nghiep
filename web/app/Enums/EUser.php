<?php

namespace App\Enums;


abstract class EUser {
	const STATUS_DELETED = -1;
	const STATUS_WAITING_TO_CONFIRM = 0;
	const STATUS_ACTIVE = 1;

    const TYPE_STAFF_SYNC = 8;

	const GENDER_OTHER = 0;
	const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    
    const TYPE_ADMINISTRATOR = 0;
    const TYPE_USER = 1;
    const TYPE_STAFF = 2;
    const TYPE_USER_WEB = 3;


	public static function valueToName($value) {
		switch ($value) {
			case self::STATUS_DELETED:
				return 'Đã xoá';
			case self::STATUS_WAITING_TO_CONFIRM:
				return 'Mới';
			case self::STATUS_ACTIVE:
				return 'Kích hoạt';
		}
		return null;
	}
}