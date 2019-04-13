<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Enums\EUser;
use App\Enums\EOrderType;
use App\Constant\SessionKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SyncDB411Repository;

class SyncDB411Service {
    protected $syncDB411Repository;

	public function __construct(SyncDB411Repository $syncDB411Repository) {
		$this->syncDB411Repository = $syncDB411Repository;
    }
    //Get Last id sync by Key App_Config 
    public function getMaxIdSync($key) {
        return $this->syncDB411Repository->getMaxIdSync($key);
    }

    //Get Data Sync Staff
    public function getDataSyncStaff($value) {
        return $this->syncDB411Repository->getDataSyncStaff($value);
    }

    //Get Data Sync User
    public function getDataSyncUser($value) {
        return $this->syncDB411Repository->getDataSyncUser($value); //Table: dbo.KhachHangNgoai
    }

    public function getDataSyncUser2($value) {
        return $this->syncDB411Repository->getDataSyncUser2($value); //Table: dbo.KhachHang
    }

    // Get Data sync Membership card
    public function getDataSyncCardMember($value, $pageSize, $page) {
        return $this->syncDB411Repository->getDataSyncCardMember($value,  $pageSize, $page);
    }

    // Get Data sync Order
    public function getDataSyncOrders($value, $pageSize, $page) {
        return $this->syncDB411Repository->getDataSyncOrders($value, $pageSize, $page);
    }
    //Get data sync detail repair vehicle
    public function getDetailRepairVehicle($value) {
        return $this->syncDB411Repository->getDetailRepairVehicle($value);
    }

    // Get data sync branch
    public function getDataSyncBranch($value) {
        return $this->syncDB411Repository->getDataSyncBranch($value);
    }

    // Get data sync manufacture model
    public function getDataSyncModelVehicle($value) {
        return $this->syncDB411Repository->getDataSyncModelVehicle($value);
    }

    public function getDataSyncManufactureVehicle($value) {
        return $this->syncDB411Repository->getDataSyncManufactureVehicle($value);
    }

    // Save Last Id Sync App Config
    public function saveMaxIdSync($key, $numeric_value, $text_value) {
        return $this->syncDB411Repository->saveMaxIdSync($key, $numeric_value, $text_value);
    }

    // Check Phone Exist ?
    public function checkPhoneExist($phone, $type) {
        return $this->syncDB411Repository->checkPhoneExist($phone, $type);
    }

    //Get data update membership card
    public function getDataSyncUpdateMemberShipCard($value) {
        return $this->syncDB411Repository->getDataSyncUpdateMemberShipCard($value);
    }

     //Get data sync type staff
     public function getDataSyncTypeStaff($value) {
        return $this->syncDB411Repository->getDataSyncTypeStaff($value);
    }

    public function checkCardMemberExistWaiting($id_user) {
        return $this->syncDB411Repository->checkCardMemberExistWaiting($id_user);
    }

    public function checkCardMemberExist($id_user) {
        return $this->syncDB411Repository->checkCardMemberExist($id_user);
    }

    public function saveStaffSyncHasUserId($id_user, $id_branch, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveStaffSync = $this->syncDB411Repository->saveStaffSync($id_user, $id_branch, $myJSON);
            if (isset($saveStaffSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! save sync staff user id id_user: {$id_user}";
                Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed save Sync Staff has User Id id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function updateTypeStaff($id_user, $staff_type_id) {
        $timestamp = Carbon::now();
        try {
            $updateTypeStaff = $this->syncDB411Repository->updateTypeStaff($id_user, $staff_type_id) ;
            if (isset($updateTypeStaff)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update staff_type_id id_user: {$id_user}";
                Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update staff_type_id card id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    //update member ship card
    public function updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status) {
        $timestamp = Carbon::now();
        try {
            $update_data_membership_card = $this->syncDB411Repository->updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status);
            if (isset($update_data_membership_card)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update data membership card id_membership_card: {$id_membership_card}";
                Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update data membership card id_membership_card: {$id_membership_card} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    // Update Meta if phone exist
    public function updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id) {
        $timestamp = Carbon::now();
        try {
            $update_meta = $this->syncDB411Repository->updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id);
            if (isset($update_meta)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update meta id_user: {$id_user}";
                Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update meta isd_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }


    public function saveStaffSync($id_sync_staff, $name, $phone, $email, $address, $id_branch, $type_staff_id, $myJSON) {
        DB::beginTransaction();
        $timestamp = Carbon::now();

        try {
            $type = EUser::TYPE_STAFF;
            $saveUserSync = $this->syncDB411Repository->saveUserSync($id_sync_staff, $id_sync_user = null, $name, $phone, $email, $address, $type, $type_staff_id, $myJSON);
            $id_user = $saveUserSync->id;
            $saveStaffSync = $this->syncDB411Repository->saveStaffSync($id_user, $id_branch, $myJSON);
            if($address != null && $address != "") {
                $saveUserAddress = $this->syncDB411Repository->saveUserAddress($id_user, $name, $phone, $address);
            }
            if (isset($saveUserSync) && isset($saveStaffSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync staff id_sync: $id_sync_staff";
                Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail sync staff : {$id_sync_staff} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
            DB::rollBack();
        }
    }

    public function saveUserSync($id_sync_user, $name, $phone, $email, $address, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $type = EUser::TYPE_USER;
            $saveUserSync = $this->syncDB411Repository->saveUserSync($id_sync_staff = null, $id_sync_user, $name, $phone, $email, $address, $type, $type_staff_id = null, $myJSON);
            if($address != null && $address != "") {
                $id_user = $saveUserSync->id;
                $name = $saveUserSync->name;
                $saveUserAddress = $this->syncDB411Repository->saveUserAddress($id_user, $name, $phone, $address);
            }
            if (isset($saveUserSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync user id_sync: {$id_sync_user}";
                Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync user id_sync: {$id_sync_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function saveMemberShipCardSync($id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON)  {
        $timestamp = Carbon::now();
        try {
            $saveMemberShipCardSync = $this->syncDB411Repository->saveMemberShipCardSync($id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
            if (isset($saveMemberShipCardSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync membership card vehicle_number: {$vehicle_number}. id_user: {$id_user}";
                Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync membership card vehicle_number: {$vehicle_number}.  id_user: {$id_user}. Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function saveOrderSync($id_user, $vehicle_number, $id_sync_order, $code, $price, $name, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON) {
        DB::beginTransaction();
        $timestamp = Carbon::now();

        try {
            $saveOrder = $this->syncDB411Repository->saveOrder($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON);
            $order_id = $saveOrder->id;
            $saveOrderDetail = $this->syncDB411Repository->saveOrderDetail($order_id, $name, $created_at, $type = EOrderType::ARBITRARY_SERVICE_ORDER, $price = null, $quantity = null, $myJSON);
            if (isset($saveOrder) && isset($saveOrderDetail)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync order MaPBH: {$id_sync_order}";
                Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
                DB::commit();
                return $saveOrder;
            } else {
                DB::rollBack();
            }

        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync order MaPBH: {$id_sync_order} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
            DB::rollBack();
        }
    }

    public function saveBranchSync($id_sync_branch, $name, $address, $phone, $myJSON) {
        $timestamp = Carbon::now();

        try {
            $saveBranch = $this->syncDB411Repository->saveBranch($id_sync_branch, $name, $address, $phone, $myJSON);
            if (isset($saveBranch)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync branch id_sync: {$id_sync_branch}";
                Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }

        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync branch id_sync: {$id_sync_branch} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveManufactureSync($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveManufactureSync = $this->syncDB411Repository->saveManufactureModelVehicleSync($name, $type, $parent_category_id, $myJSON);
            if (isset($saveManufactureSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync Manufacture id_manufacture_sync: {$id_manufacture_sync}";
                Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync Manufacture card id_manufacture_sync: {$id_manufacture_sync} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function saveModelSync($id_model_sync, $name, $type, $parent_category_id, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveModelSync = $this->syncDB411Repository->saveManufactureModelVehicleSync($name, $type, $parent_category_id, $myJSON);
            if (isset($saveModelSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync Model Vehicle id_model_sync: {$id_model_sync}";
                Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Error! Failed sync Model Vehicle card id_model_sync: {$id_model_sync} . Error: {$e->getMessage()}";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveTypeStaff($id_sync, $name, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveTypeStaff = $this->syncDB411Repository->saveTypeStaff($id_sync, $name, $myJSON);
            if (isset($saveTypeStaff)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync Type Staff: {$id_sync}";
                Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Error! Failed sync Type Staff: {$id_sync} . Error: {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveOrderDetailRepairVehicle($order_id, $name, $created_at, $type, $price, $quantity, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveOrderDetail = $this->syncDB411Repository->saveOrderDetail($order_id, $name, $created_at, $type, $price, $quantity, $myJSON);
            if (isset($saveOrderDetail)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync order_detail repair vehicle order_id: {$order_id}";
                Storage::append("error_sync/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Error! Failed order_detail repair vehicle order_id: {$order_id} . Error: {$e->getMessage()}";
            Storage::append("error_sync/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdUserByDataSync($value) {
        return $this->syncDB411Repository->getIdUserByDataSync($value);
    }

    public function getIdBranchByDataSync($value) {
        return $this->syncDB411Repository->getIdBranchByDataSync($value);
    }

    public function getIdStaffByDataSync($value) {
        return $this->syncDB411Repository->getIdStaffByDataSync($value);
    }

    public function getVehicleNumber($id_user) {
        return $this->syncDB411Repository->getVehicleNumber($id_user);
    }

    public function getIdManufactureModelByDataSync($value) {
        return $this->syncDB411Repository->getIdManufactureModelByDataSync($value);
    }

    public function getIdManufactureByDataSync($value) {
        return $this->syncDB411Repository->getIdManufactureByDataSync($value);
    }

    public function getTypeStaffIdByDataSync($value) {
        return $this->syncDB411Repository->getTypeStaffIdByDataSync($value);
    }

    public function getIdTypeStaffByDataSync($value) {
        return $this->syncDB411Repository->getIdTypeStaffByDataSync($value);
    }

    // Check user id exist in table membership card?
    public function checkUserIdMembershipCard($id_user) {
        return $this->syncDB411Repository->checkUserIdMembershipCard($id_user);
    }

    public function getDataSyncStaffUpdate() {
        return $this->syncDB411Repository->getDataSyncStaffUpdate();
    }

    public function checkUserIdExist($id_user) {
        return $this->syncDB411Repository->checkUserIdExist($id_user);
    }

    public function getPartnerId() {
        return $this->syncDB411Repository->getPartnerId();
    }
}