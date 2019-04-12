<?php
namespace App\Enums;

abstract class EProductStatus {
	const DELETED = -1;
	const ACTIVE = 1;

	public static function getStatusString($status) {
		switch ($status) {
			case EStatus::DELETED:
				return 'Deleted';
			case EStatus::ACTIVE:
				return 'Active';
		}
		return null;
	}
}