@extends('layout.base')

@section('body-content')
    <div id="manage-appointment">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route("manage-appointment", [], false)}}"> LỊCH HẸN </a>
                </h4>
            </div>
        </div> 
        <div class="row">
            <input type="hidden" name="_token" :value="csrf">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <div class="pb-3">
                    <input type="text" class="input-app mr-4" id="user_name_phone" v-model="username_phone_number" name="user_name_phone" style="width: 220px" placeholder="Nhập Tên/ SĐT">

                    <select name="type_schedule" id="type_schedule" v-model="type_appointment" class="input-app mr-4" style="width: 190px; height: 33px; cursor: pointer;">
                        <option value="">Loại lịch hẹn</option>
                        <option value="{{ \App\Enums\EAppointmentType::REPAIR }}">Sửa chữa</option>
                        <option value="{{ \App\Enums\EAppointmentType::ACCREDITATION }}">Kiểm định</option>
                        <option value="{{ \App\Enums\EAppointmentType::MAINTENANCE }}">Bảo dưỡng</option>
                    </select>

                    <select name="branch" id="branch" v-model="branch" class="input-app mr-4" style="width: 220px; height: 33px; cursor: pointer;">
                        <option value="">Chi nhánh</option>
                        @if(count($listBranch) > 0)
                            @foreach ($listBranch as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        @endif
                    </select>

                    <label class="pr-2" for="from-date">Thời gian</label>
                    <input type="text" class="input-app mr-2" name="from-date" v-model="from_date" id="from-date" placeholder="From date" onfocus="(this.type = 'date')">
                        
                    <input type="text" class="input-app" name="to-date" id="to-date" v-model="to_date" placeholder="To date" onfocus="(this.type = 'date')">
                        
                    <button class="button-app mt-4" style="margin-left: 30rem" @click="searchAppointment()"> Tìm kiếm </button><br>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                        <a class="btn btn-primary button-app mb-2 float-right" href="{{ route('customer-view-appointment', [], false) }}">Tạo lịch hẹn</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                       <a :href="'excel-appointment?username_phone_number='+result_infoExport.username_phone_number+'&type_appointment='+result_infoExport.type_appointment+'&branch='+result_infoExport.branch+'&from_date='+result_infoExport.from_date+'&to_date='+result_infoExport.to_date" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
                <div id="table_1" style="position: relative;">
                    <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                        <thead style="">
                            <tr class="text-center blue-opacity">
                                <th class="custom-view" width="5%">STT</th>
                                <th class="custom-view">Tên/Số điện thoại</th>
                                <th class="custom-view">Loại lịch hẹn</th>
                                <th class="custom-view">Thời gian</th>
                                <th class="custom-view">Chi nhánh</th>
                                <th class="custom-view">Nhắc nhở</th>
                                <th class="custom-view w-25">Ghi chú</th>
                                <th class="custom-view">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="customerFull" v-cloak>
                            <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data">
                                <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                                <td class="custom-view text-left">@{{ item.name_user }} /<br> @{{ item.phone }}</td>
                                <td class="custom-view text-left">@{{ item.type }}</td>
                                <td class="custom-view">@{{ item.appointment_at }}</td>
                                <td class="custom-view text-left">@{{ item.name_branch }}</td>
                                <td class="custom-view" v-if="item.enable_reminder === true">
                                    <input :value="item.id" name="chk[]" type="checkbox" style="transform: scale(2);  pointer-events: none;" checked readonly>    
                                </td>
                                <td class="custom-view" v-else>
                                    <input :value="item.id" name="chk[]" type="checkbox" style="transform: scale(2);  pointer-events: none;" readonly>    
                                </td>
                                <td class="custom-view text-left">@{{ item.note }}</td>
                                <td class="custom-view">
                                    <span class="btn_edit fa fa-edit" @click="getInfoAppointment(item.name_user, item.phone, item.type_value, item.appointment_at, item.branch_id, item.enable_reminder, item.note, item.id)" 
                                    data-toggle="modal" data-target="#ModalUpdateAppointment" data-id="item.id" data-code="item.code" data-name="item.name_card" data-toggle="tooltip" data-placement="left" title="Sửa lịch hẹn"></span>
                                    <span class="btn_remove fa fa-trash" @click="deleteAppointment(item.id)" data-toggle="tooltip" data-placement="right" title="Xoá lịch hẹn"></span>
                                </td>
                            <tr>
                        </tbody>   
                    </table>
                </div>
                <div class="col-12">
                    <pagination :data="results" @pagination-change-page="searchAppointment" :limit="4"></pagination>
                </div>
                <div class="row" v-if="results.last_page > 1">
                    <div class="col-md-3 ml-auto">
                        <a :href="'excel-appointment?username_phone_number='+result_infoExport.username_phone_number+'&type_appointment='+result_infoExport.type_appointment+'&branch='+result_infoExport.branch+'&from_date='+result_infoExport.from_date+'&to_date='+result_infoExport.to_date" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
            </div>
             {{-- Modal update Appointment --}}
            <div class="modal fade" id="ModalUpdateAppointment" tabindex="-1" role="dialog" aria-labelledby="ModalUpdateAppointment" aria-hidden="true">
                <div class="modal-dialog" role="document" style="width: 500px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Chỉnh sửa lịch hẹn </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>                     
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="type_appointment_update" class="col-form-label col-sm-4"> Loại lịch hẹn </label>
                                <div class="col-sm-8">
                                    <select name="type_appointment_update" id="type_appointment_update" v-model="type_appointment_update" class="form-control" style="margin-right: 10px; background-color: #fff; width: 250px" required>
                                        <option value="">Chọn loại lịch hẹn</option>
                                        <option value="{{ \App\Enums\EAppointmentType::REPAIR }}" v-if="type_appointment_update == {{ \App\Enums\EAppointmentType::REPAIR }}" selected> Sửa xe </option>
                                        <option value="{{ \App\Enums\EAppointmentType::REPAIR }}" v-else> Sửa xe </option>
                                        <option value="{{ \App\Enums\EAppointmentType::ACCREDITATION }}" v-if="type_appointment_update == {{ \App\Enums\EAppointmentType::ACCREDITATION }}" selected> Kiểm định </option>
                                        <option value="{{ \App\Enums\EAppointmentType::ACCREDITATION }}" v-else> Kiểm định </option>
                                        <option value="{{ \App\Enums\EAppointmentType::MAINTENANCE }}" v-if="type_appointment_update == {{ \App\Enums\EAppointmentType::MAINTENANCE }}" selected> Bảo dưỡng </option>
                                        <option value="{{ \App\Enums\EAppointmentType::MAINTENANCE }}" v-else> Bảo dưỡng </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="info_user_update" class="col-sm-4"> Tên khách hàng </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="info_user_update">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label for="date_send_update" class="col-sm-4"> Lịch gửi </label>
                                <div class="col-sm-5 pr-0">
                                    <input type="date" class="form-control pr-0" id="date_send_update" v-model="date_send_update">
                                </div>
                                <div class="col-sm-3 pl-0">
                                    <input type="time" class="form-control pr-0" id="time_send_update" v-model="time_send_update">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="branch_update" class="col-sm-4"> Chi nhánh </label>
                                <div class="col-sm-8">
                                    <select name="branch_update" id="branch_update" v-model="branch_update" class="form-control" style="margin-right: 10px; background-color: #fff; width: 250px">
                                    <option value="">Chọn Chi Nhánh</option>
                                        @if(count($listBranch) > 0)
                                            @foreach ($listBranch as $value)
                                                <option value="{{$value->id}}" v-if="branch_update == {{$value->id}}">{{$value->name}}</option>
                                                <option value="{{$value->id}}" v-else>{{$value->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="note_update" class="col-sm-4"> Ghi chú </label>
                                <div class="col-sm-8">
                                    <textarea name="note_update" id="note_update" cols="40" rows="7" v-model="note_update"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="reminder_update" class="col-sm-4"> Nhắc nhở </label>
                                <div class="col-sm-8">
                                    <input name="reminder" id="reminder" v-model="reminder_update" v-if="reminder_update == true" type="checkbox" style="width: 27px; height: 27px" checked>
                                    <input name="reminder" id="reminder" v-model="reminder_update" v-else type="checkbox" style="width: 27px; height: 27px">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> Đóng </button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateAppointment()"> Sửa </button>
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
            include public_path('/js/customer/manage-appointment/manage-appointment.js');
        @endphp
    </script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="application/javascript">
        $(function() {
            var availableTags = [];
            availableTags.push(
                <?php 
                    for ($i = 0; $i < count($infoCustomer); $i++) {
                        echo ("'" . str_replace("'","", $infoCustomer[$i]->phone) . " - " . trim($infoCustomer[$i]->name) . "',");   
                    }
                ?>
            );
            $("#info_customer").autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(availableTags, request.term);
                    response(results.slice(0, 10));
                }
            });
        });     
    </script>
@endsection