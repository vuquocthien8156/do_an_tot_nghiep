<?php
namespace App\Enums;

abstract class EVehicleApprove {
	const APPROVE = TRUE;
	const NOTAPPROVE = FALSE;
	public static function getApprove($status) {
		switch ($status) {
			case EVehicleApprove::APPROVE:
				return 'Đã Kiểm duyệt';
			case EVehicleApprove::NOTAPPROVE:
				return 'Chưa kiểm duyệt';
		}
		return null;
	}
}