<?php

namespace App\Constant;

abstract class ConfigKey {
    // First Sync
    const LAST_SYNC_STAFF_ID = 'last_sync_staff_id';
    const LAST_SYNC_USER_ID = 'last_sync_user_id'; // Table: dbo.KhachHangNgoai
    const LAST_SYNC_USER_ID_2 = 'last_sync_user_id_2'; // Table: dbo.KhachHang
    const LAST_SYNC_MEMBERSHIP_CARD_ID = 'last_sync_membership_card_id';
    const LAST_SYNC_BRANCH_ID = 'last_sync_branch_id';
    const LAST_SYNC_ORDER_ID = 'last_sync_order_id';
    const LAST_SYNC_MANUFACTURE_ID = 'last_sync_manufacture_id';
    const LAST_SYNC_MODEL_ID = 'last_sync_model_id';
    const LAST_SYNC_UPDATE_MEMBERSHIP_CARD_ID = 'last_sync_update_membership_card_id';
    const LAST_SYNC_TYPE_STAFF_ID = 'last_sync_type_staff_id';
    
    // Key COnfig
    const BIRTHDAY_CUSTOMER = 'birthday_customer';
    const BANK_TRANFER = 'bank_account';
    
    //Sync Auto
    const LAST_SYNC_AUTO_BRANCH_ID = 'last_sync_auto_branch_id';
    const LAST_SYNC_AUTO_GROUP_STAFF_ID = 'last_sync_auto_group_staff_id';
    const LAST_SYNC_AUTO_STAFF_ID = 'last_sync_auto_staff_id';
    const LAST_SYNC_AUTO_USER_ID_1 = 'last_sync_auto_user_id_1';
    const LAST_SYNC_AUTO_USER_ID_2 = 'last_sync_auto_user_id_2';
    const LAST_SYNC_AUTO_MANUFACTURE_ID = 'last_sync_auto_manufacture_id';
    const LAST_SYNC_AUTO_ORDER_ID = 'last_sync_auto_order_id';
    const LAST_SYNC_AUTO_MEMBERSHIPCARD_ID = 'last_sync_auto_membershipcard_id';
    const LAST_SYNC_AUTO_MEMBERSHIPCARD_ID_2 = 'last_sync_auto_membershipcard_id_2';
}