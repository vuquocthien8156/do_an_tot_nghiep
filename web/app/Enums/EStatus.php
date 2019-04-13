<?php
namespace App\Enums;

abstract class EStatus {
	const DELETED = -1;
	const WAITING = 0;
	const DRAFT = 0;
	const ACTIVE = 1;
	const TRYAGAIN = 2;

	public static function getStatusString($status) {
		switch ($status) {
			case EStatus::DELETED:
				return 'Deleted';
			case EStatus::WAITING:
				return 'Waiting';
			case EStatus::ACTIVE:
				return 'Active';
			case EStatus::TRYAGAIN:
				return 'Tryagain';	
		}
		return null;
	}
}