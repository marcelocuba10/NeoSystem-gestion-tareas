@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Parámetro</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/parameters') }}">Parámetros</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Parámetro</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== title-wrapper end ========== -->
    <div class="form-layout-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <div class="row">
              <div class="col-4">
                <div class="input-style-1">
                  <label>Nombre</label>
                  <input type="text" value="{{ $parameter->name ?? old('name') }}">
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Tipo de Parámetro</label>
                  <input type="text" value="{{ $parameter->type ?? old('type') }}">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Descripción</label>
                  <textarea type="text" value="{{ $parameter->description ?? old('description') }}"></textarea>
                </div>
              </div>
              <!-- end col -->
          
              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/parameters') }}">Atrás</a>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    </div>
  </div>
</section>

@endsection 
