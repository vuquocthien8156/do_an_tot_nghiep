<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;
use App\Repositories\RescueRequestRepository;

class RescueRequestService {
    protected $rescueRequestRepository;

	public function __construct(RescueRequestRepository $rescueRequestRepository) {
		$this->rescueRequestRepository = $rescueRequestRepository;
    }

    public function getUserRescue() {
        return $this->rescueRequestRepository->getUserRescue();
    }

    public function getListBranchStaff($branch_id_resuce) {
        return $this->rescueRequestRepository->getListBranchStaff($branch_id_resuce);
    }

    public function saveAssignStaffRescue($branch_id_rescue, $id_staff_rescue, $distance, $price, $note, $id_rescue_request, $assigned_rescuer_by) {
        return $this->rescueRequestRepository->saveAssignStaffRescue($branch_id_rescue, $id_staff_rescue, $distance, $price, $note, $id_rescue_request, $assigned_rescuer_by);
    }

    public function completeRescue($id_rescue_request, $updated_by) {
        return $this->rescueRequestRepository->completeRescue($id_rescue_request, $updated_by);
    }

    public function deleteRescue($id_rescue_request, $deleted_by) {
        return $this->rescueRequestRepository->deleteRescue($id_rescue_request, $deleted_by);
    }
}