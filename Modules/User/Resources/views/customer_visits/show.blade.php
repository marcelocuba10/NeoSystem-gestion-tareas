@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Visita Cliente - n.º {{ $customer_visit->visit_number }}</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/customer_visits') }}">Visitas Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Visita Cliente</li>
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
                  <label>Cliente</label>
                  <input type="text" value="{{ $customer_visit->customer_name ?? old('customer_name') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Fecha/Hora de Visita</label>
                  <input type="text" value="{{ $customer_visit->visit_date ?? old('visit_date') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Fecha Próxima Visita</label>
                  <input type="text" value="{{ $customer_visit->next_visit_date ?? old('next_visit_date') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-2">
                <div class="input-style-1">
                  <label>Hora Próxima Visita</label>
                    <input type="text" value="{{ $customer_visit->next_visit_hour ?? old('next_visit_hour') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-3">
                <div class="input-style-1">
                  <label>Acción</label>
                  <input type="text" value="{{ $customer_visit->action ?? old('action') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-4">
                <div class="input-style-1">
                  <label>Resultado de la Visita</label>
                  <textarea type="text" value="{{ $customer_visit->result_of_the_visit }}" readonly>{{ $customer_visit->result_of_the_visit }}</textarea>
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
                <div class="input-style-1">
                  <label>Objetivos</label>
                  <textarea type="text" value="{{ $customer_visit->objective ?? old('objective') }}" readonly>{{ $customer_visit->objective ?? old('objective') }}</textarea>
                </div>
              </div>
              <!-- end col -->

              @if ($customer_visit->type == 'Order')
                <h5 class="text-medium mb-20" >Detalles del Presupuesto</h5>

                <div class="table-responsive">
                  <table class="invoice-table table">
                    <thead style="background-color: #DAEFFE;">
                      <tr>
                        <th class="qty">
                          <h6 class="text-sm text-medium">Cod</h6>
                        </th>
                        <th class="service">
                          <h6 class="text-sm text-medium">Producto</h6>
                        </th>
                        <th class="price">
                          <h6 class="text-sm text-medium">Precio</h6>
                        </th>
                        <th class="qty">
                          <h6 class="text-sm text-medium">Cantidad</h6>
                        </th>
                        <th class="amount">
                          <h6 class="text-sm text-medium">SubTotal</h6>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($order_details as $item_order)
                        <tr>
                          <td><p class="text-sm">{{ $item_order->code }}</td>
                          <td><p class="text-sm">{{ $item_order->name }}</p></td>
                          <td><p class="text-sm">G$ {{number_format($item_order->price, 0)}}</p></td>
                          <td><p class="text-sm">{{ $item_order->quantity }}</p></td>
                          <td><p class="text-sm">G$ {{number_format($item_order->amount, 0)}}</p></td>
                        </tr>
                      @endforeach
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <h4>Total</h4>
                        </td>
                        <td>
                          <h4>G$ {{number_format($total_order, 0)}}</h4>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              @else
                <div class="title mb-30">
                  <hr>
                  <h6>Sin presupuesto.</h6>
                  <hr>
                </div>
              @endif

              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
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
