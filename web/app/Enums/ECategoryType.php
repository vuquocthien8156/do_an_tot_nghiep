<?php
namespace App\Enums;

abstract class ECategoryType {
    const PRODUCT_CATEGORY = 3;
    const PRODUCT_COLOR = 4;
    const PRODUCT_TRADEMARK = 5;
    const PRODUCT_ORIGIN = 6;
    const PRODUCT_SIZE = 7;

    const STAFF_TYPE = 8;
    const PARTNER_FIELD = 9;
    const PARTNER = 10;

	public static function valueToName($type) {
		switch ($type) {
			case ECategoryType::PRODUCT_CATEGORY:
				return 'Danh mục xe';
			case ECategoryType::PRODUCT_COLOR:
				return 'Màu xe';
			case ECategoryType::PRODUCT_TRADEMARK:
				return 'Thương hiệu';
			case ECategoryType::PRODUCT_ORIGIN:
				return 'Xuất xứ';
			case ECategoryType::PRODUCT_SIZE:
				return 'Kích thước';
			case ECategoryType::STAFF_TYPE:
				return 'Loại chi nhánh';
			case ECategoryType::PARTNER_FIELD:
				return 'Nhóm đối tượng';
			case ECategoryType::PARTNER:
				return 'Đối tượng';
		}
		return null;
	}
}