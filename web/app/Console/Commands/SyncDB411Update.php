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
use App\Services\SyncUpdateDB411Service;
use App\Services\SyncDB411Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SyncDB411Update extends Command {
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-update:411
                            {--sync-update-all : Sync Update All Data DB 411}
                            {--sync-update-membership-card-1 : Sync Test Function}
                            {--sync-update-membership-card-2 : Sync Test Function}
                            {--sync-update-order : Sync Test Function}';

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

    public function __construct(SyncDB411Service $syncDB411Service, SyncUpdateDB411Service $syncUpdateDB411Service) {
        parent::__construct();
        $this->syncUpdateDB411Service = $syncUpdateDB411Service;
        $this->syncDB411Service = $syncDB411Service;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() { 
        if ($this->option('sync-update-all')) {
            $this->syncUpdateMembershipCard2();
            $this->syncUpdateMembershipCard();
            $this->syncUpdateOrder();
        }

        if ($this->option('sync-update-membership-card-1')) {
            $this->syncUpdateMembershipCard2();
        }

        if ($this->option('sync-update-membership-card-2')) {
            $this->syncUpdateMembershipCard();
        }

        if ($this->option('sync-update-order')) {
            $this->syncUpdateOrder();
        }
    } 

    public function syncUpdateMembershipCard() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $dataUpdate = $this->syncUpdateDB411Service->getDataSyncUpdateMemberShipCard($pageSize ,$page);
            if ($lastPage == null) {
                $lastPage = $dataUpdate->lastPage();
            }
            if (count($dataUpdate) > 0) {
                foreach ($dataUpdate as $key => $value) {
                    $id_user_data = $this->syncUpdateDB411Service->getIdUserByDataSync($value->MaKH);
                    if (isset($id_user_data[0]->id)) {
                        $id_membership_card_waiting = $this->syncUpdateDB411Service->checkCardMemberExistWaiting($id_user_data[0]->id);
                        $id_membership_card = $this->syncUpdateDB411Service->checkCardMemberExist($id_user_data[0]->id);
                        if (!isset($id_membership_card[0]->id)) {
                            dump('aaaa', $id_user_data[0]->id);
                            $this->syncUpdateDB411Service->saveMemberShipCardSync($id_user_data[0]->id, $status = EStatus::ACTIVE, $id_user_data[0]->name, $vehicle_number = null, $value->MaVach, $id_manufacture = null, $id_model = null, $color = null, Carbon::parse($value->NgayPH), Carbon::parse($value->NgayHL), Carbon::parse($value->NgayHH), $approved = true, $vehicle_card_status = 1, $myJSON = null);
                            $timestamp = Carbon::now();
                            $content = "{$timestamp->format('Y:m:d H:i:s')}: Save MemberShip Card not number_vehicle id_user: {$id_user_data[0]->id} ";
                            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
                        }
                        if (isset($id_membership_card_waiting[0]->id)) { 
                            dump('bbbb', $id_user_data[0]->id);
                            $update_card = $this->syncUpdateDB411Service->updateDataMembershipCard($id_membership_card[0]->id, $value->MaVach, Carbon::parse($value->NgayPH), Carbon::parse($value->NgayHL), Carbon::parse($value->NgayHH), $approved = true, $vehicle_card_status = 1);
                        }
                    }
                }
            } else {
                $timestamp = Carbon::now();
                $content = "{$timestamp->format('Y:m:d H:i:s')}: Update MemberShip Card: All data updated already!!";
                Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncUpdateOrder() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncOrder = $this->syncUpdateDB411Service->getDataSyncUpdateOrders($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncOrder->lastPage();
            }
            if (count($syncOrder) > 0) {
                foreach ($syncOrder as $key => $value) {
                    $checkCodeExist = $this->syncUpdateDB411Service->checkCodeExist($value->SoCT);
                    if(!isset($checkCodeExist[0]->id)) {
                        dump($value->SoCT);
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
                    }
                }
            } else {
                $timestamp = Carbon::now();
                $content = "{$timestamp->format('Y:m:d H:i:s')}: Order: All data synced already!!";
                Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content); //Error
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncUpdateMembershipCard2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncMemberShipCard = $this->syncUpdateDB411Service->getDataSyncCardMemberUpdate2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncMemberShipCard->lastPage();
            }
            if (count($syncMemberShipCard) > 0) {
                foreach ($syncMemberShipCard as $key => $value) {
                    $checkExist_MaXe = $this->syncUpdateDB411Service->checkExist_MaXe($value->MaXe);
                    if(!isset($checkExist_MaXe[0]->id)){
                        dump($value->MaXe);
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
                    }
                }
            } else {
                $timestamp = Carbon::now();
                $content = "{$timestamp->format('Y:m:d H:i:s')}: MemberShipCard: All data synced already!!";
                Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content); //Error
            }
            $page += 1;
        } while ($page <= $lastPage);
    }
}