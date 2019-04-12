<?php
namespace App\Enums;

abstract class ENotificationType {

	const NOTIFY_ALL_CUSTOMER = 100;
    const NOTIFY_SPECIFICALLY_CUSTOMER = 101;

    // Rescue
    const NEW_RESCUE_REQUEST = 20;
    const RESCUE_REQUEST_ASSIGNED = 21;

    //appoitment
    const NOTIFY_APPOINTMENT = 40;

    //Forwarder
    const VEHICLE_TRANSFER_ASSIGNED = 51;

    // happy birth day
    const NOTIFY_BIRTHDAY_CUSTOMER = 1;
}