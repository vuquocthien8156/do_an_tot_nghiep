@extends('layout.base')

@section('body-content')
<div id="setting-manufacture-model">
    <div class="row mt-5 pt-3 pb-5">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route('config-view-manufacture-model', [], false)}}"> HÃNG XE-DÒNG XE </a>
            </h4>
        </div>
    </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class="row">
                <div class="col" style="border-right: 1px solid;">
                    <form action="{{route('config-save-manufacture')}}" method="POST" id="form_manufacture">
                        {{csrf_field()}}
                        <div style="height: 7rem">
                            <div class="form-group row">
                                <label for="add_model" class="col-sm-3 col-form-label">Thêm hãng xe</label>
                                <input type="text" id="name_manufacture" class="form-control" style="width: 180px" placeholder="Nhập Tên Hãng Xe">
                                <label for="icon_upload_manufacture" class="custom_file_upload mb-0 mr-1 ml-1">
                                    <i class="fas fa-upload"></i> &nbsp; Upload Logo
                                </label>
                                <span id="image_logo_upload_manufacture">
                                </span>
                                <input name="icon_upload_manufacture" id="icon_upload_manufacture" type="file" style="display:none"/>
                                <input type="button" style="width: 50px" name="add_manufacture" id="add_manufacture" class="button-app form-control" value="Add">
                            </div>
                        </div>
                        <label for="manufacture">Danh sách hãng xe</label>
                        <table class="table table-bordered table-striped table-hover" id="table_manufacture">
                            <thead class="blue-opacity">
                                <tr class="text-center">
                                    <th class="custom-view">Tên Hãng</th>
                                    <th class="custom-view">Logo</th>
                                    <th class="custom-view">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="list_manufacture">
                                @foreach ($listManufacture as $key => $value)
                                <tr class="text-center" id="tr_{{$key}}" data-id_manufacture="{{$value->id}}">
                                    <td class="custom-view text-left"> {{$value->name}} </td>
                                    <td class="custom-view">
                                        @if($value->logo_path != null) 
                                            <img style= "margin: auto; width: 30px; height: 30px;" src="{{$value->logo_path}}"/>
                                        @else

                                        @endif
                                    </td>
                                    <td class="custom-view"> <span class="btn_move up fas fa-arrow-up"> </span> <span class="btn_move down fas fa-arrow-down"> </span> <span class='btn_remove fas fa-times remove_field_manufacture' @click="deleteManuFacture({{$value->id}})"> </span> </td>
                                    <td style="display:none"> <input type="text" name="manufacture_old{{$key}}" value="{{$value->id}}" hidden> </td>
                                </tr>
                                @endforeach
                            </tbody>      
                        </table>
                        <input type="number" name="total_manufacture_old" value="{{count($listManufacture)}}" hidden>
                        <input type="number" name="total_manufacture_new" id="total_manufacture_new" hidden>
                        @if(count($listManufacture) > 0)
                            <div class="">
                                <button type="submit" class="button-app save-manufacture">Lưu Thêm Mới</button>
                                <button type="button" onclick="sortDisplayOrderManufacture()" class="button-app save-sort-order">Lưu Đổi Vị Trí</button>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="col">
                    <form action="{{route('config-save-model')}}" method="POST" id="form_model">
                        {{csrf_field()}}
                        <div style="height: 7rem">
                            <div class="form-group row">
                                <label for="manufacture" class="col-sm-3 col-form-label">Chọn hãng xe</label>
                                <select name="manufacture" v-model="id_manufacture" id="manufacture" @change="getModelManufacture()" class="form-control" style="width: 180px">
                                    <option value="">Chọn Hãng xe</option>
                                    @if(count($listManufacture) > 0)
                                        @foreach ($listManufacture as $value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="add_model" class="col-sm-3 col-form-label">Thêm dòng xe</label>
                                <input type="text" id="name_model" class="form-control" style="width: 180px">
                                <label for="icon_upload_model" class="custom_file_upload mb-0 mr-1 ml-1">
                                    <i class="fas fa-upload"></i> &nbsp; Upload Logo
                                </label>
                                <span id="image_logo_upload_model">
                                </span>
                                <input name="icon_upload_model" id="icon_upload_model" type="file" class="d-none"/>
                                <input type="button" class="button-app form-control" style="width: 50px" name="add_model" id="add_model" value="Add">
                            </div>
                        </div>
                        <label for="model_manufacture">Danh sách dòng xe</label>
                        <table class="table table-bordered table-striped table-hover" id="table_model">
                            <thead class="blue-opacity">
                                <tr class="text-center">
                                    <th class="custom-view">Tên dòng xe</th>
                                    <th class="custom-view">Logo</th>
                                    <th class="custom-view">Hành động</th> 
                                </tr>
                            </thead>
                            <tbody class="list_model" v-cloak>
                                <tr class="text-center" v-for="(item, index) in manufacture_model_results" :id="'model_tr_'+index" :data-id_model="item.id">
                                    <td class="custom-view text-left"> @{{ item.name }} </td>
                                    <td class="custom-view" v-if="(item.logo_path != null)"> 
                                        <img style= "margin: auto; width: 30px; height: 30px;" :src="item.logo_path"/>
                                    </td>
                                    <td v-else> </td>
                                    <td class="custom-view"> <span class="btn_move up fas fa-arrow-up"> </span> <span class="btn_move down fas fa-arrow-down"> </span> <span class='btn_remove fas fa-times remove_field_model' @click="deleteModel(item.id)"> </span> </td>
                                    <td class="d-none"> <input type="text" :name="'model_old'+index" v-bind:value="item.id" hidden> </td>
                                <tr>
                            </tbody>        
                        </table>
                        <input type="number" name="total_model_old" v-bind:value="manufacture_model_results.length" hidden>
                        <input type="number" name="total_model_new" id="total_model_new" hidden>
                        <div class="" v-if="manufacture_model_results.length > 0">
                            <button type="submit" class="button-app">Lưu Thêm Mới</button>
                            <button type="button" onclick="sortDisplayOrderModel()" class="button-app">Lưu Đổi Vị Trí</button>
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
            include public_path('/js/setting/manufacture-model/manufacture-model.js');
        @endphp
    </script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script> 
    <script type="application/javascript">
        $(document).ready(function() {
            // src icon
            var image_logo_manufacture = '';
            var image_logo_model = '';
            // count total manufacture, model new
            var total_manufacture_new = 0;
            var total_model_new = 0;
            // Set value post for input total_manufacture_new, total_model_new
            $("#total_manufacture_new").val(total_manufacture_new);
            $("#total_model_new").val(total_model_new);

            if (window.File && window.FileList && window.FileReader) {
                $("#icon_upload_manufacture").change(function(e){
                    var files = e.target.files, filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[0];
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            image_logo_manufacture = e.target.result;
                            $("#image_logo_upload_manufacture").append("<img style= \"width: 35px;height: 35px; margin-right:10px\" class=\"d-block\" src=\"" + e.target.result + "\" />");
                        });
                        fileReader.readAsDataURL(f);
                    }
                });
                $("#icon_upload_model").change(function(e){
                    var files = e.target.files, filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[0];
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            image_logo_model = e.target.result;
                            $("#image_logo_upload_model").append("<img style= \"width: 35px;height: 35px; margin-right:10px\" class=\"d-block\" src=\"" + e.target.result + "\" />");
                        });
                        fileReader.readAsDataURL(f);
                    }
                });                    
            } else {
                alert("Your browser doesn't support to File API");
            }
            // Add Manufacture
            $("#add_manufacture").click(function() {
                var name_manufacture = $('#name_manufacture').val();
                var html = '';
                var td_logo = '';
                if (name_manufacture == '' || name_manufacture == null) {
                    bootbox.alert("Vui lòng nhập tên hãng xe!");
                    return false;
                }
                if (image_logo_manufacture == "") {
                    td_logo = '<td></td>';
                } else {
                    td_logo = '<td class="custom-view text-center"> <img style= "margin: auto; width: 30px; height: 30px;" class="d-block" src="'+image_logo_manufacture+'"/> </td>';
                }

                html =  '<tr>'
                    +   '<td class="custom-view"> '+name_manufacture+' </td>'
                    +   td_logo
                    +   '<td class="custom-view text-center"> <span class=\'btn_remove fas fa-times remove_field_manufacture\'> </span></td>'
                    +   '<td style="display:none"> <input name="input_manufacture_new'+total_manufacture_new+'" value="'+name_manufacture+'__'+image_logo_manufacture+'" hidden> </td>'
                    +   '</tr>'
                $('.list_manufacture').append(html);
                total_manufacture_new++ ;
                $("#total_manufacture_new").val(total_manufacture_new);
                $('#name_manufacture').val('');
                image_logo_manufacture = '';
                $('#image_logo_upload_manufacture').empty();
            });
            // Add model
            $("#add_model").click(function() {
                var name_model = $('#name_model').val();
                var value_manufacture = $('#manufacture').val();
                var html = '';
                var td_logo = '';
                if (name_model == '' || name_model == null) {
                    bootbox.alert("Vui lòng nhập tên dòng xe!");
                    return false;
                }
                if (value_manufacture == "" || value_manufacture == null) {
                    bootbox.alert("Vui lòng chọn hãng xe!");
                    return false;
                }

                if (image_logo_model == "") {
                    td_logo = '<td></td>';
                } else {
                    td_logo = '<td class="custom-view text-center"> <img style= "margin: auto; width: 30px; height: 30px;" class="d-block" src="'+image_logo_model+'"/> </td>';
                }

                html =  '<tr>'
                    +   '<td class="custom-view"> '+name_model+' </td>'
                    +   td_logo
                    +   '<td class="custom-view text-center"> <span class=\'btn_remove fas fa-times remove_field_model\'> </span></td>'
                    +   '<td style="display:none"> <input name="input_model_new'+total_model_new+'" value="'+value_manufacture+'__'+name_model+'__'+image_logo_model+'" hidden> </td>'
                    +   '</tr>'
                $('.list_model').append(html);
                total_model_new++ ;
                $("#total_model_new").val(total_model_new);
                $('#name_model').val('');
                image_logo_model = '';
                $('#image_logo_upload_model').empty();
            });
        });
        //submit form has show loading and notify success or error
        $(function() {
            $("#form_manufacture").submit(function() {
                common.loading.show('.container');

                $.post($(this).attr("action"), $(this).serialize(), 
                    function(result) {
                        common.loading.hide('.container');
                        if (result.error === 0) {
                            bootbox.alert("Save Success !!", function() {
                                window.location = '/config/manufacture-model';
                            })
                        } else {
                            bootbox.alert('Save Error !!');
                        }
                    }, "json");
                return false;
            });

            $("#form_model").submit(function() {
                common.loading.show('.container');

                $.post($(this).attr("action"), $(this).serialize(), 
                    function(result) {
                        common.loading.hide('.container');
                        if (result.error === 0) {
                            bootbox.alert("Save Success !!", function() {
                                window.location = '/config/manufacture-model';
                            })
                        } else {
                            bootbox.alert('Save Error !!');
                        }
                    }, "json");
                return false;
            });
        });

        //sort table
        // $('input[type="checkbox"]').click(function() {
        //     if($(this).prop("checked") == true) {
        //         $('.save-manufacture').removeClass('d-block');
        //         $('.save-manufacture').addClass('d-none');
        //         $('.save-sort-order').removeClass('d-none');
        //         $('.save-sort-order').addClass('d-block');
        //     } else {
        //         $('.save-manufacture').removeClass('d-none');
        //         $('.save-manufacture').addClass('d-block');
        //         $('.save-sort-order').removeClass('d-block');
        //         $('.save-sort-order').addClass('d-none');
        //     }
        // });
        $('tbody').sortable();
        $('tbody span.btn_move').click(function() {
            var row = $(this).closest('tr');
            if ($(this).hasClass('up')) {
                row.prev().before(row);
            } else {
                row.next().after(row);
            }
        });
        $('.list_model').click(function() {
            $('tbody span.btn_move').click(function() {
                var row = $(this).closest('tr');
                if ($(this).hasClass('up')) {
                    row.prev().before(row);
                } else {
                    row.next().after(row);
                }
            });
        });
        
        function sortDisplayOrderManufacture() {
            var rowCount = $('#table_manufacture >tbody >tr').length;
            var arr_id_index = [];
            for(var i = 0; i < rowCount; i++) {
                var colIndex = $("#tr_" + i).index();
                var id_manufacture = $("#tr_" + i).data('id_manufacture');
                var tmp = id_manufacture + '_' + colIndex;
                arr_id_index.push(tmp);
            }
            var data = {
                id_manufacture_index : arr_id_index.toString(),
            };
            common.loading.show('body');
            $.ajax({
                url: '/config/manufacture/sort-display-order',
                method: 'POST',
                data: data,
                success: function(result) {
                    if (result.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/config/manufacture-model';
                        });
                    } else {
                        common.loading.hide('body');
                        bootbox.alert("Lỗi !!");
                    }
                },
                error: function() {
                    common.loading.hide('body');
                    bootbox.alert("Lỗi !!");
                }
            });
        }

        function sortDisplayOrderModel() {
            var rowCount = $('#table_model >tbody >tr').length;
            var arr_id_index = [];
            for(var i = 0; i < rowCount - 1; i++) {
                var colIndex = $("#model_tr_" + i).index();
                var id_model = $("#model_tr_" + i).data('id_model');
                var tmp = id_model + '_' + colIndex;
                arr_id_index.push(tmp);
            }
            var data = {
                id_model_index : arr_id_index.toString(),
            };
            $.ajax({
                url: '/config/model/sort-display-order',
                method: 'POST',
                data: data,
                success: function(result) {
                    if (result.error === 0) {
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/config/manufacture-model';
                        });
                    } else {
                        bootbox.alert("Lỗi !!");
                    }
                },
                error: function() {
                    bootbox.alert("Lỗi !!");
                }
            });
        }
	</script>
@endsection