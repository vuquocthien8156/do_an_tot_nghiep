<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use Illuminate\Http\Request;

class TestController extends Controller {
	private $testService;

	public function __construct(TestService $testService) {
		$this->testService = $testService;
	}

	public function showTestData() {
		$data = $this->testService->getAllTestData();
		dd($data);
	}

	public function showVueTest() {
		return view('example.vue');
	}
	// public function showNewTest() {
	// 	return view('example.new1');
	// }
}
