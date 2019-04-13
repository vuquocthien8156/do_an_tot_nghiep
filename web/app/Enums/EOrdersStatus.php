<?php
namespace App\Enums;

abstract class EOrdersStatus {
	const CANCLED_BY_ADMIN = -1;
	const CANCLED_BY_USER = -2;
	const WAITING_FOR_CONFIRMATION = 10;
	const CONFIRMED = 20;
	const COMPLETED = 30;

	public static function getStatusString($status) {
		switch ($status) {
			case EOrdersStatus::CANCLED_BY_ADMIN:
				return 'Hủy bởi admin';
			case EOrdersStatus::CANCLED_BY_USER:
				return 'Hủy bởi người dùng';
			case EOrdersStatus::WAITING_FOR_CONFIRMATION:
				return 'Chờ xác nahn65';
			case EOrdersStatus::CONFIRMED:
				return 'Đã Xác nhận';
			case EOrdersStatus::COMPLETED:
				return 'Hoàn thành';
		}
		return null;
	}
}