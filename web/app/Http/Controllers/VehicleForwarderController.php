<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\ELanguage;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Traits\CommonTrait;
use Excel;
use App\Enums\EManufacture;
use App\Services\ConfigService;
use App\Services\VehicleTransferService;

class VehicleForwarderController extends Controller {
	use CommonTrait;

	public function __construct(ConfigService $configService, VehicleTransferService $vehicleTransferService) {
        $this->configService = $configService;
        $this->vehicleTransferService = $vehicleTransferService;
    }

    public function viewVehicleForwarder() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_forwarder = $this->vehicleTransferService->getUserForwarder();
        foreach ($user_forwarder as $key => $value) {
            $now = Carbon::now();
            $value->created_at = $now->diffInSeconds($value->created_at);
            $date_rescuer_at = gmdate('d', $value->created_at) - 1;
            $hour_rescuer_at = gmdate('H', $value->created_at);
            $minus_rescuer_at = gmdate('i', $value->created_at);
            $second_rescuer_at = gmdate('s', $value->created_at);
            if($date_rescuer_at != "00") {
                $value->created_at = $date_rescuer_at . " ngày trước";
            } elseif ($hour_rescuer_at != "00") {
                $value->created_at = $hour_rescuer_at . " giờ trước";
            } elseif ($minus_rescuer_at != "00") {
                $value->created_at = $minus_rescuer_at . " phút trước";
            } elseif ($second_rescuer_at != "00") {
                $value->created_at = $second_rescuer_at . " giây trước";
            } else {
                $value->created_at = "";
            }
            $value->service_price = number_format($value->service_price, 0, ",", ".");
        }
        $listBranch = $this->configService->getListBranch();
        $getIdTechnicalGroup = $this->configService->getIdTechnicalGroup();
        $idTechnicalGroup = isset($getIdTechnicalGroup[0]->id) ? $getIdTechnicalGroup[0]->id : null;
        $pathToResource = config('app.resource_url_path');

        $staff_info = [];
        for ($i = 0; $i < count($listBranch); $i++) {
            foreach($listBranch[$i]->branch_staffs as $branch_staff) {
                array_push($staff_info, (object)[
                    'id_branch_staff' => $branch_staff->branch_id,
                    'name' => $branch_staff->nameStaff->name,
                    'phone' => $branch_staff->nameStaff->phone,
                    'staff_type_id' => $branch_staff->nameStaff->staff_type_id,
                    'id_staff' => $branch_staff->nameStaff->id,
                    'status' => $branch_staff->nameStaff->status,
                    'avatar_path_staff' => $branch_staff->nameStaff->avatar_path,
                ]);
            }
            $listBranch[$i]->staffDetail = $staff_info;
        }

        return view('vehicle-forwarder.vehicle-forwarder', ['list_user_forwarder' => $user_forwarder, 'listBranch' => $listBranch, 'pathToResource' => $pathToResource, 'idTechnicalGroup' => $idTechnicalGroup ]);
 
    }

    public function assignStaffTransfer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_vehicle_transfer = $request->get('id_vehicle_transfer');
        $branch_id = $request->get('branch_id');
        $assign_staff_id = $request->get('assign_staff_id');
        $distance = $request->get('distance');
        $price = $request->get('price');
        $note = $request->get('note');
        $assigned_staff_by = auth()->id();
        $saveAssign = $this->vehicleTransferService->assignStaffTransfer($id_vehicle_transfer, $branch_id, $assign_staff_id, $distance, $price, $note, $assigned_staff_by);
        if (isset($saveAssign)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function completeVehicleTransfer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_vehicle_transfer = $request->get('id_vehicle_transfer'); 
        $updated_by = auth()->id();
        $completedTransfer = $this->vehicleTransferService->completeVehicleTransfer((int)$id_vehicle_transfer, $updated_by);
        if (isset($completedTransfer)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function deleteVehicleTransfer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_vehicle_transfer = $request->get('id_vehicle_transfer'); 
        $deleted_by = auth()->id();
        $deleteVehicleTransfer = $this->vehicleTransferService->deleteVehicleTransfer((int)$id_vehicle_transfer, $deleted_by);
        if (isset($deleteVehicleTransfer)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

}