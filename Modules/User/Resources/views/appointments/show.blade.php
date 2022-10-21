@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle de la Agenda</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/appointments') }}">Agenda Visitas/Llamadas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle</li>
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
              <div class="col-6">
                <div class="input-style-1">
                  <label>Cliente</label>
                  <input type="text" value="{{ $appointment->customer_name ?? old('customer_name') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Fecha</label>
                  <input type="text" value="{{ date('d/m/Y', strtotime($appointment->date)) }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Hora</label>
                  <input type="text" value="{{ $appointment->hour ?? old('hour') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Acción</label>
                  <input type="text" value="{{ $appointment->action ?? old('action') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-9">
                <div class="input-style-1">
                  <label>Nota/Observación</label>
                  <textarea type="text" value="{{ $appointment->observation ?? old('observation') }}" readonly>{{ $appointment->observation ?? old('observation') }}</textarea>
                </div>
              </div>
              <!-- end col -->

              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/appointments') }}">Atrás</a>
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
