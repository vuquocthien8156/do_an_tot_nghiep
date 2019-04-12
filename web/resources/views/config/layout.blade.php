@extends('layout.base')

@section('body-content')
    <div class="row mt-5 pt-3 pb-3">
        <div style="padding-left: 2rem">
            <h4 class="tag-page-custom">
                @php
                    $current_path = request()->path();
                @endphp
                <a @if(starts_with($current_path, \Illuminate\Support\Facades\Route::current()->uri)) class="tag-title-show" @else class="tag-title-disable" @endif style="text-decoration: none;" href="{{route('config-view-branch', [], false)}}"> QUẢN LÝ BANNER </a>
            </h4>
        </div>
    </div>

    @section('main-body-content')
    @show
@endsection