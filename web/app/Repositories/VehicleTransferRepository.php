<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\EVehicleTransferStatus;
use App\Enums\ENotificationType;
use App\Models\VehicleTransfer;
use App\Models\Branch;
use App\Models\Users;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\EDateFormat;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Services\FirebaseService;
use App\Models\UserDevice;
use App\Services\NotificationService;

class VehicleTransferRepository {

	public function __construct(Notification $notification, VehicleTransfer $vehicleTransfer, Users $users, NotificationService $notificationService) {
        $this->vehicleTransfer = $vehicleTransfer;
        $this->users = $users;
        $this->notification = $notification;
        $this->notificationService = $notificationService;
    }

    public function getVehicleTransferById($id) {
        return $this->vehicleTransfer->find($id);
    }
    
    public function getInfoUser($id) {
        return $this->users->find($id);
    }

    public function getUserForwarder() {
        try {
            $result = DB::table('vehicle_transfer as vt')
                        ->select('vt.id', 'vt.user_id', 'vt.latitude', 'vt.longitude', 'vt.assigned_staff_at', 
                        'vt.transfer_status', 'vt.created_at', 'vt.estimated_distance', 'vt.service_price', 'vt.note', 'vt.status',
                        'us.name', 'us.phone', 'us_staff.name as name_staff', 'us_staff.phone as phone_staff')
                        ->join('users as us', 'vt.user_id', '=', 'us.id')
                        ->leftJoin('users as us_staff', 'vt.assigned_staff_id', '=', 'us_staff.id')
                        ->where('us.status', '=', EStatus::ACTIVE)->orderBy('vt.created_at', 'desc')->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed to Get vehicle transfer user message: " . $e->getMessage());
            return null;
        }
    }

    public function getListBranchStaff($branch_id) {
        try {
            $resurlt = DB::table('branch_staff as bs')
                        ->select('bs.staff_id', 'bs.branch_id', 'us.name', 'us.avatar_path', 'us.phone', 'bs.id')
                        ->join('users as us', 'bs.staff_id', '=', 'us.id')
                        ->where([['bs.branch_id', '=', $branch_id], ['us.status', '=', EStatus::ACTIVE], ['bs.status', '=', EStatus::ACTIVE], ['us.type', '=', EUser::TYPE_STAFF]])
                        ->get();
            return $resurlt;
        } catch (\Exception $e) {
            logger("Failed to Get rescue request user message: " . $e->getMessage());
            return null;
        }
    }

    public function assignStaffTransfer($id_vehicle_transfer, $branch_id, $assign_staff_id, $distance, $price, $note, $assigned_staff_by) {
        try {
                $now = Carbon::now();
                $result = DB::table('vehicle_transfer')->where('id', $id_vehicle_transfer)
                            ->update(['transfer_status' => EVehicleTransferStatus::ASSIGNED_STAFF, 
                                      'assigned_shop_id' => $branch_id,
                                      'assigned_staff_id' => $assign_staff_id,
                                      'assigned_staff_at' => $now,
                                      'assigned_staff_by' => $assigned_staff_by,
                                      'estimated_distance' => $distance,
                                      'service_price' => $price,
                                      'note' => $note,
                                      'updated_at' => $now,
                                      'updated_by' => $assigned_staff_by
                                    ]);
                //Send notification
                $vehicle_transfer = $this->getVehicleTransferById($id_vehicle_transfer);
                $id_user = $vehicle_transfer->user_id;
                $name_staff = $this->getInfoUser($assign_staff_id);
                $content = 'Nhân viên ' . $name_staff->name . ' đang tới giao nhận xe!';
                $title = 'Hệ Thống Sửa Xe 411';
                $type_notification = ENotificationType::VEHICLE_TRANSFER_ASSIGNED;

                $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);

                return $result;
        } catch (\Exception $e) {
            logger('Error save assign staff transfer vehicle', ['e' => $e]);
            return null;
        }
    }

    public function completeVehicleTransfer($id_vehicle_transfer, $updated_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('vehicle_transfer')->where('id', $id_vehicle_transfer)
                        ->update(['transfer_status' => EVehicleTransferStatus::TRANSFER_COMPLETE, 'transfer_completed_at' => $now]);
            return $result;
        } catch (\Exception $e) {
            logger('Error save completed rescue', ['e' => $e]);
            return null;
        }
    }

    public function deleteVehicleTransfer($id_vehicle_transfer, $deleted_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('vehicle_transfer')->where('id', $id_vehicle_transfer)
                        ->update(['status' => EStatus::DELETED, 'deleted_by' => $deleted_by, 'deleted_at' => $now]);
            return $result;
        } catch (\Exception $e) {
            logger('Error save completed rescue', ['e' => $e]);
            return null;
        }
    }
}