<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="shortcut icon" href="{{ asset('/public/adminLTE/images/favicon.png') }}" type="image/x-icon" />

        <title>{{ config('app.name')}} - Login</title>

        <link href="{{ asset('/public/adminLTE/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/public/css/login_register.css') }}" rel="stylesheet">
    </head>
    <body>
        @yield('content')
    </body>
</html>
