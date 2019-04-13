<?php
namespace App\Enums;

abstract class EProductCategoryStatus {
	const DELETED = -1;
	const ACTIVE = 1;

	public static function getStatusString($status) {
		switch ($status) {
			case EProductCategoryStatus::DELETED:
				return 'Deleted';
			case EProductCategoryStatus::ACTIVE:
				return 'Active';
		}
		return null;
	}
}