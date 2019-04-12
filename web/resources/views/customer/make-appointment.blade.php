@extends('layout.base')

@section('body-content')
<div id="setting-make-appointment">
    <div class="row mt-5 pt-3 pb-3">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route("manage-appointment", [], false)}}"> LỊCH HẸN </a>
            </h4>
        </div>
    </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class=" form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 45rem;">
                    <form class="form-inline"> 
                        <input type="hidden" name="_token" :value="csrf">
                        <div class="text-center mx-auto mb-3"> <h2>TẠO LỊCH HẸN</h2> </div>
                        <div class="form-group w-100 mb-3">
                            <label for="type_appointment" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Loại lịch hẹn</label>
                            <select name="type_appointment" id="type_appointment" v-model="type_appointment" class="form-control" style="margin-right: 10px; background-color: #fff; width: 250px; cursor: pointer;" required>
                                <option value="">Chọn loại lịch hẹn</option>
                                <option value="{{ \App\Enums\EAppointmentType::REPAIR }}">Sửa chữa</option>
                                <option value="{{ \App\Enums\EAppointmentType::ACCREDITATION }}">Kiểm định</option>
                                <option value="{{ \App\Enums\EAppointmentType::MAINTENANCE }}">Bảo dưỡng</option>
                            </select>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="info_customer" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên khách hàng</label>
                            <input type="text" id="info_customer" class="form-control" style="margin-right: 10px; background-color: #fff; width: 250px" placeholder="Nhập tên khách hàng" required>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="date_send" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Thời gian</label>
                            <div class="col-md-8 p-0 input-group">
                                <input id="date_send" name="date_send" v-model="date_send" type="date" style="margin-right: 10px; background-color: #fff" value="<?php date_default_timezone_set('Asia/Ho_Chi_Minh'); echo date("Y-m-d");?>" class="form-control" required>
                                <input id="time_send" name="date_send" v-model="time_send" type="time" style="margin-left: 10px; background-color: #fff" value="<?php date_default_timezone_set('Asia/Ho_Chi_Minh'); echo date("H:i");?>" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="branch" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Chọn chi nhánh</label>
                            <select name="branch" id="branch" v-model="branch" class="form-control" style="margin-right: 10px; background-color: #fff; width: 250px; cursor: pointer;" required>
                                <option value="">Chọn Chi Nhánh</option>
                                @if(count($listBranch) > 0)
                                    @foreach ($listBranch as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="note" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Ghi chú</label>
                            <div class="col-md-8 p-0 input-group">
                                <textarea class="form-control" name="note" id="note" v-model="note" cols="10" rows="5" style="background-color: #fff;" required></textarea>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="reminder" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Nhắc nhở</label>
                            <div class="col-md-8 p-0 input-group">
                                <input name="reminder" id="reminder" v-model="reminder" type="checkbox" style="width: 27px; height: 27px">
                            </div>
                        </div>

                        <div class="row">
                            <button type="button" class="button-app button_save_appoitment" style="margin: 15px 0 10px 235px" @click="saveAppointment()">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        @php
            include public_path('/js/customer/make-appointment/make-appointment.js');
        @endphp
    </script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="application/javascript">
        $(function() {
            var availableTags = [];
            availableTags.push(<?php 
                for ($i = 0; $i < count($infoCustomer); $i++) {
                    echo ("'" . str_replace("'","", $infoCustomer[$i]->phone) . " - " . trim($infoCustomer[$i]->name) . "',");   
                }
            ?>);
            $("#info_customer").autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(availableTags, request.term);
                    response(results.slice(0, 10));
                }
            });
        });
	</script>
@endsection