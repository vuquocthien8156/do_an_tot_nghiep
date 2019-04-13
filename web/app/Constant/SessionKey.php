<?php

namespace App\Constant;


abstract class SessionKey {
	const USER_ROLE = 'roles';
	const LANG = 'lang';
    const TIMEZONE = 'timezone';
    

    //Session export excel
    const EXPORT_CUSTOMER = 'export_customer';
    const EXPORT_VEHICLE = 'export_vehicle';
    const EXPORT_MEMBER_CARD = 'export_member_card';
    const EXPORT_APPOITMENT = 'export_appoitment';
    const EXPORT_STAFF = 'export_staff';

    // Authorization User
    const AUTHORIZATION_USER = 'authorization_user';
}