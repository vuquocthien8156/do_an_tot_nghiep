@extends('layout.base')

@section('body-content')
    <div id="manage-card-member"> 
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('card-member', [], false)}}"> THẺ THÀNH VIÊN </a>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact" style="min-height: 150px; width: 96%">
                <div class="pb-2">
                    <input id="username_phone_number_vehicle" type="text" class="input-app mr-4" style="width: 200px" placeholder="Tên/SĐT/Biển số xe" v-model="username_phone_number_vehicle">
                    <select name="manufacture" v-model="id_manufacture" id="manufacture" @change="getModelManufacture()" class="input-app mr-4" style="width: 150px; height: 33px; cursor: pointer;">
                        <option value=""> Hãng xe </option>
                        @if(count($listManufacture) > 0)
                            @foreach ($listManufacture as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        @endif
                    </select>     
                    <select name="model" id="model" class="input-app mr-4" v-model="model" style="width: 150px; height: 33px; cursor: pointer;">
                        <option value=""> Dòng xe </option>
                        <option v-for="manufacture_model_result in manufacture_model_results" v-bind:value="manufacture_model_result.id">
                            @{{ manufacture_model_result.name }}
                        </option>
                    </select>
                    <select name="status" id="status" class="input-app mr-4" v-model="status" style="width: 150px; height: 33px; cursor: pointer;">
                        <option value=""> Trạng thái </option>
                        <option value="{{ \App\Enums\EStatus::ACTIVE }},false,2"> Chưa đăng kí </option>
                        <option value="{{ \App\Enums\EStatus::ACTIVE }},false,1"> Đã đăng kí </option>
                        <option value="{{ \App\Enums\EStatus::ACTIVE }},true,1"> Đã kích hoạt </option>
                        <option value="{{ \App\Enums\EStatus::DELETED }}"> Đã xoá </option>
                    </select>             
                    <input id="code" type="text" class="input-app mr-4" style="width: 150px" placeholder="Mã thẻ thành viên" v-model="code">
                    <button class="button-app float-right" @click="searchCardMember()"> Tìm kiếm </button><br>
                </div>
                <div class="row">
                    <div class="col-md-9 ml-auto mt-3">
                        <a class="btn btn-primary button-app float-left" @click="syncDB411()">Đồng bộ</a>
                    </div>
                    <div class="col-md-3 ml-auto mt-3">
                        <a :href="'excel-card-member?username_phone='+result_infoExport.username_phone_number_vehicle+'&manufacture='+result_infoExport.manufacture+'&model='+result_infoExport.model+'&code='+result_infoExport.code+'&status='+result_infoExport.status+'&approved='+result_infoExport.approved+'&vehicle_card_status='+result_infoExport.vehicle_card_status" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
                <div id="table_1" class="position-relative mb-3">
                    <table class="table table-bordered table-striped" style="min-height: 150px; line-height: 1.4; width: 100%">
                        <thead style="">
                            <tr class="text-center blue-opacity">
                                <th class="custom-view" width="3%"> STT </th>
                                <th class="custom-view" width="10%"> Tên / SĐT </th>
                                <th class="custom-view" width="10%"> Tên trên thẻ </th>
                                <th class="custom-view" width="8%"> Biển số xe </th>
                                {{-- <th class="custom-view"> Hãng xe </th>
                                <th class="custom-view"> Dòng xe </th>
                                <th class="custom-view"> Màu xe </th> 
                                <th class="custom-view"> Thông tin chuyển khoản </th> --}}
                                <th class="custom-view" width="5%"> Mã thẻ thành viên </th>
                                <th class="custom-view" width="8%"> Ngày đăng ký </th>
                                <th class="custom-view" width="8%"> Ngày hiệu lực </th>
                                <th class="custom-view" width="8%"> Ngày hết hạn </th>
                                <th class="custom-view" width="8%"> Trạng thái </th>
                                <th class="custom-view" width="7%"> Hành động </th>
                            </tr>
                        </thead>
                        <tbody v-cloak>
                            <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data">
                                <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold"> @{{ (results.current_page - 1) * results.per_page + index + 1 }} </td>
                                <td class="custom-view text-left"><a href="#" class="Feedback" style="text-decoration: none; cursor: pointer;" data-toggle="modal" data-target="#ModalSeeMoreCardMember"
                                    @click="seeMoreDetail(item.name_user, item.phone, item.name_card, item.vehicle_number, item.vehicle_manufacture_id, 
                                        item.vehicle_model_id, item.vehicle_color, item.bank_transfer_info, item.status, item.code, item.created_at, item.approved_at, item.expired_at, item.approved, item.vehicle_card_status)">@{{ item.name_user }}/ <br> @{{ item.phone }}</a></td>
                                <td class="custom-view text-left" v-if="(item.code != null && item.code != '')">
                                    @{{ item.name_card }} 
                                </td>
                                <td class="custom-view" v-else>
                                    <input type="text" :id="'name_card_'+item.id" :value="item.name_card" class="input-app" style="width: 150px">
                                </td>
                                <td class="custom-view text-left"> @{{ item.vehicle_number }} </td>
                                {{-- <td class="custom-view"> @{{ item.vehicle_manufacture_id }} </td>
                                <td class="custom-view"> @{{ item.vehicle_model_id }} </td>
                                <td class="custom-view text-left"> @{{ item.vehicle_color }} </td>
                                <td class="custom-view text-left"> @{{ item.bank_transfer_info }} </td> --}}
                                

                                <td class="custom-view" v-if="(item.code != null && item.code != '')"> @{{ item.code }} </td>
                                <td class="custom-view" v-else>
                                    <input type="text" :id="item.id" class="input-app" style="width: 110px">
                                </td>
                                <td class="custom-view"> @{{ item.created_at }} </td>
                                <td class="custom-view"> @{{ item.approved_at }} </td>
                                <td class="custom-view"> @{{ item.expired_at }} </td>
                                <td class="custom-view text-left" v-if="item.status == {{ \App\Enums\EStatus::DELETED }}"> Đã xoá </td>
                                <td class="custom-view text-left" v-else-if="item.approved == true"> Đã kích hoạt </td>
                                <td class="custom-view text-left" v-else-if="item.approved == false && item.vehicle_card_status == 1"> Đã đăng ký</td>
                                <td class="custom-view text-left" v-else> Chưa đăng ký </td>
                                <td class="custom-view" v-if="(item.code != null && item.code != '')">
                                    <span class="btn_edit fa fa-edit" @click="getInfo(item.id, item.code, item.name_card)" data-toggle="modal" data-target="#ModalUpdateCardMember" data-id="item.id" data-code="item.code" data-name="item.name_card"  data-toggle="tooltip" data-placement="left" title="Sửa"></span>
                                    <span class="btn_remove fa fa-trash" @click="deleteCardMember(item.id)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span>
                                    {{-- <span class="btn_save fas fa-level-up-alt" data-toggle="modal" data-target="#ModalSeeMoreCardMember" @click="seeMoreDetail(item.name_user, item.phone, item.name_card, item.vehicle_number, item.vehicle_manufacture_id, item.vehicle_model_id, item.vehicle_color, item.bank_transfer_info, item.status, item.code, item.created_at, item.approved_at, item.expired_at)"></span> --}}
                                </td>
                                <td class="custom-view" v-else>
                                    <span class="btn_save fa fa-save" @click="saveCodeCardMember(item.id, item.user_id)" data-toggle="tooltip" data-placement="left" title="Lưu"></span>
                                    <span class="btn_remove fa fa-trash" @click="deleteCardMember(item.id)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span>
                                    {{-- <span class="btn_save fas fa-level-up-alt"data-toggle="modal" data-target="#ModalSeeMoreCardMember" @click="seeMoreDetail(item.name_user, item.phone, item.name_card, item.vehicle_number, item.vehicle_manufacture_id, item.vehicle_model_id, item.vehicle_color, item.bank_transfer_info, item.status, item.code, item.created_at, item.approved_at, item.expired_at)"></span> --}}
                                </td>
                            <tr>
                        </tbody>   
                    </table>    
                </div>
                <div class="col-12">
                    <pagination :data="results" @pagination-change-page="searchCardMember" :limit="4"></pagination>
                </div>
                 <div class="row" v-if="results.last_page > 1" >
                    <div class="col-md-12 mx-auto">
                        <a :href="'excel-card-member?username_phone='+result_infoExport.username_phone_number_vehicle+'&manufacture='+result_infoExport.manufacture+'&model='+result_infoExport.model+'&code='+result_infoExport.code+'&status='+result_infoExport.status" class="btn btn-primary button-app mb-4 float-right" >Xuất File Excel</a>
                    </div>
                </div>
            </div>
            {{-- Modal see more detail card member --}}
            <div class="modal fade" id="ModalSeeMoreCardMember" tabindex="-1" role="dialog" aria-labelledby="ModalSeeMoreCardMember" aria-hidden="true">
                <div class="modal-dialog" role="document" style="width: 500px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Chi tiết thẻ thành viên </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>                     
                        <div class="modal-body">
                            <div style="width: 50%; float: left;">
                                <div class="form-group">
                                    <label><b>Tên:</b></label><br>
                                    <span id="name" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Số điện thoại:</b></label><br>
                                    <span id="phonenNumber" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Tên trên thẻ:</b></label><br>
                                    <span id="nameOnCard" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Biển số xe:</b></label><br>
                                    <span id="vehicleNumber" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Hãng xe:</b></label><br>
                                    <span id="Manufacture" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Dòng xe:</b></label><br>
                                    <span id="Model" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Màu xe:</b></label><br>
                                    <span id="Color" style="word-wrap: break-word;"></span>
                                </div>    
                            </div>
                            <div style="width: 50%;float: left;">
                                {{-- <div class="form-group">
                                    <label><b>Thông tin chuyển khoản:</b></label><br>
                                    <span id="bankTransferInfo" style="word-wrap: break-word;"></span>
                                </div> --}}
                                <div class="form-group">
                                    <label><b>Trạng thái:</b></label><br>
                                    <span id="Status" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Mã thẻ thành viên:</b></label><br>
                                    <span id="codeOfCard" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Ngày đăng ký:</b></label><br>
                                    <span id="createdAt" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Ngày hiệu lực:</b></label><br>
                                    <span id="approvedAt" style="word-wrap: break-word;"></span>
                                </div>
                                <div class="form-group">
                                    <label><b>Ngày hết hạn:</b></label><br>
                                    <span id="expiredAt" style="word-wrap: break-word;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"> Đóng </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal update Name and Code card member --}}
            <div class="modal fade" id="ModalUpdateCardMember" tabindex="-1" role="dialog" aria-labelledby="ModalUpdateCardMember" aria-hidden="true">
                <div class="modal-dialog" role="document" style="width: 350px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Chỉnh sửa thẻ thành viên </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>                     
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name_update"> Tên trên thẻ </label>
                                <input type="name_update" class="form-control" id="name_update" v-model="name_update">
                            </div>
                            <div class="form-group">
                                <label for="code_update"> Code </label>
                                <input type="text" class="form-control" id="code_update" v-model="code_update">
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> Đóng </button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateNameCodeCardMember()"> Sửa </button>
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
            include public_path('/js/customer/manage-card-member/manage-card-member.js');
        @endphp
    </script>
    <script type="application/javascript">
        $('#ModalUpdateCardMember').on('hidden.bs.modal', function (e) {
            $('#name_update').val('');
            $('#code_update').val('');
        })
    </script>
@endsection