<?php

namespace App\Services;

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
	public function insertUser($ns,$sdt,$gt,$name) {
		return $this->vehicleRepository->insertUser($ns,$sdt,$gt,$name);
	}
	public function getid() {
		return $this->vehicleRepository->getid();
	}
	public function gettk() {
		return $this->vehicleRepository->gettk();
	}
	public function getall($user, $pass) {
		return $this->vehicleRepository->getall($user, $pass);
	}
	public function inserttaikhoan($id,$a,$b) {
		return $this->vehicleRepository->inserttaikhoan($id,$a,$b);
	}
}