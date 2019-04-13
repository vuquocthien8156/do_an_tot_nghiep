@extends('layout.base')

@section('body-content')
<div id="setting-make-appointment">
    <div class="row mt-5 pt-3 pb-5">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route('employees-add-view', [], false)}}"> THÊM NHÂN VIÊN </a>
            </h4>
        </div>
    </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 33rem;">
                    <form method="POST" action="/employees/save" class="form-inline" enctype="multipart/form-data"> 
                        @csrf
                        <div class="form-group w-100 mb-3">
                            <label for="name_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Tên nhân viên </label>
                            <input type="text" id="name_employees" name="name_employees" class="form-control" style="width: 285px" placeholder="Nhập tên nhân viên">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="phone_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Số điện thoại </label>
                            <input type="text" id="phone_employees" name="phone_employees" class="form-control" style="width: 285px" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="email_employees" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Email </label>
                            <input type="text" id="email_employees" name="email_employees" class="form-control" style="width: 285px" placeholder="Nhập email">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="branch" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold"> Chọn chi nhánh </label>
                            <select name="branch" id="branch" class="form-control" style="width: 285px; cursor: pointer;">
                                <option value=""> Chọn Chi Nhánh </option>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="button-app" style="margin: 15px 0 10px 135px"> Save </button>
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
        });     
    </script>
@endsection