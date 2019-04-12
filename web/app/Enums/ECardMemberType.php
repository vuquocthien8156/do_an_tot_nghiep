<?php
namespace App\Enums;

abstract class ECardMemberType {
	const DELETED = -1;
	const WAITING = 0;
	const ACTIVE = 1;

	public static function valueToName($status) {
		switch ($status) {
			case ECardMemberType::DELETED:
				return 'Hết hạn/ Đã xoá';
			case ECardMemberType::WAITING:
				return 'Chờ phản hồi';
			case ECardMemberType::ACTIVE:
				return 'Đã kích hoạt';	
		}
		return null;
	}
}