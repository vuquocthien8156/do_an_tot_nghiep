<?php

namespace App\Console\Commands;

use App\Constant\ConfigKey;
use App\Enums\ENotificationType;
use App\Enums\ENotificationScheduleType;
use App\Enums\EStatus;
use App\Enums\EAppointmentType;
use App\Enums\EDateFormat;
use App\Enums\EManufacture;
use App\Enums\EUser;
use App\Helpers\ConfigHelper;
use App\Traits\CommonTrait;
use App\Services\SyncUpdateDB411Service;
use App\Services\SyncDB411Service;
use App\Services\SyncAutoDB411Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SyncDB411Auto extends Command {
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-auto:411
                            {--sync-auto-all : Sync Update All Data DB 411}
                            {--sync-auto-test-function : Sync Test Function}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct(SyncDB411Service $syncDB411Service, SyncUpdateDB411Service $syncUpdateDB411Service, SyncAutoDB411Service $syncAutoDB411Service) {
        parent::__construct();
        $this->syncUpdateDB411Service = $syncUpdateDB411Service;
        $this->syncDB411Service = $syncDB411Service;
        $this->syncAutoDB411Service = $syncAutoDB411Service;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() { 
        if ($this->option('sync-auto-all')) {
            $this->Sync_Auto_Branch();
            $this->Sync_Auto_GroupStaff();
            $this->Sync_Auto_User_1();
            $this->Sync_Auto_User_2();
            $this->Sync_Auto_Staff();
            $this->Sync_Manufacture_Auto();
            $this->Sync_Model_Auto();
            $this->Sync_MembershipCard_Auto();
            $this->Sync_MemberShipCard_Auto_2();
            $this->Sync_Order_Auto();
        }
        if ($this->option('sync-auto-test-function')) {
            $this->Sync_MemberShipCard_Auto_2();
        }
    } 

    //branch 
    public function Sync_Auto_Branch() {
        $keyBranchAuto = ConfigKey::LAST_SYNC_AUTO_BRANCH_ID;
        $maxIdBranchBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyBranchAuto);
        $syncBranchAuto = $this->syncAutoDB411Service->getDataSyncBranchAuto((int)$maxIdBranchBefore[0]->numeric_value);
        if (count($syncBranchAuto) > 0) {
            foreach ($syncBranchAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaCuaHang = $value->CTMaCuaHang;
                $myObj->TenCuaHang = $value->TenCuaHang;
                $myObj->DiaChi = $value->DiaChi;
                $myObj->SoDT = $value->SoDT;
                $myObj->MaHuyen = $value->MaHuyen;
                $myObj->MaNV = $value->MaNV;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($value->SYS_CHANGE_OPERATION == "I") {
                    $checkValueExist = $this->syncAutoDB411Service->checkValueExist($value->CTMaCuaHang);
                    if (isset($checkValueExist[0]->id)) {
                        $this->syncAutoDB411Service->syncBranchAuto($value->CTMaCuaHang, $value->TenCuaHang, $value->DiaChi, preg_replace('/\D/', '', $value->SoDT), $myJSON, $insert = false, $update = true, $delete = false);
                    } else {
                        $this->syncAutoDB411Service->syncBranchAuto($value->CTMaCuaHang, $value->TenCuaHang, $value->DiaChi, preg_replace('/\D/', '', $value->SoDT), $myJSON, $insert = true, $update = false, $delete = false);
                    }
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncBranchAuto($value->CTMaCuaHang, $value->TenCuaHang, $value->DiaChi, preg_replace('/\D/', '', $value->SoDT), $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncBranchAuto($value->CTMaCuaHang, $value->TenCuaHang, $value->DiaChi, preg_replace('/\D/', '', $value->SoDT), $myJSON, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyBranchAuto, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_branch_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }
    //Staff
    public function Sync_Auto_GroupStaff() {
        $keyGroupStaff = ConfigKey::LAST_SYNC_AUTO_GROUP_STAFF_ID;
        $maxIdGroupStaffBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyGroupStaff);
        $syncGroupStaffAuto = $this->syncAutoDB411Service->getDataSyncGroupStaffAuto((int)$maxIdGroupStaffBefore[0]->numeric_value);
        if (count($syncGroupStaffAuto) > 0) {
            foreach ($syncGroupStaffAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->CT_ID = $value->CT_ID;
                $myObj->TenNhom = $value->TenNhom;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($value->SYS_CHANGE_OPERATION == "I") {

                    $this->syncAutoDB411Service->syncGroupStaffAuto($value->CT_ID, $value->TenNhom, $myJSON, $insert = true, $update = false, $delete = false);
                
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    
                    $this->syncAutoDB411Service->syncGroupStaffAuto($value->CT_ID, $value->TenNhom, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncGroupStaffAuto($value->CT_ID, $value->TenNhom, $myJSON, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyGroupStaff, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function Sync_Auto_Staff() {
        $keyStaff = ConfigKey::LAST_SYNC_AUTO_STAFF_ID;
        $maxIdStaffBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyStaff);
        $syncStaffAuto = $this->syncAutoDB411Service->getDataSyncStaffAuto((int)$maxIdStaffBefore[0]->numeric_value);
        if (count($syncStaffAuto) > 0) {
            foreach ($syncStaffAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaNV = $value->CT_MaNV;
                $myObj->HoTenNV = $value->HoTenNV;
                $myObj->Mobile = $value->Mobile;
                $myObj->DiaChi = $value->DiaChi;
                $myObj->CMND = $value->CMND;
                $myObj->MaCuaHang = $value->MaCuaHang;
                $myObj->MaCV = $value->MaCV;
                $myObj->MaBoPhan = $value->MaBoPhan;
                $myObj->MaNhomNhanVien = $value->MaNhomNhanVien;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $branch_data_id = $this->syncAutoDB411Service->getIdBranchByDataSync($value->MaCuaHang);
                $type_staff_data_id = $this->syncAutoDB411Service->getTypeStaffIdByDataSync($value->MaNhomNhanVien);
                $data_user_staff_id = $this->syncAutoDB411Service->getIdTypeStaffByDataSync($value->CT_MaNV);

                $branch_id = isset($branch_data_id[0]->id) ? $branch_data_id[0]->id : null;
                $staff_type_id = isset($type_staff_data_id[0]->id) ? $type_staff_data_id[0]->id : null;

                if ($value->SYS_CHANGE_OPERATION == "I") {
                    if (!isset($data_user_staff_id[0]->id)) {
                        if ($value->Mobile != null && preg_replace('/\D/', '', $value->Mobile) != "") {
                            $check_phone_exist = $this->syncAutoDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->Mobile), $type = EUser::TYPE_STAFF);
                            if (isset($check_phone_exist[0]->id)) {
                                $id_user = $check_phone_exist[0]->id;
                                $meta_old = $check_phone_exist[0]->meta;
                                if ($meta_old == null) {
                                    $this->syncAutoDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type, $staff_type_id);
                                } else {
                                    $myJson_Old = json_decode($meta_old, true);
                                    array_push($myJson_Old['syncData'], $myObj2);
                                    $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    $type = EUser::TYPE_STAFF;
                                    $this->syncAutoDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type, $staff_type_id);
                                }
                            } else {
                                $this->syncAutoDB411Service->syncStaffAuto($value->CT_MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON, $insert = true, $update = false, $delete = false);
                            }
                        } else {
                            $this->syncAutoDB411Service->syncStaffAuto($value->CT_MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON, $insert = true, $update = false, $delete = false);
                        }
                    } else {
                        $type_staff_data_id_2 = $this->syncAutoDB411Service->getTypeStaffIdByDataSync($val = 2);
                        $staff_type_id_2 = isset($type_staff_data_id_2[0]->id) ? $type_staff_data_id_2[0]->id : null;
                        $this->syncAutoDB411Service->updateTypeStaff($data_user_staff_id[0]->id, $staff_type_id_2);
                    }

                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncStaffAuto($value->CT_MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncStaffAuto($value->CT_MaNV, null, null, null, null, null, null, null, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyStaff, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }
    //User
    public function Sync_Auto_User_1() { // Table: dbo.KhachHangNgoai
        $keyUser1 = ConfigKey::LAST_SYNC_AUTO_USER_ID_1;
        $maxIdUserBefore1 = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyUser1);
        $syncUserAuto1 = $this->syncAutoDB411Service->getDataSyncUserAuto1((int)$maxIdUserBefore1[0]->numeric_value);
        if (count($syncUserAuto1) > 0) {
            foreach ($syncUserAuto1 as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaKH = $value->CT_MaKH;
                $myObj->HoTenKH = $value->HoTenKH;
                $myObj->DTKH = $value->DTKH;
                $myObj->EmailKH = $value->EmailKH;
                $myObj->DiaChiKH = $value->DiaChiKH;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                if ($value->SYS_CHANGE_OPERATION == "I") {
                    if ($value->DTKH != null && preg_replace('/\D/', '', $value->DTKH) != "") {
                        $check_phone_exist = $this->syncAutoDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->DTKH), $type = EUser::TYPE_USER);
                        if (isset($check_phone_exist[0]->id)) {
                            $id_user = $check_phone_exist[0]->id;
                            $meta_old = $check_phone_exist[0]->meta;
                            if ($meta_old == null) {
                                $this->syncAutoDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type = null,  $staff_type_id = null);
                            } else {
                                $myJson_Old = json_decode($meta_old, true);
                                array_push($myJson_Old['syncData'], $myObj2);
                                $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $this->syncAutoDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type = null,  $staff_type_id = null);
                            }
                        } else {
                            $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON, $insert = true, $update = false, $delete = false);
                        }
                    } else {
                        $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON, $insert = true, $update = false, $delete = false);
                    }

                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, null, null, null, null, null, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyUser1, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function Sync_Auto_User_2() { // Table: dbo.KhachHangNgoai
        $keyUser2 = ConfigKey::LAST_SYNC_AUTO_USER_ID_2;
        $maxIdUserBefore2 = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyUser2);
        $syncUserAuto2 = $this->syncAutoDB411Service->getDataSyncUserAuto2((int)$maxIdUserBefore2[0]->numeric_value);
        //dd($maxIdUserBefore2, $syncUserAuto2);
        if (count($syncUserAuto2) > 0) {
            foreach ($syncUserAuto2 as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaKH = $value->CT_MaKH;
                $myObj->HoTen = $value->HoTen;
                $myObj->DTDiDong = $value->DTDiDong;
                $myObj->EmailKH = $value->EmailKH;
                $myObj->DiaChi = $value->DiaChi;
                $myObj->MaHieuKH = $value->MaHieuKH;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                if ($value->SYS_CHANGE_OPERATION == "I") {
                    if ($value->DTDiDong != null && preg_replace('/\D/', '', $value->DTDiDong) != "") {
                        $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->DTDiDong), $type = EUser::TYPE_USER);
                        if (isset($check_phone_exist[0]->id)) {
                            $id_user = $check_phone_exist[0]->id;
                            $meta_old = $check_phone_exist[0]->meta;
                            if ($meta_old == null) {
                                $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type = null, $staff_type_id = null);
                            } else {
                                $myJson_Old = json_decode($meta_old, true);
                                array_push($myJson_Old['syncData'], $myObj2);
                                $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type = null, $staff_type_id = null);
                            }
                        } else {
                            $this->syncDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON, $insert = true, $update = false, $delete = false);
                        }
                    } else {
                        $this->syncDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON, $insert = true, $update = false, $delete = false);
                    }

                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncUserAuto($value->CT_MaKH, null, null, null, null, null, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyUser2, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }
    // Membershipcard

    public function Sync_Manufacture_Auto() {
        $keyManufactureAuto = ConfigKey::LAST_SYNC_AUTO_MANUFACTURE_ID;
        $type = EManufacture::MANUFACTURE;
        $maxIdManufactureBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyManufactureAuto);
        $syncManufactureAuto = $this->syncAutoDB411Service->getDataSyncManufactureAuto((int)$maxIdManufactureBefore[0]->numeric_value);
        
        if (count($syncManufactureAuto) > 0) {
            foreach ($syncManufactureAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaHangXe = $value->CT_MaHangXe;
                $myObj->TenHangXe = $value->TenHangXe;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($value->SYS_CHANGE_OPERATION == "I") {
                    $this->syncAutoDB411Service->syncManufactureAuto($value->MaHangXe, $value->TenHangXe, $type, $parent_category_id = null, $myJSON, $insert = true, $update = false, $delete = false);
                
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {

                    $this->syncAutoDB411Service->syncManufactureAuto($value->MaHangXe, $value->TenHangXe, $type, $parent_category_id = null, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncManufactureAuto($value->MaHangXe, null, null, null, null, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyManufactureAuto, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_manufacture_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function Sync_Model_Auto() {
        $keyModelAuto = ConfigKey::LAST_SYNC_AUTO_MODEL_ID;
        $type = EManufacture::MANUFACTURE_MODEL;
        $maxIdModelBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyModelAuto);
        $syncModelAuto = $this->syncAutoDB411Service->getDataSyncModelAuto((int)$maxIdModelBefore[0]->numeric_value);
        if (count($syncModelAuto) > 0) {
            foreach ($syncModelAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[]; 
                $myObj3 = (object)[]; 
                $myObj->MaLoai = $value->CT_MaLoai;
                $myObj->TenLoai = $value->TenLoai; 
                $myObj->MaHangXe = $value->MaHangXe;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $id_manufacture_data = $this->syncAutoDB411Service->getIdManufactureByDataSync($value->MaHangXe);
                $parent_category_id = isset($id_manufacture_data[0]->id) ? $id_manufacture_data[0]->id : null;
                if ($value->SYS_CHANGE_OPERATION == "I") {
                    $this->syncAutoDB411Service->syncModelAuto($value->CT_MaLoai, $value->TenLoai, $type, $parent_category_id, $myJSON, $insert = true, $update = false, $delete = false);
                
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncModelAuto($value->CT_MaLoai, $value->TenLoai, $type, $parent_category_id, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncModelAuto($value->CT_MaLoai, null, null, null, null, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyModelAuto, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_model_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function Sync_MembershipCard_Auto() {
        $keyMembershipCardAuto = ConfigKey::LAST_SYNC_AUTO_MEMBERSHIPCARD_ID;
        $maxIdMemberShipCardBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyMembershipCardAuto);
        $syncMembershipCardAuto = $this->syncAutoDB411Service->getDataSyncMemberShipCardAuto((int)$maxIdMemberShipCardBefore[0]->numeric_value);
        //dd($maxIdMemberShipCardBefore, $syncMembershipCardAuto);
        if (count($syncMembershipCardAuto) > 0) {
            foreach ($syncMembershipCardAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->ID = $value->ID;
                $myObj->SoThe = $value->SoThe;
                $myObj->MaVach = $value->MaVach;
                $myObj->NgayPH = $value->NgayPH;
                $myObj->NgayHL = $value->NgayHL;
                $myObj->NgayHH = $value->NgayHH;
                $myObj->MaKH = $value->MaKH;
                $myObj->NgayNhap = $value->NgayNhap;
                $myObj->TienThe = $value->TienThe;
                $myObj->DienGiai = $value->DienGiai;
                $myObj->MaXe = $value->CT_MaXe;
                $myObj->BienSoXe = $value->BienSoXe;
                $myObj->SoMay = $value->SoMay;
                $myObj->SoKhung = $value->SoKhung;
                $myObj->DongXe = $value->DongXe;
                $myObj->SoBaoHanh = $value->SoBaoHanh;
                $myObj->MaLoai = $value->MaLoai;
                $myObj->MauXe = $value->MauXe;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                $id_user_data = $this->syncAutoDB411Service->getIdUserByDataSync($value->MaKH);
                $id_manufacture_model_data = $this->syncAutoDB411Service->getIdManufactureModelByDataSync($value->MaLoai);

                $id_manufacture = isset($id_manufacture_model_data[0]->parent_category_id) ? $id_manufacture_model_data[0]->parent_category_id : null;
                $id_model = isset($id_manufacture_model_data[0]->id) ? $id_manufacture_model_data[0]->id : null;

                $id_user = isset( $id_user_data[0]->id) ? $id_user_data[0]->id : null;
                $name = isset( $id_user_data[0]->name) ? $id_user_data[0]->name : null;

                $check_user_id_exist = $this->syncAutoDB411Service->checkUserIdMembershipCard($id_user);
                $status = isset($check_user_id_exist[0]->id) ? 2 : EStatus::ACTIVE;
                if ($value->MaVach != null) {
                    $approved = true;
                    $vehicle_card_status = 1;
                } else {
                    $approved = false;
                    $vehicle_card_status = 2;
                }
                $created_at = ($value->NgayPH != null) ? Carbon::parse($value->NgayPH) : null;
                $approved_at = ($value->NgayHL != null) ? Carbon::parse($value->NgayHL) : null;
                $expired_at = ($value->NgayHH != null) ? Carbon::parse($value->NgayHH) : null;

                if ($value->SYS_CHANGE_OPERATION == "I") {
                    $this->syncAutoDB411Service->syncMemberShipCardAuto($value->CT_MaXe, $id_user, $status, $name, $value->BienSoXe, $value->MaVach, $id_manufacture, $id_model, $value->MauXe, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON, $insert = true, $update = false, $delete = false);
                
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncMemberShipCardAuto($value->CT_MaXe, $id_user, $status, $name, $value->BienSoXe, $value->MaVach, $id_manufacture, $id_model, $value->MauXe, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncMemberShipCardAuto($value->CT_MaXe, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyMembershipCardAuto, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_model_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function Sync_MemberShipCard_Auto_2() {
        $keyMembershipCardAuto2 = ConfigKey::LAST_SYNC_AUTO_MEMBERSHIPCARD_ID_2;
        $maxIdMemberShipCardBefore2 = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyMembershipCardAuto2);
        $syncMembershipCardAuto2 = $this->syncAutoDB411Service->getDataSyncCardMember_km_TheVIP((int)$maxIdMemberShipCardBefore2[0]->numeric_value);
        if (count($syncMembershipCardAuto2) > 0) {
            foreach ($syncMembershipCardAuto2 as $key => $value) {
                if ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->updateMemberShipCard_kmTheVIP($value->CT_ID, $value->MaVach, $value->NgayPH, $value->NgayHL, $value->NgayHH);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->setMemberShipCardIsNotActive($value->CT_ID);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyMembershipCardAuto2, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        }
    }

    public function Sync_Order_Auto() {
        $keyOrderAuto = ConfigKey::LAST_SYNC_AUTO_ORDER_ID;
        $maxIdOrderBefore = $this->syncAutoDB411Service->getLastIdAppConfigSyncAuto($keyOrderAuto);
        $syncOrderAuto = $this->syncAutoDB411Service->getDataSyncOrderAuto((int)$maxIdOrderBefore[0]->numeric_value);
        if (count($syncOrderAuto) > 0) {
            foreach ($syncOrderAuto as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[]; 
                $myObj3 = (object)[]; 
                $myObj->MaPTBH = $value->CT_MaPTBH;
                $myObj->MaKH = $value->MaKH;
                $myObj->NgayCT = $value->NgayCT;
                $myObj->NgayHT = $value->NgayHT;
                $myObj->SoCT = $value->SoCT;
                $myObj->SoTienTra = $value->SoTienTra;
                $myObj->MaNV = $value->MaNV;
                $myObj->NoiDungNop = $value->NoiDungNop;
                $myObj->MaPSC = $value->MaPSC;
                $myObj->NhanVienSuaChua = $value->NhanVienSuaChua;
                $myObj->NgayThucHien = $value->NgayThucHien;
                $myObj->NgayGiaoXe = $value->NgayGiaoXe;
                $myObj->NoiDung = $value->NoiDung;
                $myObj->MaPT = $value->MaPT;
                $myObj->SoKM = $value->SoKM;
                $myObj->MaCH = $value->MaCH;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $id_user_data = $this->syncDB411Service->getIdUserByDataSync($value->MaKH);
                $branch_data_id = $this->syncDB411Service->getIdBranchByDataSync($value->MaCH);
                $branch_id = isset($branch_data_id[0]->id) ? $branch_data_id[0]->id : null;
                if ($value->NoiDungNop != null && $value->NoiDungNop != "") {
                    $content = $value->NoiDungNop;
                } else {
                    if ($value->NoiDung != null && $value->NoiDung != "") {
                        $content = $value->NoiDung;
                    } else {
                        $content = 'sửa chữa nhỏ';
                    }
                }
                if ($value->NhanVienSuaChua != null) {
                    $id_staff_data = $this->syncDB411Service->getIdStaffByDataSync($value->NhanVienSuaChua);
                } else {
                    $id_staff_data = $this->syncDB411Service->getIdStaffByDataSync($value->MaNV);
                }
                $id_staff = isset($id_staff_data[0]->staff_id) ? $id_staff_data[0]->staff_id : null;
                if (isset($id_user_data[0]->id)) {
                    $id_user = $id_user_data[0]->id;
                    $vehicle_number_data = $this->syncDB411Service->getVehicleNumber($id_user);
                    $vehicle_number = isset($vehicle_number_data[0]->vehicle_number) ? $vehicle_number_data[0]->vehicle_number : null;
                } else {
                    $id_user = null;
                    $timestamp = Carbon::now();
                    $content = "{$timestamp->format('Y:m:d H:i:s')}: Order: Not found data!! MaKH: {$value->MaKH}";
                    Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                }

                
                if ($value->SYS_CHANGE_OPERATION == "I") {
                    $saveOrderSync = $this->syncAutoDB411Service->syncOrderAuto($id_user, $vehicle_number, $value->CT_MaPTBH, $value->SoCT, $value->SoTienTra, $content, Carbon::parse($value->NgayCT), Carbon::parse($value->NgayHT), $id_staff, $value->SoKM, $branch_id, $myJSON, $insert = true, $update = false, $delete = false);
                    $order_id = $saveOrderSync->id;
                    if ($value->MaPSC != null) {
                        $dataDetailRepairVehicles = $this->syncDB411Service->getDetailRepairVehicle($value->MaPSC);
                        if (count($dataDetailRepairVehicles) > 0) { 
                            foreach ($dataDetailRepairVehicles as $index => $detailRepairVehicle) {
                                $myObj_CTSCX = (object)[];
                                $myObj_PT = (object)[];
                                $myObj2_CTSCX = (object)[]; 
                                $myObj2_PT = (object)[]; 
                                $myObj3_CTSCX = (object)[]; 
                                $myObj3_PT = (object)[]; 
                                $myObj3_PTBH = (object)[]; 
                                $myObj4 = (object)[];
    
                                $myObj_CTSCX->MaPSC = $detailRepairVehicle->MaPSC;
                                $myObj_CTSCX->MaPT = $detailRepairVehicle->MaPT;
                                $myObj_CTSCX->SoLuong = $detailRepairVehicle->SoLuong;
                                $myObj_CTSCX->DonGia = $detailRepairVehicle->DonGia;
                                $myObj_CTSCX->DienGiai = $detailRepairVehicle->DienGiai;
                                $myObj_CTSCX->GiaVon = $detailRepairVehicle->GiaVon;
                                $myObj_CTSCX->ChietKhau = $detailRepairVehicle->ChietKhau;
                                $myObj_CTSCX->ThueGTGT = $detailRepairVehicle->ThueGTGT;
    
                                $myObj2_CTSCX->hash = hash('sha256', serialize($myObj_CTSCX));
                                $myObj2_CTSCX->data = $myObj_CTSCX;
    
                                $myObj3_CTSCX->ChiTietSuaChuaXe = $myObj2_CTSCX;
    
                                $myObj_PT->MaPhuTung = $detailRepairVehicle->MaPhuTung;
                                $myObj_PT->TenPhuTung = $detailRepairVehicle->TenPhuTung;
                                $myObj_PT->MaVach = $detailRepairVehicle->MaVach;
                                $myObj_PT->MaQuyCach = $detailRepairVehicle->MaQuyCach;
                                $myObj_PT->LoaiXe = $detailRepairVehicle->LoaiXe;
                                $myObj_PT->MaKho = $detailRepairVehicle->MaKho;
    
                                $myObj2_PT->hash = hash('sha256', serialize($myObj_PT));
                                $myObj2_PT->data = $myObj_PT;
    
                                $myObj3_PT->PhuTung = $myObj2_PT;
    
                                $myObj3_PTBH->PhieuThuBanHang = $myObj2;
    
                                $myObj4->syncData = array($myObj3_PTBH, $myObj3_CTSCX, $myObj3_PT);
    
                                $myJSONDetail = json_encode($myObj4, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $saveOrderDetail = $this->syncDB411Service->saveOrderDetailRepairVehicle($order_id, $detailRepairVehicle->DienGiai, $created_at = null, $type = EOrderType::REPLACEABLE_ITEM, $detailRepairVehicle->DonGia, $detailRepairVehicle->SoLuong, $myJSONDetail);
                            }
                        }
                    }
                } elseif ($value->SYS_CHANGE_OPERATION == "U") {
                    $this->syncAutoDB411Service->syncOrderAuto($id_user, $vehicle_number, $value->CT_MaPTBH, $value->SoCT, $value->SoTienTra, $content, Carbon::parse($value->NgayCT), Carbon::parse($value->NgayHT), $id_staff, $value->SoKM, $branch_id, $myJSON, $insert = false, $update = true, $delete = false);

                } elseif ($value->SYS_CHANGE_OPERATION == "D") {
                    $this->syncAutoDB411Service->syncOrderAuto($id_user, $vehicle_number, $value->CT_MaPTBH, $value->SoCT, $value->SoTienTra, $content, Carbon::parse($value->NgayCT), Carbon::parse($value->NgayHT), $id_staff, $value->SoKM, $branch_id, $myJSON, $insert = false, $update = false, $delete = true);
                
                }
                $this->syncAutoDB411Service->saveMaxIdSyncAuto($keyOrderAuto, $value->SYS_CHANGE_VERSION, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync_auto/error_model_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }
}