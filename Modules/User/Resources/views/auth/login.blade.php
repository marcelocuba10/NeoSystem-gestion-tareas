@extends('user::layouts.auth.auth-master')

@section('content')

<div class="container">
    <div class="left"></div>
    <div class="right">
        <div class="ergts" style="display: flex;">
            <a href="{{ url('/') }}"><button class="ththhf" type="button" class="btn btn-block create-account">Página Web</button></a>
        </div>    
        <div class="login-texto off-mobile">
            <p class="login-title">Bienvenido a {{ config('app.name') }}</p>
            <p class="login-message">Gestión Tareas</p>
        </div>
    </div>    
</div> 

<div class="registration-form">
    
    <form method="post" action="{{ url('/user/login') }}">

        <div class="form-icon"><img class="img-logo" src="{{ asset('/public/adminLTE/images/logo/logo-pyp.png') }}"></div>
        <p class="login-message2">Iniciar Sesión</p>
        
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

        <div class="form-group">
            <input name="email" value="{{ old('email') }}" type="text" class="form-control item" placeholder="Email" required>
        </div>

        <div class="form-group">
            <input name="password" value="{{ old('password') }}" type="password" class="form-control item" placeholder="Contraseña" required>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-check checkbox-style mb-30">
                  <input class="form-check-input" type="checkbox" name="remember" value="1" id="checkbox-remember">
                  <label class="form-check-label" for="remember"><small>Recordarme</small></label>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="text-md-end text-lg-end text-xxl-end mb-30">
                  <a href="{{ url('/user/forget-password') }} " style="color: #212529;" class="hover-underline">
                    <small>¿Has olvidado tu contraseña?</small>
                  </a>
                </div>
            </div>
        </div>

        <div class="form-group" style="text-align: center;">
            <button style="padding: 10px 50px;" type="submit" class="btn btn-block create-account">Ingresar</button>
        </div>
        <div class="text-muted text-center" style="margin-top: 20px">
            <small>¿No tenes una cuenta? <a class="footer-link-login" style="color: #212529;" href="{{ url('/user/register') }}">Registrate Acá</a></small>
        </div>
    </form>
</div>

@endsection