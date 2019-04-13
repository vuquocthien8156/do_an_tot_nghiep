<?php
namespace App\Enums;

abstract class EVehicleResourceKind {
	const IMAGE = 1;
	const CAVET = 2;

	public static function getStatusString($status) {
		switch ($status) {
			case EStatus::IMAGE:
				return 'Hình ảnh';
			case EStatus::CAVET:
				return 'Cavet';	
		}
		return null;
	}
}