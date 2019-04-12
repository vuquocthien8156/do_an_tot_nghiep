<?php
namespace App\Enums;

abstract class EVehicleTransferStatus {
	const NOT_PROCESSED = 0;
    const ASSIGNED_STAFF = 1;
    const TRANSFER_COMPLETE = 2;
}