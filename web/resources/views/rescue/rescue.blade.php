@extends('layout.base')

@section('body-content')
    <div id="manage-rescue" class="mt-3 w-100">
        <div class="row mt-0 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('config-view-rescue', [], false)}}">CỨU HỘ</a>
                </h4>
            </div>
        </div>
        <div class="w-100">
            <input type="hidden" name="_token" :value="csrf">
            <div>
                <div class="row" style="min-height: 561px">
                    <div class="col-3 list_rescue p-0 collapse show"  id="collapseExample" style="border: 1px solid #6c757d;">
                        <ul class="nav mb-4 m-1">
                            <li class="nav-item p-1">
                                <a class="nav-link custom_li active_custom_li" id="new_rescue" href="#" style="width: 49px; padding: 0.5rem 0.7rem">Mới</a>
                            </li> 
                            <li class="nav-item p-1">
                                <a class="nav-link custom_li" id="handling_rescue" href="#" style="width: 100px;">Đang xử lý</a>
                            </li>
                            <li class="nav-item p-1">
                                <a class="nav-link custom_li" id="completed_rescue" href="#" style="width: 105px;">Hoàn thành</a>
                            </li>
                            <li class="nav-item p-1">
                                <a class="nav-link pr-2 custom_li" id="deleted_rescue" href="#" style="width: 70px; padding: 0.5rem 0.7rem">Đã xoá</a>
                            </li>
                        </ul>
                        {{-- Rescue Not proccess --}}
                        <div class="p-2 rescue-new-class">
                            <input type="text" name="search-rescue" onkeyup="filterNotProcessed()" id="search-rescue-not_processed" class="form-control mb-3" placeholder="Tên, Số điện thoại">
                            <div class="text-right">
                               Có <b id="number_not_process_yet"></b> TH cần cứu hộ
                            </div>
                            <div class="list-group" id="div_not_processed" style="max-height: 410px; overflow-y: auto;">
                                @foreach ($list_user_rescue as $key => $value)
                                    @if($value->rescure_status == \App\Enums\ERescueRequestStatus::NOT_PROCESSED  && $value->status == \App\Enums\EStatus::ACTIVE )
                                        <a onclick="clickRoute({{$value->latitude}}, {{$value->longitude}})" style="cursor: pointer;"
                                            class="list-group-item list-group-item-user-rescue list-group-item-action flex-column align-items-start info_user_rescue" 
                                            data-name="{{$value->name}}" data-phone="{{$value->phone}}" id="item-user-rescue{{$value->id}}" ref="myBtn">
                                            <div class="d-flex w-100">
                                                <img src="/images/user-image.jpg" alt="" style="width: 50px; height: 50px">
                                                <div class="mb-1 ml-3" @click="getInfo({{"'" . $value->name . "'"}}, {{"'" . $value->phone . "'"}}, {{"'" . $value->id . "'"}})" data-toggle="modal" data-target="#ModalRescue"> {{$value->name}} <br> {{$value->phone}} </div>
                                                <div class="ml-auto remove_rescue" style="font-size: 20px">
                                                    <small><button class="btn" style="background-color: #f8f9fa" @click="removeRescue({{"'" . $value->id . "'"}})">
                                                        <i class="fa fa-times text-danger" aria-hidden="true"></i> </button>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <small> {{$value->created_at}} </small>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        {{-- Assigned Staff --}}
                        <div class="p-2 rescue-handling-class d-none">
                            <input type="text" name="search-rescue" onkeyup="filterHandling()" id="search-rescue-handling" class="form-control mb-3" placeholder="Tên, Số điện thoại">
                            <div class="text-right">
                               Có <b id="number_handling"></b> TH cứu hộ đang xử lí
                            </div>
                            <div class="list-group" id="div_handling" style="max-height: 470px; overflow-y: auto;">
                                @foreach ($list_user_rescue as $key => $value)
                                    @if($value->rescure_status == \App\Enums\ERescueRequestStatus::ASSIGNED_STAFF && $value->status == \App\Enums\EStatus::ACTIVE )
                                        <a onclick="clickRoute({{$value->latitude}}, {{$value->longitude}})" ref="myBtn" style="cursor: pointer;"
                                                class="list-group-item list-group-item-user-rescue list-group-item-action flex-column align-items-start info_user_rescue" 
                                                data-name="{{$value->name}}" data-phone="{{$value->phone}}" id="item-user-rescue{{$value->id}}">
                                            <div class="d-flex w-100">
                                                <img src="/images/user-image.jpg" alt="" style="width: 50px; height: 50px">
                                                <div class="mb-1 ml-3" @click="getInfoAssignedStaff( {{"'" . $value->id . "'"}}, {{"'" . $value->name . "'"}}, {{"'" . $value->phone . "'"}},
                                                        {{"'" . $value->estimated_distance. "'"}}, {{"'" . $value->service_price. "'"}}, {{"'" . $value->admin_note. "'"}}, {{"'" . $value->name_staff. "'"}}, {{"'" . $value->phone_staff. "'"}})" data-toggle="modal" data-target="#ModalInfoRescueHandling" > {{$value->name}} <br> {{$value->phone}} </div>
                                                <div class="ml-auto remove_rescue" style="font-size: 20px">
                                                    <small><button class="btn bg-white" @click="removeRescue({{"'" . $value->id . "'"}})"><i class="fa fa-times text-danger" aria-hidden="true"></i> </button></small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <small> {{$value->created_at}}</small>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        {{-- Complete --}}
                        <div class="p-2 rescue-completed-class d-none">
                            <input type="text" name="search-rescue" onkeyup="filterCompleted()" id="search-rescue-completed" class="form-control mb-3" placeholder="Tên, Số điện thoại">
                            <div class="text-right">
                               Có <b id="number_completed"></b> TH cứu hộ đã hoàn thành
                            </div>
                            <div class="list-group" id="div_completed" style="max-height: 470px; overflow-y: auto;">
                                @foreach ($list_user_rescue as $key => $value)
                                    @if($value->rescure_status == \App\Enums\ERescueRequestStatus::RESCUE_COMPLETE)
                                        <a @click="getInfoCompleted( {{"'" . $value->id . "'"}}, {{"'" . $value->name . "'"}}, {{"'" . $value->phone . "'"}},
                                            {{"'" . $value->estimated_distance. "'"}}, {{"'" . $value->service_price. "'"}}, {{"'" . $value->admin_note. "'"}}, {{"'" . $value->name_staff. "'"}}, {{"'" . $value->phone_staff. "'"}} )" 
                                        onclick="clickRoute({{$value->latitude}}, {{$value->longitude}})" ref="myBtn" style="cursor: pointer;" id="item-user-rescue{{$value->id}}"
                                        class="list-group-item list-group-item-user-rescue list-group-item-action flex-column align-items-start info_user_rescue" data-toggle="modal" data-target="#ModalRescueCompleted" data-name="{{$value->name}}" data-phone="{{$value->phone}}">
                                            <div class="d-flex w-100">
                                                <img src="/images/user-image.jpg" alt="" style="width: 50px; height: 50px">
                                                <div class="mb-1 ml-3"> {{$value->name}} <br> {{$value->phone}} </div>
                                                <small><i class="ml-5"></i></small>
                                            </div>
                                            <div class="text-right">
                                                <small> {{$value->created_at}}</small>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        {{-- Deleted --}}
                        <div class="p-2 rescue-deleted-class d-none">
                            <input type="text" name="search-rescue" onkeyup="filterDeleted()" id="search-rescue-deleted" class="form-control mb-3" placeholder="Tên, Số điện thoại">
                            <div class="text-right">
                               Có <b id="number_deleted"></b> TH cứu hộ đã xoá
                            </div>
                            <div class="list-group" id="div_deleted" style="max-height: 470px; overflow-y: auto;">
                                @foreach ($list_user_rescue as $key => $value)
                                    @if($value->status == \App\Enums\EStatus::DELETED)
                                        <a @click="getInfoDeleted( {{"'" . $value->id . "'"}}, {{"'" . $value->name . "'"}}, {{"'" . $value->phone . "'"}},
                                            {{"'" . $value->estimated_distance. "'"}}, {{"'" . $value->service_price. "'"}}, {{"'" . $value->admin_note. "'"}}, {{"'" . $value->name_staff. "'"}}, {{"'" . $value->phone_staff. "'"}} )" 
                                        onclick="clickRoute({{$value->latitude}}, {{$value->longitude}})" ref="myBtn" style="cursor: pointer;" id="item-user-rescue{{$value->id}}"
                                        class="list-group-item list-group-item-user-rescue list-group-item-action flex-column align-items-start info_user_rescue" data-toggle="modal" data-target="#ModalRescueDeleted" data-name="{{$value->name}}" data-phone="{{$value->phone}}">
                                            <div class="d-flex w-100">
                                                <img src="/images/user-image.jpg" alt="" style="width: 50px; height: 50px">
                                                <div class="mb-1 ml-3"> {{$value->name}} <br> {{$value->phone}} </div>
                                                <small><i class="ml-5"></i></small>
                                            </div>
                                            <div class="text-right">
                                                <small> {{$value->created_at}} </small>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-9 p-0">
                        <div id="map" style="width: 100%; height: 100%; border: 1px solid"></div>
                    </div>               
                </div>
            </div>
            {{-- Modal Chọn cửa hàng. nhân viên --}}
            <div class="modal fade" id="ModalRescue" tabindex="-1" role="dialog" aria-labelledby="ModalRescue">
                <div class="modal-dialog" role="document mx-auto" style="width: 430px; margin-top: 6rem; margin-left: 33rem">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Danh sách cửa hàng </h5>
                            <button type="button" class="close pt-0 mt-0" data-dismiss="modal" style="color: red; line-height: 0" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex w-100 text-center">
                                <img src="/images/user-image.jpg" class="mx-auto" style="width: 50px; height: 50px">
                                <div class="ml-0 mr-auto" style="line-height: 2rem;"> <span id="name_user_rescue"> </span>  <br> <span id="phone_user_rescue"></span></div>
                            </div>
                            <div id="accordion" style="max-height: 350px; overflow-y: scroll">
                                @if(count($listBranch) > 0)
                                    @foreach ($listBranch as $key => $value)
                                        <div class="card">
                                            <div class="card-header" id="heading{{$key}}">
                                                <h5 class="mb-0"><span class="distance_fly_bird" id="distance_fly_bird{{$value->id}}"></span> <span>(km)</span>
                                                    <button class="btn btn-link btn-collapse-branch" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                                                        {{$value->name}} (
                                                            @php $nb_member = 0; 
                                                                foreach ($value->staffDetail as $staff) { 
                                                                    if(($value->id == $staff->id_branch_staff) && ($staff->status == \App\Enums\EStatus::ACTIVE) && $staff->staff_type_id == $idTechnicalGroup) { 
                                                                        $nb_member++;
                                                                    }
                                                                } 
                                                                echo $nb_member; 
                                                            @endphp
                                                        )                                          
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse{{$key}}" class="collapse" aria-labelledby="heading{{$key}}" data-parent="#accordion">
                                                <div class="card-body">
                                                    @if(count($value->staffDetail) > 0)
                                                        @foreach ($value->staffDetail as $key2 => $staff)
                                                            @if($value->id == $staff->id_branch_staff && $staff->status == \App\Enums\EStatus::ACTIVE && $staff->staff_type_id == $idTechnicalGroup)
                                                                <a class="list-group-item list-group-item-branch-transfer list-group-item-action" style="cursor: pointer;"
                                                                @click="getStaffBranch( {{"'" . $value->id . "'"}}, {{"'" . $staff->id_staff . "'"}}, {{"'" . trim($staff->name) . "'"}}, {{"'" . $staff->phone . "'"}} )" data-dismiss="modal"> {{$staff->name}} / {{$staff->phone}} </a>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            {{-- <button type="button" @click="getStaffBranch()" class="btn btn-primary" data-dismiss="modal"> Chọn </button> --}}
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal nhập khoảng cách, giá tiền thanh toán --}}
            <div class="modal fade" id="ModalInfoPayment" tabindex="-1" role="dialog" aria-labelledby="ModalInfoPayment">
                <div class="modal-dialog" role="document mx-auto" style="margin-top: 10rem; margin-left: 33rem">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Thông tin thanh toán </h5>
                            <button type="button" class="close pt-0 mt-0" data-dismiss="modal" @click="dimissModal()" style="color: red; line-height: 0" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-4 col-form-label"> Người cần cứu hộ: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_user_rescue">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-4 col-form-label"> Nhân viên cứu hộ </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="staff_rescue">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-4 col-form-label"> Khoảng cách: </label>
                                    <div class="col-sm-8">
                                        <input type="number" class="form-control" id="length_way" v-model="distance">
                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                            Ví dụ: 20 (Đơn vị: km)
                                        </small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-4 col-form-label"> Giá: </label>
                                    <div class="col-sm-8">
                                        <input type="number" class="form-control" id="price" v-model="price">
                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                            Ví dụ: 500000 (Đơn vị: đồng)
                                        </small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-4 col-form-label"> Ghi chú: </label>
                                    <div class="col-sm-8">
                                        <textarea name="note" id="note" class="form-control" v-model="note"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="saveAssignStaffRescue()" class="btn btn-primary" data-dismiss="modal"> Xác nhận </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Thông tin rescuer đang cứu hộ --}}
            <div class="modal fade" id="ModalInfoRescueHandling" tabindex="-1" role="dialog" aria-labelledby="ModalInfoRescueHandling">
                <div class="modal-dialog" role="document mx-auto" style="margin-top: 10rem; margin-left: 33rem">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Thông tin </h5>
                            <button type="button" class="close pt-0 mt-0" data-dismiss="modal" style="color: red; line-height: 0" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Người cần cứu hộ: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_user_rescue_handling">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Nhân viên cứu hộ </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_staff_rescue_handling">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Khoảng cách: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="distance_rescue_handling">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Giá: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="price_rescue_handling">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Ghi chú: </label>
                                    <div class="col-sm-8">
                                        <textarea name="note" readonly class="form-control-plaintext" id="note_handling"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="completedRescue()" id="id_handling" class="btn btn-primary" data-dismiss="modal"> Hoàn thành </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Thông tin rescue hoàn thành --}}
            <div class="modal fade" id="ModalRescueCompleted" tabindex="-1" role="dialog" aria-labelledby="ModalRescueCompleted">
                <div class="modal-dialog" role="document mx-auto" style="margin-top: 10rem; margin-left: 33rem">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Thông tin </h5>
                            <button type="button" class="close pt-0 mt-0" data-dismiss="modal" style="color: red; line-height: 0" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Người cần cứu hộ: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_user_rescue_completed">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Nhân viên cứu hộ </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_staff_rescue_completed">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Khoảng cách: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="distance_rescue_completed">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Giá: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="price_rescue_completed">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Ghi chú: </label>
                                    <div class="col-sm-8">
                                        <textarea readonly class="form-control-plaintext" id="note_completed"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"> Đóng </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Thông tin rescue huỷ bỏ --}}
            <div class="modal fade" id="ModalRescueDeleted" tabindex="-1" role="dialog" aria-labelledby="ModalRescueDeleted">
                <div class="modal-dialog" role="document mx-auto" style="margin-top: 10rem; margin-left: 33rem">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Thông tin </h5>
                            <button type="button" class="close pt-0 mt-0" data-dismiss="modal" style="color: red; line-height: 0" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Người cần cứu hộ: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_user_rescue_deleted">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Nhân viên cứu hộ </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="name_phone_staff_rescue_deleted">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Khoảng cách: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="distance_rescue_deleted">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Giá: </label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="price_rescue_deleted">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Ghi chú: </label>
                                    <div class="col-sm-8">
                                        <textarea readonly class="form-control-plaintext" id="note_deleted"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"> Đóng </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        @php
            include public_path('/js/rescue/manage-rescue/manage-rescue.js');
        @endphp
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAovLBug6QmhZ9R6S-3wzBKDg6smAZFiK4&callback=initMap"> --}}
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{config('app.maps_api_key')}}&callback=initMap"></script>
    <script type="text/javascript">
        var map_a;
        var infowindow_;
        var markers = [];
        var markers_store = [];
        var arr_lat_long_new_rescue = [];
            arr_lat_long_new_rescue.push(<?php 
                for ($i = 0; $i < count($list_user_rescue); $i++) {
                    if ($list_user_rescue[$i]->latitude != null && $list_user_rescue[$i]->latitude != "" && $list_user_rescue[$i]->longitude != null && $list_user_rescue[$i]->longitude != "" && $list_user_rescue[$i]->rescure_status == 0 && $list_user_rescue[$i]->status == 1) {
                        echo ("['" . $list_user_rescue[$i]->name . "',"  . (float)$list_user_rescue[$i]->latitude . "," . (float)$list_user_rescue[$i]->longitude . ",'" . 
                            $list_user_rescue[$i]->phone . "','"  . $list_user_rescue[$i]->id . "','"  . $list_user_rescue[$i]->status . "','" . $list_user_rescue[$i]->rescure_status . "','"  
                            . $list_user_rescue[$i]->name_staff  ."','"  . $list_user_rescue[$i]->phone_staff . "','"  . $list_user_rescue[$i]->estimated_distance . "','"  . $list_user_rescue[$i]->service_price . "','"  . $list_user_rescue[$i]->admin_note . "'],");   
                    }
                }
            ?>);
        var arr_lat_long_handling_rescue = [];
            arr_lat_long_handling_rescue.push(<?php 
                for ($i = 0; $i < count($list_user_rescue); $i++) {
                    if ($list_user_rescue[$i]->latitude != null && $list_user_rescue[$i]->latitude != "" && $list_user_rescue[$i]->longitude != null && $list_user_rescue[$i]->longitude != "" && $list_user_rescue[$i]->rescure_status == 1 && $list_user_rescue[$i]->status == 1) {
                        echo ("['" . $list_user_rescue[$i]->name . "',"  . (float)$list_user_rescue[$i]->latitude . "," . (float)$list_user_rescue[$i]->longitude . ",'" . 
                            $list_user_rescue[$i]->phone . "','"  . $list_user_rescue[$i]->id . "','"  . $list_user_rescue[$i]->status . "','" . $list_user_rescue[$i]->rescure_status . "','"  
                            . $list_user_rescue[$i]->name_staff  ."','"  . $list_user_rescue[$i]->phone_staff . "','"  . $list_user_rescue[$i]->estimated_distance . "','"  . $list_user_rescue[$i]->service_price . "','"  . $list_user_rescue[$i]->admin_note . "'],");   
                    }
                }
            ?>);

        var arr_lat_long_completed_rescue = [];
            arr_lat_long_completed_rescue.push(<?php 
                for ($i = 0; $i < count($list_user_rescue); $i++) {
                    if ($list_user_rescue[$i]->latitude != null && $list_user_rescue[$i]->latitude != "" && $list_user_rescue[$i]->longitude != null && $list_user_rescue[$i]->longitude != "" && $list_user_rescue[$i]->rescure_status == 2 && $list_user_rescue[$i]->status == 1) {
                        echo ("['" . $list_user_rescue[$i]->name . "',"  . (float)$list_user_rescue[$i]->latitude . "," . (float)$list_user_rescue[$i]->longitude . ",'" . 
                            $list_user_rescue[$i]->phone . "','"  . $list_user_rescue[$i]->id . "','"  . $list_user_rescue[$i]->status . "','" . $list_user_rescue[$i]->rescure_status . "','"  
                            . $list_user_rescue[$i]->name_staff  ."','"  . $list_user_rescue[$i]->phone_staff . "','"  . $list_user_rescue[$i]->estimated_distance . "','"  . $list_user_rescue[$i]->service_price . "','"  . $list_user_rescue[$i]->admin_note . "'],");   
                    }
                }
            ?>);

        var arr_lat_long_deleted_rescue = [];
            arr_lat_long_deleted_rescue.push(<?php 
                for ($i = 0; $i < count($list_user_rescue); $i++) {
                    if ($list_user_rescue[$i]->latitude != null && $list_user_rescue[$i]->latitude != "" && $list_user_rescue[$i]->longitude != null && $list_user_rescue[$i]->longitude != "" && $list_user_rescue[$i]->status == -1) {
                        echo ("['" . $list_user_rescue[$i]->name . "',"  . (float)$list_user_rescue[$i]->latitude . "," . (float)$list_user_rescue[$i]->longitude . ",'" . 
                            $list_user_rescue[$i]->phone . "','"  . $list_user_rescue[$i]->id . "','"  . $list_user_rescue[$i]->status . "','" . $list_user_rescue[$i]->rescure_status . "','"  
                            . $list_user_rescue[$i]->name_staff  ."','"  . $list_user_rescue[$i]->phone_staff . "','"  . $list_user_rescue[$i]->estimated_distance . "','"  . $list_user_rescue[$i]->service_price . "','"  . $list_user_rescue[$i]->admin_note . "'],");   
                    }
                }
            ?>);

        var arr_lat_long_distance = [];
            arr_lat_long_distance.push(<?php 
                for ($i = 0; $i < count($listBranch); $i++) {
                    //if ($listBranch[$i]->latitude != null && $listBranch[$i]->latitude != "" && $listBranch[$i]->longitude != null && $listBranch[$i]->longitude != "") {
                        echo ("['" . $listBranch[$i]->id . "',"  . (float)$listBranch[$i]->latitude . "," . (float)$listBranch[$i]->longitude . ",'" . $listBranch[$i]->name . "'],");   
                    //}
                }
            ?>);

        var shape = {
                coords: [1, 1, 1, 20, 18, 20, 18, 1],
                type: 'poly'
            };

        function CenterControl(controlDiv, map) {

            // Set CSS for the control border.
            var controlUI = document.createElement('div');
            controlUI.style.backgroundColor = '#fff';
            controlUI.style.border = '2px solid #fff';
            controlUI.style.borderRadius = '3px';
            controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
            controlUI.style.cursor = 'pointer';
            controlUI.style.marginBottom = '22px';
            controlUI.style.textAlign = 'center';
            controlUI.title = 'Click to show rescue list';
            controlDiv.appendChild(controlUI);

            // Set CSS for the control interior.
            var controlText = document.createElement('div');
            controlText.style.color = 'rgb(25,25,25)';
            controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
            controlText.style.fontSize = '26px';
            controlText.style.lineHeight = '38px';
            controlText.style.paddingLeft = '5px';
            controlText.style.paddingRight = '5px';
            controlText.innerHTML = '<a class="collapse-list-rescue" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> <i class="fas fa-angle-right"> </i> </a>';
            controlUI.appendChild(controlText);
            controlUI.addEventListener('click', function() {
                showListRescue();
            });       
        }

        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 11,
                center: new google.maps.LatLng(10.797, 106.718),
                //mapTypeId: google.maps.MapTypeId.ROADMAP
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_CENTER
                },
            });

            var infowindow = new google.maps.InfoWindow();
            map_a = map;
            infowindow_ = infowindow;
            var centerControlDiv = document.createElement('div');
            var centerControl = new CenterControl(centerControlDiv, map);

            centerControlDiv.index = 1;
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(centerControlDiv);
            var image1 = {
                url: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
                size: new google.maps.Size(20, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 32)
            }; // new rescue

            var image5 = {
                url: '/images/logo32x32.png',
                size: new google.maps.Size(30, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 32)
            };//icon logo

            for (var i = 0; i < arr_lat_long_distance.length; i++) {
                    var lat_long_distance = arr_lat_long_distance[i];
                    var marker_store = new google.maps.Marker({
                        position: new google.maps.LatLng (lat_long_distance[1], lat_long_distance[2]),
                        map: map,
                        icon: image5,
                        shape: shape,
                        title: lat_long_distance[3],
                        data_store: [lat_long_distance[0], lat_long_distance[1], lat_long_distance[2], lat_long_distance[3]],
                        //data_store = [id, lat, long,name]
                    });

                    var infowindow_store = new google.maps.InfoWindow();
                    google.maps.event.addListener(marker_store, 'click',(function (marker_store, i) {
                        return function () {
                            for (var j = 0; j < markers_store.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow_store.setContent(this.data_store[3]);
                                infowindow_store.open(map, marker_store);
                                markers_store[j].setIcon(image5);
                            }
                            marker_store.setIcon("http://maps.google.com/mapfiles/kml/pal2/icon39.png");
                        };
                    })(marker_store, i));
                markers_store.push(marker_store);
            }

            for (var i = 0; i < arr_lat_long_new_rescue.length; i++) {
                    var name_lat_long = arr_lat_long_new_rescue[i];
                    var image = image1;  
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng (name_lat_long[1], name_lat_long[2]),
                        map: map,
                        icon: image,
                        shape: shape,
                        title: name_lat_long[0],
                        data_k: [name_lat_long[0], name_lat_long[1], name_lat_long[2], name_lat_long[3], name_lat_long[4], name_lat_long[5], name_lat_long[6],
                                name_lat_long[7], name_lat_long[8], name_lat_long[9], name_lat_long[10], name_lat_long[11]],
                        //data_k = [name, lat, long, phone, id, status, rescue_status, name_staff, phone_staff, distance, price, note]
                    });
                    google.maps.event.addListener(marker, 'click',(function (marker, i) {
                        return function () {
                            for (var j = 0; j < markers.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow.setContent(this.data_k[0]);
                                infowindow.open(map, marker);
                                markers[j].setIcon(image1);
                                if (this.data_k[5] == 1 && this.data_k[6] == 0) {
                                    var lati1 = this.data_k[1];
                                    var long1 = this.data_k[2];  
                                    arr_lat_long_distance.forEach(element => {
                                        var lati2= element[1];
                                        var long2= element[2];
                                        var dist_ = getDistanceFromLatLonInKm(lati1, long1, lati2, long2).toFixed(2);
                                        $('#distance_fly_bird' + element[0]).text(dist_);
                                    });
                                    $('#accordion').html(
                                        $('#accordion').children('.card').sort(function (a, b) {
                                            var result = a.getElementsByClassName('distance_fly_bird')[0].innerText - b.getElementsByClassName('distance_fly_bird')[0].innerText;
                                                    return result;
                                        })
                                    );
                                            //set name phone user rescue
                                        $("#name_user_rescue").text(this.data_k[0]);
                                        $("#phone_user_rescue").text(this.data_k[3]);
                                        $("#name_phone_user_rescue").val(this.data_k[0] + ' / ' + this.data_k[3]);
                                        $(".list-group-item-user-rescue").removeClass("active");
                                        $("#item-user-rescue" + this.data_k[4]).addClass("active");
                                        // Active class narbar
                                        $("#new_rescue").addClass("active_custom_li");
                                        $("#handling_rescue").removeClass('active_custom_li');
                                        $("#completed_rescue").removeClass('active_custom_li');
                                        $("#deleted_rescue").removeClass('active_custom_li');
                                        $(".rescue-new-class").removeClass('d-none');
                                        $(".rescue-new-class").addClass('d-block');

                                        $(".rescue-handling-class").removeClass('d-block');
                                        $(".rescue-handling-class").addClass('d-none');
                                        $(".rescue-completed-class").removeClass('d-block');
                                        $(".rescue-completed-class").addClass('d-none');

                                        $(".rescue-deleted-class").removeClass('d-block');
                                        $(".rescue-deleted-class").addClass('d-none');
                                            //show modal
                                        $("#ModalRescue").modal('show');
                                }
                            }
                            marker.setIcon("http://www.google.com/mapfiles/marker.png");
                        };
                    })(marker, i));
                markers.push(marker);
            }

            $("#new_rescue").click(function() {
                deleteMarkers();
                for (var i = 0; i < arr_lat_long_distance.length; i++) {
                    var lat_long_distance = arr_lat_long_distance[i];
                    var marker_store = new google.maps.Marker({
                        position: new google.maps.LatLng (lat_long_distance[1], lat_long_distance[2]),
                        map: map,
                        icon: image5,
                        shape: shape,
                        title: lat_long_distance[3],
                        data_store: [lat_long_distance[0], lat_long_distance[1], lat_long_distance[2], lat_long_distance[3]],
                        //data_store = [id, lat, long, name]
                    });
                    google.maps.event.addListener(marker_store, 'click',(function (marker_store, i) {
                        return function () {
                            for (var j = 0; j < markers_store.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow_store.setContent(this.data_store[3]);
                                infowindow_store.open(map, marker_store);
                                markers_store[j].setIcon(image5);
                            }
                            marker_store.setIcon("http://maps.google.com/mapfiles/kml/pal2/icon39.png");
                        };
                    })(marker_store, i));
                    markers_store.push(marker_store);
                }
                var latLng = new google.maps.LatLng(10.797, 106.718);
                map.setZoom(11);
                map.panTo(latLng);
                for (var i = 0; i < arr_lat_long_new_rescue.length; i++) {
                    var name_lat_long = arr_lat_long_new_rescue[i];
                    var image = image1;
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng (name_lat_long[1], name_lat_long[2]),
                        map: map,
                        icon: image,
                        shape: shape,
                        title: name_lat_long[0],
                        data_k: [name_lat_long[0], name_lat_long[1], name_lat_long[2], name_lat_long[3], name_lat_long[4], name_lat_long[5], name_lat_long[6],
                                name_lat_long[7], name_lat_long[8], name_lat_long[9], name_lat_long[10], name_lat_long[11]],
                        //data_k = [name, lat, long, phone, id, status, rescue_status, name_staff, phone_staff, distance, price, note]
                    });
                    google.maps.event.addListener(marker, 'click',(function (marker, i) {
                        return function () {
                            for (var j = 0; j < markers.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow.setContent(this.data_k[0]);
                                infowindow.open(map, marker);
                                markers[j].setIcon(image1);
                                if (this.data_k[5] == 1 && this.data_k[6] == 0) {
                                    var lati1 = this.data_k[1];
                                    var long1 = this.data_k[2];

                                    arr_lat_long_distance.forEach(element => {
                                        var lati2= element[1];
                                        var long2= element[2];
                                        var dist_ = getDistanceFromLatLonInKm(lati1, long1, lati2, long2).toFixed(2);
                                        $('#distance_fly_bird' + element[0]).text(dist_);
                                    });
                                    $('#accordion').html(
                                        $('#accordion').children('.card').sort(function (a, b) {
                                            var result = a.getElementsByClassName('distance_fly_bird')[0].innerText - b.getElementsByClassName('distance_fly_bird')[0].innerText;
                                                    return result;
                                        })
                                    );
                                            //set name phone user rescue
                                        $("#name_user_rescue").text(this.data_k[0]);
                                        $("#phone_user_rescue").text(this.data_k[3]);
                                        $("#name_phone_user_rescue").val(this.data_k[0] + ' / ' + this.data_k[3]);
                                        $(".list-group-item-user-rescue").removeClass("active");
                                        $("#item-user-rescue" + this.data_k[4]).addClass("active");
                                            // Active class narbar
                                        $("#new_rescue").addClass("active_custom_li");
                                        $("#handling_rescue").removeClass('active_custom_li');
                                        $("#completed_rescue").removeClass('active_custom_li');
                                        $("#deleted_rescue").removeClass('active_custom_li');
                                        $(".rescue-new-class").removeClass('d-none');
                                        $(".rescue-new-class").addClass('d-block');

                                        $(".rescue-handling-class").removeClass('d-block');
                                        $(".rescue-handling-class").addClass('d-none');

                                        $(".rescue-completed-class").removeClass('d-block');
                                        $(".rescue-completed-class").addClass('d-none');

                                        $(".rescue-deleted-class").removeClass('d-block');
                                        $(".rescue-deleted-class").addClass('d-none');
                                            //show modal
                                        $("#ModalRescue").modal('show');
                                }
                            }
                            marker.setIcon("http://www.google.com/mapfiles/marker.png");
                        };
                    })(marker, i));
                    markers.push(marker);
                }
            });
            
            $("#handling_rescue").click(function() {
                deleteMarkers();
                for (var i = 0; i < arr_lat_long_distance.length; i++) {
                    var lat_long_distance = arr_lat_long_distance[i];
                    var marker_store = new google.maps.Marker({
                        position: new google.maps.LatLng (lat_long_distance[1], lat_long_distance[2]),
                        map: map,
                        icon: image5,
                        shape: shape,
                        title: lat_long_distance[3],
                        data_store: [lat_long_distance[0], lat_long_distance[1], lat_long_distance[2], lat_long_distance[3]],
                        //data_store = [id, lat, long, name]
                    });
                    google.maps.event.addListener(marker_store, 'click',(function (marker_store, i) {

                        return function () {
                            for (var j = 0; j < markers_store.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow_store.setContent(this.data_store[3]);
                                infowindow_store.open(map, marker_store);
                                markers_store[j].setIcon(image5);
                            }
                            marker_store.setIcon("http://maps.google.com/mapfiles/kml/pal2/icon39.png");
                        };
                    })(marker_store, i));
                    markers_store.push(marker_store);
                }
                var latLng = new google.maps.LatLng(10.797, 106.718);
                map.setZoom(11);
                map.panTo(latLng);
                for (var i = 0; i < arr_lat_long_handling_rescue.length; i++) {
                    var name_lat_long = arr_lat_long_handling_rescue[i];
                    var image = image1;
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng (name_lat_long[1], name_lat_long[2]),
                        map: map,
                        icon: image,
                        shape: shape,
                        title: name_lat_long[0],
                        data_k: [name_lat_long[0], name_lat_long[1], name_lat_long[2], name_lat_long[3], name_lat_long[4], name_lat_long[5], name_lat_long[6],
                                name_lat_long[7], name_lat_long[8], name_lat_long[9], name_lat_long[10], name_lat_long[11]],
                        //data_k = [name, lat, long, phone, id, status, rescue_status, name_staff, phone_staff, distance, price, note]
                    });
                    google.maps.event.addListener(marker, 'click',(function (marker, i) {

                        return function () {
                            for (var j = 0; j < markers.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow.setContent(this.data_k[0]);
                                infowindow.open(map, marker);
                                markers[j].setIcon(image1);
                                if (this.data_k[5] == 1 && this.data_k[6] == 1) {
                            //set value
                                    $("#name_phone_user_rescue_handling").val(this.data_k[0] + ' / ' +this.data_k[3]);
                                    $("#name_phone_staff_rescue_handling").val(this.data_k[7] + ' / ' +this.data_k[8]);
                                    $("#distance_rescue_handling").val(this.data_k[9]);
                                    $("#price_rescue_handling").val(this.data_k[10]);
                                    $("#note_handling").val(this.data_k[11]);
                                    $("#id_handling").data('id_handling', this.data_k[4]);
                                    $(".list-group-item-user-rescue").removeClass("active");
                                    $("#item-user-forwarder" + this.data_k[4]).addClass("active");
                                    
                                    //active class narbar
                                    $("#handling_rescue").addClass("active_custom_li");
                                    $("#new_rescue").removeClass('active_custom_li');
                                    $("#completed_rescue").removeClass('active_custom_li');
                                    $("#deleted_rescue").removeClass('active_custom_li');

                                    $(".rescue-handling-class").removeClass('d-none');
                                    $(".rescue-handling-class").addClass('d-block');

                                    $(".rescue-new-class").removeClass('d-block');
                                    $(".rescue-new-class").addClass('d-none');

                                    $(".rescue-completed-class").removeClass('d-block');
                                    $(".rescue-completed-class").addClass('d-none');

                                    $(".rescue-deleted-class").removeClass('d-block');
                                    $(".rescue-deleted-class").addClass('d-none');
                                    //show modal
                                    $("#ModalInfoRescueHandling").modal('show');
                                }
                            }
                            marker.setIcon("http://www.google.com/mapfiles/marker.png");
                        };
                    })(marker, i));
                    markers.push(marker);
                }
            });
            
            $("#completed_rescue").click(function() {
                deleteMarkers();
                for (var i = 0; i < arr_lat_long_distance.length; i++) {
                    var lat_long_distance = arr_lat_long_distance[i];
                    var marker_store = new google.maps.Marker({
                        position: new google.maps.LatLng (lat_long_distance[1], lat_long_distance[2]),
                        map: map,
                        icon: image5,
                        shape: shape,
                        title: lat_long_distance[3],
                        data_store: [lat_long_distance[0], lat_long_distance[1], lat_long_distance[2], lat_long_distance[3]],
                        //data_store = [id, lat, long, name]
                    });
                    google.maps.event.addListener(marker_store, 'click',(function (marker_store, i) {

                        return function () {
                            for (var j = 0; j < markers_store.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow_store.setContent(this.data_store[3]);
                                infowindow_store.open(map, marker_store);
                                markers_store[j].setIcon(image5);
                            }
                            marker_store.setIcon("http://maps.google.com/mapfiles/kml/pal2/icon39.png");
                        };
                    })(marker_store, i));
                    markers_store.push(marker_store);
                }
                var latLng = new google.maps.LatLng(10.797, 106.718);
                map.setZoom(11);
                map.panTo(latLng);
                for (var i = 0; i < arr_lat_long_completed_rescue.length; i++) {
                    var name_lat_long = arr_lat_long_completed_rescue[i];
                    var image = image1;
            
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng (name_lat_long[1], name_lat_long[2]),
                        map: map,
                        icon: image,
                        shape: shape,
                        title: name_lat_long[0],
                        data_k: [name_lat_long[0], name_lat_long[1], name_lat_long[2], name_lat_long[3], name_lat_long[4], name_lat_long[5], name_lat_long[6],
                                name_lat_long[7], name_lat_long[8], name_lat_long[9], name_lat_long[10], name_lat_long[11]],
                        //data_k = [name, lat, long, phone, id, status, rescue_status, name_staff, phone_staff, distance, price, note]
                    });
                    google.maps.event.addListener(marker, 'click',(function (marker, i) {
                        return function () {
                            for (var j = 0; j < markers.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow.setContent(this.data_k[0]);
                                infowindow.open(map, marker);
                                markers[j].setIcon(image1);
                                if (this.data_k[5] == 1 && this.data_k[6] == 2) {
                             //set value
                                    $("#name_phone_user_rescue_completed").val(this.data_k[0] + ' / ' +this.data_k[3]);
                                    $("#name_phone_staff_rescue_completed").val(this.data_k[7] + ' / ' +this.data_k[8]);
                                    $("#distance_rescue_completed").val(this.data_k[9]);
                                    $("#price_rescue_completed").val(this.data_k[10]);
                                    $("#note_completed").val(this.data_k[11]);
                                    $(".list-group-item-user-rescue").removeClass("active");
                                    $("#item-user-forwarder" + this.data_k[4]).addClass("active");

                                    // active Class narbar
                                    $("#completed_rescue").addClass("active_custom_li");
                                    $("#new_rescue").removeClass('active_custom_li');
                                    $("#handling_rescue").removeClass('active_custom_li');
                                    $("#deleted_rescue").removeClass('active_custom_li');

                                    $(".rescue-completed-class").removeClass('d-none');
                                    $(".rescue-completed-class").addClass('d-block');

                                    $(".rescue-handling-class").removeClass('d-block');
                                    $(".rescue-handling-class").addClass('d-none');

                                    $(".rescue-new-class").removeClass('d-block');
                                    $(".rescue-new-class").addClass('d-none');

                                    $(".rescue-deleted-class").removeClass('d-block');
                                    $(".rescue-deleted-class").addClass('d-none');

                                    //show modal
                                    $("#ModalRescueCompleted").modal('show');
                                }
                            }
                            marker.setIcon("http://www.google.com/mapfiles/marker.png");
                        };
                    })(marker, i));
                    markers.push(marker);
                }
            });

            $("#deleted_rescue").click(function() {
                deleteMarkers();
                for (var i = 0; i < arr_lat_long_distance.length; i++) {
                    var lat_long_distance = arr_lat_long_distance[i];
                    var marker_store = new google.maps.Marker({
                        position: new google.maps.LatLng (lat_long_distance[1], lat_long_distance[2]),
                        map: map,
                        icon: image5,
                        shape: shape,
                        title: lat_long_distance[3],
                        data_store: [lat_long_distance[0], lat_long_distance[1], lat_long_distance[2], lat_long_distance[3]],
                        //data_store = [id, lat, long, name]
                    });
                    google.maps.event.addListener(marker_store, 'click',(function (marker_store, i) {

                        return function () {
                            for (var j = 0; j < markers_store.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow_store.setContent(this.data_store[3]);
                                infowindow_store.open(map, marker_store);
                                markers_store[j].setIcon(image5);
                            }
                            marker_store.setIcon("http://maps.google.com/mapfiles/kml/pal2/icon39.png");
                        };
                    })(marker_store, i));
                    markers_store.push(marker_store);
                }
                var latLng = new google.maps.LatLng(10.797, 106.718);
                map.setZoom(11);
                map.panTo(latLng);
                for (var i = 0; i < arr_lat_long_deleted_rescue.length; i++) {
                    var name_lat_long = arr_lat_long_deleted_rescue[i];
                    var image = image1;
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(name_lat_long[1], name_lat_long[2]),
                        map: map,
                        icon: image,
                        shape: shape,
                        title: name_lat_long[0],
                        data_k: [name_lat_long[0], name_lat_long[1], name_lat_long[2], name_lat_long[3], name_lat_long[4], name_lat_long[5], name_lat_long[6],
                                name_lat_long[7], name_lat_long[8], name_lat_long[9], name_lat_long[10], name_lat_long[11]],
                        //data_k = [name, lat, long, phone, id, status, rescue_status, name_staff, phone_staff, distance, price, note]
                    });
                    google.maps.event.addListener(marker, 'click',(function (marker, i) {
                        return function () {
                            for (var j = 0; j < markers.length; j++) {
                                map.setZoom(19);
                                map.panTo(this.getPosition());
                                infowindow.setContent(this.data_k[0]);
                                infowindow.open(map, marker);
                                markers[j].setIcon(image1);
                                if (this.data_k[5] == -1) {
                             //set value
                                    $("#name_phone_user_rescue_deleted").val(this.data_k[0] + ' / ' +this.data_k[3]);
                                    $("#name_phone_staff_rescue_deleted").val(this.data_k[7] + ' / ' +this.data_k[8]);
                                    $("#distance_rescue_deleted").val(this.data_k[9]);
                                    $("#price_rescue_deleted").val(this.data_k[10]);
                                    $("#note_deleted").val(this.data_k[11]);
                                    $(".list-group-item-user-rescue").removeClass("active");
                                    $("#item-user-forwarder" + this.data_k[4]).addClass("active");

                                    //active class
                                    $("#deleted_rescue").addClass("active_custom_li");
                                    $("#new_rescue").removeClass('active_custom_li');
                                    $("#handling_rescue").removeClass('active_custom_li');
                                    $("#completed_rescue").removeClass('active_custom_li');

                                    $(".rescue-deleted-class").removeClass('d-none');
                                    $(".rescue-deleted-class").addClass('d-block');

                                    $(".rescue-handling-class").removeClass('d-block');
                                    $(".rescue-handling-class").addClass('d-none');

                                    $(".rescue-new-class").removeClass('d-block');
                                    $(".rescue-new-class").addClass('d-none');

                                    $(".rescue-completed-class").removeClass('d-block');
                                    $(".rescue-completed-class").addClass('d-none');
                                    // show modal
                                    $("#ModalRescueDeleted").modal('show');
                                }
                            }
                            marker.setIcon("http://www.google.com/mapfiles/marker.png");
                        };
                    })(marker, i));
                    markers.push(marker);
                }
            });
        }

        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
            for (var i = 0; i < markers_store.length; i++) {
                markers_store[i].setMap(map);
            }
        }

        function clearMarkers() {
            setMapOnAll(null);
        }

      // Deletes all markers in the array by removing references to them.
        function deleteMarkers() {
            clearMarkers();
            markers = [];
            markers_store = [];
        }

        function clickRoute(lati1, long1) {
            infowindow_.close();
            var dot_lati1 = (lati1 + "").indexOf('.');
            var dot_long1 = (long1 + "").indexOf('.');
            var latLng = new google.maps.LatLng(lati1, long1);
            map_a.setZoom(19);
            map_a.panTo(latLng);
            for (var i = 0; i < markers.length; i++) {
                markers[i].setIcon("https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png");
                var dot_lat_marker = (markers[i].data_k[1] + "").indexOf('.');
                var dot_long_marker = (markers[i].data_k[2] + "").indexOf('.');
                if ((markers[i].data_k[1]+"").slice(0, dot_lat_marker) == (lati1 + "").slice(0, dot_lati1) && (markers[i].data_k[1]+"").substr(dot_lat_marker+1,9) == (lati1 + "").substr(dot_lati1+1,9) && (markers[i].data_k[2]+"").slice(0, dot_long_marker) == (long1 + "").slice(0, dot_long1) && (markers[i].data_k[2]+"").substr(dot_long_marker+1, 9) == (long1 + "").substr(dot_long1+1, 9)) {
                    markers[i].setIcon("http://www.google.com/mapfiles/marker.png");
                    infowindow_.setContent(markers[i].data_k[0]);
                    infowindow_.open(map, markers[i]);
                }               
            }
            arr_lat_long_distance.forEach(element => {
                var lati2= element[1];
                var long2= element[2];
                var dist_ = getDistanceFromLatLonInKm(lati1, long1, lati2, long2).toFixed(2);
                $('#distance_fly_bird'+element[0]).text(dist_);
            });
            $('#accordion').html(
                $('#accordion').children('.card').sort(function (a, b) {
                    var result = a.getElementsByClassName('distance_fly_bird')[0].innerText - b.getElementsByClassName('distance_fly_bird')[0].innerText;
                    return result;
                })
            );
            
        }

        function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
            var R = 6371; // Radius of the earth in km
            var dLat = deg2rad(lat2-lat1);  // deg2rad below
            var dLon = deg2rad(lon2-lon1); 
            var a =  Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                     Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180)
        }

        function showListRescue() {
            var bol = $("#collapseExample").hasClass("show");
            if (bol) {
                $('.list_rescue').removeClass('d-block');
                $('.list_rescue').addClass('d-none');
                $('#map').css("width", "133%");
                $('#map').css("height", "561px");
            } else {
                $('.list_rescue').removeClass('d-none');
                $('.list_rescue').addClass('d-block');
                $('#map').css("width", "100%");
                $('#map').css("height", "100%");
            }
        }

        $(document).ready(function() { 
            var number_not_process_yet = $(".rescue-new-class .list-group a").length;            
            $('#number_not_process_yet').text(number_not_process_yet);
            var number_handling = $(".rescue-handling-class .list-group a").length;            
            $('#number_handling').text(number_handling);
            var number_completed = $(".rescue-completed-class .list-group a").length;            
            $('#number_completed').text(number_completed);
            var number_deleted = $(".rescue-deleted-class .list-group a").length;            
            $('#number_deleted').text(number_deleted);
        });

        $(".list-group-item-user-rescue").on("click", function() {
            $(".list-group-item-user-rescue").removeClass("active");
            $(this).addClass("active");
        });

        $(".list-group-item-branch-rescue").on("click", function() {
            //$(".list-group-item-branch-rescue").removeClass("active");
            $(this).addClass("active");
        });

        $(".list-group-item-staff-rescue").on("click", function() {
            $(".list-group-item-staff-rescue").removeClass("active");
            $(this).addClass("active");
        });

        $("#ModalRescue").draggable(); 
        // navbar
        $("#new_rescue").click(function() { 
            $("#new_rescue").addClass('active_custom_li');
            $("#handling_rescue").removeClass('active_custom_li');
            $("#completed_rescue").removeClass('active_custom_li');
            $("#deleted_rescue").removeClass('active_custom_li');

            $(".rescue-new-class").removeClass('d-none');
            $(".rescue-new-class").addClass('d-block');

            $(".rescue-handling-class").removeClass('d-block');
            $(".rescue-handling-class").addClass('d-none');

            $(".rescue-completed-class").removeClass('d-block');
            $(".rescue-completed-class").addClass('d-none');

            $(".rescue-deleted-class").removeClass('d-block');
            $(".rescue-deleted-class").addClass('d-none');
        });

        $("#handling_rescue").click(function() { 
            $("#handling_rescue").addClass('active_custom_li');
            $("#new_rescue").removeClass('active_custom_li');
            $("#completed_rescue").removeClass('active_custom_li');
            $("#deleted_rescue").removeClass('active_custom_li');

            $(".rescue-handling-class").removeClass('d-none');
            $(".rescue-handling-class").addClass('d-block');

            $(".rescue-new-class").removeClass('d-block');
            $(".rescue-new-class").addClass('d-none');

            $(".rescue-completed-class").removeClass('d-block');
            $(".rescue-completed-class").addClass('d-none');

            $(".rescue-deleted-class").removeClass('d-block');
            $(".rescue-deleted-class").addClass('d-none');
        });
        $("#completed_rescue").click(function() { 
            $("#completed_rescue").addClass('active_custom_li');
            $("#handling_rescue").removeClass('active_custom_li');
            $("#new_rescue").removeClass('active_custom_li');
            $("#deleted_rescue").removeClass('active_custom_li');

            $(".rescue-completed-class").removeClass('d-none');
            $(".rescue-completed-class").addClass('d-block');

            $(".rescue-handling-class").removeClass('d-block');
            $(".rescue-handling-class").addClass('d-none');

            $(".rescue-new-class").removeClass('d-block');
            $(".rescue-new-class").addClass('d-none');

            $(".rescue-deleted-class").removeClass('d-block');
            $(".rescue-deleted-class").addClass('d-none');
        });
        $("#deleted_rescue").click(function() { 
            $("#deleted_rescue").addClass('active_custom_li');
            $("#handling_rescue").removeClass('active_custom_li');
            $("#new_rescue").removeClass('active_custom_li');
            $("#completed_rescue").removeClass('active_custom_li');

            $(".rescue-deleted-class").removeClass('d-none');
            $(".rescue-deleted-class").addClass('d-block');

            $(".rescue-handling-class").removeClass('d-block');
            $(".rescue-handling-class").addClass('d-none');

            $(".rescue-new-class").removeClass('d-block');
            $(".rescue-new-class").addClass('d-none');

            $(".rescue-completed-class").removeClass('d-block');
            $(".rescue-completed-class").addClass('d-none');
        }); 
        function filterNotProcessed() {
            var input, filter, a, i;
            input = document.getElementById("search-rescue-not_processed");
            filter = input.value.toUpperCase();
            div = document.getElementById("div_not_processed");
            a = div.getElementsByTagName("a");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                } else {
                    a[i].style.display = "none";
                }
            }
        }
        function filterHandling() {
            var input, filter, a, i;
            input = document.getElementById("search-rescue-handling");
            filter = input.value.toUpperCase();
            div = document.getElementById("div_handling");
            a = div.getElementsByTagName("a");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                } else {
                    a[i].style.display = "none";
                }
            }
        }
        function filterCompleted() {
            var input, filter, a, i;
            input = document.getElementById("search-rescue-completed");
            filter = input.value.toUpperCase();
            div = document.getElementById("div_completed");
            a = div.getElementsByTagName("a");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                } else {
                    a[i].style.display = "none";
                }
            }
        }
        function filterDeleted() {
            var input, filter, a, i;
            input = document.getElementById("search-rescue-deleted");
            filter = input.value.toUpperCase();
            div = document.getElementById("div_deleted");
            a = div.getElementsByTagName("a");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                } else {
                    a[i].style.display = "none";
                }
            }
        }
    </script>
@endsection