<?php
namespace App\Enums;

abstract class EPriceStatus {
	const DELETED = -1;
	const DRAFT = 0;
	const ACTIVE = 1;
	const FINISHED = 2;

	public static function getStatusString($status) {
		switch ($status) {
			case EPriceStatus::DELETED:
				return 'Deleted';
			case EPriceStatus::DRAFT:
				return 'Draft';
			case EPriceStatus::ACTIVE:
				return 'Active';
			case EPriceStatus::FINISHED:
				return 'finished';	
		}
		return null;
	}
}