<?php
namespace App\Enums;

abstract class ENotificationScheduleType {
	const ALL_CUSTOMER = 1;
	const SPECIFICALLY_CUSTOMER = 5; 
	const GROUP_CUSTOMER = 8; 

    const NOTIFY_COMMON_TYPE = 1;
    const NOTIFY_COMMERCIAL_TYPE = 2;

	public static function valueToStringTarget($status) {
		switch ($status) {
			case ENotificationScheduleType::ALL_CUSTOMER:
				return 'Tất cả khách hàng';
			case ENotificationScheduleType::SPECIFICALLY_CUSTOMER:
				return 'Khách hàng cụ thể';
			case ENotificationScheduleType::GROUP_CUSTOMER:
				return 'Nhóm khách hàng';
		}
		return null;
    }
    
    public static function valueToStringType($status) {
		switch ($status) {
			case ENotificationScheduleType::NOTIFY_COMMON_TYPE:
				return 'Khác';
			case ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE:
				return 'Khuyến mái';
		}
		return null;
	}
}