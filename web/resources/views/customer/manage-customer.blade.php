
@extends('layout.base')

@section('body-content')
    <div id="manage-customer"> 
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('manage-customer', [], false)}}"> QUẢN LÝ KHÁCH HÀNG </a>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <div class="pb-2">
                    <input id="username_phone" type="text" class="input-app mr-4" style="width: 170px" placeholder="Enter Email/Phone" v-model="username_phone">
                    <select name="status" id="status" class="input-app mr-4" v-model="status" style="width: 140px; height: 33px; cursor: pointer;">
                        <option value=""> Trạng thái </option>
                        <option value="{{ \App\Enums\EStatus::ACTIVE }}"> Đã kích hoạt </option>
                        <option value="{{ \App\Enums\EStatus::DELETED }}"> Đã xoá </option>
                    </select>                
                    <label for="from_date">Ngày tạo</label>
                    <input id="from_date" type="text" class="input-app mr-4"  placeholder="From date" onfocus="(this.type='date')" style="width: 173px" v-model="from_date">
                    <input id="to_date" type="text" class="input-app mr-4" placeholder="To date" onfocus="(this.type='date')" style="width: 174px" v-model="to_date">
                    <select name="partner" id="partner" class="input-app mr-4" v-model="partner" style="width: 140px; height: 33px; cursor: pointer;" @change="getPartner()">
                        <option value="">Chọn đơn vị</option>
                        @if (count($listPartnerField) > 0)
                            @foreach($listPartnerField as $item)
                                <option value="{{$item->id}}">{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <select name="partnerfield" id="partnerfield" class="input-app mr-4" v-model="partner_field" style="width: 140px; height: 33px; cursor: pointer;">
                        <option value="" selected>Chọn đối tác</option>
                        <option v-for="(item, idex) in result_partner" :value=item.id>@{{item.name}}</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mt-3">
                        <button class="button-app ml-5 float-right" @click="searchCustomer()"> Tìm kiếm </button>
                    </div>
                    <div class="col-md-6 mt-3">
                        <a :href="'excel-customer?username='+result_infoExport.username+'&from_date='+result_infoExport.from_date+'&to_date='+result_infoExport.to_date+'&status='+result_infoExport.status+'&partner_field='+result_infoExport.partner_field" class="btn btn-primary button-app mb-4 float-left" >Xuất File Excel</a>
                    </div>
                </div>
                {{--<div class="row">
                    <div class="col-md-12 ml-auto mb-3" style="text-align: right;">
                         <button class="button-app ml-3 float-right" style="border: 1px solid transparent" @click="deleteCustomer()"> Xoá </button>
                    </div>
                </div>--}}
                <div id="table_1" class="position-relative">
                    <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                        <thead style="">
                            <tr class="text-center blue-opacity">
                                <th class="custom-view" width="5%"> STT </th>
                                <th class="custom-view" width="15%"> Tên khách hàng </th>
                                <th class="custom-view" width="10%"> Số điện thoại </th>
                                <th class="custom-view" width="20%"> Địa chỉ </th>
                                <th class="custom-view"> Ngày sinh </th>
                                <th class="custom-view"> Trạng thái </th>
                                <th class="custom-view"> Ngày tạo </th>
                                <th class="custom-view"> Đối tác </th>
                                <th class="custom-view" width="10%"> Hành động </th>
                            </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data">
                                <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                                <td class="custom-view text-left"> @{{ item.name }}</td>
                                <td class="custom-view text-left">@{{ item.phone }}</td>
                                <td class="custom-view text-left">@{{ item.address }}</td>
                                <td class="custom-view">@{{ item.date_of_birth }}</td>
                                <td class="custom-view">@{{ item.status }}</td>
                                <td class="custom-view">@{{ item.created_at }}</td>
                                <td class="custom-view">@{{ item.partnerName }}</td>
                                <td class="custom-view">
                                    <span class="btn_edit fa fa-edit"  data-toggle="tooltip" data-placement="left" title="Sửa" @click="getEditCustomer(item.id, item.name, item.phone, item.address, item.date_of_birth, item.partnerID)"></span>
                                    <span class="btn_save fas fa-level-up-alt" @click="getInfoUser(item.name, item.phone, item.email, item.avatar_path, item.id)" data-toggle="tooltip" data-placement="left" title="Nâng Cấp"></span>
                                    <span class="btn_remove fa fa-trash" @click="deleteCustomer(item.id)" data-toggle="tooltip" data-placement="right" title="Xóa khách hàng"></span>
                                </td>
                            <tr>
                        </tbody>   
                    </table>    
                </div>
                <div class="col-12">
                    <pagination :data="results" @pagination-change-page="searchCustomer" :limit="3"></pagination>
                </div>
                
                <div class="row" v-if="results.last_page > 1">
                    <div class="col-md-10 mx-auto" style="text-align: right;">
                        <a :href="'excel-customer?username='+result_infoExport.username+'&from_date='+result_infoExport.from_date+'&to_date='+result_infoExport.to_date+'&status='+result_infoExport.status" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
                 {{-- Modal Upgrade Customer--}}
                <div class="modal fade" id="ModalUpgradeCustomer" tabindex="-1" role="dialog" aria-labelledby="ModalUpgradeCustomer" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">Cập nhật thông tin khách hàng</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <form id="form_edit_password" method="POST" action="/customer/edit-customer" class="form-inline" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group w-100 mb-3">
                                        <label for="name_employees_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên nhân viên</label>
                                        <input type="text" id="name_employees_edit" name="name_employees_edit" placeholder="Nhập tên nhân viên" class="form-control" style="width: 285px">
                                    </div>
                                    <div class="form-group w-100 mb-3" hidden>
                                        <label for="phone" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Số điện thoại</label>
                                        <input type="text" id="phone_employees_edit" name="phone_employees_edit" placeholder="Nhập số điện thoại" class="form-control" style="width: 285px">
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="address_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Địa chỉ</label>
                                        <input type="text" id="address_edit" name="address_edit" class="form-control" placeholder="Nhập địa chỉ" style="width: 285px">
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="birthday_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Ngày sinh</label>
                                        <input id="birthday_edit" type="text" name="birthday_edit" class="form-control" placeholder="Nhập ngày sinh" onfocus="(this.type='date')" style="width: 285px; height: 33px">
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="partnerfield_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Đơn vị</label>
                                        <select name="partnerfield_edit" id="partnerfield_edit" class="form-control" style="width: 285px; height: 33px; cursor: pointer;" @change="getPartnerEdit()">
                                            @if (count($listPartnerField) > 0)
                                                @foreach($listPartnerField as $item)
                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group w-100 mb-3" v-if="result_partner_edit.length > 0">
                                        <label for="partner_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Đối tác</label>
                                        <select name="partner_edit" id="partner_edit" class="form-control" style="width: 285px; height: 33px; cursor: pointer;">
                                            <option v-for="(item, idex) in result_partner_edit" :value=item.id>@{{item.name}}</option>
                                        </select>
                                    </div>
                                    <input name="id_user_edit" type="text" class="mb-3 d-none" id="id_user_edit" hidden/>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"> Huỷ bỏ </button>
                                <button type="button" v-on:click="submitEditCustomer" data-dismiss="modal" class="btn btn-primary"> Xác nhận </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal Upgrade User--}}
                <div class="modal fade" id="ModalUpgradeUser" tabindex="-1" role="dialog" aria-labelledby="ModalUpgradeUser" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Nâng cấp thành Nhân viên</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <form id="form_upgrade_user" method="POST" action="/customer/upgrade" class="form-inline" enctype="multipart/form-data"> 
                                    @csrf
                                    <div class="form-group w-100 mb-3">
                                        <label for="name_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên nhân viên</label>
                                        <input type="text" id="name_employees" name="name_employees" class="form-control" style="width: 285px" placeholder="Nhập tên nhân viên">
                                    </div>
                                    <div class="form-group w-100 mb-3" hidden>
                                        <label for="phone_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Số điện thoại</label>
                                        <input type="text" id="phone_employees" name="phone_employees" class="form-control" style="width: 285px" placeholder="Nhập số điện thoại">
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="email_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Email</label>
                                        <input type="text" id="email_employees" name="email_employees" class="form-control" style="width: 285px" placeholder="Nhập email">
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="branch" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Chọn chi nhánh</label>
                                        <select name="branch" id="branch" class="form-control" style="width: 285px; cursor: pointer;">
                                            <option value="">Chọn Chi Nhánh</option>
                                            @if(count($listBranch) > 0)
                                                @foreach ($listBranch as $value)
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="type_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Loại nhân viên</label>
                                        <select name="type_employees" id="type_employees" class="form-control" style="width: 285px; cursor: pointer;">
                                            <option value="" selected="true"> Chọn loại nhân viên </option>
                                            @if (count($liststaff)>0)
                                                @foreach($liststaff as $value)
                                                    <option value="{{$value->id}}"> {{$value->name}} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group w-100 mb-3">
                                        <label for="name" class="col-md-4 p-0 justify-content-start font-weight-bold"> Avatar </label>
                                        <div class="col-md-8 p-0 input-group">
                                            <input name="files" type="file" class="mb-3" id="files" accept="image/*"/>
                                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    <span v-if="avatar_path != null && avatar_path != ''">
                                                        <div class="carousel-item carousel-item-avatar active">
                                                        <img id="avatarcollector" style="width: 150px; height: 150px;" class="d-block" :src="avatar_path" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input name="id_user" type="text" class="mb-3 d-none" id="id_user" hidden/>
                                    <input name="avatar_path" type="text" class="mb-3 d-none" id="avatar_path" hidden/>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"> Huỷ bỏ </button>
                                <button type="button" v-on:click="submitForm" class="btn btn-primary"> Xác nhận </button>
                            </div>
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
            include public_path('/js/customer/manage-customer/manage-customer.js');
        @endphp
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            if (window.File && window.FileList && window.FileReader) {
                $("#files").change(function(e) {
                    var files = $(this)[0].files,
                        filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[0];
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            $(".carousel-item-avatar").remove();
                            $(".carousel-inner").append("<div class=\"carousel-item carousel-item-avatar active\">"
                                                +  "<img id=\"avatarcollector\" style= \"width: 150px;height: 150px;\" class=\"d-block\" src=\"" + e.target.result + "\" />" 
                                                +  "<span class=\"btn_remove_image fas fa-times\"></span>"
                                                +  "</div>" );
                            $(".btn_remove_image").click(function() {
                                $(this).parent(".carousel-item").remove();
                                $("#files").val('');
                            });                                            
                        });
                        fileReader.readAsDataURL(f);
                    }
                });               
            } else {
                alert("Your browser doesn't support to File API")
            }
            $('#ModalUpgradeUser').on('hidden.bs.modal', function (e) {
                $('.carousel-inner').empty();
                $("#files").val('');
                $('#partnerfield_edit').val('');
            })    
        }); 
        
    </script>
@endsection