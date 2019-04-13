<?php
namespace App\Enums;

abstract class EManufacture {
	const DELETED = -1;
	const WAITING = 0;
	const ACTIVE = 1;

    const MANUFACTURE = 1;
    const MANUFACTURE_MODEL = 2;

	public static function valueToName($status) {
		switch ($status) {
			case EManufacture::DELETED:
				return 'Đã xoá';
			case EManufacture::WAITING:
				return 'Chưa kích hoạt';
			case EManufacture::ACTIVE:
				return 'Đã kích hoạt';	
		}
		return null;
	}
}