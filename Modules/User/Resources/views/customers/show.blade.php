@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Información del Cliente</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/customers') }}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Cliente</li>
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
                  <label>Razón Social</label>
                  <input type="text" value="{{ $customer->name ?? old('name') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Doc Identidad</label>
                  <input type="text" value="{{ $customer->doc_id ?? old('doc_id') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Teléfono</label>
                  <input type="text" value="{{ $customer->phone ?? old('phone') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-6">
                <div class="input-style-1">
                  <label>Email</label>
                  <input type="text" value="{{ $customer->email ?? old('email') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Rubro</label>
                  <div class="select-position">
                    <select class="select2-multiple_1" multiple="multiple"  disabled="true">
                      @foreach ($categories as $item)
                        <option value="{{ $item->id }}" @if(!empty($customerCategories)) {{ in_array($item->id,$customerCategories)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
                      @endforeach 
                    </select>
                  </div>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Equipos Potenciales</label>
                  <div class="select-position">
                    <select class="select2-multiple_2" multiple="multiple"  disabled="true">
                      @foreach ($potential_products as $item)
                        <option value="{{ $item->id }}" @if(!empty($customerPotentialProducts)) {{ in_array($item->id,$customerPotentialProducts)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
                      @endforeach 
                    </select>
                  </div>
                </div>
              </div>
              <!-- end col -->
              <div class="col-2">
                <div class="input-style-1">
                  <label>Cantidad de Unidades</label>
                  <input type="number" min="0" value="{{ $customer->unit_quantity ?? old('unit_quantity') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Resultado de la Visita</label>
                  <textarea type="text" value="{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}" readonly>{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Objetivos</label>
                  <textarea type="text" value="{{ $customer->objective ?? old('objective') }}" readonly>{{ $customer->objective ?? old('objective') }}</textarea>
                </div>
              </div>
              <!-- end col -->
              <div class="col-4">
                <div class="input-style-1">
                  <label>Fecha Próxima Visita</label>
                  <input type="date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer->next_visit_date ?? old('next_visit_date') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-4">
                <div class="input-style-1">
                  <label>Hora Próxima Visita</label>
                    <input type="time" value="{{ $customer->next_visit_hour ?? old('next_visit_hour') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-4">
                <div class="form-check checkbox-style mb-30" style="margin-top: 40px;">
                  <input @if(!empty($customer->is_vigia)) {{ $customer->is_vigia = 'on'  ? 'checked' : '' }} @endif class="form-check-input" type="checkbox" id="checkbox-not-robot" checked onclick="return false;">
                  <label class="form-check-label" for="checkbox-not-robot" >¿Es Cliente Vigia?</label>
                </div>
              </div>
              <!-- end col -->
              <div class="col-6">
                <div class="input-style-1">
                  <label>Ciudad</label>
                  <input type="text" value="{{ $customer->city ?? old('city') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-6">
                <div class="input-style-1">
                  <label>Departamento</label>
                  <input type="text" value="{{ $customer->estate ?? old('estate') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-12">
                <div class="input-style-1">
                  <label>Dirección</label>
                  <input type="text" value="{{ $customer->address ?? old('address') }}" readonly>
                </div>
              </div>
              <!-- end col -->
          
              <div class="col-12">
                <div class="button-groupd-flexjustify-content-centerflex-wrap">
                  <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/customers') }}">Atrás</a>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection 
