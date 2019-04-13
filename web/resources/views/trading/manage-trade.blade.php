@extends('layout.base')
@section('body-content')
	<div id="manage-trade"> 
		<div class="row mt-5 pt-3">
			<div style="padding-left: 2rem">
				<h4 class="tag-page-custom">
					<a class="tag-title-show" style="text-decoration: none;" href="{{route('manage-trade', [], false)}}">QUẢN LÝ GIAO DỊCH</a> 
				</h4>
			</div>
		</div>
		<div class="row">
			<div class="set-row background-contact w-100" style="min-height: 150px">
				<div class="pb-2" style="margin-bottom: 15px;">
					<input id="name" type="text" class="input-app mr-4"  placeholder="Tên - SĐT Khách Hàng"  style="width: 150px" v-model="customer_name_phone">
					<input id="phone" type="text" class="input-app mr-4"  placeholder="Biển số xe"  style="width: 150px" v-model="vehicle_number">
					<input id="employees" type="text" class="input-app mr-4" style="width: 150px" placeholder="Tên nhân viên" v-model="employees">
					<select class="input-app mr-2" v-model="branch_id">
						<option value="">Chọn cửa hàng</option>
						@if(count($listBranch) > 0)
							@foreach ($listBranch as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
							@endforeach
						@endif
					</select>
					<label for="from_date">Ngày tạo</label>
                    <input id="from_date" type="text" class="input-app mr-4"  placeholder="From date" onfocus="(this.type='date')" style="width: 150px" v-model="from_date">
                    <label for="from_date"></label>
                    <input id="to_date" type="text" class="input-app mr-4" placeholder="To date" onfocus="(this.type='date')" style="width: 150px" v-model="to_date">
				</div>
				<div class="row">
					<div class="col-md-12">
						<button class="button-app mt-4" style="margin-left: 30rem" @click="searchTrading()">Tìm kiếm</button>
					</div>
				</div>
				<div class="row">
						<a style="margin-left: 83.7%" :href="'manage/exportList?customer_name='+result_infoExport.trade_name+'&vehicle_number='+result_infoExport.vehicle_number+'&from_date='+result_infoExport.from_date+'&employees='+result_infoExport.employees+'&to_date='+result_infoExport.to_date+'&name_store='+result_infoExport.name_store" class="btn btn-primary button-app mb-4 float-right">Xuất File Excel</a>
				</div>
				{{-- Modal Detail Trade--}}
				<div class="modal fade" id="ModalDetailTrade" tabindex="-1" role="dialog" aria-labelledby="ModalDetailTrade" aria-hidden="true">
                <div class="modal-dialog" role="document" style="max-width: 800px; margin:1.75rem auto">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabelTrade">Chi tiết giao dịch</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>                     
                        <div class="modal-body">
                           	<table id="table_detail" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
						<thead style="">
							<tr class="text-center blue-opacity">
								<th class="custom-view" width="5%">STT</th>
								<th class="custom-view">Danh sách phụ tùng</th>
								<th class="custom-view">Giá gốc</th>
								<th class="custom-view">Chiết khấu</th>
								<th class="custom-view">Số lượng</th>
								<th class="custom-view">Tiền phải trả</th>
								<th class="custom-view">Mã vạch</th>
							</tr>
						</thead>
						<tbody v-cloak>
							<tr class="text-center" v-for="(item, index) in results_detail">
								<td class="custom-view">@{{index+1}}</td>
								<td class="custom-view">@{{item.service}}</td>
								<td class="custom-view">@{{item.price_total}}</td>
								<td class="custom-view">@{{item.discount}}%</td>
								<td class="custom-view">@{{item.quantity}}</td>
								<td class="custom-view">@{{item.price_after_discount}}</td>
								<td class="custom-view">@{{item.meta}}</td>
							</tr>
						</tbody>   
					</table> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"> Đóng </button>
                        </div>
                    </div>
                </div>
            </div>
				{{-- Modal Update Feedback--}}
				<div class="modal fade" id="ModalUpdateFeedback" tabindex="-1" role="dialog" aria-labelledby="ModalUpgradeUser" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Phản hồi</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <form id="form_feedback" action="/trade/feedback" class="form-inline" enctype="multipart/form-data"> 
                        		@csrf
                        			<input type="text" id="id_feedback" name="id_feedback" hidden="true">
                        			<textarea style="width: 400px; height: 200px;margin: 0 auto" id="content_feedback" name="content_feedback">
                        				
                        			</textarea>
                        			<div class="row" style="margin-top: 5%">
                        				<div class="col-md-6">
                        					<button type="button" style="margin-left: 10%" class="button-app" data-dismiss="modal">Bỏ qua</button>
                        				</div>
                                		<div class="col-md-6">
                        					<button v-on:click="submitForm" style="margin-left: 15%" class="button-app" data-dismiss="modal">Cập nhật</button>
                        				</div>
                        			</div>
                    			</form>
                            </div>
                        </div>
                    </div>
                </div>
				<div id="table_1" class="position-relative">
					<table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
						<thead style="">
							<tr class="text-center blue-opacity">
								<th class="custom-view" width="5%">STT</th>
								<th class="custom-view">Tên cửa hàng</th>
								<th class="custom-view">Tên khách hàng</th>
								<th class="custom-view">Nội dung</th>
								<th class="custom-view">Mã PT</th>
								<th class="custom-view">Biển số xe</th>
								<th class="custom-view">Tổng tiền</th>
								<th class="custom-view">Nhân viên</th>
								<th class="custom-view">Số Km</th>
								<th class="custom-view">Ngày tạo</th>
								<th class="custom-view" width="15%">Phản hồi</th>
							</tr>
						</thead>
						<tbody v-cloak>
							<tr class="text-center" style="font-weight:bold" v-for="(item, index) in results_search.data" :key="item.selling_vehicle_id">
								<td class="custom-view td-grey" :class="{'grey-blue' : index % 2 != 0}" style="font-weight: bold">@{{ (results_search.current_page - 1) * results_search.per_page + index + 1 }}</td>
								<td class="custom-view" style="width: 100px;">@{{item.branch_user}}</td>
								<td class="custom-view" style="width: 150px;">@{{item.user_name}} / @{{item.user_phone}}</td>
								<td class="custom-view" v-if="item.description == null || item.description == ''">Sửa chửa nhỏ</td>
								<td class="custom-view" v-else="">@{{item.description}}</td>
								<td class="custom-view"><a href="#" class="Feedback" style="text-decoration: none; cursor: pointer;" data-toggle="modal" data-target="#ModalSeeMoreCardMember"
                                    @click="seeMoreDetail(item.order_id);">@{{item.code}}</a></td>
								<td class="custom-view">@{{item.vehicle_number}}</td>
								<td class="custom-view">@{{item.price}} VND</td>
								<td class="custom-view">@{{item.staff}} </td>
								<td class="custom-view" v-if="item.kilometer != null && item.kilometer != ''">@{{item.kilometer}} km</td>
                                <td class="custom-view" v-else></td>
								<td class="custom-view">@{{item.created_at}} </td>
								<td class="custom-view" v-if="item.feedback != null && item.feedback != ''" style="text-align: left;width: 250px;">	
									<a href="#" v-if="item.feedback.length > 80" class="Feedback" style="cursor: pointer;text-decoration: none;" @click="getInfoFeedback(item.feedback,item.order_id)" >@{{item.feedback | feedbackSubstr}} ...</a>
									<span v-if="item.feedback.length > 80" style="cursor: pointer; color: blue;margin-left: 80%" class="see_more_less"@click="deleteFeedback(item.order_id)">Xóa</span>
									<span v-if="item.feedback.length < 80" class="d-block" >@{{item.feedback}}</span>
									<div class="option-feedback">
										<span v-if="item.feedback.length < 80" style="cursor: pointer; color: blue;" class="see_more_less"@click="getInfoFeedback(item.feedback,item.order_id)">Chỉnh sửa</span>
										<span v-if="item.feedback.length < 80" style="cursor: pointer; color: blue;margin-left: 35%" class="see_more_less"@click="deleteFeedback(item.order_id)">Xóa</span>
									</div>
								</td>
								<td class="custom-view" class="see_more" v-if="item.feedback == null || item.feedback == ''">
									<a style="cursor: pointer; color: blue;" @click="addFeedback(item.feedback,item.order_id)">Nhập phản hồi</a>
								</td>
								
							</tr>
						</tbody>   
					</table>    
				</div>
				<div class="col-12">
					<pagination :data="results_search" @pagination-change-page="searchTrading" :limit="4"></pagination> 
				</div>
				<div class="row" v-if="results_search.last_page > 1">
					<div class="col-md-12 mx-auto">
						<a style="margin-left: 83.7%" :href="'manage/exportList?customer_name='+result_infoExport.trade_name+'&vehicle_number='+result_infoExport.vehicle_number+'&from_date='+result_infoExport.from_date+'&employees='+result_infoExport.employees+'&to_date='+result_infoExport.to_date+'&name_store='+result_infoExport.name_store" class="btn btn-primary button-app mb-4 float-right">Xuất File Excel</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
    <script type="text/javascript">
        @php
            include public_path('/js/trading/manage-trading/manage-trading.js');
            include public_path('/js/trading/manage-trading/see-more-feedback.js');
        @endphp
    </script>
@endsection