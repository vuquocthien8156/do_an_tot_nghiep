<?php
namespace App\Enums;

abstract class EProductResourceStatus {
	const DELETED = -1;
	const ACTIVE = 1;

	public static function getStatusString($status) {
		switch ($status) {
			case EProductResourceStatus::DELETED:
				return 'Deleted';
			case EProductResourceStatus::ACTIVE:
				return 'Active';
		}
		return null;
	}
}