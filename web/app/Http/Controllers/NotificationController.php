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
use App\Enums\ENotificationType;
use App\Enums\ENotificationScheduleType;
use App\Enums\EAppointmentType;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\CustomerService;
use App\Services\ConfigService;
use App\Services\NotificationService;
use App\Traits\CommonTrait;
use Excel;
use App\Enums\EManufacture;
use App\Services\AuthorizationService;

class NotificationController extends Controller {
	use CommonTrait;

	public function __construct(ConfigService $configService, NotificationService $notificationService, AuthorizationService $authorizationService) {
        $this->configService = $configService;
        $this->notificationService = $notificationService;
        $this->authorizationService = $authorizationService;
    }

    public function viewNotification() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::NOTIFICATION)) {
            return abort(403, 'Unauthorized action!');
        }
        $infoCustomer = $this->configService->infoCustomer();
        $listGroupCustomer = $this->notificationService->listGroupCustomer();
        return view('notification.notification', ['infoCustomer' => $infoCustomer, 'listGroupCustomer' => $listGroupCustomer]);
    }

    public function listNotification(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::NOTIFICATION)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
			$listNotification = $this->notificationService->getNotification();
            $timezone = $this->getUserTimezone();
			$tmp = $listNotification->map(function ($item) use ($timezone) {
				return [
                    'id' => $item->id,
                    'content' => $item->content,
                    'schedule_at' => isset($item->schedule_at) ? Carbon::parse($item->schedule_at)->timezone('Asia/Ho_Chi_Minh')->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : null,
                    'target_type' => $item->target_type,
                    'type' => $item->type,
                    'status' => $item->status,
				];
            });

			$listNotification->setCollection($tmp);
			return response()->json($listNotification);
		}
		return response()->json([]);
    }

    public function saveNotificationSchedule(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::NOTIFICATION)) {
            return abort(403, 'Unauthorized action!');
        }
        $content = $request->input('content_notification');
        $target_type = $request->input('customer_type'); 
        $time_config = $request->input('time_config');
        $type= $request->input('type_notification');
        $schedule_at = Carbon::parse($time_config);
        if ($target_type == ENotificationScheduleType::ALL_CUSTOMER) {
            $saveNotificationSchedule = $this->notificationService->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
        
        } else if ($target_type == ENotificationScheduleType::SPECIFICALLY_CUSTOMER) {
            $number_of_customer = $request->input('number_of_customer');
            for ( $i = 0; $i < $number_of_customer; $i++ ) {
                $info_customer_post = $request->input('info_customer_post' . $i);
                if ( $info_customer_post !== null && $info_customer_post !== '') {
                    $getIdCustomer = $this->notificationService->getIdCustomer($info_customer_post);
                    if (!isset($getIdCustomer[0]->id)) {
                        return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Không tìm thấy khách hàng!']);
                    }
                    $saveNotificationSchedule = $this->notificationService->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
                    $id_notification = $saveNotificationSchedule->id;
                    $id_customer = $getIdCustomer[0]->id;
                    $saveNotificationTarget = $this->notificationService->saveNotificationTarget($id_notification, $id_customer);
                    if(!isset($saveNotificationTarget)) {
                        return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Lỗi']);
                    }
                }
            }
        } else if ($target_type == ENotificationScheduleType::GROUP_CUSTOMER) {
            $partner = $request->input('partner');
            $group_customer = $request->input('group_customer');
            if($group_customer !== null && $group_customer !== '') {
                $getCustomer = $this->notificationService->getCustomer($group_customer);
                if(!isset($getCustomer[0]->id)) { 
                    if ($partner != 'all') {
                        $getCustomerbyIdPartner = $this->notificationService->getCustomer($partner);
                        $saveNotificationSchedule = $this->notificationService->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
                        $id_notification_group = $saveNotificationSchedule->id;
                        if (count($getCustomerbyIdPartner) > 1) {
                            for ($i = 0; $i < count($getCustomerbyIdPartner); $i++) { 
                                $id_group_customer[] = $getCustomerbyIdPartner[$i]->id;    
                            }
                            $saveNotificationPartnerTarget = $this->notificationService->saveNotificationPartnerTarget($id_notification_group, $id_group_customer);
                        }
                    } else {
                        $listPartner = $this->notificationService->getPartner($group_customer);
                        for($i = 0; $i < count($listPartner); $i++) {
                            $id_list_partner[] = $listPartner[$i]->id;        
                        }

                        $saveNotificationSchedule = $this->notificationService->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
                        $id_notification = $saveNotificationSchedule->id;
                        $getListCustomer = $this->notificationService->getListCustomer($id_list_partner);

                        for ($i = 0; $i < count($getListCustomer); $i++) {
                            if (isset($getListCustomer[$i][0]->id) == true) {
                                foreach ($getListCustomer[$i] as $key => $value) {
                                    $id_list_customer[] = $value->id;

                                }
                            }
                        }

                        $saveNotificationPartner = $this->notificationService->saveNotificationPartner($id_notification, $id_list_customer);
                    }
                    
                } else {
                    $saveNotificationSchedule = $this->notificationService->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
                    $id_notification_group = $saveNotificationSchedule->id;
                    for ($i = 0; $i < count($getCustomer); $i++) { 
                        $id_group_customer[] = $getCustomer[$i]->id;    
                    }
                    $saveNotificationPartnerTarget = $this->notificationService->saveNotificationPartnerTarget($id_notification_group, $id_group_customer);
                }
            }
        }
        if (isset($saveNotificationSchedule)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Lỗi']);
        }    
    }
    
    public function deleteNotificationSchedule(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::NOTIFICATION)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_notification_schedule = $request->input('id_notification_schedule');
        $deleteNotification = $this->notificationService->deleteNotificationSchedule($id_notification_schedule);
        if (isset($deleteNotification)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Lỗi']);
        }
    }

    public function getPartner(Request $request) {
        if ($request->ajax()) {
            $id_partner_field = $request->get('id_partner_field');
            $getListPartner = $this->notificationService->getPartner($id_partner_field);
             return response()->json($getListPartner);
        }
        return response()->json([]);
    }
}