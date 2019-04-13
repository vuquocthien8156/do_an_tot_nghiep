@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-vehicle"> 
		<div class="row mt-5 pt-3">
			<div style="padding-left: 2rem">
				<h4 class="tag-page-custom">
					<a class="tag-title-show" style="text-decoration: none;" href="
				</h4>
			</div>
		</div>
		<div class="row">
			<div class="set-row background-contact w-100" style="min-height: 150px">
				<div class="pb-2">
					<input id="code" type="text" class="input-app mr-4"  placeholder="Nhập mã bài đăng"  style="width: 200px" v-model="code">
					<input id="poster" type="text" class="input-app mr-4"  placeholder="Người đăng hoặc SĐT"  style="width: 200px" v-model="poster">
					<select name="manufacture" v-model="id_manufacture" id="manufacture"@change="getModelManufacture()" class="input-app mr-4" style="width: 200px; height: 33px">
						
					<select name="model" id="model" class="input-app mr-4" v-model="model" style="width: 200px; height: 33px">
						<option value=""> Dòng xe </option>
						
					</select>
					<select name="status" id="status" class="input-app mr-4" v-model="status" style="width: 200px; height: 33px">
						<option value="">Chọn trạng thái</option>
						<option value="{{ \App\Enums\EVehicleStatus::SOLD }}">Đã bán</option>
						<option value="{{ \App\Enums\EVehicleStatus::SELLING }}">Chưa bán</option>
					</select> 
				</div>
				<div class="row">
					<div class="col-md-6 mt-3 ml-auto">
						<button class="button-app ml-5 float-right" @click="searchVehicle()">Tìm kiếm</button>
					</div>
					<div class="col-md-6 mt-3 ml-auto">
						<a :href="'manage/exportList?poster='+result_infoExport.poster+'&vehicle_manufacture_id='+result_infoExport.id_manufacture_selling+'&selling_status='+result_infoExport.selling_status+'&model='+result_infoExport.model" class="btn btn-primary button-app mb-4" >Xuất File Excel</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 mb-4" style="text-align: right;">
						<button class="button-app mt-3 ml-auto" style="border: 1px solid transparent;margin-right: 8%" @click="checkaccredited()">Kiểm định</button>
						<button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 8%" @click="approveSellingRequest()">Duyệt</button>
					</div>
				</div>
				<div id="table_1" class="position-relative">
					<table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
						<thead style="">
							<tr class="text-center blue-opacity">
								<th class="custom-view" width="2%">STT</th>
								<th class="custom-view">Mã bài đăng</th>
								<th class="custom-view">Hãng xe</th>
								<th class="custom-view">Dòng Xe</th>
								<th class="custom-view">Giá</th>
								<th class="custom-view">Người đăng</th>
								<th class="custom-view">Tiêu đề bài đăng</th>
								<th class="custom-view">Mô tả</th>
								<th class="custom-view">Kiểm định</th>
								<th class="custom-view">Trạng thái</th>
								<th class="custom-view">Tình trạng</th>
								<th class="custom-view">Ưu tiên</th>
								<th class="custom-view">Hình ảnh</th>
							</tr>
						</thead>
						<tbody v-cloak>
							<tr class="text-center" style="font-weight:bold" v-for="(item, index) in results_search.data" :key="item.selling_vehicle_id">
								
							</tr>
						</tbody>   
					</table>    
				</div>
				<div class="col-12">
					<pagination :data="results_search" @pagination-change-page="searchVehicle" :limit="4"></pagination> 
				</div>
				<div class="row" v-if="results_search.last_page > 1">
					<div class="col-md-10 mx-auto" style="text-align: right;">
						<button class="button-app ml-3 mr-4" style="border: 1px solid transparent;margin-right: 8%" @click="approveSellingRequest()">Duyệt</button>
						<button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 8%" @click="checkaccredited()">Kiểm duyệt</button>
					</div>
				</div>
			</div>
		</div>
		
		
		
	</div>
@endsection
@section('scripts')
	<script type="text/javascript">
		@php
			include public_path('/js/vehicle/manage-vehicle/manage-vehicle.js');
			include public_path('/js/vehicle/manage-vehicle/jquery.fancybox.min.js');
			include public_path('/js/vehicle/manage-vehicle/see-more-description.js');
		@endphp
	// $(document).ready(function() {
	// 	$('body').on('click', '.check_approve', function() {
	// 				$(this).prop('checked',true);
	// 		})
	// });
	</script>
@endsection