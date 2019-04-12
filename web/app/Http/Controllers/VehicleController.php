<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\ELanguage;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleType;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\EAppointmentType;
use App\Enums\EVehicleAccredited;
use App\Enums\EManufacture;
use App\Enums\ECodePermissionGroup;
use App\Enums\EVehicleDisplayOrder;
use App\Helpers\ConfigHelper;
use App\Services\CustomerService;
use App\Services\VehicleService;
use App\Services\ConfigService;
use App\Traits\CommonTrait;
use App\Exports\VehicleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class VehicleController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(ConfigService $configService, VehicleService $vehicleService) {
		$this->configService = $configService;
		$this->vehicleService = $vehicleService;
	}
	
	public function viewManageVehicle() {  
		if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
			return abort(403, 'Unauthorized action!');
		}
		$listManufacture = $this->vehicleService->getManufacture();
		return view('vehicle.manage-vehicle',['listManufacture'=>$listManufacture]);
	}

	    public function searchVehicle(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $manufacture_selling = $request->get('id_manufacture');
            $poster_selling = $request->get('poster');
            $selling_status = $request->get('status'); 
            $model = $request->get('model');
            $code = $request->get('code');

            $infoExportExcel = ['id_manufacture_selling'=>$manufacture_selling, 'poster'=>$poster_selling, 'selling_status'=>$selling_status, 'model'=>$model];
            
            $page = 1;
            if ($request->get('page') !== null) {
                $page = $request->get('page');
            }
            $listSearchVehicle = $this->vehicleService->searchVehicle($manufacture_selling, $model, $selling_status, $poster_selling, $code, $page);
            $tmp = $listSearchVehicle->map(function ($item) {
                $getNameManufactureById = $this->vehicleService->getNameManufactureById($item->vehicle_manufacture_id);
                $getNameManufactureModelById = $this->vehicleService->getNameManufactureById($item->vehicle_model_id);
                return [
                    'selling_vehicle_id' => $item->selling_vehicle_id,
                    'manufacture' => $item->manufacture_selling,
                    'description' => $item->description,
                    'price' => number_format($item->price),
                    'vehicle_manufacture_id' => $getNameManufactureById[0]->name,
                    'vehicle_model_id' =>$getNameManufactureModelById[0]->name,
                    'selling_status' => $item->selling_status,
                    'poster_name' => $item->poster_name,
                    'title' => $item->title,
                    'accredited' => $item->accredited,
                    'number_phone' => $item->phone,
                    'display_order' => $item->display_order,
                    'code' => $item->code,
                    'approved' => $item->approved,
                ];
            });

            $listSearchVehicle->setCollection($tmp);
            return response()->json(['listSearch'=>$listSearchVehicle,'exportVehicleList'=>$infoExportExcel]);
        }
        return response()->json([]);
    }

	public function getManufactureModel(Request $request) {
		if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
			return abort(403, 'Unauthorized action!');
		}
		if ($request->ajax()) {
			$id_manufacture = $request->get('id_manufacture');
			$listManufacture = $this->vehicleService->getManufactureModel($id_manufacture);
			return response()->json($listManufacture);
		}
		return response()->json([]);
	}

	public function loadSellingRequestResource(Request $request) {
		if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
			return abort(403, 'Unauthorized action!');
		}
		$pathToResource = config('app.resource_url_path');
		$id = $request->get('id');
		$listImage = $this->vehicleService->loadSellingRequestResource($id);
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'path' => $pathToResource,'imageVehicle'=> $listImage]);
	}

	public function updateAccredited(Request $request) {
		if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
			return abort(403, 'Unauthorized action!');
		}
		$created_by = auth()->id();
		$arrayId = $request->get('accredited');
		if (count($arrayId) < 1) {
			return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Not find id Update']);
		}

		foreach ($arrayId as $idVehicle) {
			$accreditedSellingRequest = $this->vehicleService->accreditedSellingRequest($idVehicle, $created_by);
		}
		
		if (isset($accreditedSellingRequest)) {
			return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		} else {
			return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
		}
	}

	public function exportVehicleList(Request $request) {
		if (Gate::denies('enable_feature', ECodePermissionGroup::SERVICE)) {
			return abort(403, 'Unauthorized action!');
		}
		$poster = $request->get('poster');
		$id_manufacture_selling = $request->get('vehicle_manufacture_id');
		$selling_status = $request->get('selling_status');
		$model = $request->get('model');
		return Excel::download(new VehicleExport($poster, $id_manufacture_selling, $selling_status, $model), 'vehicle-411.xlsx');
	}

	public function updateStatus(Request $rq) {
		if ($rq ->ajax()) {
			$idStatus = $rq->get('selling_id');
			$updateStatus = $this->vehicleService->updateStatus($idStatus);
			if (isset($updateStatus)) {
				return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
			} else {
					return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
			}
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}
	
	public function updateprioritize(Request $rq){
		if ($rq->ajax()) {
			$id = $rq->get('selling_id');
			$order = $rq->get('displayOrder');
			$disPlayOrder = $this->vehicleService->updateprioritize($id,$order);
			if (isset($disPlayOrder)) {
				return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
			} else {
					return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
			}
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}

	public function updateApproved(Request $request) {
		if ($request->ajax()) {
			$created_by = auth()->id();
			$selling_id = $request->get('approved');
			if(count($selling_id) < 1) {
				return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
			}
			foreach ($selling_id as $key) {
				$result = $this->vehicleService->updateApproved($key, $created_by);
			}
			if (isset($result)) {
				return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
			} else {
				return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
			}
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}
}
