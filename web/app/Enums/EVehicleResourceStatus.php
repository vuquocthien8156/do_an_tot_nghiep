<?php
namespace App\Enums;

abstract class EVehicleResourceStatus {
	const DELETE = -1;
	const NOTACTIVE = 0;
	const ACTIVE = 1;
	public static function getStatusString($status) {
		switch ($status) {
			case EVehicleResourceStatus::DELETE:
				return -1;
				case EVehicleResourceStatus::NOTACTIVE:
				return 'Chưa duyệt';
			case EVehicleResourceStatus::ACTIVE:
				return 'Đã duyệt';
		}
		return null;
	}
}