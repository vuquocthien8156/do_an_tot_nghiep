<?php
namespace App\Enums;

abstract class EOrderPaymentMethod {
	const COD = 1;
	const VISA_OR_MASTER_CARD = 3;
	const BANK_TRANSFER = 4;

	public static function getStatusString($status) {
		switch ($status) {
			case EOrderPaymentMethod::COD:
				return 'Cod';
			case EOrderPaymentMethod::VISA_OR_MASTER_CARD:
				return 'Visa or master card';
			case EOrderPaymentMethod::BANK_TRANSFER:
				return 'bank transfer';
		}
		return null;
	}
}