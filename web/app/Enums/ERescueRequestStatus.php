<?php
namespace App\Enums;

abstract class ERescueRequestStatus {
	const NOT_PROCESSED = 0;
    const ASSIGNED_STAFF = 1;
    const RESCUE_COMPLETE = 2;
}