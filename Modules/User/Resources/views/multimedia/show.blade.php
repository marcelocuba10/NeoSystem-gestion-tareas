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
              @if ($multimedia->type == "Imágenes")
                <a class="thumbnail fancybox" rel="ligthbox" href="{{ asset('/public/images/files/'.$multimedia->filename) }}">
                  <img class="card-img-top" width="500" height="500" style="max-width: 100%;max-height: 100%;" src="{{ asset('/public/images/files/'.$multimedia->filename) }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                </a>
              @else
                <img class="card-img-top" width="500" height="500" style="max-width: 100%;max-height: 100%;" src="{{ asset('/public/images/image-docs-show.png') }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-style mb-30">
            <div class="row">
              <div class="col-5">
                <div class="input-style-1">
                  <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
                  <input value="{{ $multimedia->filename ?? old('filename') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Categoría</label>
                  <input value="{{ $multimedia->type ?? old('type') }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Tamaño</label>
                  <input value="{{ $file_size_format }}" type="text">
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Fecha de Creación</label>
                  <input value="{{ $created_at_format }}" type="text">
                </div>
              </div>
              <!-- end col -->  
              <div class="col-10">
                <div class="input-style-1">
                  <label>Descripción</label>
                  <textarea type="text" value="{{ $multimedia->description ?? old('description') }}" class="bg-transparent">{{ $multimedia->description ?? old('description') }}</textarea>
                </div>
              </div>
              <!-- end col -->      

              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <a href="{{ asset('/public/images/files/'.$multimedia->filename) }}" download="{{ $multimedia->filename }}"><button class="main-btn success-btn btn-hover m-2">Descargar</button></a>
                  <div class="button-group d-flex justify-content-center flex-wrap">
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
