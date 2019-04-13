@extends('layout.base')

@section('body-content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="manage-notification">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('notification-view', [], false)}}">QUẢN LÝ THÔNG BÁO</a>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 45rem;">
                    <form id="form_notification" class="form-inline" action="/notification/save-notification" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group w-100 mb-3" >
                            <label for="content-notification" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Nội dung thông báo</label>
                            <div class="col-md-8 p-0 input-group">
                                <textarea class="form-control bg-white" name="content_notification" id="content_notification" cols="10" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="type_notification" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Loại thông báo</label>
                            <div class="col-md-8 p-0 input-group">
                                <select name="type_notification" id="branch" class="form-control" style="cursor: pointer;">
                                    <option value="{{\App\Enums\ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE}}">Khuyến Mãi</option>
                                    <option value="{{\App\Enums\ENotificationScheduleType::NOTIFY_COMMON_TYPE}}"> Hệ Thống Sửa Xe 411</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="cmnd" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Áp dụng lịch gửi</label>
                            <div class="col-md-8 p-0 input-group">
                                <div class="col-md-8 p-0 mb-2">
                                    <div class="form-group">
                                        <input id="allcustomer" name="customer_type" type="radio" class="button_radio" value="{{ \App\Enums\ENotificationScheduleType::ALL_CUSTOMER }}" checked style="cursor: pointer;"> 
                                        <label for="allcustomer"> Toàn bộ khách hàng </label><br>
                                    </div>
                                </div>
                                <div class="col-md-8 p-0">
                                    <div class="form-group">
                                        <input id="customer-specifically" name="customer_type" type="radio" class="button_radio" value="{{ \App\Enums\ENotificationScheduleType::SPECIFICALLY_CUSTOMER }}" style="cursor: pointer;"> 
                                        <label for="customer-specifically"> Khách hàng cụ thể </label><br>
                                    </div>
                                </div>
                                <div class="col-md-10 p-0 d-none mt-2" id="form-customer-specifically">
                                    <div class="form-group mb-2">
                                        <input id="info_customer" name="info_customer" class="form-control bg-white" type="text" style="width: 250px;" placeholder="Nhập Tên hoặc SĐT">  
                                        <input type="button" id="add_customer" class="btn-app form-control ml-3 bg-primary text-white" value="Thêm" style="cursor: pointer;"><br>
                                    </div>
                                </div>
                                <div class="col-md-8 p-0">
                                    <div class="form-group">
                                        <table class="tb_customer_specifically">
                                            <tbody id="list-customer-specifically">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-8 p-0">
                                    <div class="form-group" id="form-group-customer">
                                        <input id="group-customer-specifically" name="customer_type" type="radio" class="button_radio" value="{{ \App\Enums\ENotificationScheduleType::GROUP_CUSTOMER }}" style="cursor: pointer;"> 
                                        <label for="group-customer-specifically">Đơn vị</label><br><br>
                                    </div>
                                </div>
                                <div class="col-md-12 p-0 d-none" id="form-customer">
                                    <div class="form-group ">
                                        <select name="group_customer" id="group-customer" @change="getPartner()" v-model="id_partner_field" class="form-control mr-2 bg-white" style="cursor: pointer; width: 199px;">
                                            <option value="">Chọn đơn vị</option>
                                            @if(count($listGroupCustomer) > 0)
                                                @foreach ($listGroupCustomer as $value)
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <select name="partner" id="partner_field" v-if="list_partner.length > 0" class="form-control ml-2 bg-white" style="width: 199px; cursor: pointer;">
                                            <option value="all" id="all">Tất cả</option>
                                            <option v-for="list_partner in list_partner" v-bind:value="list_partner.id">@{{list_partner.name}}</option>
                                        </select>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="phone" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Lịch gửi</label>
                            <div class="col-md-8 p-0 input-group">
                                <input id="date_send" name="date_send" type="date" value="<?php date_default_timezone_set('Asia/Ho_Chi_Minh'); echo date("Y-m-d");?>" class="form-control mr-2 bg-white" required>
                                <input id="time_send" name="time_send" type="time" value="<?php date_default_timezone_set('Asia/Ho_Chi_Minh'); echo date("H:i");?>" class="form-control ml-2 bg-white" required>
                            </div>
                        </div>
                        <input type="text" name="time_config" id="time_config" hidden>
                        <input type="number" name="number_of_customer" id="number_of_customer" hidden>
                        <div class="row">
                            <button v-on:click="submitForm" class="button-app" style="margin: 15px 0 10px 235px">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact w-100" style="min-height: 150px">    
                <div id="table_1" class="position-relative">
                    <table class="table table-bordered table-striped w-100" style="min-height: 150px">
                        <thead>
                            <tr class="text-center blue-opacity">
                                <th class="custom-view" width="5%">STT</th>
                                <th class="custom-view">Nội dung</th>
                                <th class="custom-view">Thời gian</th>
                                <th class="custom-view">Đối tượng</th>
                                <th class="custom-view">Loại thông báo</th>
                                <th class="custom-view">Trạng thái</th>
                                <th class="custom-view">Hành động</th>
                            </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data" data-id="item.id">
                                <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                                <td class="custom-view text-left"> @{{ item.content }}</td>
                                <td class="custom-view">@{{ item.schedule_at }}</td>
                                <td class="custom-view text-left" v-if="item.target_type == {{\App\Enums\ENotificationScheduleType::ALL_CUSTOMER}}"> Tất cả khách hàng </td>
                                <td class="custom-view text-left" v-else-if="item.target_type == {{\App\Enums\ENotificationScheduleType::SPECIFICALLY_CUSTOMER}}"> Khách hàng cụ thể </td>
                                <td class="custom-view text-left" v-else-if="item.target_type == {{\App\Enums\ENotificationScheduleType::GROUP_CUSTOMER}}">Nhóm khách hàng</td>
                                <td class="custom-view text-left" v-else="">Nhóm khách hàng</td>

                                <td class="custom-view text-left" v-if="item.type == {{\App\Enums\ENotificationScheduleType::NOTIFY_COMMERCIAL_TYPE}}"> Khuyến mãi </td>
                                <td class="custom-view text-left" v-else-if="item.type == {{\App\Enums\ENotificationScheduleType::NOTIFY_COMMON_TYPE}}"> Hệ thống Sửa xe 411 </td>
                                <td class="custom-view text-left" v-else> </td>

                                <td class="custom-view" v-if="item.status == {{\App\Enums\EStatus::ACTIVE}}">Kích hoạt</td>
                                <td class="custom-view" v-else>Ngừng</td>
                                <td class="custom-view">
                                    <span class="btn_remove fas fa-times remove_field" @click="deleteNotification(item.id)"></span>
                                </td>
                            <tr>
                        </tbody> 
                    </table>
                </div>
                <div class="col-12">
                    <pagination :data="results" @pagination-change-page="listNotification" :limit="4"></pagination>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        @php
            include public_path('/js/notification/manage-notification/manage-notification.js');
        @endphp
    </script>
    <script type="application/javascript">
        var numbercustomer = 0; 
        $('#number_of_customer').val(numbercustomer);
        var a = [];
        $('#add_customer').click(function() {
            var html = '';
            var namephoneemail = $('#info_customer').val().replace(" ", "");
            var number_phone_customer = namephoneemail.split('-');
            if (namephoneemail == '') {
                bootbox.alert('Vui lòng nhập khách hàng!');
                return false;
            }
            if (a == '' || a ==null) {
                a.push(number_phone_customer[0]);
            }
            else {
                for (var i = 0; i < a.length; i++) {
                    if (number_phone_customer[0] == a[i]) {
                        bootbox.alert('đa có');
                        return false;
                    }            
                }
            }
            html =   '<tr> '
                +       '<td> ' + namephoneemail + ' </td>'
                +       '<td> <input name="info_customer_post' + numbercustomer + '" value="' + number_phone_customer[0] + '" hidden> </td>'
                +       '<td> <span class=\'btn_remove fas fa-times remove_field\'> </span> </td>'
                +    '</tr>'
            $('#list-customer-specifically').append(html);
            numbercustomer++;
            $('#number_of_customer').val(numbercustomer);
            $('#info_customer').val('');
        });

        $(".button_radio").change(function() {
            if ($('#customer-specifically').is(':checked') == true) {
                $("#form-customer-specifically").removeClass('d-none');
                $("#form-customer-specifically").addClass('d-block');
                $("#form-customer").removeClass('d-block');
                $("#form-customer").addClass('d-none');
            }
        
            if ($('#group-customer-specifically').is(':checked') == true) {
                    $("#form-customer").removeClass('d-none');
                    $("#form-customer").addClass('d-block');
                    $(".tb_customer_specifically > tbody").empty();
                    $("#form-customer-specifically").removeClass('d-block');
                    $("#form-customer-specifically").addClass('d-none');  
                }
             if ($('#allcustomer').is(':checked') == true) 
                {
                    $("#info_customer").val("");
                    $(".tb_customer_specifically > tbody").empty();
                    $("#form-customer-specifically").removeClass('d-block');
                    $("#form-customer-specifically").addClass('d-none');
                    $("#form-customer").removeClass('d-block');
                    $("#form-customer").addClass('d-none');
                 }
        });
        $(function() {
            var availableTags = [];
            availableTags.push(<?php 
                for ($i = 0; $i < count($infoCustomer); $i++) {
                    echo ("'" . str_replace("'","", $infoCustomer[$i]->phone) . " - " . trim($infoCustomer[$i]->name) . "',");   
                }
            ?>);
            $( "#info_customer" ).autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(availableTags, request.term);
                    response(results.slice(0, 10));
                }
            });
        }); 
        
        $("table #list-customer-specifically").on('click', 'span.remove_field', function () {
            $(this).closest('tr').remove();
            return false;
        });
	</script>
@endsection