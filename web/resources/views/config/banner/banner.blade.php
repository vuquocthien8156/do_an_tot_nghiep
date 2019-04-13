@extends('config.layout')

@section('stylesheet')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.4.3/cropper.min.css" integrity="sha256-d2pK8EVd0fI3O9Y+/PYWrCfAZ9hyNvInLoUuD7qmWC8=" crossorigin="anonymous"/>
    <style>
        img {
            max-width: 100%;
        }
    </style>
@endsection

@section('main-body-content')
    <div id="app_banner" class="row">
        <div class="col-12">
            <ul class="nav nav-pills mb-3 d-none" id="pills-tab" role="tablist" >
                @foreach($banner_types as $index => $banner_type)
                    <li class="nav-item @if($index === 0) active @endif">
                        <a class="nav-link @if($index === 0) active ml-3 @endif" id="pills-{{ $banner_type['type'] }}-tab" data-toggle="pill" href="#pills-part-{{ $banner_type['type'] }}"
                           role="tab" aria-controls="pills-part-{{ $banner_type['type'] }}" aria-expanded="true">{{ $banner_type['name'] }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="pills-tabContent">
                {{-- @foreach($banner_types as $index => $banner_type) --}}
                    {{-- <div class="tab-pane fade show @if($index === 0) active @endif" id="pills-part-{{ $banner_type['type'] }}" role="tabpanel" aria-labelledby="pills-part-{{ $banner_type['type'] }}"> --}}
                    <div>
                        <div class="col-12">
                            <button class="btn btn-primary-app mr-3" @click="showEditBannerModal()">Thêm mới</button>
                            <button class="btn btn-primary-app" @click="saveDisplayOrder(2)">Lưu thứ tự hiển thị</button>
                        </div>
                        <div class="col-12 mt-5">
                            <list-banner :banners="{{ json_encode($banners) }}" @edit-banner="function(banner) { showEditBannerModal({{ $banner_type['type'] }}, banner) }" ref="listBanner{{ $banner_type['type'] }}"></list-banner>
                        </div>
                    </div>
                {{-- @endforeach --}}
            </div>
        </div>
        <banner ref="banner"></banner>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.4.3/cropper.min.js" integrity="sha256-xqnUCb6f1p7h5IqwwOJ7kHsGm9bRUgsrUe3VQNuqzUE=" crossorigin="anonymous"></script>

    <script type="text/x-template" id="banner_modal">
        <div class="modal" tabindex="-1" role="dialog" ref="modalEl">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm banner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row">
                                <label for="banner_type" class="col-4 col-form-label">Loại banner</label>
                                <div class="col-4">
                                    <select class="custom-select" id="banner_type" v-model="bannerType">
                                        <option selected value="{{ \App\Enums\Banner\EBannerType::SHOW_AS_POP_UP_AFTER_LOG_IN }}">Flash Screen</option>
                                        <option value="{{ \App\Enums\Banner\EBannerType::MAIN_BANNER_ON_HOME_SCREEN }}">Home</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="banner_click_action" class="col-4 col-form-label">Hành động khi bấm vào banner</label>
                                <div class="col-4">
                                    <select class="custom-select" id="banner_click_action" v-model.number="bannerActionType">
                                        <option selected value="{{ \App\Enums\Banner\EBannerActionType::DO_NOTHING }}">Không làm gì cả!</option>
                                        <option value="{{ \App\Enums\Banner\EBannerActionType::OPEN_WEBSITE }}">Mở trang web</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" v-cloak v-if="bannerActionType === {{ \App\Enums\Banner\EBannerActionType::OPEN_WEBSITE }}">
                                <label for="open_url" class="col-4 col-form-label">Đường dẫn (URL) trang web</label>
                                <div class="col-4">
                                    <input type="text" class="form-control" id="open_url" placeholder="Đường dẫn trang web" v-model="urlToOpen">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Hình ảnh banner</label>
                                <div class="col-10">
                                    <div class="mb-3">
                                        <input type="file" accept="image/*" @change="onSelectImageHandler" ref="fileInputEl" class="form-control-file">
                                    </div>
                                    <div v-show="!!imageUrl">
                                        <div>
                                            <div class="form-group form-inline">
                                                <label class="col-form-label mr-3">Tỉ lệ</label>
                                                <div class="btn-group d-flex flex-nowrap" data-toggle="buttons">
                                                    <label class="btn btn-primary" @click="changeAspectCropRatio(1.7777777777777777)">
                                                        <input type="radio" class="sr-only" id="aspectRatio1" name="aspectRatio" value="1.7777777777777777">
                                                        <span class="docs-tooltip">16:9</span>
                                                    </label>
                                                    <label class="btn btn-primary active" @click="changeAspectCropRatio(1.3333333333333333)">
                                                        <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1.3333333333333333">
                                                        <span class="docs-tooltip">4:3</span>
                                                    </label>
                                                    <label class="btn btn-primary" @click="changeAspectCropRatio(1)">
                                                        <input type="radio" class="sr-only" id="aspectRatio3" name="aspectRatio" value="1">
                                                        <span class="docs-tooltip">1:1</span>
                                                    </label>
                                                    <label class="btn btn-primary" @click="changeAspectCropRatio(1.5)">
                                                        <input type="radio" class="sr-only" id="aspectRatio4" name="aspectRatio" value="1.5">
                                                        <span class="docs-tooltip">3:2</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <img :src="imageUrl" alt="selected banner" ref="bannerImgEl">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" @click.stop.prevent="saveBanner()">Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="banner_list">
        <div class='position-relative'>
            <table class="table table-striped table-hover table-bordered w-100 list_banner">
                <thead class="text-center blue-opacity">
                    <tr>
                        <th class="custom-view text-center" scope="col">STT</th>
                        <th class="custom-view text-center" scope="col">Banner</th>
                        <th class="custom-view text-center" scope="col">Hành động khi bấm vào banner</th>
                        <th class="custom-view text-center" scope="col">Loại banner</th>
                        <th class="custom-view text-center" scope="col">Thay đổi thứ tự hiển thị</th>
                        <th class="custom-view text-center" scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center" style="font-weight:bold" v-for="(item, index) in banners" :key="item.id">
                        <td class="custom-view text-center">@{{ index + 1 }}</td>
                        <td class="custom-view text-center">
                            <a :href="item.pathToResource" target="_blank">
                                <img :src="item.pathToResource" width="100" class="img-fluid">
                            </a>
                        </td>
                        <td class="custom-view text-center">
                            @{{ item.actionOnClick }}
                            <br>
                            <a :href="item.actionOnClickTarget" target="_blank" v-if="item.bannerActionType === {{ \App\Enums\Banner\EBannerActionType::OPEN_WEBSITE  }}">@{{ item.actionOnClickTarget }}</a>
                        </td>
                        <td class="custom-view text-left">
                                @{{ item.type }}
                        </td>
                        <td class="custom-view text-center">
                            <a title="Thay đổi thứ tự hiển thị banner" class="move_banner"><i class="fas fa-arrows-alt-v fa-2x"></i></a>
                        </td>
                        <td class="custom-view text-center">
                            <a title="Sửa banner" class="mr-3" @click.stop.prevent="editBanner(item)"><i data-toggle="tooltip" data-placement="top" title="Sửa banner" class="btn_edit fas fa-edit"></i></a>
                            <a title="Xóa banner" @click.stop.prevent="deleteBanner(item)"><i data-toggle="tooltip" data-placement="top" title="Xoá banner" class="btn_remove fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </script>

    <script type="text/javascript">
        @php
            include public_path('/js/config/banner.js');
        @endphp
    </script>
@endsection