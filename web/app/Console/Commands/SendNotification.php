<?php

namespace App\Console\Commands;

use App\Constant\ConfigKey;
use App\Enums\ENotificationType;
use App\Enums\ENotificationScheduleType;
use App\Enums\EStatus;
use App\Enums\EAppointmentType;
use App\Helpers\ConfigHelper;
use App\Services\FirebaseService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DateInterval;
use App\Traits\CommonTrait;
use App\Enums\EDateFormat;

class SendNotification extends Command {
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send
                                {--admin-allcustomer : All Customer}
                                {--admin-customer : Target Customer}
                                {--admin-customer-unit : Target Customer of unit}
                                {--appointment : Target Customer Appointment}
                                {--birthday-customer : Customer Has Birthday Today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send users notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $firebaseService;
    protected $notificationService;

    public function __construct(NotificationService $notificationService) {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() {
        if ($this->option('admin-allcustomer')) {
		    $this->notifyForAllCustomer();
        }
        if ($this->option('admin-customer')) {
		    $this->notifyForCustomer();
        } 
        if ($this->option('appointment')) {
		    $this->notifyAppointmentForCustomer();
        }
        if ($this->option('admin-customer-unit')) {
            $this->notifyForCustomerOfUnit();
        } 
        if ($this->option('birthday-customer')) {
		    $this->notifyHappyBirthDayForCustomer();
        }
    }

    public function notifyForAllCustomer() {
        $target_type = ENotificationScheduleType::ALL_CUSTOMER;
        $results = $this->notificationService->getNotifyAdmin($target_type);
        foreach ($results as $value) {
            if(isset($results) && isset($value->schedule_at)) {
                $now = Carbon::now();
                $datetime_now = strtotime($now); 
                $datetimeNotifyAdmin = strtotime($value->schedule_at);
                $diffInSeconds = $datetimeNotifyAdmin - $datetime_now;
                if ($diffInSeconds <= 60) {
                    $title = "Hệ Thống Sửa Xe 411";
                    $content = $value->content;
                    $type_notification = $value->type;
                    if($value->type == ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE) {
                        $title = "Khuyến mãi";
                    }
                    $getAllUser= $this->notificationService->GetAllUser();
                    for ($i = 0; $i < count($getAllUser); $i++) {
                        $id_user = $getAllUser[$i]->user_id;
                        $results = $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);
                    }              
                }
            }
        }     
    }

    public function notifyForCustomer() {
        $target_type = ENotificationScheduleType::SPECIFICALLY_CUSTOMER;
        $results = $this->notificationService->getNotifyAdminCustomerTarget($target_type);
        foreach ($results as $value) {
            if(isset($results) && isset($value->schedule_at)) {
                $now = Carbon::now();
                $datetime_now = strtotime($now); 
                $datetimeNotifyAdmin = strtotime($value->schedule_at);
                $diffInSeconds = $datetimeNotifyAdmin - $datetime_now;
                if ($diffInSeconds <= 60) {
                    $title = "Hệ Thống Sửa Xe 411";
                    $content = $value->content;
                    $type_notification = $value->type;
                    if($value->type == ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE) {
                        $title = "Khuyến mãi";
                    }
                    $id_user = $value->customer_id;
                    $results = $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);
                }
            }  
        }
    }

    public function notifyForCustomerOfUnit() {
        $target_type = ENotificationScheduleType::GROUP_CUSTOMER;
        $results = $this->notificationService->getNotifyAdminCustomerTarget($target_type);
        foreach ($results as $value) {
            if(isset($results) && isset($value->schedule_at)) {
                $now = Carbon::now();
                $datetime_now = strtotime($now); 
                $datetimeNotifyAdmin = strtotime($value->schedule_at);
                $diffInSeconds = $datetimeNotifyAdmin - $datetime_now;
                if ($diffInSeconds <= 60) {
                    $title = "Hệ Thống Sửa Xe 411";
                    $content = $value->content;
                    $type_notification = $value->type;
                    if($value->type == ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE) {
                        $title = "Khuyến mãi";
                    }
                    $id_user = $value->customer_id;
                    $results = $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);
                }
            }  
        }
    }

    public function notifyAppointmentForCustomer() {
        $results = $this->notificationService->getNotifyAppointment();
        $now = Carbon::now();
        $timezone = $this->getUserTimezone();
        foreach ($results as $value) {
            $datetime_now = strtotime($now); 
            $datetime_notifyAppointment = strtotime($value->appointment_at); 
            $timeDiff = $datetime_notifyAppointment - $datetime_now;
            $timeDiff = FLOOR($timeDiff / 60 / 60);            
            if ($timeDiff == '24' || $timeDiff == '72') {
                $title = 'Hệ Thống Sửa Xe 411';
                $time_appointment = Carbon::parse($value->appointment_at)->timezone('Asia/Ho_Chi_Minh')->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT);
                $content = 'Bạn có lịch hẹn ' . EAppointmentType::valueToName($value->type)  . ' vào lúc ' . $time_appointment . ' tại ' . $value->branch_name;
                $type = ENotificationType::NOTIFY_APPOINTMENT;
                $id_user = $value->user_id;
                $results = $this->notificationService->saveNotification($title, $content, $type, $id_user);
            }
        }
    }

    public function notifyHappyBirthDayForCustomer() {
        $results = $this->notificationService->getUserHasBirthDay();
        $now = Carbon::now();
        $timezone = $this->getUserTimezone();
        foreach ($results as $value) {
            $date_now = $now->day;
            $month_now = $now->month;
            $date_of_birth_user = Carbon::parse($value->date_of_birth); 
            $date_user = $date_of_birth_user->day;
            $month_user = $date_of_birth_user->month;

            if ($date_now == $date_user && $month_now == $month_user) {
                $title = 'Hệ Thống Sửa Xe 411';
                $type = ENotificationType::NOTIFY_BIRTHDAY_CUSTOMER;
                $content_data = $this->notificationService->getContentBirthday();
                $content = $content[0]->text_value;
                $id_user = $value->id;
                $results = $this->notificationService->saveNotification($title, $content, $type, $id_user);
            }
        }
    }
}
