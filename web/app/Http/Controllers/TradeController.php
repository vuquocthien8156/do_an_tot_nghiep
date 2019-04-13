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
use App\Enums\EAppointmentType;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use App\Services\TradeService;
use App\Traits\CommonTrait;
use App\Exports\TradeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class TradeController extends Controller {
	public function index() {
		return view('trade.index');
	}

	private $tradeService;

    public function __construct(TradeService $tradeService) {
        $this->tradeService = $tradeService;
    }

    public function viewManageTrade() {
        $listBranch = $this->tradeService->getBranch();  
        return view('trading.manage-trade',['listBranch'=>$listBranch]);
    }

    public function searchTrade(Request $request) {
      
        if ($request->ajax()) {
                $customer_name = $request->get('customer_name_phone');
                $customer_phone = $request->get('customer_name_phone');
                $vehicle_number = $request->get('vehicle_number');
                $from_date = $request->get('from_date');
                $employees = $request->get('employees');
                $to_date = $request->get('to_date');
                if(isset($from_date)) {
                $from_date = Carbon::parse($from_date)->toDateTimeString();
                } else {
                    $from_date = null;
                }
                if (isset($to_date)) {
                    $to_date = date("Y-m-d H:i:s", (strtotime(Carbon::parse($to_date)->toDateTimeString()) + (24 * 60 * 60 - 1)));
                } else {
                    $to_date = null;
                }
                $name_store = $request->get('name_store');
                $infoExportExcel = ['customer_phone'=>$customer_phone, 'from_date'=>$from_date, 'employees'=>$employees, 'to_date'=>$to_date,'trade_name'=>$customer_name, 'vehicle_number'=>$vehicle_number, 'name_store'=>$name_store];

                $page = 1;
                    if ($request->get('page') !== null) {
                    $page = $request->get('page');
                }
                $listSearchTrade = $this->tradeService->searchTrade($from_date, $employees, $customer_name, $customer_phone, $to_date, $vehicle_number, $name_store, $page);
                    $tmp = $listSearchTrade->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'status' => $item->status,
                        'code' => $item->code,
                        'price' => number_format($item->price),
                        'user_name' => $item->user_name,
                        'user_phone' => $item->user_phone,
                        'vehicle_number' => $item->vehicle_number,
                        'feedback' => $item->feedback,
                        'order_id' => $item->orders_id,
                        'staff' => $item->staff,
                        'kilometer' => $item->kilometer,
                        'created_at' => isset($item->order_created_at) ? Carbon::parse($item->order_created_at)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : null,
                        'branch_user' => $item->branch_user,
                    ];
                });

            $listSearchTrade->setCollection($tmp);
            return response()->json(['listSearch'=>$listSearchTrade, 'exportVehicleList'=>$infoExportExcel]);
        }
        return response()->json([]);
    }

    public function showDetail(Request $request) {
        if ($request->ajax()) {
            $id_order_detail = $request->get('id_order_detail');
            $listOrderDetail = $this->tradeService->showDetail($id_order_detail);
            $tmp = $listOrderDetail->map(function ($item) {
                $meta = json_decode($item->meta);
                if (isset($meta->syncData[2]->PhuTung) && isset($meta->syncData[1]->ChiTietSuaChuaXe)) {
                    $code = $meta->syncData[2]->PhuTung->data->MaVach;
                    $discount = $meta->syncData[1]->ChiTietSuaChuaXe->data->ChietKhau;
                } else {
                        $code = '';
                        $discount = 0;
                }
                return [
                    'service' => isset($item->service)? $item->service : '',
                    'price_total' => number_format($item->price_total),
                    'quantity' => isset($item->quantity)? $item->quantity : '',
                    'discount' => number_format($discount),
                    'price_after_discount' =>number_format($item->price_total - ($item->price_total*$discount)/100),
                    'meta' => $code
                ];
            });
            return response()->json(['listOrderDetail'=>$tmp]);
        }
        return response()->json([]);
    }

    public function feedbackTrade(Request $request) {
        try {
            $id_feedback = $request->get('id_feedback');
            $content_feedback = $request->get('content_feedback');
            $created_by = auth()->id();
            $feedbackTrade = $this->tradeService->feedbackTrade($id_feedback, $content_feedback, $created_by);
            if (isset($feedbackTrade)) {
                return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
            } else {
                return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
            }
        } catch (\Exception $e) {
            logger('Fail to update feedback' . $id_feedback , ['e' => $e]);
            return null;
        }
    }
    
    public function DeleteTradingRequest(Request $request) {
        $arrayId =$request->get('idDelete');
        if (count($arrayId) < 1) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Not find id Update']);
        }

        foreach ($arrayId as $idDelete) {
            $DeleteTradingRequest = $this->tradeService->DeleteTradingRequest($idDelete);
        }
        
        if (isset($DeleteTradingRequest)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function deleteFeedbackRequest(Request $request) {
        $id_delete_feedback =$request->get('id_delete_feedback');
        if ($id_delete_feedback == null && $id_delete_feedback == '') {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Not find id Delete']);
        }

        $deleteFeedbackRequest = $this->tradeService->deleteFeedbackRequest($id_delete_feedback);
        
        if (isset($deleteFeedbackRequest)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function exportTradeList(Request $request) {
        $customer_phone = $request->get('customer_name');
        $vehicle_number = $request->get('vehicle_number');
        $from_date = $request->get('from_date');
        $employees = $request->get('employees');
        $to_date = $request->get('to_date');
        $name_store = $request->get('name_store');
        return Excel::download(new TradeExport($from_date, $employees, $to_date, $customer_phone, $vehicle_number, $name_store), 'Trade-411.xlsx');
        
    }
}
