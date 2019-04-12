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
use App\Enums\EOrderType;
use App\Helpers\ConfigHelper;
use App\Traits\CommonTrait;
use App\Services\SyncDB411Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SyncDB411 extends Command {
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:411 
                            {--sync-all : Sync All Data DB 411}
                            {--sync-staff : Sync Staff}
                            {--sync-user : Sync User}
                            {--sync-membership-card : Sync User}
                            {--sync-order : Sync Order}
                            {--sync-user2 : Sync User2 Table: dbo.KhachHang}
                            {--sync-branch : Sync Branch}
                            {--sync-manufacture : Sync Manufacture Vehicle}
                            {--sync-model-vehicle : Sync Model Vehicle}
                            {--sync-user1 : Sync User1 Table: dbo.KhachHangNgoai}
                            {--sync-update-membership-card : Sync Update MemberShip Card Table: dbo.kmTheVIP}
                            {--sync-test-function : Sync Test Function}';

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

    public function __construct(SyncDB411Service $syncDB411Service) {
        parent::__construct();
        $this->syncDB411Service = $syncDB411Service;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() { 
        if ($this->option('sync-all')) {
            $this->syncUser2(); //Table dbo.KhachHang
            $this->syncUser(); //Table dbo.KhachHangNgoai
            $this->syncBranch();
            $this->syncTypeStaff();
            $this->syncStaff(); // Then sync Staff
            $this->syncManufacture(); // Then sync Manufacture
            $this->syncModel(); // Then sync Manufacture
            $this->syncMemberShipCard(); // Then Memberbership Card
            $this->updateMembershipCard();
		    $this->syncOrder();
        }
        if ($this->option('sync-staff')) {
            $this->syncTypeStaff();
            $this->syncBranch();
            $this->syncStaff();
        }
        if ($this->option('sync-user')) {
            $this->syncUser2(); //Table dbo.KhachHang
            $this->syncUser(); //Table dbo.KhachHangNgoai
        }
        if ($this->option('sync-membership-card')) {
            $this->syncManufacture(); 
            $this->syncModel(); 
            $this->syncMemberShipCard();
            $this->updateMembershipCard();
        }
        if ($this->option('sync-order')) {
		    $this->syncOrder(); //dbo.PhieuMua_CuaHang, PhieuSuaXe
        }
        if ($this->option('sync-user1')) {
            $this->syncUser(); //Table dbo.KhachHangNgoai
        }
        if ($this->option('sync-user2')) {
            $this->syncUser2(); //Table dbo.KhachHang
        }
        if ($this->option('sync-manufacture')) {
            $this->syncManufacture(); //Table dbo.HangXe
        }
        if ($this->option('sync-model-vehicle')) {
            $this->syncModel(); //Table dbo.LoaiXe
        }
        if ($this->option('sync-branch')) {
            $this->syncBranch(); //Table dbo.CuaHang
        }
        if ($this->option('sync-update-membership-card')) {
            $this->updateMembershipCard(); //Table dbo.kmTheVIP
        }
        if ($this->option('sync-test-function')) {
            $this->updateMembershipCard();
        }
    }

    //Sync Branch  Table: dbo.CuaHang
    public function syncBranch() {
        $keyBranch = ConfigKey::LAST_SYNC_BRANCH_ID;
        $maxIdBranchBefore = $this->syncDB411Service->getMaxIdSync($keyBranch);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id branch: " .  $maxIdBranchBefore[0]->numeric_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncBranch = $this->syncDB411Service->getDataSyncBranch((int)$maxIdBranchBefore[0]->numeric_value);
        if (count($syncBranch) > 0) {
            foreach ($syncBranch as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaCuaHang = $value->MaCuaHang;
                $myObj->TenCuaHang = $value->TenCuaHang;
                $myObj->DiaChi = $value->DiaChi;
                $myObj->SoDT = $value->SoDT;
                $myObj->MaHuyen = $value->MaHuyen;
                $myObj->MaNV = $value->MaNV;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $this->syncDB411Service->saveBranchSync($value->MaCuaHang, $value->TenCuaHang, $value->DiaChi, preg_replace('/\D/', '', $value->SoDT), $myJSON);
                $this->syncDB411Service->saveMaxIdSync($keyBranch, $value->MaCuaHang, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: All data synced already!!";
            Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    // sync Staff Table: dbo.NhanVien
    public function syncStaff() {
        //Syn Staff
        $keyStaff = ConfigKey::LAST_SYNC_STAFF_ID;
        $maxIdStaffBefore = $this->syncDB411Service->getMaxIdSync($keyStaff);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id staff: " . $maxIdStaffBefore[0]->numeric_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncStaff = $this->syncDB411Service->getDataSyncStaff((int)$maxIdStaffBefore[0]->numeric_value);

        if (count($syncStaff) > 0) {
            foreach ($syncStaff as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaNV = $value->MaNV;
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
                $branch_data_id = $this->syncDB411Service->getIdBranchByDataSync($value->MaCuaHang);
                $type_staff_data_id = $this->syncDB411Service->getTypeStaffIdByDataSync($value->MaNhomNhanVien);
                $data_user_staff_id = $this->syncDB411Service->getIdTypeStaffByDataSync($value->MaNV);

                $branch_id = isset($branch_data_id[0]->id) ? $branch_data_id[0]->id : null;
                $staff_type_id = isset($type_staff_data_id[0]->id) ? $type_staff_data_id[0]->id : null;

                if (!isset($data_user_staff_id[0]->id)) {
                    if ($value->Mobile != null && preg_replace('/\D/', '', $value->Mobile) != "") {
                        $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->Mobile), $type = EUser::TYPE_STAFF);
                        if (isset($check_phone_exist[0]->id)) {
                            $id_user = $check_phone_exist[0]->id;
                            $meta_old = $check_phone_exist[0]->meta;
                            if ($meta_old == null) {
                                $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type, $staff_type_id);
                            } else {
                                $myJson_Old = json_decode($meta_old, true);
                                array_push($myJson_Old['syncData'], $myObj2);
                                $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $type = EUser::TYPE_STAFF;
                                $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type, $staff_type_id);
                            }
                            //$this->syncDB411Service->saveStaffSyncHasUserId($id_user, $branch_id, $myJSON);
                        } else {
                            $this->syncDB411Service->saveStaffSync($value->MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON);
                        }
                    } else {
                        $this->syncDB411Service->saveStaffSync($value->MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON);
                    }
                } else {
                    $type_staff_data_id_2 = $this->syncDB411Service->getTypeStaffIdByDataSync($val = 2);
                    $staff_type_id_2 = isset($type_staff_data_id_2[0]->id) ? $type_staff_data_id_2[0]->id : null;
                    $this->syncDB411Service->updateTypeStaff($data_user_staff_id[0]->id, $staff_type_id_2);
                }
                $this->syncDB411Service->saveMaxIdSync($keyStaff, $value->MaNV, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Staff: All data synced already!!";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    // Sync User Table: dbo.KhachHangNgoai
    public function syncUser() {
        $keyUser = ConfigKey::LAST_SYNC_USER_ID;
        $maxIdUserfBefore = $this->syncDB411Service->getMaxIdSync($keyUser);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id user dbo.KhachHangNgoai :" . $maxIdUserfBefore[0]->numeric_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncUser = $this->syncDB411Service->getDataSyncUser((int)$maxIdUserfBefore[0]->numeric_value);
        if (count($syncUser) > 0) {
            foreach ($syncUser as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaKH = $value->MaKH;
                $myObj->HoTenKH = $value->HoTenKH;
                $myObj->DTKH = $value->DTKH;
                $myObj->EmailKH = $value->EmailKH;
                $myObj->DiaChiKH = $value->DiaChiKH;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($value->DTKH != null && preg_replace('/\D/', '', $value->DTKH) != "") {
                    $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->DTKH), $type = EUser::TYPE_USER);
                    if (isset($check_phone_exist[0]->id)) {
                        $id_user = $check_phone_exist[0]->id;
                        $meta_old = $check_phone_exist[0]->meta;
                        if ($meta_old == null) {
                            $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type = null,  $staff_type_id = null);
                        } else {
                            $myJson_Old = json_decode($meta_old, true);
                            array_push($myJson_Old['syncData'], $myObj2);
                            $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type = null,  $staff_type_id = null);
                        }
                    } else {
                        $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON);
                    }
                } else {
                    $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON);
                }
                $this->syncDB411Service->saveMaxIdSync($keyUser, (int)$value->MaKH, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content= "{$timestamp->format('Y:m:d H:i:s')}: User: All data synced already!!";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    // Sync User Table: dbo.KhachHang
    public function syncUser2() {
        $keyUser2 = ConfigKey::LAST_SYNC_USER_ID_2;
        $maxIdUserfBefore2 = $this->syncDB411Service->getMaxIdSync($keyUser2);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id user2 dbo.KhachHang: " . $maxIdUserfBefore2[0]->text_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncUser2 = $this->syncDB411Service->getDataSyncUser2($maxIdUserfBefore2[0]->text_value);
        if (count($syncUser2) > 0) {
            foreach ($syncUser2 as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaKH = $value->MaKH;
                $myObj->HoTen = $value->HoTen;
                $myObj->DTDiDong = $value->DTDiDong;
                $myObj->EmailKH = $value->EmailKH;
                $myObj->DiaChi = $value->DiaChi;
                $myObj->MaHieuKH = $value->MaHieuKH;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
                        $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON);
                    }
                } else {
                    $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON);
                }
                $this->syncDB411Service->saveMaxIdSync($keyUser2, $numeric_value = null, $text_value = $value->MaKH);
            }
        } else {
            $timestamp = Carbon::now();
            $content= "{$timestamp->format('Y:m:d H:i:s')}: User: All data synced already!!";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function syncMemberShipCard() {
        $keyMemberShipCard = ConfigKey::LAST_SYNC_MEMBERSHIP_CARD_ID;
        $maxIdMemberBefore = $this->syncDB411Service->getMaxIdSync($keyMemberShipCard);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id membership_card: " . $maxIdMemberBefore[0]->text_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncMemberShipCard = $this->syncDB411Service->getDataSyncCardMember($maxIdMemberBefore[0]->text_value, $pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncMemberShipCard->lastPage();
            }
            if (count($syncMemberShipCard) > 0) {
                foreach ($syncMemberShipCard as $key => $value) {
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
                    $myObj->MaXe = $value->MaXe;
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
                    $id_user_data = $this->syncDB411Service->getIdUserByDataSync($value->MaKH);
                    $id_manufacture_model_data = $this->syncDB411Service->getIdManufactureModelByDataSync($value->MaLoai);
                    $id_manufacture = isset($id_manufacture_model_data[0]->parent_category_id) ? $id_manufacture_model_data[0]->parent_category_id : null;
                    $id_model = isset($id_manufacture_model_data[0]->id) ? $id_manufacture_model_data[0]->id : null;
                    if (isset($id_user_data[0]->id) && isset($id_user_data[0]->name)) {
                        $id_user = $id_user_data[0]->id;
                        $name = $id_user_data[0]->name;
                    } else {
                        $id_user = null;
                        $name = null;
                        $timestamp = Carbon::now();
                        $content = "{$timestamp->format('Y:m:d H:i:s')}: MemberShipCard: Not found data!! MaKH: {$value->MaKH}";
                        Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                    }
                    $check_user_id_exist = $this->syncDB411Service->checkUserIdMembershipCard($id_user);
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
                    $this->syncDB411Service->saveMemberShipCardSync($id_user, $status, $name, $value->BienSoXe, $value->MaVach, $id_manufacture, $id_model, $value->MauXe, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
                    $this->syncDB411Service->saveMaxIdSync($keyMemberShipCard, $numeric_value = null, $value->MaXe);
                }
            } else {
                $timestamp = Carbon::now();
                $content = "{$timestamp->format('Y:m:d H:i:s')}: MemberShipCard: All data synced already!!";
                Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncOrder() {
        $keyOrder = ConfigKey::LAST_SYNC_ORDER_ID;
        $maxIdSyncOrder = $this->syncDB411Service->getMaxIdSync($keyOrder);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id orders: " . $maxIdSyncOrder[0]->text_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncOrder = $this->syncDB411Service->getDataSyncOrders($maxIdSyncOrder[0]->text_value, $pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncOrder->lastPage();
            }
            if (count($syncOrder) > 0) {
                foreach ($syncOrder as $key => $value) {
                    $myObj = (object)[];
                    $myObj2 = (object)[]; 
                    $myObj3 = (object)[]; 
                    $myObj->MaPTBH = $value->MaPTBH;
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
                    $saveOrderSync = $this->syncDB411Service->saveOrderSync($id_user, $vehicle_number, $value->MaPTBH, $value->SoCT, $value->SoTienTra, $content, Carbon::parse($value->NgayCT), Carbon::parse($value->NgayHT), $id_staff, $value->SoKM, $branch_id, $myJSON);
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
                    $this->syncDB411Service->saveMaxIdSync($keyOrder, $numeric_value = null, $value->MaPTBH);
                }
            } else {
                $timestamp = Carbon::now();
                $content = "{$timestamp->format('Y:m:d H:i:s')}: Order: All data synced already!!";
                Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content); //Error
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncManufacture() {
        $keyManufacture = ConfigKey::LAST_SYNC_MANUFACTURE_ID;
        $type = EManufacture::MANUFACTURE;
        $maxIdSyncManufacture = $this->syncDB411Service->getMaxIdSync($keyManufacture);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id Manufacture: " . $maxIdSyncManufacture[0]->numeric_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncManufacture = $this->syncDB411Service->getDataSyncManufactureVehicle((int)$maxIdSyncManufacture[0]->numeric_value);
        if (count($syncManufacture) > 0) {
            foreach ($syncManufacture as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[]; 
                $myObj3 = (object)[]; 
                $myObj->MaHangXe = $value->MaHangXe;
                $myObj->TenHangXe = $value->TenHangXe; 
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $this->syncDB411Service->saveManufactureSync($value->MaHangXe, $value->TenHangXe, $type, $parent_category_id = null, $myJSON);
                $this->syncDB411Service->saveMaxIdSync($keyManufacture, $value->MaHangXe, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Manufacture: All data synced already!!";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function syncModel() {
        $keyModel = ConfigKey::LAST_SYNC_MODEL_ID;
        $type = EManufacture::MANUFACTURE_MODEL;
        $maxIdSyncModel = $this->syncDB411Service->getMaxIdSync($keyModel);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id Model: " . $maxIdSyncModel[0]->text_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncModelVehicle = $this->syncDB411Service->getDataSyncModelVehicle((int)$maxIdSyncModel[0]->numeric_value);
        if (count($syncModelVehicle) > 0) {
            foreach ($syncModelVehicle as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[]; 
                $myObj3 = (object)[]; 
                $myObj->MaLoai = $value->MaLoai;
                $myObj->TenLoai = $value->TenLoai; 
                $myObj->MaHangXe = $value->MaHangXe;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $id_manufacture_data = $this->syncDB411Service->getIdManufactureByDataSync($value->MaHangXe);
                if (isset($id_manufacture_data[0]->id)) {
                    $parent_category_id = $id_manufacture_data[0]->id;
                } else {
                    $parent_category_id = null;
                    $timestamp = Carbon::now();
                    $content = "{$timestamp->format('Y:m:d H:i:s')}: ModelVehicle: Not found data!! MaDonngXe: " . $value->MaLoai;
                    Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                }
                $this->syncDB411Service->saveManufactureSync($myObj->MaLoai, $value->TenLoai, $type, $parent_category_id, $myJSON);
                $this->syncDB411Service->saveMaxIdSync($keyModel, $value->MaLoai, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: ModelVehicle: All data synced already!!";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function updateMembershipCard() {
        $keyUpdateMemberShipCard = ConfigKey::LAST_SYNC_UPDATE_MEMBERSHIP_CARD_ID;
        $maxIdUpdateMemberShipCard = $this->syncDB411Service->getMaxIdSync($keyUpdateMemberShipCard);
        $dataUpdate = $this->syncDB411Service->getDataSyncUpdateMemberShipCard((int)$maxIdUpdateMemberShipCard[0]->numeric_value);
        if (count($dataUpdate) > 0) {
            foreach ($dataUpdate as $key => $value) {
                $id_user_data = $this->syncDB411Service->getIdUserByDataSync($value->MaKH);
                if (isset($id_user_data[0]->id)) {
                    $id_membership_card_waiting = $this->syncDB411Service->checkCardMemberExistWaiting($id_user_data[0]->id);
                    $id_membership_card = $this->syncDB411Service->checkCardMemberExist($id_user_data[0]->id);
                    if (!isset($id_membership_card[0]->id)) {
                        $this->syncDB411Service->saveMemberShipCardSync($id_user_data[0]->id, $status = EStatus::ACTIVE, $id_user_data[0]->name, $vehicle_number = null, $value->MaVach, $id_manufacture = null, $id_model = null, $color = null, Carbon::parse($value->NgayPH), Carbon::parse($value->NgayHL), Carbon::parse($value->NgayHH), $approved = true, $vehicle_card_status = 1, $myJSON = null);
                        $timestamp = Carbon::now();
                        $content = "{$timestamp->format('Y:m:d H:i:s')}: Save MemberShip Card not number_vehicle id_user: {$id_user_data[0]->id} ";
                        Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                    }
                    if (isset($id_membership_card_waiting[0]->id)) { 
                        $update_card = $this->syncDB411Service->updateDataMembershipCard($id_membership_card[0]->id, $value->MaVach, Carbon::parse($value->NgayPH), Carbon::parse($value->NgayHL), Carbon::parse($value->NgayHH), $approved = true, $vehicle_card_status = 1);
                    }
                }
                $this->syncDB411Service->saveMaxIdSync($keyUpdateMemberShipCard, $value->ID, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Update MemberShip Card: All data updated already!!";
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function syncStaffUpdate() {
        $syncStaff = $this->syncDB411Service->getDataSyncStaffUpdate();
        if (count($syncStaff) > 0) {
            foreach ($syncStaff as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[];
                $myObj3 = (object)[];
                $myObj->MaNV = $value->MaNV;
                $myObj->HoTenNV = $value->HoTenNV;
                $myObj->Mobile = $value->Mobile;
                $myObj->CMND = $value->CMND;
                $myObj->MaCuaHang = $value->MaCuaHang;
                $myObj->MaCV = $value->MaCV;
                $myObj->MaBoPhan = $value->MaBoPhan;
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $branch_id = $this->syncDB411Service->getIdBranchByDataSync($value->MaCuaHang);
                if (isset($branch_id[0]->id)) {
                    $branch_id = $branch_id[0]->id;
                } else {
                    $branch_id = null;
                    $timestamp = Carbon::now();
                    $content = "{$timestamp->format('Y:m:d H:i:s')}: Branch: Not found data!! MaCuaHang: {$value->MaCuaHang}";
                    Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                }
                if ($value->Mobile != null && preg_replace('/\D/', '', $value->Mobile) != "") {
                    $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->Mobile), $type = EUser::TYPE_STAFF);
                    if (isset($check_phone_exist[0]->id)) {
                        $id_user = $check_phone_exist[0]->id;
                        $check_user_id_exist = $this->syncDB411Service->checkUserIdExist($id_user);
                        if (!isset($check_user_id_exist[0]->staff_id)) {
                            $this->syncDB411Service->saveStaffSyncHasUserId($id_user, $branch_id, $myJSON);
                        }
                    }
                }
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Staff: All data synced already!!";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }

    public function syncTypeStaff() {
        $keyTypeStaff = ConfigKey::LAST_SYNC_TYPE_STAFF_ID;
        $maxIdSyncTypeStaff = $this->syncDB411Service->getMaxIdSync($keyTypeStaff);
        $timestamp_ = Carbon::now();
        $content = "{$timestamp_->format('Y:m:d H:i:s')}: Last sync id Type Staff: " . $maxIdSyncTypeStaff[0]->numeric_value;
        Storage::append("error_sync/last_id_sync{$timestamp_->format('Y_m_d')}.txt", $content); //Error

        $syncTypeStaff = $this->syncDB411Service->getDataSyncTypeStaff((int)$maxIdSyncTypeStaff[0]->numeric_value);
        if (count($syncTypeStaff) > 0) {
            foreach ($syncTypeStaff as $key => $value) {
                $myObj = (object)[];
                $myObj2 = (object)[]; 
                $myObj3 = (object)[]; 
                $myObj->ID = $value->ID;
                $myObj->TenNhom = $value->TenNhom; 
                $myObj2->hash = hash('sha256', serialize($myObj));
                $myObj2->data = $myObj;
                $myObj3->syncData = array($myObj2);
                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $this->syncDB411Service->saveTypeStaff($value->ID, $value->TenNhom, $myJSON);
                $this->syncDB411Service->saveMaxIdSync($keyTypeStaff, $value->ID, $text_value = null);
            }
        } else {
            $timestamp = Carbon::now();
            $content = "{$timestamp->format('Y:m:d H:i:s')}: Type Staff: All data synced already!!";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content); //Error
        }
    }
}
