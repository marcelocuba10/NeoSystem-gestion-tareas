@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Usuario</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="form-layout-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <form method="POST">
              @csrf
              <div class="row">
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Nombre</label>
                    <input type="text" value="{{ $user->name ?? old('name') }}" name="name" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Apellidos</label>
                    <input type="text" value="{{ $user->last_name ?? old('last_name') }}" name="last_name" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                    <div class="input-style-1">
                        <label>Email</label>
                        <input type="email" readonly value="{{ $user->email ?? old('email') }}" name="email">
                    </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Teléfono</label>
                    <input type="text" name="phone" value="{{ $user->phone ?? old('phone') }}" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Rol Asignado</label>
                    <input type="text" name="userRole" value="{{ $userRole ?? old('userRole') }}" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Doc Identidad</label>
                    <input type="text" name="doc_id" value="{{ $user->doc_id ?? old('doc_id') }}"readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-12">
                  <div class="input-style-1">
                    <label>Dirección</label>
                    <input type="text" name="address" value="{{ $user->address ?? old('address') }}" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-12">
                  <div class="button-group d-flex justify-content-center flex-wrap">
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/users') }}">Atrás</a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection  
