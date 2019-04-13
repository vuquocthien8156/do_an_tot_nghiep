<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class VehicleService {
	protected $vehicleRepository;

	public function __construct(VehicleRepository $vehicleRepository) {
		$this->vehicleRepository = $vehicleRepository;
	}
	
	public function dangnhap($a,$b) {
		return $this->vehicleRepository->dangnhap($a,$b);
	}
}