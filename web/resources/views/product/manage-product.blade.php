@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-product"> 
		<div class="row mt-5 pt-3">
			<div style="padding-left: 2rem">
				<h4 class="tag-page-custom">
					<a class="tag-title-show" style="text-decoration: none;" href="{{route('manage-product', [], false)}}">QUẢN SẢN PHẨM</a> 
				</h4>
			</div>
		</div>
		<div class="row">
			<div class="set-row background-contact w-100" style="min-height: 150px">
				<div class="pb-2">
					<input id="product_name" name="product_name" type="text" placeholder="Tên sản phẩm" class="input-app mr-4" style="width: 200px; height: 33px" v-model="product_name">
					<select name="id_category_product" v-model="id_category_product" id="id_category_product"class="input-app mr-4" style="width: 200px; height: 33px" @change="getInformationProduct()">
                                <option value="">Chọn danh mục</option>
                                @if(count($listCategory) > 0)
                                    @foreach ($listCategory as $value)
                                       <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                @endif
                    </select>
					<select name="status" id="status" class="input-app mr-4" v-model="status" style="width: 200px; height: 33px">
						<option value="">Chọn trạng thái</option>
						<option value="{{ \App\Enums\EProductStatus::ACTIVE }}">Đang bán</option>
						<option value="{{ \App\Enums\EProductStatus::DELETED }}">Đã xóa</option>
					</select> 
					<button class="button-app ml-5 float-right" @click="searchProduct()">Tìm kiếm</button>
				</div>
				<div class="modal fade" id="ModalUpdateProduct" tabindex="-1" role="dialog" aria-labelledby="ModalUpdateProduct" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Cập nhật</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
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
				<div id="table_1" class="position-relative">
					<table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
						<thead style="">
							<tr class="text-center blue-opacity">
								<th class="custom-view" width="5%">STT</th>
								<th class="custom-view">Tên sản phẩm</th>
								<th class="custom-view">Giá</th>	
								<th class="custom-view">Danh mục</th>
								<th class="custom-view">Màu sắc</th>
								<th class="custom-view">Thương hiệu</th>
								<th class="custom-view">Xuất xứ</th>
								<th class="custom-view">SKU</th>
								<th class="custom-view">Kích thước</th>
								<th class="custom-view">Dòng sản phẩm</th>
								<th class="custom-view">Mô tả</th>
								<th class="custom-view">Trạng thái</th>
								<th class="custom-view">Hình ảnh</th>
								<th class="custom-view">Xóa</th>
								<th class="custom-view">Sửa</th>
							</tr>
						</thead>
						<tbody v-cloak>
							<tr class="text-center" style="font-weight:bold" v-for="(item, index) in results_search.data" :key="item.selling_vehicle_id">
								<td class="custom-view td-grey" :class="{'grey-blue' : index % 2 != 0}" style="font-weight: bold">@{{ (results_search.current_page - 1) * results_search.per_page + index + 1 }}</td>
								<td class="custom-view ">@{{item.product_name}}</td>
								
								<td class="custom-view ">@{{item.promotion_price}} VND</td>
								<td class="custom-view" style="width:250px;">@{{item.product_category}}</td>
								<td class="custom-view ">@{{item.product_color}}</td>
								<td class="custom-view ">@{{item.product_trademark}}</td>
								<td class="custom-view ">@{{item.product_origin}}</td>
								<td class="custom-view ">@{{item.product_SKU}}</td>
								<td class="custom-view " style="width: 350px;">@{{item.product_size}}</td>
								<td class="custom-view ">@{{item.product_model}}</td>
								<td class="custom-view ">@{{item.product_description}}</td>
								<td class="custom-view ">
									<p v-if="item.product_status == '{{\App\Enums\EProductStatus::DELETED}}'">Đã xóa</p>
  									<p v-if="item.product_status == '{{\App\Enums\EProductStatus::ACTIVE}}'">Đang bán</p>
								</td>
								<td>
									<a href="#" style="text-decoration: none;color: white"><button  class="button-app see_image" style="width: 80px;" @click="loadSellingRequestResource(item.product_id)">Ảnh</button></a> 
								</td>
								<td class="custom-view">
									<input class="check_approve" :value="item.product_id" name="check[]" v-model="checkDelete" type="checkbox">
								</td>
								<td class="custom-view "><span class="btn_save fa fa-edit" @click="getInfoProduct()" data-toggle="tooltip" data-placement="left" title="Sửa"></span></td>
							</tr>
						</tbody>   
					</table>    
				</div>
				<div class="col-12">
					<pagination :data="results_search" @pagination-change-page="searchProduct"></pagination> 
				</div>
				<div class="row">
					<div class="col-md-10 mx-auto" style="text-align: right;">
						<button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 8%" @click="DeleteProduct()">Xóa</button>
					</div>
				</div>
			</div>
		</div>
		<div id="product" class="demo-gallery" style="display: none">
			<label>Hình ảnh sản phẩm</label>
			<ul id="lightgallery1" class="list-unstyled row">
				<li style="margin-bottom:2%; width: 100px; margin-left: 1%; height: 100px;" v-for="(item, index) in results_image.imageProduct">
					<a data-fancybox="galleryVehicle" :href="results_image.path +'/'+ item.path_to_resource">
						<img class="img-responsive" width="100px" height="100px" :src="results_image.path +'/'+ item.path_to_resource">
					</a>
				</li> 
			</ul>
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
                            output.innerHTML = output.innerHTML +"<img width='100px;' height='100px;' style='margin-left:2%;margin-top:2%' src='" + picFile.result + "'" + "title=''/>";
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
            include public_path('/js/product/manage-product/manage-product.js');
            include public_path('/js/product/manage-product/jquery.fancybox.min.js');
            include public_path('/js/product/manage-product/see-more-description.js');
        @endphp
    </script>
@endsection