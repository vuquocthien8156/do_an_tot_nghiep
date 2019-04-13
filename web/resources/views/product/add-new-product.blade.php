@extends('layout.base')

@section('body-content')
<div id="add-product">
    <div class="row mt-5 pt-3 pb-5">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route('add-new-product', [], false)}}">THÊM SẢN PHẨM</a>
            </h4>
        </div>
    </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 33rem;">
                    <form method="POST" action="/product/save" class="form-inline" enctype="multipart/form-data"> 
                        @csrf
                        <div class="form-group w-100 mb-3">
                            <label for="name_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên sản phẩm</label>
                            <input type="text" id="name_product" name="name_product" class="form-control" style="width: 285px" placeholder="Nhập tên sản phẩm">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="origin_price_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Giá gốc</label>
                            <input type="text" id="origin_price_product" name="origin_price_product" class="form-control" style="width: 285px" placeholder="Nhập giá gốc">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="promotion_price" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Giá khuyến mãi</label>
                            <input type="text" id="promotion_price" name="promotion_price" class="form-control" style="width: 285px" placeholder="Nhập giá khuyến mãi">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="category_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Danh mục</label>
                            <select name="id_category_product" v-model="id_category_product" id="id_category_product" class="form-control" style="width: 285px; cursor: pointer;" @change="getInformationProduct()">
                                <option value="">Chọn danh mục</option>
                                @if(count($listCategory) > 0)
                                    @foreach ($listCategory as $value)
                                       <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="color_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Màu sắc</label>
                            <select name="color_product" id="color_product"  class="form-control" style="width: 285px; cursor: pointer;">
                                <option value="">Chọn màu</option>
                                <option v-for="Color_product in Color_product" v-bind:value="Color_product.id">@{{Color_product.name}}</option>
                            </select>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="trademark_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Thương hiệu</label>
                            <select name="trademark_product" id="trademark_product" class="form-control" style="width: 285px; cursor: pointer;">
                                <option value="">Chọn thương hiệu</option>
                                <option v-for="Trade_Mark_product in Trade_Mark_product" v-bind:value="Trade_Mark_product.id">@{{Trade_Mark_product.name}}</option>
                            </select>

                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="origin_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Xuất xứ</label>
                            <select name="origin_product" id="origin_product" class="form-control" style="width: 285px; cursor: pointer;">
                                <option value="">Chọn Xuất xứ</option>
                                <option v-for="Origin_product in Origin_product" v-bind:value="Origin_product.id">@{{Origin_product.name}}</option>
                            </select>
                            
                        </div>
                         <div class="form-group w-100 mb-3">
                            <label for="size_product" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">kích thước</label>
                            <select name="size_product" id="size_product" class="form-control" style="width: 285px; cursor: pointer;">
                                <option value="">Chọn kích thước</option>
                                <option v-for="Size_product in Size_product" v-bind:value="Size_product.id">@{{Size_product.name}}</option>
                            </select>
                            
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="SKU" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">SKU</label>
                            <input type="text" id="SKU" name="SKU" class="form-control" style="width: 285px" placeholder="SKU">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="model" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Dòng sản phẩm</label>
                            <input type="text" id="model" name="model" class="form-control" style="width: 285px" placeholder="Nhập Dòng sản phẩm">
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="description" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Mô tả</label>
                            <textarea type="text" id="description" name="description" class="form-control" style="width: 285px" placeholder=""> </textarea>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="model" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Hình Ảnh</label>
                            <input id="_imagesInput" name="files[]" type="file" multiple>
                            <div id="_displayImages">
                                <div>
                                    <ul id="frames" class="frames">
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="button-app" style="margin: 15px 0 10px 135px">Save</button>
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
            $('#_uploadImages').click(function() {
                $('#_imagesInput').click();
            });

            $('#_imagesInput').on('change', function() {
                 handleFileSelect();
            });

            function handleFileSelect() {
                if (window.File && window.FileList && window.FileReader) {
                    var files = event.target.files; 
                    var output = document.getElementById("frames");
                    var arrFilesCount = [];
                    for (var i = 0; i < files.length; i++) {
                        arrFilesCount.push(i);
                        var file = files[i];
                        if (!file.type.match('image')) continue;
                        var picReader = new FileReader();
                        picReader.addEventListener("load", function (event) {
                            var picFile = event.target;
                            output.innerHTML = output.innerHTML +"<div class=\"carousel-item carousel-item-avatar active\">"+"<img width='100px;' height='100px;' style='margin-left:2%;margin-top:2%' src='" + picFile.result + "'" + "title=''/>"+"<span class=\"btn_remove_image fas fa-times\"></span>"
                                                +  "</div>";
                                                $(".btn_remove_image").click(function() {
                                $(this).parent(".carousel-item").remove();
                                $("#frames").val('');
                            });      
                        });
                        picReader.readAsDataURL(file);
                    }
                 } else {
                    console.log("Your browser does not support File API");
                 }
        }
        });     
    </script>
     <script type="text/javascript">
        @php
            include public_path('/js/product/add-product/add-product.js');
        @endphp
    </script>
@endsection