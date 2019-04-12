<?php
namespace App\Enums;

abstract class EVehicleAccredited {
	const ACCREDITED = TRUE;
	const NOTACCREDITED = FALSE;
	public static function getAccredited($status) {
		switch ($status) {
			case EVehicleAccredited::ACCREDITED:
				return 'Đã Duyệt';
			case EVehicleAccredited::NOTACCREDITED:
				return 'Chưa duyệt';
		}
		return null;
	}
}