<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui, shrink-to-fit=no"/>
        <meta name="description" content="page description"/>

        <title>Sửa xe 411</title>

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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha384-OHBBOqpYHNsIqQy8hL1U+8OXf9hH6QRxi0+EODezv82DfnZoV7qoHAZDwMwEJvSw" crossorigin="anonymous">
        {{--<link rel="stylesheet" href="/css/glyphicons/glyphicons.css" type="text/css"/>--}}
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css"/>
        <script src='https://www.google.com/recaptcha/api.js'></script>

        @section('stylesheet')
        @show
    </head>
    <body class="theme-primary d-flex flex-column">
    <div class="row mb-5">
        <div class="form-box col-12 m-auto">
            <div class="mx-auto px-sm-5 py-sm-3 my-sm-5">
                <form method="POST" action="{{ route('register', [], false) }}" class="formlogin" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group w-100 mb-3">
                        <label for="name" class="col-md-4 p-0 justify-content-start font-weight-bold">Name</label>
                        <div class="col-md-8 p-0 input-group">
                            <input id="name" type="text" class="form-control w-100 {{ $errors->has('name') ? ' is-invalid' : 'is-valid' }}"
                                   name="name" value="{{ old('name') }}" required>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group w-100 mb-3">
                        <label for="phone" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Phone</label>
                        <div class="col-md-8 p-0 input-group">
                            <input id="phone" type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : 'is-valid' }}"
                                   name="phone" required autofocus>
                            @if ($errors->has('phone'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group w-100 mb-3">
                        <label for="email" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Email</label>
                        <div class="col-md-8 p-0 input-group">
                            <input id="email" type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : 'is-valid' }}"
                                   name="email" required autofocus>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group w-100 mb-3">
                        <label for="password" class="col-md-4 p-0 justify-content-start font-weight-bold">Password</label>
                        <div class="col-md-8 p-0 input-group">
                            <input id="password" type="password" class="form-control w-100 {{ $errors->has('password') ? ' is-invalid' : 'is-valid' }}"
                                   name="password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group w-100 mb-3">
                        <label for="password-confirm" class="col-md-4 p-0 justify-content-start font-weight-bold">Confirm Password</label>
                        <div class="col-md-8 p-0">
                            <input id="password-confirm" type="password" class="form-control w-100"
                                   name="password_confirmation" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-app btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>
    @section('scripts')
    <script type="application/javascript">
    </script>
@endsection
<script type="text/javascript" src="https://cdn.jsdelivr.net/bluebird/latest/bluebird.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js" integrity="sha384-u/bQvRA/1bobcXlcEYpsEdFVK/vJs3+T+nXLsBYJthmdBuavHvAW6UsmqO2Gd/F9" crossorigin="anonymous"></script>
@show
</body>
</html>