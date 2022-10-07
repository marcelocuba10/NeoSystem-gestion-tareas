@extends('admin::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="titlemb-30">
            <h2>Detalle Vendedor</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/admin/sellers') }}">Agentes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Agentes</li>
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
            <form method="POST">
              @csrf
              <div class="row">
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Razón Social</label>
                    <input value="{{ $user->name ?? old('name') }}" readonly type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Nombre del Encargado</label>
                    <input value="{{ $user->seller_contact_1 ?? old('seller_contact_1') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Email</label>
                    <input value="{{ $user->email ?? old('email') }}" type="email" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Status</label>
                    @foreach ($status as $value)
                      @if ($value[0] == $user->status)
                        <input value="{{ $value[1] ?? old('status') }}" type="text"  readonly >
                      @endif
                    @endforeach 
                  </div>
                </div>
                <!-- end col -->
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Doc Identidad</label>
                    <input value="{{ $user->doc_id ?? old('doc_id') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Teléfono 1</label>
                    <input value="{{ $user->phone_1 ?? old('phone_1') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Teléfono 2</label>
                    <input value="{{ $user->phone_2 ?? old('phone_2') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Ciudad</label>
                    <input value="{{ $user->city ?? old('city') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-4">
                  <div class="select-style-1">
                    <label>(*) Departamento</label>
                    <div class="select-position bg-gray">
                      <select name="estate" disabled="true">
                        @foreach ($estates as $key)
                          <option {{ ( $key[1] == $userEstate) ? 'selected' : '' }}> {{ $key[1] }} </option>
                        @endforeach 
                      </select>
                    </div>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-8">
                  <div class="input-style-1">
                    <label>Dirección</label>
                    <input value="{{ $user->address ?? old('address') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->

                <div class="col-12">
                  <div class="button-group d-flex justify-content-center flex-wrap">
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/sellers') }}">Atrás</a>
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
