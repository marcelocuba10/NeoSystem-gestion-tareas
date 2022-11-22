@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle {{ $sale->type }} - n.º {{ $sale->invoice_number }}</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/user/sales') }}">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle {{ $sale->type }}</li>
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
              @if (!$sale->visit_id)

                <div class="col-5">
                  <div class="input-style-1">
                    <label>Cliente</label>
                    <input type="text" value="{{ $sale->customer_name ?? old('customer_name') }}" readonly>
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Fecha/Hora</label>
                    <input type="text" value="{{ date('d/m/Y - H:i', strtotime($sale->sale_date)) }}" readonly>
                  </div>
                </div>
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Tipo</label>
                    <input type="text" value="{{ $sale->type }}" readonly>
                  </div>
                </div>
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Estado</label>
                    <input type="text" value="{{ $sale->status }}" readonly>
                  </div>
                </div>

              @else

                <div class="col-5">
                  <div class="input-style-1">
                    <label>Cliente</label>
                    <input type="text" value="{{ $sale->customer_name ?? old('customer_name') }}" readonly>
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Fecha/Hora</label>
                    <input type="text" value="{{ date('d/m/Y - H:i', strtotime($sale->visit_date)) }}" readonly>
                  </div>
                </div>
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Fecha Próxima Visita</label>
                    <input type="text" value="{{ $sale->next_visit_date ?? old('next_visit_date') }}" readonly>
                  </div>
                </div>
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Hora Próxima Visita</label>
                      <input type="text" value="{{ $sale->next_visit_hour ?? old('next_visit_hour') }}" readonly>
                  </div>
                </div>
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Estado</label>
                    <input type="text" value="{{ $sale->status ?? old('status') }}" readonly>
                  </div>
                </div>
                <div class="col-5">
                  <div class="input-style-1">
                    <label>Resultado de la Visita</label>
                    <textarea type="text" value="{{ $sale->result_of_the_visit }}" readonly>{{ $sale->result_of_the_visit }}</textarea>
                  </div>
                </div>
                <div class="col-5">
                  <div class="input-style-1">
                    <label>Objetivos</label>
                    <textarea type="text" value="{{ $sale->objective ?? old('objective') }}" readonly>{{ $sale->objective ?? old('objective') }}</textarea>
                  </div>
                </div>

              @endif
              
              <h5 class="text-medium mb-20" >Detalles {{ $sale->type }}</h5>
              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #DAEFFE;">
                    <tr>
                      <th>
                        <h6 class="text-sm text-medium">Cod</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Producto</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Precio</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Cantidad</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">SubTotal</h6>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($order_detail as $item_order)
                      <tr>
                        <td><p class="text-sm">{{ $item_order->custom_code }}</td>
                        <td><p class="text-sm" data-toggle="tooltip" data-placement="bottom" title="{{ $item_order->name }}">{{ Str::limit($item_order->name, 65) }}</p></td>
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
          
              <div class="col-12">
                <div class="button-group d-flex justify-content-center flex-wrap">
                  <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/sales') }}">Atrás</a>
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
