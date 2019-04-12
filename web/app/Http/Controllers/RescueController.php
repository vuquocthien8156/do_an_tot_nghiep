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
use App\Enums\EManufacture;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\RescueRequestService;
use App\Services\ConfigService;
use App\Services\AuthorizationService;
use App\Traits\CommonTrait;
use Excel;

class RescueController extends Controller {
	use CommonTrait;

	public function __construct(ConfigService $configService, RescueRequestService $rescueRequestService, AuthorizationService $authorizationService) {
        $this->rescueRequestService = $rescueRequestService;
        $this->configService = $configService;
        $this->authorizationService = $authorizationService;
    }

    public function viewRescue() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_rescue = $this->rescueRequestService->getUserRescue();
        foreach ($user_rescue as $key => $value) {
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
                    'staff_type_id' => $branch_staff->nameStaff->staff_type_id,
                    'phone' => $branch_staff->nameStaff->phone,
                    'id_staff' => $branch_staff->nameStaff->id,
                    'status' => $branch_staff->nameStaff->status,
                    'avatar_path_staff' => $branch_staff->nameStaff->avatar_path,
                ]);
            }
            $listBranch[$i]->staffDetail = $staff_info;
        }

        return view('rescue.rescue', ['list_user_rescue' => $user_rescue, 'listBranch' => $listBranch, 'pathToResource' => $pathToResource, 'idTechnicalGroup' => $idTechnicalGroup ]);
    }
    
    public function getListBranchStaff(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $branch_id_rescue = $request->get('branch_id_rescue');
        $branch_staff_rescue = $this->rescueRequestService->getListBranchStaff($branch_id_rescue);
        $pathToResource = config('app.resource_url_path');
        if (isset($branch_staff_rescue)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'branch_staff_rescue' => $branch_staff_rescue, 'pathToResource' => $pathToResource]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function saveAssignStaffRescue(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_rescue_request = $request->get('id_rescue_request');
        $branch_id_rescue = $request->get('branch_id_rescue');
        $id_staff_rescue = $request->get('id_staff_rescue');
        $distance = $request->get('distance');
        $price = $request->get('price');
        $note = $request->get('note');
        $assigned_rescuer_by = auth()->id();
        $saveAssign = $this->rescueRequestService->saveAssignStaffRescue($branch_id_rescue, $id_staff_rescue, $distance, $price, $note, $id_rescue_request, $assigned_rescuer_by);
        $pathToResource = config('app.resource_url_path');
        if (isset($saveAssign)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function completeRescue(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_rescue_request = $request->get('id_rescue_request'); 
        $updated_by = auth()->id();
        $completedRescue = $this->rescueRequestService->completeRescue((int)$id_rescue_request, $updated_by);
        if (isset($completedRescue)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function deleteRescue(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_rescue_request = $request->get('id_rescue_request'); 
        $deleted_by = auth()->id();
        $deleteRescue = $this->rescueRequestService->deleteRescue((int)$id_rescue_request, $deleted_by);
        if (isset($deleteRescue)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }
}