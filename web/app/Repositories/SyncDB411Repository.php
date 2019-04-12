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

class SyncDB411Repository {
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

    public function getByKey($key) {
        try {
            return $this->appConfig->where('name', '=', $key)->first();
        } catch (\Exception $e) {
            logger("Failed to Get model app_cofig by key: " . $e->getMessage());
            return null;
        }
    }

    public function getByUserById($id) {
        try {
            return $this->users->find($id);
        } catch (\Exception $e) {
            logger("Failed to Get model user by id: $id" . $e->getMessage());
            return null;
        }
    }

    public function getMaxIdSync($key) {
        try {
            $result = DB::connection('pgsql')->table('app_config')->select('numeric_value', 'text_value')->where('name', '=', $key)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error!! Failed get max id sync. '{$key}'. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
    
    public function getDataSyncTypeStaff($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.NhomNhanVien')
                        ->select('ID', 'TenNhom');
            if($value != null) {
                $result->where('ID', '>', $value);
            }
            $result = $result->orderBy('ID', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff sync table: dbo.NhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
    
    public function getDataSyncStaff($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.NhanVien as nv')
                        ->select('nv.MaNV', 'nv.HoTenNV', 'nv.Mobile', 'nv.Email', 'nv.DiaChi', 'nv.CMND', 'nv.MaCuaHang', 'nv.MaCV', 'nv.MaBoPhan', 'nnv.MaNhomNhanVien')
                        ->join('dbo.NhomNhanVienChiTiet as nnv', 'nnv.MaNhanVien', '=', 'nv.MaNV');
            if($value != null) {
                $result->where('MaNV', '>', $value);
            }
            $result = $result->orderBy('MaNV', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff sync table: dbo.NhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncUser($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHangNgoai')
                        ->select('MaKH', 'HoTenKH', 'DTKH', 'EmailKH', 'DiaChiKH');
            if($value != null) {
                $result->where('MaKH', '>', $value);
            }
            $result = $result->orderBy('MaKH', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.KhachHangNgoai. message: {$e->getMessage()}";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
    
    public function getDataSyncUser2($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHang')
                        ->select('MaKH', 'HoTen', 'DiaChi', 'DTDiDong', 'MaHieuKH', 'EmailKH');
            if($value != null) {
                $result->where('MaKH', '>', $value);
            }
            $result = $result->orderBy('MaKH', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.KhachHang. message: {$e->getMessage()}";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncCardMember($value, $pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.XeMuaCHKhac as xmchk')
                    ->select('kmtv.ID', 'kmtv.SoThe', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'xmchk.MaKH', 
                            'kmtv.NgayNhap', 'kmtv.TienThe', 'kmtv.DienGiai', 
                            'xmchk.MaXe', 'xmchk.BienSoXe', 'xmchk.SoMay', 'xmchk.SoKhung', 'xmchk.DongXe', 'xmchk.SoBaoHanh', 'xmchk.MaLoai', 'mx.TenMau as MauXe')
                    ->leftJoin('dbo.kmTheVIP as kmtv', 'kmtv.MaKH', '=', 'xmchk.MaKH')
                    ->join('dbo.MauXe as mx', 'mx.Khoa', '=', 'xmchk.MaMau');
            if($value != null) {
                $result->where('xmchk.MaXe', '>', $value);
            }
            $query = $result->orderBy('xmchk.MaXe', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.XeMuaCHKhac. message: {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    } 

    public function getDataSyncUpdateMemberShipCard($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.kmTheVIP as kmtv')
                        ->select('kmtv.ID', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'kmtv.MaKH');
            if($value != null) {
                $result->where('kmtv.ID', '>', $value);
            }
            $result = $result->orderBy('kmtv.ID', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get data table: dbo.kmTheVIP. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncOrders($value, $pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.PhieuThu_BanHang as ptbh')
                    ->select('ptbh.MaPTBH', 'ptbh.MaKH', 'ptbh.NgayCT', 'ptbh.NgayHT', 'ptbh.SoCT', 'ptbh.SoTienTra', 'ptbh.MaNV', 'ptbh.NoiDungNop', 'ptbh.MaCH',
                            'psc.MaPSC', 'psc.NhanVienSuaChua', 'psc.NgayThucHien', 'psc.NgayGiaoXe', 'psc.NoiDung', 'psc.MaPT', 'psc.SoKM')
                    ->leftJoin('dbo.PhieuSuaChua as psc', 'psc.MaPT', '=', 'ptbh.MaPTBH');
            if($value != null) {
                $result->where('ptbh.MaPTBH', '>', $value);
            }
            $query = $result->orderBy('ptbh.MaPTBH', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data transaction sync. message: {$e->getMessage()}";
            Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
    
    public function getDataSyncBranch($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.CuaHang as ch')
                        ->select('ch.MaCuaHang', 'ch.TenCuaHang', 'ch.DiaChi', 'ch.SoDT', 'ch.MaHuyen', 'ch.MaNV');
            if($value != null) {
                $result->where('ch.MaCuaHang', '>', $value);
            }
            $result = $result->orderBy('ch.MaCuaHang', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data branch sync. message: {$e->getMessage()}";
            Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
    
    public function getDataSyncManufactureVehicle($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.HangXeMay as hxm')
                        ->select('hxm.MaHangXe', 'hxm.TenHangXe');
            if($value != null) {
                $result->where('hxm.MaHangXe', '>', $value);
            }
            $result = $result->orderBy('hxm.MaHangXe', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data Manufacture sync. message: {$e->getMessage()}";
            Storage::append("error_sync/error_manufacture_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncModelVehicle($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.LoaiXe as lx')
                    ->select('lx.MaLoai','lx.TenLoai', 'lx.MaHangXe')
                    ->whereNotNull('lx.MaHangXe');
            if($value != null) {
                $result->where('lx.MaLoai', '>', $value);
            }
            $result = $result->orderBy('lx.MaLoai', 'asc')->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all data Model sync. message: {$e->getMessage()}";
            Storage::append("error_sync/error_model_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdUserByDataSync($value) {
        try {
            // $result = DB::connection('pgsql')->table('users as us')
            //             ->select('us.id', 'us.name')
            //             ->whereRaw('meta::text like  ? ', ['%' . $value . '%'])
            //             ->get();
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaKH\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkCardMemberExistWaiting($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id', 'vehicle_number')
                        ->where([['user_id', '=', $id_user], ['approved', '=', false]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed check user id Membership_card status: waiting, User_id: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdBranchByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaCuaHang\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('branch')
                        ->select('id')
                        ->whereNotNull('meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id branch, MaCuaHang: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getIdTypeStaffByDataSync($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users staff, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkUserIdMembershipCard($id_user) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id')
                        ->where('user_id', '=', $id_user)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Failed get user id, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status) {
        try {
            $now = Carbon::now();
            $membership_card = MemberShipCard::where('id', '=', $id_membership_card)
                            ->update([  'code' => $code, 'status' => EStatus::ACTIVE, 
                                        'created_at' => $created_at, 
                                        'approved_at' => $approved_at, 
                                        'approved' => $approved, 
                                        'vehicle_card_status' => $vehicle_card_status, 
                                        'expired_at' => $expired_at]);
            return $membership_card;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update Data membership card id_membership_card: {$id_membership_card}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMetaIfPhoneSame($id_user, $meta, $type, $staff_type_id) {
        try {
            $now = Carbon::now();
            // $user = $this->getByUserById($id_user);
            // $user->setConnection('pgsql'); 
            // $user->meta = $meta;
            // $user->save();
            if($type != null) {
                $user = Users::where('id', '=', $id_user)->update(['meta' => $meta, 'type' => $type, 'staff_type_id' => $staff_type_id, 'updated_at' => $now]);
            } else {
                $user = Users::where('id', '=', $id_user)->update(['meta' => $meta, 'updated_at' => $now]);
            }
            return $user;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update Meta user (Phone Same) id_user: {$id_user}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveTypeStaff($id_sync, $name, $myJSON) {
        try {
            if($id_sync == 1) {
                $value = 2;
            } else {
                $value = 1;
            }
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
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Type Staff id_sync = {$id_sync}. Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveUserSync($id_sync_staff, $id_sync_user, $name, $phone, $email, $address, $type, $type_staff_id, $myJSON) {
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
            Storage::append("error_sync/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveStaffSync($id_user, $id_branch, $myJSON) {
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
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveOrder($id_user, $vehicle_number, $id_sync_order, $code, $price, $created_at, $completed_at, $id_staff, $number_km, $branch_id, $myJSON) {
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
            Storage::append("error_sync/error_order_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveOrderDetail($order_id, $name, $created_at, $type, $price, $quantity, $myJSON) {
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
            Storage::append("error_sync/error_order_detail_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveBranch($id_sync_branch, $name, $address, $phone, $myJSON) {
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
            Storage::append("error_sync/error_branch_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveMaxIdSync($key, $numeric_value, $text_value) {
        try {
            $now = Carbon::now();
            // $app_config = $this->getByKey($key);
            // $app_config->setConnection('pgsql');
            // $app_config->numeric_value = $numeric_value;
            // $app_config->text_value = $text_value;
            // $app_config->save();
            $app_config = $this->appConfig->setConnection('pgsql')->where('name', '=', $key)->update(['numeric_value' => $numeric_value, 'text_value' => $text_value]);
            return $app_config;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save max id sync: {$key} : {$numeric_value}  {$text_value} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveUserAddress($id_user, $name, $phone, $address) {
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
            Storage::append("error_sync/error_user_address_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function saveManufactureModelVehicleSync($name, $type, $parent_category_id, $myJSON) {
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
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Fail Save Manufacture Model Vehicle. type = {$type}. name: {$name} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_category_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getDataSyncStaffUpdate() {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.NhanVien')
                        ->select('MaNV', 'HoTenNV', 'Mobile', 'Email', 'CMND', 'MaCuaHang', 'MaCV', 'MaBoPhan')
                        ->orderBy('MaNV', 'asc')
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff sync table: dbo.NhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkUserIdExist($id_user) {
        try {
            $result = DB::connection('pgsql')->table('branch_staff')
                        ->select('id', 'staff_id')
                        ->where('staff_id', '=', $id_user)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}: Failed get user id, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
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
            Storage::append("error_sync/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

}