<?php
namespace App\Enums;

abstract class EAppointmentType {
	const REPAIR = 1;
	const ACCREDITATION = 2;
	const MAINTENANCE = 3;

	public static function valueToName($status) {
		switch ($status) {
			case EAppointmentType::REPAIR:
				return 'Sửa xe';
			case EAppointmentType::ACCREDITATION:
				return 'Kiểm định xe';
			case EAppointmentType::MAINTENANCE:
				return 'Bảo dưỡng xe';	
		}
		return null;
	}
}