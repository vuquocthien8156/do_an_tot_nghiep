<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleDisplayOrder;
use App\Models\Users;
use App\Models\SellingVehicle;
use App\Models\taikhoan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	public function dangnhap($a, $b) {
		$result = DB::table('taikhoan')->select('email')->where('id', '=', 1)->get();
		return $result;
	}

}