<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui, shrink-to-fit=no"/>
        <meta name="description" content="page description"/>

        <title>Sửa Xe 411</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- for ios 7 style, multi-resolution icon of 152x152 --}}
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
        <link rel="apple-touch-icon" href="/images/logo.png">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        {{-- for Chrome on Android, multi-resolution icon of 196x196 --}}
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="shortcut icon" sizes="196x196" href="/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha384-OHBBOqpYHNsIqQy8hL1U+8OXf9hH6QRxi0+EODezv82DfnZoV7qoHAZDwMwEJvSw" crossorigin="anonymous">
        {{--<link rel="stylesheet" href="/css/glyphicons/glyphicons.css" type="text/css"/>--}}
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css"/>
        <script src='https://www.google.com/recaptcha/api.js'></script>
       
        @section('stylesheet')
        @show
    </head>
    <body class="theme-primary d-flex flex-column @hasSection('body-class')@yield('body-class')@endif" @hasSection('body-id')id="@yield('body-id')"@endif @hasSection('body-data') @yield('body-data')@endif>
            @section('body-content')
            @show
        <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-database.js"></script>
        <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-storage.js"></script>
        <script type = "application/javascript">
            var app = firebase.initializeApp(JSON.parse('{!! json_encode(config('app.firebase')) !!}'));
            var database = firebase.database();
            var storage = firebase.storage();  
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js" integrity="sha384-u/bQvRA/1bobcXlcEYpsEdFVK/vJs3+T+nXLsBYJthmdBuavHvAW6UsmqO2Gd/F9" crossorigin="anonymous"></script>
        
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/styles/alert-bangtidy.min.css" />         --}}
        <script src="/js/lib/bootbox.min.js"></script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>         --}}
        <script src="{{ mix('/js/lib/prefixfree.min.js') }}"></script>
        <script src="{{ mix('/js/lib/notify.min.js') }}"></script>
        <script src="{{ mix('/js/ui.js') }}"></script>
        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="{{ mix('/js/common.js') }}"></script>
        @include('partial.base-js')
        @section('scripts')
        @show
    </body>
</html>
