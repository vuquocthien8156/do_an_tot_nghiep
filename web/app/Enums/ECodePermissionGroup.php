<?php
namespace App\Enums;

abstract class ECodePermissionGroup {
	const CUSTOMER = 'CUSTOMER';
	const VEHICLE = 'VEHICLE';
	const STAFF = 'STAFF';
    const SERVICE = 'SERVICE';
    const NOTIFICATION = 'NOTIFICATION';
	const CONFIG = 'CONFIG';
    const CHAT = 'CHAT';
    

    // SOURCE
    const SOURCE_SYSTERM = 1;
    const SOURCE_USER_CREATED = 2;
}