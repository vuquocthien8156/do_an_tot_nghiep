<?php

namespace App\Repositories;

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
		$result = DB::table('taikhoan')->select('email','mat_khau')->where([
			'email' => $a,
			'mat_khau' => $b
		])->get();
		return $result;
	}
	public function insertUser($ns,$sdt,$gt,$name) {
		$user = new Users();
		$user->ten = $name;
		$user->ngay_sinh = $ns;
		$user->gioi_tinh = $gt;
		$user->sdt = $sdt;
		$user->trang_thai = 1;
		$user->save();
		return $user;
	}
	public function getid() {
		$result = DB::table('users')->max('id');
		return $result;
	}
	public function gettk() {
		$result = DB::table('taikhoan')->select('email')->where('trang_thai','=',1)->get();
		return $result;
	}
	public function getall($user, $pass) {
		$result= DB::table('taikhoan')->select('email','user_id')->where([
			'trang_thai' => 1,
			'email' => $user,
			'mat_khau' => $pass
		])->get();
		return $result;
	}
	public function inserttaikhoan($id,$a,$b) {
		$tk = new taikhoan();
		$tk->user_id = $id;
		$tk->email = $a;
		$tk->mat_khau = $b;
		$tk->trang_thai = 1;
		$tk->save();
		return $tk;
	}
}