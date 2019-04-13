<?php
namespace App\Enums;

abstract class EVehicleStatus {
	const DELETE = -1;
	const DRAFT = 0;
	const SELLING = 1;
	const SOLD = 2;
	public static function getStatusString($status) {
		switch ($status) {
			case EVehicleStatus::DELETE:
				return 'Đã xóa';
			case EVehicleStatus::DRAFT:
				return 'Chờ duyệt';
			case EVehicleStatus::SELLING:
				return 'Chưa bán';
			case EVehicleStatus::SOLD:
				return 'Đã bán';
			
		}
		return null;
	}
}