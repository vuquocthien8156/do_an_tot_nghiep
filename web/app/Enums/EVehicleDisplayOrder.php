<?php
namespace App\Enums;

abstract class EVehicleDisplayOrder {
	const PRIORITIZE = 1;
	const NOPRIORITIZE = 0;
	public static function getStatusString($status) {
		switch ($status) {
			case EVehicleDisplayOrder::PRIORITIZE:
				return 'Ưu tiên';
			case EVehicleDisplayOrder::NOPRIORITIZE:
				return 'Không ưu tiên';
			
		}
		return null;
	}
}