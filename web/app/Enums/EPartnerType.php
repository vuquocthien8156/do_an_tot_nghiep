<?php
namespace App\Enums;

abstract class EPartnerType {
    const PRODUCT_PARTNERFIELD = 9;
    const PRODUCT_PARTNER = 10;

	public static function valueToName($type) {
		switch ($type) {
			case ECategoryType::PRODUCT_PARTNERFIELD:
				return 'Đối tác';
			case ECategoryType::PRODUCT_PARTNER:
				return 'Đơn vị';
		}
		return null;
	}
}