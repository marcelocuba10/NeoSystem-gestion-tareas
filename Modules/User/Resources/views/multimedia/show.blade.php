@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Archivo</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/multimedia') }}">Multimedia</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Archivo</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== title-wrapper end ========== -->
    <div class="form-layout-wrapper">
      <div class="row">
        <div class="col-lg-6">
          <div class="card-style mb-30">
            <div class="row">

                <div class="card">
                  <a class="thumbnail fancybox" rel="ligthbox" href="{{ asset('/public/images/products/'.$multimedia->filename) }}">
                    <img class="card-img-top" width="500" height="500" style="max-width: 100%;max-height: 100%;" src="{{ asset('/public/images/products/'.$multimedia->filename) }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                  </a>
                </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-style mb-30">
            <div class="row">
              <div class="col-5">
                <div class="input-style-1">
                  <label>Nombre</label>
                  <input value="{{ $multimedia->filename ?? old('filename') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Descripción</label>
                  <input value="{{ $multimedia->description ?? old('description') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Categoría</label>
                  <input value="{{ $multimedia->type ?? old('type') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Tamaño</label>
                  <input value="{{ $multimedia->size ?? old('size') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Fecha de Creación</label>
                  <input value="{{ $multimedia->created_at ?? old('created_at') }}" type="text">
                </div>
              </div>
              <!-- end col -->        

              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <button class="main-btn success-btn btn-hover m-2">Descargar</button>
                  <div class="button-groupd-flexjustify-content-center flex-wrap">
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/multimedia') }}">Atrás</a>
                  </div>
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
