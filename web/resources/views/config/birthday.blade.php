@extends('layout.base')

@section('body-content')
    <div id="birthday_config">
        <div class="row mt-5 pt-3 mb-4">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="{{route('config-view-birthday', [], false)}}">THÔNG BÁO SINH NHẬT</a> / 
                    <a class="tag-title-disable" style="text-decoration: none;" href="{{route('config-view-bank-tranfer', [], false)}}">TÀI KHOẢN NGÂN HÀNG</a>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 45rem;">
                    <div class="form-inline">
                        <div class="form-group w-100 mb-3" >
                            <label for="content-notification" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Nội dung thông báo</label>
                            <div class="col-md-8 p-0 input-group">
                                <textarea class="form-control bg-white" name="content_notification" id="content_notification" cols="10" rows="15" required>{{$contentBirthday[0]->text_value}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <button class="button-app" @click="saveContentBirthday" style="margin: 15px 0 10px 235px">Lưu</button>
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
            include public_path('/js/setting/birthday/birthday.js');
        @endphp
    </script>
@endsection