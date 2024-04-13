@extends('user::layouts.auth.auth-master')

@section('content')

    <div class="container">
        <div class="left"></div>
        <div style="right">
            <div class="rwedrt">
                <a href="{{ url('/user/login/') }}"><button class="ththhf" type="button" class="btn btn-block create-account">Iniciar Sesión</button></a>
                <a href="{{ url('/') }}"><button class="ththhf" type="button" class="btn btn-block create-account">Página Web</button></a>
            </div>
            <div class="register-texto">
                <p class="login-message">Facilidades increíbles para tu empresa! :)</p>
            </div>
        </div>
    </div>

    <div class="registration-form-2">
        
        <form class="m-t" role="form" method="post" action="#">
            <div class="form-icon">
                <img class="img-logo" src="{{ asset('/public/adminLTE/images/logo/logo-pyp.png') }}">
            </div>
            <br>
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif
            @if ($errors->has('name'))
                <div class="alert alert-warning" role="alert">
                    {{ $errors->first('name') }}
                </div>
            @endif
            @if ($errors->has('email'))
                <div class="alert alert-warning" role="alert">
                    {{ $errors->first('email') }}
                </div>
            @endif
            @if ($errors->has('password'))
                <div class="alert alert-warning" role="alert">
                    {{ $errors->first('password') }}
                </div>
            @endif
            @if ($errors->has('terms'))
                <div class="alert alert-warning" role="alert">
                    {{ $errors->first('terms') }}
                </div>
            @endif

            <div class="form-group">
                <input name="name" value="{{ old('name') }}" type="text" class="form-control item" placeholder="Nombre Completo" required>
            </div>
            <div class="form-group">
                <input name="email" value="{{ old('email') }}" type="email" class="form-control item" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input name="password" value="{{ old('password') }}" type="password" class="form-control item" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <div class="checkbox i-checks"><label> <input name="terms" value="1" style="margin-right: 10px;" type="checkbox"><i></i><small>He leído y acepto los <a class="sdfdd" href="#" target="_blank">términos</a> y <a class="sdfdd" href="#" target="_blank">condiciones de uso</a>. </small></label></div>
            </div>

            <div class="form-group" style="text-align: center;">
                <button style="padding: 10px 50px;" type="submit" class="btn btn-block create-account">Registrarse</button>
            </div>
            <div class="text-muted text-center" style="margin-top: 20px">
                <small>¿Ya tienes una cuenta? <a class="footer-link-login" style="color: #212529;" href="{{ url('/user/login') }}">Iniciar Sesión</a></small>
            </div>
        </form>
    </div>
    
@endsection

