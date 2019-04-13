<?php

namespace App\Http\Controllers;

use App\Services\VehicleService;
use App\Traits\CommonTrait;
use App\Enums\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class logintestController extends Controller {
	use CommonTrait;

	private $aService;

	public function __construct( VehicleService $vehicleService) {

		$this->vehicleService = $vehicleService;
	}
	
	public function login() {
		return view('logintest.logintest');
	}
	public function register() {
		return view('dangky.dangky');
	}
	public function dangnhap(Request $request) {
		$a = $request->get('user');
		$b = $request->get('pass');
		$c = $this->vehicleService->dangnhap($a,$b);
		return \Response::json(['a' =>$c]);
	}
	public function d($user, $pass) {
			$c = $this->vehicleService->getall($user, $pass);
			if ($c[0]->email) {
				return \Response::json(['status' =>"ok",'success' => true,'item' => $c]);
			}
			return \Response::json(['status' =>"error",'success' => false]);
	}

	public function d1(Request $request) {
			$user = $request->input('user');
			$user = $request->input('pass');
			$c = $this->vehicleService->getall($user, $pass);
			if ($c[0]->email) {
				return \Response::json(['status' =>"ok",'success' => true,'item' => $c]);
			}
			return \Response::json(['status' =>"error",'success' => false]);
	}

	public function dangkytaikhoan(Request $request) {
		$a = $request->get('user');
		$b = $request->get('pass');
		$ns = $request->get('ns');
		$gt = $request->get('gioitinh');
		$sdt = $request->get('sdt');
		$name = $request->get('name');
		$listtk = $this->vehicleService->gettk();
		for ($i=0; $i < count($listtk) ; $i++) { 
			if ($a == $listtk[$i]->email) {
				return 1;		
			}
			$c = $this->vehicleService->insertUser($ns,$sdt,$gt,$name);
			$id = $this->vehicleService->getid();
			$tk = $this->vehicleService->inserttaikhoan($id,$a,$b);
			if (isset($c) && isset($tk)) {
				return 0;
			}
		}
	}
}
