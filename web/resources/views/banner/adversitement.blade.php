@extends('layout.base')

@section('body-id', 'login-page')

@section('body-content')
    <div class="row mt-5 pt-3">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                <a class="tag-title-show" style="text-decoration: none;" href="{{route('banner-view')}}">QUẢN LÝ BANNER </a>
            </h4>
        </div>
    </div>
	<div class="row">
        <div class="set-row background-contact" style="width: 100%; min-height: 150px">
                This is page for adversitement banner
        </div>
	</div>
@endsection

@section('scripts')
    <script type="application/javascript">
        
	</script>
@endsection