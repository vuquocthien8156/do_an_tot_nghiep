<?php
namespace App\Enums;

abstract class EProductResourceType {
	const IMAGE = 1;
	const VIDEO = 2;

	public static function getStatusString($type) {
		switch ($type) {
			case EProductResourceType::IMAGE:
				return 'Image';
			case EProductResourceType::VIDEO:
				return 'Video';
		}
		return null;
	}
}