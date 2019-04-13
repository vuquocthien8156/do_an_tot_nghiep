<?php
namespace App\Enums;

abstract class EOrderStatus {
	const CANCEL_BY_USER = -1;
	const CANCEL_BY_ADMIN = -2;
	const WAITING = 10;
	const CONFIRMED = 20;
	const COMPLETED = 30;
}