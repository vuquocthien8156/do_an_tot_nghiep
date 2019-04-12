<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EVehicleType;
use App\Enums\EOrderType;
use App\Enums\EOrderStatus;
use App\Enums\EManufacture;
use App\Enums\ECategoryType;
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

class SyncAutoDB411Repository {
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
    //General
    public function getLastIdAppConfigSyncAuto($value) {
        try {
            $result = DB::connection('pgsql')->table('app_config')->select('numeric_value', 'text_value')->where('name', '=', $value)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error!! Failed get max id sync. '{$value}'. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveMaxIdSyncAuto($key, $numeric_value, $text_value) {
        try {
            $now = Carbon::now();
            $app_config = $this->appConfig->setConnection('pgsql')->where('name', '=', $key)->update(['numeric_value' => $numeric_value, 'text_value' => $text_value]);
            return $app_config;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save max id sync: {$key} : {$numeric_value}  {$text_value} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    //Branch
    public function getDataSyncBranchAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [CuaHang], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaCuaHang as CTMaCuaHang', 'ch.MaCuaHang', 'ch.TenCuaHang', 'ch.DiaChi', 'ch.SoDT', 'ch.MaHuyen', 'ch.MaNV')
                        ->leftJoin('dbo.CuaHang as ch', 'ch.MaCuaHang', '=', 'CT.MaCuaHang')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data branch sync. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkValueExist($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaCuaHang\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch')
                            ->whereRaw($sql)
                            ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get value branch, MaCuaHang: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveBranchSyncAuto($id_sync_branch, $name, $address, $phone, $myJSON) {
        try {
            $now = Carbon::now();
            $branch = new Branch();
            $branch->setConnection('pgsql');
            $branch->name = $name;
            $branch->status = EStatus::ACTIVE;
            $branch->address = $address;
            $branch->phone1 = $phone;
            $branch->meta = $myJSON;
            $branch->save();
            return $branch;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Branch. id_sync_branch: {$id_sync_branch} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateBranchSyncAuto($id_sync_branch, $name, $address, $phone, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaCuaHang\":\"$id_sync_branch\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'address' => $address, 'phone1' => $phone, 'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update branch, MaCuaHang: {$id_sync_branch}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteBranchSyncAuto($id_sync_branch) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaCuaHang\":\"$id_sync_branch\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete branch, MaCuaHang: {$id_sync_branch}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    // Nhom Nhan Vien

    public function getDataSyncGroupStaffAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [NhomNhanVien], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.ID as CT_ID', 'nnv.ID', 'nnv.TenNhom')
                        ->leftJoin('dbo.NhomNhanVien as nnv', 'nnv.ID', '=', 'CT.ID')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all group staff table: dbo.NhomNhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveGroupStaffSyncAuto($id_group_staff_sync, $name, $myJSON) {
        try {
            $value = ($id_group_staff_sync == 1) ?  2 : 1;
            $now = Carbon::now();
            $category = new Category();
            $category->setConnection('pgsql');
            $category->name = $name;
            $category->type = EUser::TYPE_STAFF_SYNC;
            $category->status = EStatus::ACTIVE;
            $category->value = $value;
            $category->meta = $myJSON;
            $category->save();
            return $category;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Type Staff id_group_staff_sync = {$id_group_staff_sync}. Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateGroupStaffSyncAuto($id_group_staff_sync, $name, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"ID\":\"$id_group_staff_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update group staff, id_sync_group_staff: {$id_sync_branch}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteGroupStaffSyncAuto($id_group_staff_sync) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"ID\":\"$id_group_staff_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete group staff, id_sync_group_staff: {$id_group_staff_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    //Nhan Vien

    public function getDataSyncStaffAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [NhanVien], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaNV as CT_MaNV', 'nv.MaNV', 'nv.HoTenNV', 'nv.Mobile', 'nv.Email', 'nv.DiaChi', 'nv.CMND', 'nv.MaCuaHang', 'nv.MaCV', 'nv.MaBoPhan', 'nnvct.MaNhomNhanVien')
                        ->leftJoin('dbo.NhanVien as nv', 'nv.MaNV', '=', 'CT.MaNV')
                        ->leftJoin('dbo.NhomNhanVienChiTiet as nnvct', 'nnvct.MaNhanVien', '=', 'CT.MaNV')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff table: dbo.NhomNhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdBranchByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaCuaHang\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch')
                        ->select('id')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id branch, MaCuaHang: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getTypeStaffIdByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"ID\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                        ->select('id')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id type staff, ID: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdTypeStaffByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users staff, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkPhoneExist($phone, $type) {
        try {
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name', 'us.phone', 'us.meta', 'staff_type_id')
                        ->where([['us.phone', '=', $phone], ['us.type', '=', $type]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed check phone user, Phone: {$phone}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id) {
        try {
            $now = Carbon::now();
            if($type != null) {
                $user = Users::where('id', '=', $id_user)->update(['meta' => $meta, 'type' => $type, 'staff_type_id' => $staff_type_id, 'updated_at' => $now]);
            } else {
                $user = Users::where('id', '=', $id_user)->update(['meta' => $meta, 'updated_at' => $now]);
            }
            return $user;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update Meta user (Phone Same) id_user: {$id_user}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateTypeStaff($id_user, $staff_type_id) {
        try {
            $now = Carbon::now();
            $result = Users::where('id', '=', $id_user)->update(['staff_type_id' => $staff_type_id]);
            return $result;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update staff_type_id: {$id_user}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveBranchStaffSyncAuto($id_user, $id_branch, $myJSON) {
        try {
            $now = Carbon::now();
            $branch_staff = new BranchStaff();
            $branch_staff->setConnection('pgsql');
            $branch_staff->branch_id = $id_branch;
            $branch_staff->staff_id = $id_user;
            $branch_staff->status = EStatus::ACTIVE;
            $branch_staff->created_at = $now;
            $branch_staff->meta = $myJSON;
            $branch_staff->save();
            return $branch_staff;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Save id_staff: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getPartnerId() {
        try {
            $result = DB::connection('pgsql')->table('category')
                        ->select('id', 'name')
                        ->where([['type', '=', ECategoryType::PARTNER_FIELD], ['value', '=', 0]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Failed get partner id type = 9, value = 0, message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateUserStaffSyncAuto($id_sync_staff, $name, $phone, $email, $address, $type_staff_id, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$id_sync_staff\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'phone' => $phone, 'email' => $email, 'address' => $address, 'staff_type_id' => $type_staff_id, 'meta' => $myJSON]);
                           
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update user, id_sync_staff: {$id_sync_staff}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateBranchStaffSyncAuto($id_sync_staff, $id_branch, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$id_sync_staff\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch_staff')
                            ->whereRaw($sql)
                            ->update(['branch_id' => $id_branch, 'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update branch staff, id_sync_staff: {$id_sync_staff}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteBranchStaffSyncAuto($id_sync_staff) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$id_sync_staff\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch_staff')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete group staff, id_sync_group_staff: {$id_group_staff_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteUserStaffSyncAuto($id_sync_staff) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$id_sync_staff\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete group staff, id_sync_group_staff: {$id_group_staff_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveUserSyncAuto($id_sync_staff, $id_sync_user, $name, $phone, $email, $address, $type, $type_staff_id, $myJSON) {
        try {
            $partner_id = $this->getPartnerId();
            $now = Carbon::now();
            $user = new Users();
            $user->setConnection('pgsql');
            $user->name = $name;
            $user->phone = $phone;
            $user->email = $email;
            $user->status = EStatus::ACTIVE;
            $user->type = $type;
            $user->password = Hash::make('411411');
            $user->address = $address;
            $user->staff_type_id = $type_staff_id;
            $user->partner_id = isset($partner_id[0]->id) ? $partner_id[0]->id : null;
            $user->meta = $myJSON;
            $user->save();
            return $user;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Save user id_staff: {$id_sync_staff} .id_use: {$id_sync_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveUserAddressAuto($id_user, $name, $phone, $address) {
        try {
            $now = Carbon::now();
            $user_address = new UserAddress();
            $user_address->setConnection('pgsql');
            $user_address->user_id = $id_user;
            $user_address->name = $name;
            $user_address->phone = $phone;
            $user_address->address = $address;
            $user_address->status = EStatus::ACTIVE;
            $user_address->is_default = true;
            $user_address->created_at = $now;
            $user_address->save();
            return $user_address;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save User address. id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_address_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    // User
    public function getDataSyncUserAuto1($value) { // Table: dbo.KhachHangNgoai
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [KhachHangNgoai], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaKH as CT_MaKH', 'khn.MaKH', 'khn.HoTenKH', 'khn.DTKH', 'khn.EmailKH', 'khn.DiaChiKH')
                        ->leftJoin('dbo.KhachHangNgoai as khn', 'khn.MaKH', '=', 'CT.MaKH')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user table: dbo.KhachHangNgoai. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateUserSyncAuto($id_sync_user, $name, $phone, $email, $address, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaKH\":\"$id_sync_user\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'phone' => $phone, 'email' => $email, 'address' => $address,  'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update user, id_sync_user: {$id_sync_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteUserSyncAuto($id_sync_user) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaKH\":\"$id_sync_user\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete user, id_sync_user: {$id_sync_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncUserAuto2($value) { //table: dbo.KhachHang
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [KhachHang], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaKH as CT_MaKH', 'kh.MaKH', 'kh.HoTen', 'kh.DiaChi', 'kh.DTDiDong', 'kh.MaHieuKH', 'kh.EmailKH')
                        ->leftJoin('dbo.KhachHang as kh', 'kh.MaKH', '=', 'CT.MaKH')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user table: dbo.KhachHang. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    //Membership Card

    public function getDataSyncManufactureAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [HangXeMay], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaHangXe as CT_MaHangXe', 'hxm.MaHangXe', 'hxm.TenHangXe')
                        ->leftJoin('dbo.HangXeMay as hxm', 'hxm.MaHangXe', '=', 'CT.MaHangXe')
                        ->setBindings([$SYS_CHANGE_VERSION])->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all Manufacture table: dbo.HangXeMay. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveManufactureModelVehicleSync($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON) {
        try {
            $now = Carbon::now();
            $category = new Category();
            $category->setConnection('pgsql');
            $category->name = $name;
            $category->type = $type;
            $category->parent_category_id = $parent_category_id;
            $category->status = EStatus::ACTIVE;
            $category->meta = $myJSON;
            $category->save();
            return $category;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Manufacture Model Vehicle. id_manufacture_sync = {$id_manufacture_sync}. type = {$type}. name: {$name} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateManufactureSyncAuto($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaHangXe\":\"$id_manufacture_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'type' => $type, 'parent_category_id' => $parent_category_id, 'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update manufacture , id_manufacture_sync: {$id_manufacture_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteManufactureSyncAuto($id_manufacture_sync) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaHangXe\":\"$id_manufacture_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete manufacture, id_manufacture_sync: {$id_manufacture_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncModelAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [LoaiXe], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaLoai as CT_MaLoai', 'lx.MaLoai','lx.TenLoai', 'lx.MaHangXe')
                        ->leftJoin('dbo.LoaiXe as lx', 'lx.MaLoai', '=', 'CT.MaLoai')
                        ->setBindings([$SYS_CHANGE_VERSION]);
            if($value != null) {
                $result->where('CT.SYS_CHANGE_VERSION', '>', $value);
            }
            $result = $result->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all Model table: dbo.LoaiXe. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdManufactureByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaHangXe\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                        ->select('id')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id manufacture, MaHangXe: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateModelSyncAuto($id_manufacture_sync, $name, $type, $parent_category_id, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaHangXe\":\"$id_manufacture_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['name' => $name, 'type' => $type, 'parent_category_id' => $parent_category_id, 'meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update manufacture , id_manufacture_sync: {$id_manufacture_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deleteModelSyncAuto($id_manufacture_sync) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaHangXe\":\"$id_manufacture_sync\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete manufacture, id_manufacture_sync: {$id_manufacture_sync}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncMemberShipCardAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [XeMuaCHKhac], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaXe as CT_MaXe', 'kmtv.ID', 'kmtv.SoThe', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'xmchk.MaKH', 
                                'kmtv.NgayNhap', 'kmtv.TienThe', 'kmtv.DienGiai', 
                                'xmchk.MaXe', 'xmchk.BienSoXe', 'xmchk.SoMay', 'xmchk.SoKhung', 'xmchk.DongXe', 'xmchk.SoBaoHanh', 'xmchk.MaLoai', 'mx.TenMau as MauXe')
                        ->leftJoin('dbo.XeMuaCHKhac as xmchk', 'xmchk.MaXe', '=', 'CT.MaXe')
                        ->leftJoin('dbo.kmTheVIP as kmtv', 'kmtv.MaKH', '=', 'xmchk.MaKH')
                        ->leftjoin('dbo.MauXe as mx', 'mx.Khoa', '=', 'xmchk.MaMau')
                        ->setBindings([$SYS_CHANGE_VERSION]);
            if($value != null) {
                $result->where('CT.SYS_CHANGE_VERSION', '>', $value);
            }
            $result = $result->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all Menbership Card table: dbo.XeMuaCHKhac. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdManufactureModelByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaLoai\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('category as ca')
                        ->select('ca.id', 'ca.parent_category_id')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id manufacture model, MaLoai: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkUserIdMembershipCard($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id')
                        ->where('user_id', '=', $id_user)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Failed get user id, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveMemberShipCardSyncAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON) {
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
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Save Membership Card. id_sync_membership_card: {$id_sync_membership_card} vehicle_number: {$vehicle_number} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMemberShipCardSyncAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON) {
        //dd($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaXe\":\"$id_sync_membership_card\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                            ->whereRaw($sql)
                            ->update(['vehicle_number' => $vehicle_number, 'code' => $code, 'vehicle_manufacture_id' =>  $id_manufacture,
                                      'vehicle_model_id' => $id_model, 'vehicle_color' => $color, 'created_at' => $created_at, 'approved_at' => $approved_at, 'expired_at' => $expired_at]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update membershipcard, id_sync_membership_card: {$id_sync_membership_card}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deteleMemberShipCardSyncAuto($id_sync_membership_card, $id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaXe\":\"$id_sync_membership_card\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete membershipcard, id_sync_membership_card: {$id_sync_membership_card}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    // Order 

    public function getDataSyncOrderAuto($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [PhieuThu_BanHang], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.MaPTBH as CT_MaPTBH', 'ptbh.MaPTBH', 'ptbh.MaKH', 'ptbh.NgayCT', 'ptbh.NgayHT', 'ptbh.SoCT', 'ptbh.SoTienTra', 'ptbh.MaNV', 'ptbh.NoiDungNop', 'ptbh.MaCH',
                                'psc.MaPSC', 'psc.NhanVienSuaChua', 'psc.NgayThucHien', 'psc.NgayGiaoXe', 'psc.NoiDung', 'psc.MaPT', 'psc.SoKM')
                        ->leftJoin('dbo.PhieuThu_BanHang as ptbh', 'ptbh.MaPTBH', '=', 'CT.MaPTBH')
                        ->leftJoin('dbo.PhieuSuaChua as psc', 'psc.MaPT', '=', 'ptbh.MaPTBH')
                        ->setBindings([$SYS_CHANGE_VERSION]);
            if($value != null) {
                $result->where('CT.SYS_CHANGE_VERSION', '>', $value);
            }
            $result = $result->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all Order table: dbo.PhieuThu_BanHang. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDetailRepairVehicle($MaPhieuSuaChua) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.ChiTietSuaChuaXe as ctscx')
                        ->select('ctscx.MaPSC', 'ctscx.MaPT', 'ctscx.SoLuong', 'ctscx.DonGia', 'ctscx.DienGiai', 'ctscx.GiaVon', 'ctscx.ChietKhau', 'ctscx.ThueGTGT',
                                 'pt.MaPhuTung', 'pt.TenPhuTung', 'pt.MaVach', 'pt.MaQuyCach', 'pt.LoaiXe', 'pt.MaKho')
                        ->join('dbo.PhuTung_CuaHang as ptch', 'ptch.MaPhuTung_CuaHang', '=', 'ctscx.MaPT')
                        ->join('dbo.PhuTung as pt', 'pt.MaPhuTung', '=', 'ptch.MaPhuTung')
                        ->where('ctscx.MaPSC', '=', $MaPhieuSuaChua)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get data table: dbo.ChiTietSuaChuaXe. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdStaffByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch_staff')
                        ->select('id', 'staff_id', 'branch_id')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id staff, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getVehicleNumber($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('vehicle_number', 'odo_km')
                        ->where('user_id', '=', $id_user)
                        ->orderBy('status', 'asc')
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get vehicle number, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveOrderAuto($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON) {
        try {
            $now = Carbon::now();
            $order = new Order();
            $order->setConnection('pgsql');
            $order->vehicle_number = $vehicle_number;
            $order->status = EOrderStatus::COMPLETED;
            $order->user_id = $id_user;
            $order->code = $code;
            $order->price = $price;
            $order->created_at = $created_at;
            $order->completed_at = $completed_at;
            $order->completed_by = $id_staff;
            $order->odo_km = $number_km;
            $order->branch_id = $branch_id;
            $order->meta = $myJSON;
            $order->save();
            return $order;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Order. id_sync_order MaPBH: {$id_sync_order} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveOrderDetailAuto($order_id, $name, $created_at, $type, $price, $quantity, $myJSON) {
        try {
            $now = Carbon::now();
            $order_detail = new OrderDetail();
            $order_detail->setConnection('pgsql');
            $order_detail->order_id = $order_id;
            $order_detail->type = $type;
            $order_detail->name = $name;
            $order_detail->created_at = $created_at;
            $order_detail->meta = $myJSON;
            $order_detail->price = $price;
            $order_detail->original_price = $price;
            $order_detail->quantity = $quantity;
            $order_detail->save();
            return $order_detail;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Order Detail. id_order: {$order_id} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateOrderSyncAuto($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON) {
        try {
            $result = DB::connection('pgsql')->table('orders')
                            ->where('code', '=', $code)
                            ->update(['vehicle_number' => $vehicle_number, 'code' => $code, 'price' => $price, 'created_at' => $created_at, 'user_id' => $id_user,
                                      'completed_at' => $completed_at, 'completed_by' => $id_staff, 'odo_km' => $number_km, 'branch_id' => $branch_id]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete order, id_sync_order: {$id_sync_order}. code : {$code} message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deteleOrderSyncAuto($code) {
        try {
            $result = DB::connection('pgsql')->table('orders')
                            ->where('code', '=', $code)
                            ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete order, id_sync_order: {$id_sync_order}. code : {$code} message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncCardMember_km_TheVIP($value) {
        try {
            $SYS_CHANGE_VERSION = ($value != null) ? ($value) : 0;
            $result = DB::connection('sqlsrv')->table(DB::raw('CHANGETABLE (CHANGES [kmTheVIP], ?) as CT'))
                        ->select('CT.SYS_CHANGE_VERSION', 'CT.SYS_CHANGE_OPERATION', 'CT.ID as CT_ID', 'kmtv.ID', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH')
                        ->leftJoin('dbo.kmTheVIP as kmtv', 'kmtv.ID', '=', 'CT.ID')
                        ->setBindings([$SYS_CHANGE_VERSION]);
            if($value != null) {
                $result->where('CT.SYS_CHANGE_VERSION', '>', $value);
            }
            $result = $result->orderBy('CT.SYS_CHANGE_VERSION', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all Card Member table: dbo.Km_TheVIP. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }


    public function setMemberShipCardIsNotActive($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"ID\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                            ->whereRaw($sql)
                            ->update(['status' => EStatus::WAITING, 'code' => null, 'approved' => false, 'vehicle_card_status' => 2, 'created_at' => null, 'expired_at' => null, 'approved_at' => null]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed set membershipcard, is not active ID: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMemberShipCard_kmTheVIP($value, $code, $created_at, $approved_at, $expired_at) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"ID\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                            ->whereRaw($sql)
                            ->update(['code' => $code, 'created_at' => $created_at, 'expired_at' => $expired_at, 'approved_at' => $approved_at]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update Info Card Membership, ID: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_auto/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

}