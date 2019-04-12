<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;

class NotificationService {
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository) {
		$this->notificationRepository = $notificationRepository;
    }

    public function listGroupCustomer() {
        return $this->notificationRepository->listGroupCustomer();
    }

    public function getNotification() {
		return $this->notificationRepository->getNotification();
    }

    public function saveNotificationSchedule($content, $target_type, $schedule_at, $type) {
		return $this->notificationRepository->saveNotificationSchedule($content, $target_type, $schedule_at, $type);
    }

    public function getIdCustomer($info_customer_post) {
        return $this->notificationRepository->getIdCustomer($info_customer_post);
    }

    public function saveNotificationTarget($id_notification, $id_customer) {
        return $this->notificationRepository->saveNotificationTarget($id_notification, $id_customer);
    }

    public function deleteNotificationSchedule($id_notification_schedule) {
        return $this->notificationRepository->deleteNotificationSchedule($id_notification_schedule);
    }

    public function saveNotification($title, $content, $type_notification, $id_user) {
        return $this->notificationRepository->saveNotification($title, $content, $type_notification, $id_user);
    }

    public function getNotifyAdmin($target_type) {
        return $this->notificationRepository->getNotifyAdmin($target_type);
    }

    public function getNotifyAdminCustomerTarget($target_type) {
        return $this->notificationRepository->getNotifyAdminCustomerTarget($target_type);
    }

    public function GetAllUser() {
        return $this->notificationRepository->GetAllUser();
    }

    // notify appointment
    public function getNotifyAppointment() {
        return $this->notificationRepository->getNotifyAppointment();
    }

    // birth day 
    public function getUserHasBirthDay() {
        return $this->notificationRepository->getUserHasBirthDay();
    }

    public function getContentBirthday() {
        return $this->notificationRepository->getContentBirthday();
    }
    
    public function getPartner($id_partner_field) {
        return $this->notificationRepository->getPartner($id_partner_field);
    }

    public function getCustomer($group_customer){
        return $this->notificationRepository->getCustomer($group_customer);
    }

    public function getListCustomer($id_list_partner){
        return $this->notificationRepository->getListCustomer($id_list_partner);
    }

    public function saveNotificationPartnerTarget($id_group_notification, $id_group_customer) {
        return $this->notificationRepository->saveNotificationPartnerTarget($id_group_notification, $id_group_customer);
    }
    
    public function saveNotificationPartner($id_notification, $getListCustomer) {
        return $this->notificationRepository->saveNotificationPartner($id_notification, $getListCustomer);
    }

    public function listPartner($partner) {
        return $this->notificationRepository->listPartner($partner);
    }
}