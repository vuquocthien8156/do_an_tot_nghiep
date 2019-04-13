@extends('layout.base')

@section('body-content')
    <div id="manage-employees"> 
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('employees-view', [], false)}}"> QUẢN LÝ NHÂN VIÊN </a>
                </h4>
            </div>
        </div>
        <div class="row">
            <input type="hidden" name="_token" :value="csrf">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <div class="pb-4">
                    <input id="username_phone" type="text" class="input-app mr-5" v-model="name_phone_email" style="width: 240px" placeholder="Nhập Tên/Email/SĐT">
                    <select name="branch" id="branch" class="input-app ml-4 mr-5 bg-white" v-model="branch_id" style="width: 250px; height: 2rem; cursor: pointer;">
                        <option value="">Chọn Chi Nhánh</option>
                        @if(count($listBranch) > 0)
                            @foreach ($listBranch as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        @endif
                    </select>               
                    <select name="type_employees" id="type_employees" class="input-app ml-4" v-model="type_employees" style="width: 200px; height: 2rem; cursor: pointer;">
                        <option value="">Chọn loại nhân viên</option>
                        @if (count($listStaff) > 0)
                            @foreach ($listStaff as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        @endif
                    </select>     
                    <button class="button-app float-right" @click="searchEmployees()">Tìm kiếm</button><br>
                </div>
                <div class="row">
                    {{--<div class="col-md-10 mx-auto" style="text-align: right;">
                       <button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 12.5%" @click="deleteEmployees()">Xoá</button>
                    </div>--}}
                    <div class="col-md-12 mx-auto">
                       <a :href="'excel-employees?name_phone_email='+result_infoExport.name_phone_email+'&branch_id='+result_infoExport.branch_id+'&status='+result_infoExport.status+'&type_employees='+result_infoExport.type_employees" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
                <div id="table_1" class="position-relative">
                    <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                        <thead style="">
                            <tr class="text-center blue-opacity">
                                <th class="custom-view" width="5%">STT</th>
                                <th class="custom-view">Tên nhân viên/SĐT</th>
                                <th class="custom-view">Email</th>
                                <th class="custom-view">Ngày sinh</th>
                                <th class="custom-view">Chi nhánh</th>
                                <th class="custom-view">Loại nhân viên</th>
                                <th class="custom-view">Avatar</th>
                                <th class="custom-view">Hành động</th>
                            </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr class="text-center font-weight-bold" v-for="(item, index) in results.data">
                                <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                                <td class="custom-view text-left"> @{{ item.name }} /<br> @{{ item.phone }} </td>
                                <td class="custom-view text-left">@{{ item.email }}</td>
                                <td class="custom-view">@{{ item.birthday }}</td>
                                <td class="custom-view">@{{ item.branch_name }}</td>
                                <td class="custom-view">@{{item.category_name}}</td>
                                <td class="custom-view" v-if="item.avatar_path != null && item.avatar_path != ''"> <img class="image_chat1" @click="modalImage(item.path_to_resource , item.avatar_path)" style="width:50px; height:50px" :src="item.path_to_resource +'/' + item.avatar_path"></td>
                                <td class="custom-view" v-else> <img class="image_chat1" style="width:50px; height:50px" src="/images/user.png"></td>
                                <td class="custom-view">
                                    <span class="btn_edit fa fa-edit"  data-toggle="tooltip" data-placement="left" title="Sửa" @click="getmodelEmployess(item.id, item.name, item.email, item.branch_id, item.categoryid, item.avatar_path, item.birthday)"></span>
                                    <span class="btn_remove fa fa-trash" @click="deleteEmployees(item.id)" data-toggle="tooltip" data-placement="right" title="Xóa nhân viên"></span>
                                </td>
                            <tr>
                        </tbody>   
                    </table>    
                </div>
                <div class="col-12">
                    <pagination :data="results" @pagination-change-page="searchEmployees" :limit="4"></pagination>
                </div>
                <div class="row" v-if="results.last_page > 1">
                    <div class="col-md-8 mx-auto" style="text-align: right;">
                       {{-- <button class="button-app ml-3 mr-4" style="border: 1px solid transparent;margin-right: 8%" @click="deleteEmployees()">Xoá</button>  --}}
                        <a :href="'excel-employees?name_phone_email='+result_infoExport.name_phone_email+'&branch_id='+result_infoExport.branch_id+'&status='+result_infoExport.status" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
            </div>
            <div id="myModal_chat" class="modal_chat">
                <span class="close_chat" style="top: 100px">&times;</span>
                <img class="modal-content-chat h-100" id="img01">
            </div>
        </div>
         {{-- Modal Upgrade Employess--}}
                <div class="modal fade" id="ModalUpgradeEmployess" tabindex="-1" role="dialog" aria-labelledby="ModalUpgradeEmployess" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">CẬP NHẬT THÔNG TIN NHÂN VIÊN</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                             <form method="POST" action="/employees/update" class="form-inline" id="from_upgrade_employess" enctype="multipart/form-data" ref="form" @submit.prevent="submitEditEmployees">
                                @csrf
                                <div class="form-group w-100 mb-3">
                                    <label for="name_employees_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Tên nhân viên </label>
                                    <input type="text" id="name_employees_edit" name="name_employees_edit" class="form-control" style="width: 285px" placeholder="Nhập tên nhân viên">
                                </div>
                                <div class="form-group w-100 mb-3">
                                    <label for="email_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Email </label>
                                    <input type="text" id="email_edit" name="email_edit" class="form-control" style="width: 285px" placeholder="Nhập số điện thoại">
                                </div>
                                <div class="form-group w-100 mb-3">
                                    <label for="birthday_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Ngày sinh </label>
                                    <input id="birthday_edit" type="text" name="birthday_edit" class="form-control" placeholder="Nhập ngày sinh" onfocus="(this.type='date')" style="width: 285px; height: 33px">
                                </div>
                                <div class="form-group w-100 mb-3">
                                    <label for="branch_name_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Chọn chi nhánh </label>
                                    <select name="branch_name_edit" id="branch_name_edit" class="form-control" style="width: 285px; cursor: pointer;">
                                        <option value=""> Chọn Chi Nhánh </option>
                                        @if(count($listBranch) > 0)
                                            @foreach ($listBranch as $value)
                                                <option value="{{$value->id}}">{{$value->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group w-100 mb-3">
                                    <label for="category_name_edit" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Loại nhân viên</label>
                                    <select name="category_name_edit" id="category_name_edit" class="form-control" style="width: 285px; cursor: pointer;">
                                        <option value=""> Chọn loại nhân viên </option>
                                        @if (count($listStaff) > 0)
                                            @foreach ($listStaff as $value)
                                                <option value="{{$value->id}}">{{$value->name}}</option>
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
                                                        <img id="avatarcollector" style="width: 150px; height: 150px;" class="d-block" :src="avatar_path"/>
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="avatar_path" id="avatar_path" class="mb-0 d-none" hidden/>
                                <input name="id_user_edit" type="text" class="mb-3 d-none" id="id_user_edit" hidden/>
                                <div class="form-group w-100 modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Huỷ bỏ </button>
                                    <button type="submit" class="btn btn-primary"> Xác nhận </button>
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
            include public_path('/js/employees/manage-employees/manage-employees.js');
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
                                                +  "<img id=\"avatarcollector\" style= \"width: 250px;height: 250px;\" class=\"d-block\" src=\"" + e.target.result + "\" />" 
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
            $('#ModalUpgradeEmployess').on('hidden.bs.modal', function (e) {
                $('.carousel-item-avatar').empty();
                $("#files").val('');
            })
        });     
    </script>
@endsection