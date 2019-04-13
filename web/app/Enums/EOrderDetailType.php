<?php
namespace App\Enums;

abstract class EOrderDetailType {
	const  ARBITRARY_ORDER = 1;
	const BUY_PRODUCT_ORDER = 2;

	public static function getStatusString($type) {
		switch ($type) {
			case EOrderDetailType::CANCLED_BY_ADMIN:
				return 'Canceled by admin';
			case EOrderDetailType::CANCLED_BY_USER:
				return 'Canceled by user';
		}
		return null;
	}
}