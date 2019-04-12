<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EVehicleType;
use App\Enums\EOrderType;
use App\Enums\EOrderStatus;
use App\Enums\EManufacture;
use App\Models\Users;
use App\Models\BranchStaff;
use App\Models\MemberShipCard;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Branch;
use App\Models\AppConfig;
use App\Models\UserAddress;
use App\Models\Category;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class SyncUpdateDB411Repository {
    public function __construct(Users $users, BranchStaff $branchStaff, MemberShipCard $memberShipCard, UserAddress $userAddress,
                                Order $order, OrderDetail $orderDetail, Branch $branch, AppConfig $appConfig, Category $category) {
        $this->users = $users;
        $this->branchStaff = $branchStaff;
        $this->memberShipCard = $memberShipCard;
        $this->order = $order;
        $this->orderDetail = $orderDetail;
        $this->branch = $branch;
        $this->appConfig = $appConfig;
        $this->userAddress = $userAddress;
        $this->category = $category;
    }

    public function getDataSyncUpdateMembershipCard($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.kmTheVIP as kmtv')
                        ->select('kmtv.ID', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'kmtv.MaKH');

            $query = $result->orderBy('kmtv.ID', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get data table: dbo.kmTheVIP. message: {$e->getMessage()}";
            Storage::append("error_sync/error_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdUserByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaKH\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkCardMemberExistWaiting($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id', 'vehicle_number', 'approved')
                        ->where([['user_id', '=', $id_user], ['status', '=', EStatus::ACTIVE], ['approved', '=', false]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed check user id Membership_card status: waiting, User_id: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkCardMemberExist($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id', 'vehicle_number')
                        ->where('user_id', '=', $id_user)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed check user id Membership_card, User_id: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveMemberShipCardSync($id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON) {
        try {
            $now = Carbon::now();
            $membership_card = new MemberShipCard();
            $membership_card->setConnection('pgsql');
            $membership_card->vehicle_number = $vehicle_number;
            $membership_card->user_id = $id_user;
            $membership_card->code = $code;
            $membership_card->vehicle_manufacture_id = $id_manufacture;
            $membership_card->vehicle_model_id = $id_model;
            $membership_card->vehicle_color = $color;
            $membership_card->name = $name;
            $membership_card->status = $status;
            $membership_card->vehicle_type = EVehicleType::TYPE_MOTORBIKE;
            $membership_card->created_at = $created_at;
            $membership_card->approved_at = $approved_at;
            $membership_card->approved = $approved;
            $membership_card->vehicle_card_status = $vehicle_card_status;
            $membership_card->expired_at = $expired_at;
            $membership_card->meta = $myJSON;
            $membership_card->save();
            return $membership_card;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Save Membership Card. vehicle_number: {$vehicle_number} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_update_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status) {
        try {
            $now = Carbon::now();
            $membership_card = MemberShipCard::where('id', '=', $id_membership_card)
                            ->update([  'code' => $code,
                                        'created_at' => $created_at, 
                                        'approved_at' => $approved_at, 
                                        'approved' => $approved, 
                                        'vehicle_card_status' => $vehicle_card_status, 
                                        'expired_at' => $expired_at]);
            return $membership_card;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update Data membership card id_membership_card: {$id_membership_card}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_update_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    ////////////// ORDER

    public function getDataSyncUpdateOrders($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.PhieuThu_BanHang as ptbh')
                    ->select('ptbh.MaPTBH', 'ptbh.MaKH', 'ptbh.NgayCT', 'ptbh.NgayHT', 'ptbh.SoCT', 'ptbh.SoTienTra', 'ptbh.MaNV', 'ptbh.NoiDungNop', 'ptbh.MaCH',
                            'psc.MaPSC', 'psc.NhanVienSuaChua', 'psc.NgayThucHien', 'psc.NgayGiaoXe', 'psc.NoiDung', 'psc.MaPT', 'psc.SoKM')
                    ->leftJoin('dbo.PhieuSuaChua as psc', 'psc.MaPT', '=', 'ptbh.MaPTBH');
            $query = $result->orderBy('ptbh.MaPTBH', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data transaction sync. message: {$e->getMessage()}";
            Storage::append("error_sync/error_order_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkCodeExist($value) {
        try {
            $result = DB::connection('pgsql')->table('orders')
                        ->select('id', 'user_id', 'code')
                        ->where('code', '=', $value)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed check user code in orders, code: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    ///membership card 

    public function getDataSyncCardMemberUpdate2($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.XeMuaCHKhac as xmchk')
                    ->select('kmtv.ID', 'kmtv.SoThe', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'xmchk.MaKH', 
                            'kmtv.NgayNhap', 'kmtv.TienThe', 'kmtv.DienGiai', 
                            'xmchk.MaXe', 'xmchk.BienSoXe', 'xmchk.SoMay', 'xmchk.SoKhung', 'xmchk.DongXe', 'xmchk.SoBaoHanh', 'xmchk.MaLoai', 'mx.TenMau as MauXe')
                    ->leftJoin('dbo.kmTheVIP as kmtv', 'kmtv.MaKH', '=', 'xmchk.MaKH')
                    ->join('dbo.MauXe as mx', 'mx.Khoa', '=', 'xmchk.MaMau');
            $query = $result->orderBy('xmchk.MaXe', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.XeMuaCHKhac. message: {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkExist_MaXe($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaXe\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users, MaXe: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
}