@extends('layout.base')

@section('body-content')
<div id="authorization-user-web">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mt-5 pt-3 pb-5">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route('config-view-authorization', [], false)}}"> PHÂN QUYỀN </a>
            </h4>
        </div>
    </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class=" form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 37rem;">
                    <form class="form-inline"> 
                        <input type="hidden" name="_token" :value="csrf">
                        <div class="text-center mx-auto mb-3"> <h2>TẠO USER</h2> </div>
                        <div class="form-group w-100 mb-3">
                            <label for="name_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="name_user" v-model="name_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập tên" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="email_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Email</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="email_user" v-model="email_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập email" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="phone_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Số điện thoại</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="phone_user" v-model="phone_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập số điện thoại" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="password_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Mật khẩu</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="password" id="password_user" v-model="password_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập password" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="cmnd" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Cho phép truy cập</label>
                            <div class="col-md-8 p-0 input-group">
                                @foreach ($permissionGroup as $value)
                                    <div class="col-md-8 p-0 mb-2">
                                        <div class="form-group">
                                            <input type="checkbox" name="chk_permission_group[]" class="input_type_check" id="permission_{{$value->id}}" value="{{$value->id}}"> 
                                            <label for="customer_type"> {{$value->name}} </label><br>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row mx-auto">
                            <button type="button" class="button-app" @click="saveUserWeb">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="set-row background-contact w-100" style="min-height: 150px">    
            <div id="table_1" class="position-relative">
                <table class="table table-bordered table-striped w-100" style="min-height: 150px">
                    <thead>
                        <tr class="text-center blue-opacity">
                            <th class="custom-view"> STT </th>
                            <th class="custom-view"> Họ Tên/ Số điện thoại </th>
                            <th class="custom-view"> Email </th>
                            <th class="custom-view"> Cho phép truy cập </th>
                            <th class="custom-view"> Hành động </th>
                        </tr>
                    </thead>
                    <tbody v-cloak>
                        <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data">
                            <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                            <td class="custom-view text-left"> @{{ item.name }} / <br> @{{ item.phone }}</td>
                            <td class="custom-view text-left">@{{ item.email }}</td>
                            <td class="custom-view">
                                <span v-for="(access, key) in item.listAccess"> <span v-if="key != 0">-</span>  @{{access.name}} </span>
                            </td>
                            <td class="custom-view">
                                <span class="btn_edit fa fa-edit"  @click="getInfo(item.id, item.name, item.phone, item.email, item.listAccess_id)" data-toggle="tooltip" data-placement="left" title="Sửa"></span>
                                <span class="btn_remove fa fa-trash" @click="deleteUserWeb(item.id)" data-toggle="tooltip" data-placement="right" title="Xoá"></span>
                            </td>
                        <tr>
                    </tbody> 
                </table>
            </div>
        </div>
    </div>
     {{-- Modal update User authorization --}}
     <div class="modal fade" id="ModalUpdateUserAuthorization" tabindex="-1" role="dialog" aria-labelledby="ModalUpdateUserAuthorization" aria-hidden="true">
            <div class="modal-dialog" role="document" style="width: 500px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Chỉnh sửa thông tin </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>                     
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="name_user_update" class="col-sm-4"> Tên </label>
                            <div class="col-sm-8">
                                <input type="text" id="name_user_update" class="form-control" v-model="name_user_update">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="email_user_update" class="col-sm-4"> Email </label>
                            <div class="col-sm-8">
                                <input type="text" id="email_user_update" class="form-control" v-model="email_user_update">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="phone_user_update" class="col-sm-4"> Số điện thoại </label>
                            <div class="col-sm-8">
                                <input type="text" id="phone_user_update" class="form-control" v-model="phone_user_update">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_user_update" class="col-sm-4"> Cho phép truy cập </label>
                            <div class="col-sm-8">
                                @foreach ($permissionGroup as $value)
                                    <div class="col-md-8">
                                        <div>
                                            <input type="checkbox" name="chk_permission_group_update[]" class="input_type_check" id="permission_update_{{$value->id}}" value="{{$value->id}}"> 
                                            <label for="permission_update_{{$value->id}}"> {{$value->name}} </label><br>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> Đóng </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateUserWeb()"> Cập nhật </button>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        @php
            include public_path('/js/authorization/authorization-user-web/authorization-user-web.js');
        @endphp
    </script>
    <script type="application/javascript">
        
    </script>
@endsection