<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Models\Notification;
use App\User;
use App\Enums\EUser;
use App\Enums\EUserDeviceType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationScheduleTarget;
use App\Models\NotificationSchedule;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use App\Models\UserDevice;
use App\Models\Users;
use App\Enums\ECategoryType;
use App\Services\FirebaseService;
use App\Constant\ConfigKey;

class NotificationRepository {

    public function __construct(Notification $notification, NotificationSchedule $notificationSchedule, NotificationScheduleTarget $notificationScheduleTarget ) {
        $this->notification = $notification;
        $this->notificationSchedule = $notificationSchedule;
        $this->notificationScheduleTarget = $notificationScheduleTarget; 
    }

    public function sendNotificationForUser($content, $id_user, $title, $type_notification) {
        //Send Message for Devide Id
        $messaging = FirebaseService::messaging();
        $messagingNotification = \Kreait\Firebase\Messaging\Notification::fromArray([
            'title' => $title,
            'body' => $content,
        ]);
        $messagingData = [
            'type' => "$type_notification",
            'title' => $title,
            'body' => $content,
            'android_channel_id' => "abcdef",
        ];
        $config = AndroidConfig::fromArray([
            'priority' => 'high',
        ]);
        $userDevices = UserDevice::where([['user_id', '=', $id_user], ['status', '=', EStatus::ACTIVE]])->get();
        foreach ($userDevices as $device) {
            try {
                if ($device->os_type == EUserDeviceType::DEVICE_IOS) {
                    $message = CloudMessage::withTarget('token', $device->device_token)
                                            ->withNotification($messagingNotification)
                                            ->withData($messagingData);
                } else {
                    $message = CloudMessage::withTarget('token', $device->device_token)->withData($messagingData);
                }
                $messaging->send($message);
            } catch (\Exception $e) {
                logger('error when send GCM message', ['e' => $e]);
            }
        }
        return true;
    }

    public function saveNotificationSchedule($content, $target_type, $schedule_at, $type) {
		try {
            $notification_schedule = new NotificationSchedule();
            $now = Carbon::now();
            $notification_schedule->content = $content;
            $notification_schedule->title = "Hệ Thống Sửa Xe 411";
            $notification_schedule->target_type = $target_type;
            $notification_schedule->schedule_at = $schedule_at;
            $notification_schedule->type = $type;
            $notification_schedule->created_at = $now;
            $notification_schedule->status = EStatus::ACTIVE;
            $notification_schedule->created_by = auth()->id();
            $notification_schedule->save();
            return $notification_schedule;
		} catch (\Exception $e) {
			logger("Failed to create Notification. Content: " . $content . "Target type: " . $target_type . "Created_at: " . $now . " message: " . $e->getMessage());
			return null;
		}
    }

    public function getNotification() {
        $result = NotificationSchedule::where('status', EStatus::ACTIVE)
                ->orderBy('id', 'desc')
                ->paginate(15);            
        return $result;
    }

    public function saveNotificationTarget($id_notification, $id_customer) {
        try {
            $notification_schedule_target = new NotificationScheduleTarget();
            $now = Carbon::now();
            $notification_schedule_target->notification_schedule_id = $id_notification;
            $notification_schedule_target->customer_id = $id_customer;
            $notification_schedule_target->created_at = $now;
            $notification_schedule_target->created_by = auth()->id();
            $notification_schedule_target->save();
            return $notification_schedule_target;
		} catch (\Exception $e) {
			logger("Failed to create notification_schedule_target. ID_Notification: " . $id_notification . "id_customer: " . $id_customer . "message: " . $e->getMessage());
			return null;
		}
    }

    public function saveNotificationPartnerTarget($id_notification_group, $id_group_customer) {
        try {
            for ($i=0; $i < count($id_group_customer); $i++) { 
                $notification_schedule_Partner_target = new NotificationScheduleTarget();
                $now = Carbon::now();
                $notification_schedule_Partner_target->notification_schedule_id = $id_notification_group;
                $notification_schedule_Partner_target->customer_id = $id_group_customer[$i];
                $notification_schedule_Partner_target->created_at = $now;
                $notification_schedule_Partner_target->created_by = auth()->id();
                $notification_schedule_Partner_target->save();
            }
            return $notification_schedule_Partner_target;
        } catch (\Exception $e) {
            logger("Failed to create notification_schedule_Partner_target. id_notification_group: " . $id_notification_group . "id_group_customer: " . $id_group_customer . "message: " . $e->getMessage());
            return null;
        }
    }

    public function saveNotificationPartner($id_notification, $id_list_customer) {
        try {
            for ($i=0; $i < count($id_list_customer); $i++) {   
                $notification_schedule_Partner = new NotificationScheduleTarget();
                $now = Carbon::now();
                $notification_schedule_Partner->notification_schedule_id = $id_notification;
                $notification_schedule_Partner->customer_id = $id_list_customer[$i];
                $notification_schedule_Partner->created_at = $now;
                $notification_schedule_Partner->created_by = auth()->id();
                $notification_schedule_Partner->save();
            }

            return $notification_schedule_Partner;
        } catch (\Exception $e) {
            logger("Failed to create notification_schedule_Partner. id_notification: " . $id_notification . "id_list_customer: " . $id_list_customer[0] . "message: " . $e->getMessage());
            return null;
        }
    }

    public function deleteNotificationSchedule($id_notification_schedule) {
        try {
            $result = NotificationSchedule::where('id', '=', $id_notification_schedule)->update(['status'=> EStatus::DELETED]);
            return $result;
		} catch (\Exception $e) {
			logger("Failed to update Status Notification Schedule: " . $id . "message: " . $e->getMessage());
			return null;
		}
    }

    public function saveNotification($title, $content, $type_notification, $id_user) {
        try {
            // Save noyification into DB
            $notification = new Notification();
            $now = Carbon::now();
            $notification->content = $content;
            $notification->title = $title;
            $notification->type = $type_notification;
            $notification->is_seen = false;
            $notification->created_at = $now;
            $notification->user_id = $id_user;
            $notification->save();
            $this->sendNotificationForUser($content, $id_user, $title, $type_notification);
            return $notification;
		} catch (\Exception $e) {
			logger("Failed to Save Notification message: " . $e->getMessage());
			return null;
		}
    }

    public function getIdCustomer($info_customer_post) {
        $result = Users::select('id')->where('phone', '=', $info_customer_post)->get();            
        return $result;
    }
    public function getCustomer($group_customer) {
        $result = Users::select('id')->where('partner_id', '=', $group_customer)->get();
        return $result;
    }
    // public function getListCustomer($id_list_partner) {
    //     //dd(count($id_list_partner));
    //     for ($i=0; $i < count($id_list_partner); $i++) { 
    //         $result[] = Users::select('id', 'name')->where('partner_id', '=', $id_list_partner[$i])->get();    
    //     }
    //     return $result;
    // }
    public function getListCustomer($id_list_partner) {
        for ($i=0; $i < count($id_list_partner); $i++) {
            $result[] = DB::table('users')->select('id')
                ->where('partner_id' , '=', $id_list_partner[$i])->get();
        }
        return $result;
    }
    public function getNotifyAdmin($target_type) {
        try {
            $now = Carbon::now();
            $result = DB::table('notification_schedule')
                    ->select('title', 'target_type', 'content', 'schedule_at', 'type')
                    ->where([
                        ['status', '=', EStatus::ACTIVE],
                        ['schedule_at', '>', $now],
                        ['target_type', '=', $target_type]])
                    ->orderBy('schedule_at', 'asc')
                    ->get();
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get Notify Admin Table Notification_schedule message: " . $e->getMessage());
			return null;
		}
    }

    public function getNotifyAdminCustomerTarget($target_type) {
        try {
            $now = Carbon::now();
            $result = DB::table('notification_schedule_target as nst')
                    ->select('ns.title', 'ns.target_type', 'ns.content', 'ns.schedule_at', 'ns.type',
                                'nst.customer_id', 'nst.notification_schedule_id')
                    ->join('notification_schedule as ns', 'ns.id', '=', 'nst.notification_schedule_id')
                    ->where([
                        ['ns.status', '=', EStatus::ACTIVE],
                        ['ns.schedule_at', '>', $now],
                        ['ns.target_type', '=', $target_type]])
                    ->orderBy('schedule_at', 'asc')
                    ->get();
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get Notify Admin Table Notification_schedule_target message: " . $e->getMessage());
			return null;
		}
    }

    public function GetAllUser() {
        try {
            $result = DB::table('users')
                    ->select('id as user_id')
                    ->where([
                        ['status', '=', EStatus::ACTIVE],
                        ['type', '=', EUser::TYPE_USER]])
                    ->orWhere([
                            ['status', '=', EStatus::ACTIVE],
                            ['type', '=', EUser::TYPE_STAFF]])
                    ->get();            
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get All User message: " . $e->getMessage());
			return null;
		}
    }

    public function getNotifyAppointment() {
        try {
            $now = Carbon::now();
            $result = DB::table('appointment as appo')
                    ->select('appo.id', 'appo.user_id', 'appo.type', 'appo.appointment_at', 'appo.branch_id',
                                'appo.note', 'appo.enable_reminder', 'bra.name as branch_name')
                    ->join('branch as bra', 'bra.id', '=', 'appo.branch_id')
                    ->where([
                        ['appo.status', '=', EStatus::ACTIVE],
                        ['appo.appointment_at', '>', $now],
                        ['appo.enable_reminder', '=', true]])
                    ->orderBy('appointment_at', 'asc')
                    ->get();
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get User has appoitment, need notify. Message: " . $e->getMessage());
			return null;
		}
    }

    public function getUserHasBirthDay() {
        try {
            $result = DB::table('users')
                    ->select('id', 'date_of_birth')
                    ->whereNotNull('date_of_birth')
                    ->where('status', '=', EStatus::ACTIVE)
                    ->get();            
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get All User has birth day today message: " . $e->getMessage());
			return null;
		}
    }

    public function getContentBirthday() {
        try {
            $result = DB::table('app_config')
                    ->select('text_value')
                    ->where('name', '=', ConfigKey::BIRTHDAY_CUSTOMER)
                    ->get();            
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get content birth day message: " . $e->getMessage());
			return null;
		}
    }

    public function listGroupCustomer() {
        $result = DB::table('category')->select('id', 'name')
            ->where([
                'status' => EStatus::ACTIVE,
                'type' => ECategoryType::PARTNER_FIELD,
            ])->orderBy('seq', 'asc')->get();
        return $result;
    }

    public function getPartner($id_partner_field) {
        $result = DB::table('category')->select('id', 'parent_category_id','name')
            ->where([
                'status' => EStatus::ACTIVE,
                'type' => ECategoryType::PARTNER,
                'parent_category_id' => $id_partner_field,
            ])->orderBy('seq', 'asc')->get();
        return $result;
    }
    
    public function listPartner($partner) {
        $result = DB::table('category')->select('id','name')
            ->where([
                'status' => EStatus::ACTIVE,
                'type' => ECategoryType::PARTNER,
                'parent_category_id' => $partner,
            ])->orderBy('seq', 'asc')->get();
        return $result;   
    }
}