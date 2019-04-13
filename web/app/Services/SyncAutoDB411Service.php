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
use App\Repositories\SyncAutoDB411Repository;

class SyncAutoDB411Service {
    protected $syncAutoDB411Repository;

	public function __construct(SyncAutoDB411Repository $syncAutoDB411Repository) {
		$this->syncAutoDB411Repository = $syncAutoDB411Repository;
    }
    //general
    public function getLastIdAppConfigSyncAuto($value) {
        return $this->syncAutoDB411Repository->getLastIdAppConfigSyncAuto($value);
    }

    public function saveMaxIdSyncAuto($key, $numeric_value, $text_value) {
        return $this->syncAutoDB411Repository->saveMaxIdSyncAuto($key, $numeric_value, $text_value);
    }
    //Chi nhanh

    public function getDataSyncBranchAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncBranchAuto($value);
    }

    public function checkValueExist($value) {
        return $this->syncAutoDB411Repository->checkValueExist($value);
    }

    public function syncBranchAuto($id_sync_branch, $name, $address, $phone, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $saveBranchSync = $this->syncAutoDB411Repository->saveBranchSyncAuto($id_sync_branch, $name, $address, $phone, $myJSON);
            
            } elseif ($update == true) {
                $updateBranchSync = $this->syncAutoDB411Repository->updateBranchSyncAuto($id_sync_branch, $name, $address, $phone, $myJSON);
            
            } elseif ($delete == true) {
                $deleteBranchSync = $this->syncAutoDB411Repository->deleteBranchSyncAuto($id_sync_branch);
            
            }
            if (isset($saveBranchSync) || isset($updateBranchSync) || isset($deleteBranchSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync branch id_sync_branch: {$id_sync_branch}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync branch id_sync_branch: {$id_sync_branch}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    // NHOM NHAN VIEN

    public function getDataSyncGroupStaffAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncGroupStaffAuto($value);
    }

    public function syncGroupStaffAuto($id_group_staff_sync, $name, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $saveGroupStaffSync = $this->syncAutoDB411Repository->saveGroupStaffSyncAuto($id_group_staff_sync, $name, $myJSON);
            
            } elseif ($update == true) {
                $updateGroupStaffSync = $this->syncAutoDB411Repository->updateGroupStaffSyncAuto($id_group_staff_sync, $name, $myJSON);
            
            } elseif ($delete == true) {
                $deleteGroupStaffSync = $this->syncAutoDB411Repository->deleteGroupStaffSyncAuto($id_group_staff_sync);
            
            }
            if (isset($saveGroupStaffSync) || isset($updateGroupStaffSync) || isset($deleteGroupStaffSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync group staff id_group_staff_sync: {$id_group_staff_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync group staff id_group_staff_sync: {$id_group_staff_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    //Nhan Vien

    public function getDataSyncStaffAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncStaffAuto($value);
    }

    public function getIdBranchByDataSync($value) {
        return $this->syncAutoDB411Repository->getIdBranchByDataSync($value);
    }

    public function getTypeStaffIdByDataSync($value) {
        return $this->syncAutoDB411Repository->getTypeStaffIdByDataSync($value);
    }

    public function getIdTypeStaffByDataSync($value) {
        return $this->syncAutoDB411Repository->getIdTypeStaffByDataSync($value);
    }

    public function checkPhoneExist($phone, $type) {
        return $this->syncAutoDB411Repository->checkPhoneExist($phone, $type);
    }

    public function updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id) {
        $timestamp = Carbon::now();
        try {
            $update_meta = $this->syncAutoDB411Repository->updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id);
            if (isset($update_meta)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update meta id_user: {$id_user}";
                Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update meta isd_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function updateTypeStaff($id_user, $staff_type_id) {
        $timestamp = Carbon::now();
        try {
            $updateTypeStaff = $this->syncAutoDB411Repository->updateTypeStaff($id_user, $staff_type_id) ;
            if (isset($updateTypeStaff)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update staff_type_id id_user: {$id_user}";
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update staff_type_id card id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function syncStaffAuto($id_sync_staff, $name, $phone, $email, $address, $id_branch, $type_staff_id, $myJSON, $insert, $update, $delete) {
        DB::beginTransaction();
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $type = EUser::TYPE_STAFF;
                $saveUserStaffSync = $this->syncAutoDB411Repository->saveUserSyncAuto($id_sync_staff, $id_sync_user = null, $name, $phone, $email, $address, $type, $type_staff_id, $myJSON);
                $id_user = $saveUserStaffSync->id;
                $saveBranchStaffSync = $this->syncAutoDB411Repository->saveBranchStaffSyncAuto($id_user, $id_branch, $myJSON);
                if($address != null && $address != "") {
                    $saveUserAddress = $this->syncAutoDB411Repository->saveUserAddressAuto($id_user, $name, $phone, $address);
                }
                if (isset($saveUserStaffSync) && isset($saveBranchStaffSync)) {
                    $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! save sync staff id_sync: $id_sync_staff";
                    Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            
            } elseif ($update == true) {
                $updateUserStaffSync = $this->syncAutoDB411Repository->updateUserStaffSyncAuto($id_sync_staff, $name, $phone, $email, $address, $type_staff_id, $myJSON);
                $updateBranchStaffSync = $this->syncAutoDB411Repository->updateBranchStaffSyncAuto($id_sync_staff, $id_branch, $myJSON);
                
                if (isset($updateUserStaffSync) && isset($updateBranchStaffSync)) {
                    $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update sync staff id_sync_staff: $id_sync_staff";
                    Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } elseif ($delete == true) {
                $deleteUserStaffSync = $this->syncAutoDB411Repository->deleteUserStaffSyncAuto($id_sync_staff);
                $deleteBranchStaffSync = $this->syncAutoDB411Repository->deleteBranchStaffSyncAuto($id_sync_staff);
                if (isset($deleteUserStaffSync) && isset($deleteBranchStaffSync)) {
                    $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! detele sync staff id_sync_staff: $id_sync_staff";
                    Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync group staff id_group_staff_sync: {$id_group_staff_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    //User 
    public function getDataSyncUserAuto1($value) {  //table: dbo. KhachHangNgoai
        return $this->syncAutoDB411Repository->getDataSyncUserAuto1($value);
    }

    public function syncUserAuto($id_sync_user, $name, $phone, $email, $address, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $type = EUser::TYPE_USER;
                $saveUserStaffSync = $this->syncAutoDB411Repository->saveUserSyncAuto($id_sync_staff = null, $id_sync_user, $name, $phone, $email, $address, $type, $type_staff_id = null, $myJSON);
            
            } elseif ($update == true) {
                $updateUserSync = $this->syncAutoDB411Repository->updateUserSyncAuto($id_sync_user, $name, $phone, $email, $address, $myJSON);
            
            } elseif ($delete == true) {
                $deleteUserSync = $this->syncAutoDB411Repository->deleteUserSyncAuto($id_sync_user);
            
            }
            if (isset($saveUserStaffSync) || isset($updateUserSync) || isset($deleteUserSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync user id_sync_user: {$id_sync_user}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync user id_sync_user: {$id_sync_user}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getDataSyncUserAuto2($value) {
        return $this->syncAutoDB411Repository->getDataSyncUserAuto2($value);
    }

    //Membership Card

    public function getDataSyncManufactureAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncManufactureAuto($value);
    }

    public function syncManufactureAuto($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $type = EUser::TYPE_USER;
                $saveManufactureSync = $this->syncAutoDB411Repository->saveManufactureModelVehicleSync($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON);
            
            } elseif ($update == true) {
                $updateManufactureSync = $this->syncAutoDB411Repository->updateManufactureSyncAuto($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON);
            
            } elseif ($delete == true) {
                $deleteManufactureSync = $this->syncAutoDB411Repository->deleteManufactureSyncAuto($id_manufacture_sync);
            
            }
            if (isset($saveUserStaffSync) || isset($updateUserSync) || isset($deleteUserSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync Manufacture id_group_staff_sync: {$id_group_staff_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync manufature id_group_staff_sync: {$id_group_staff_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getDataSyncModelAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncModelAuto($value);
    }

    public function getIdManufactureByDataSync($value) {
        return $this->syncAutoDB411Repository->getIdManufactureByDataSync($value);
    }


    public function syncModelAuto($id_model_sync, $name, $type, $parent_category_id, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $type = EUser::TYPE_USER;
                $saveModelSync = $this->syncAutoDB411Repository->saveManufactureModelVehicleSync($id_model_sync, $name, $type, $parent_category_id, $myJSON);
            
            } elseif ($update == true) {
                $updateModelSync = $this->syncAutoDB411Repository->updateModelSyncAuto($id_model_sync, $name, $type, $parent_category_id, $myJSON);
            
            } elseif ($delete == true) {
                $deleteModelSync = $this->syncAutoDB411Repository->deleteModelSyncAuto($id_model_sync);
            
            }
            if (isset($saveModelSync) || isset($updateModelSync) || isset($deleteModelSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync model id_model_sync: {$id_model_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync model id_model_sync: {$id_model_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getDataSyncMemberShipCardAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncMemberShipCardAuto($value);
    }

    public function getIdUserByDataSync($value) {
        return $this->syncAutoDB411Repository->getIdUserByDataSync($value);
    }

    public function getIdManufactureModelByDataSync($value) {
        return $this->syncAutoDB411Repository->getIdManufactureModelByDataSync($value);
    }

    public function checkUserIdMembershipCard($value) {
        return $this->syncAutoDB411Repository->checkUserIdMembershipCard($value);
    }

    public function syncMemberShipCardAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON, $insert, $update, $delete) {
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                $type = EUser::TYPE_USER;
                $saveMemberShipCardSync = $this->syncAutoDB411Repository->saveMemberShipCardSyncAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
            
            } elseif ($update == true) {
                $updateMemberShipCardSync = $this->syncAutoDB411Repository->updateMemberShipCardSyncAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
            
            } elseif ($delete == true) {
                $deleteMemberShipCardSync = $this->syncAutoDB411Repository->deteleMemberShipCardSyncAuto($id_sync_membership_card);
            
            }
            if (isset($saveUserStaffSync) || isset($updateUserSync) || isset($deleteUserSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync model id_model_sync: {$id_model_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync model id_model_sync: {$id_model_sync}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getDataSyncCardMember_km_TheVIP($value) {
        return $this->syncAutoDB411Repository->getDataSyncCardMember_km_TheVIP($value);
    }

    public function setMemberShipCardIsNotActive($value) {
        return $this->syncAutoDB411Repository->setMemberShipCardIsNotActive($value);
    }

    public function updateMemberShipCard_kmTheVIP($value, $code, $created_at, $approved_at, $expired_at) {
        return $this->syncAutoDB411Repository->updateMemberShipCard_kmTheVIP($value, $code, $created_at, $approved_at, $expired_at);
    }

    // Order

    public function getDataSyncOrderAuto($value) {
        return $this->syncAutoDB411Repository->getDataSyncOrderAuto($value);
    }

    public function getIdStaffByDataSync($value) {
        return $this->syncDB411Repository->getIdStaffByDataSync($value);
    }

    public function getVehicleNumber($id_user) {
        return $this->syncDB411Repository->getVehicleNumber($id_user);
    }

    public function getDetailRepairVehicle($value) {
        return $this->syncDB411Repository->getDetailRepairVehicle($value);
    }

    public function syncOrderAuto($id_user, $vehicle_number, $id_sync_order, $code, $price, $name, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON, $insert, $update, $delete) {
        
        $timestamp = Carbon::now();
        try {
            if ($insert == true) {
                DB::beginTransaction();
                $saveOrderSync = $this->syncDB411Repository->saveOrderAuto($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON);
                $order_id = $saveOrder->id;
                $saveOrderDetail = $this->syncDB411Repository->saveOrderDetailAuto($order_id, $name, $created_at, $type = EOrderType::ARBITRARY_SERVICE_ORDER, $price = null, $quantity = null, $myJSON);
                if (isset($saveOrder) && isset($saveOrderDetail)) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } elseif ($update == true) {
                $updateOrderSync = $this->syncAutoDB411Repository->updateOrderSyncAuto($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON);
            
            } elseif ($delete == true) {
                $deleteOrderSync = $this->syncAutoDB411Repository->deteleOrderSyncAuto($code);
            
            }
            if (isset($saveOrderSync) || isset($updateOrderSync) || isset($deleteOrderSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync order id_sync_order: {$id_sync_order}. Status: insert: {$insert}, update: {$update}, delete: {$delete}" ;
                Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync order id_sync_order: {$id_sync_order}. Status: insert: {$insert}, update: {$update}, delete: {$delete} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function saveOrderDetailRepairVehicle($order_id, $name, $created_at, $type, $price, $quantity, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $saveOrderDetail = $this->syncDB411Repository->saveOrderDetail($order_id, $name, $created_at, $type, $price, $quantity, $myJSON);
            if (isset($saveOrderDetail)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync order_detail repair vehicle order_id: {$order_id}";
                Storage::append("error_sync_auto/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Error! Failed order_detail repair vehicle order_id: {$order_id} . Error: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
}