<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Enums\EUser;
use App\Constant\SessionKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SyncUpdateDB411Repository;

class SyncUpdateDB411Service {
    protected $syncUpdateDB411Repository;

	public function __construct(SyncUpdateDB411Repository $syncUpdateDB411Repository) {
		$this->syncUpdateDB411Repository = $syncUpdateDB411Repository;
    }
    /// MemberShipCard

    //Get data update membership card
    public function getDataSyncUpdateMemberShipCard($pageSize, $page) {
        return $this->syncUpdateDB411Repository->getDataSyncUpdateMemberShipCard($pageSize, $page);
    }

    public function getIdUserByDataSync($value) {
        return $this->syncUpdateDB411Repository->getIdUserByDataSync($value);
    }

    public function checkCardMemberExistWaiting($id_user) {
        return $this->syncUpdateDB411Repository->checkCardMemberExistWaiting($id_user);
    }

    public function checkCardMemberExist($id_user) {
        return $this->syncUpdateDB411Repository->checkCardMemberExist($id_user);
    }

    public function saveMemberShipCardSync($id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON)  {
        $timestamp = Carbon::now();
        try {
            $saveMemberShipCardSync = $this->syncUpdateDB411Repository->saveMemberShipCardSync($id_user, $status, $name, $vehicle_number, $code, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);
            if (isset($saveMemberShipCardSync)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! sync membership card vehicle_number: {$vehicle_number}. id_user: {$id_user}";
                Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed sync membership card vehicle_number: {$vehicle_number}.  id_user: {$id_user}. Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    //update member ship card
    public function updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status) {
        $timestamp = Carbon::now();
        try {
            $update_data_membership_card = $this->syncUpdateDB411Repository->updateDataMembershipCard($id_membership_card, $code, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status);
            if (isset($update_data_membership_card)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update data membership card id_membership_card: {$id_membership_card}";
                Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update data membership card id_membership_card: {$id_membership_card} . Error:  {$e->getMessage()}";
            Storage::append("error_sync/error_membership_card_update_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }


    /// ORDER
    public function getDataSyncUpdateOrders($pageSize, $page) {
        return $this->syncUpdateDB411Repository->getDataSyncUpdateOrders($pageSize, $page);
    }

    public function checkCodeExist($value) {
        return $this->syncUpdateDB411Repository->checkCodeExist($value);
    }


    //Membershipcard2
    public function getDataSyncCardMemberUpdate2($pageSize, $page) {
        return $this->syncUpdateDB411Repository->getDataSyncCardMemberUpdate2($pageSize, $page);
    }

    public function checkExist_MaXe($value) {
        return $this->syncUpdateDB411Repository->checkExist_MaXe($value);
    }
}