<?php

namespace App\Enums;


abstract class EVehicleType {    
    const TYPE_MOTORBIKE = 1;
    const TYPE_CAR = 2;

	public static function valueToName($value) {
		switch ($value) {
			case self::TYPE_CAR:
				return 'Xe hơi';
			case self::TYPE_MOTORBIKE:
				return 'Xe máy';
		}
		return null;
	}
}