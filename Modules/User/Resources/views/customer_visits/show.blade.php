@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Visita Cliente</h2>
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
              <div class="col-2">
                <div class="input-style-1">
                  <label>Estado</label>
                  <input type="text" value="{{ $customer_visit->status ?? old('status') }}" readonly>
                </div>
              </div>
              <!-- end col -->
              <div class="col-5">
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

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #3f51b566;">
                    <tr>
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
                        <h6 class="text-sm text-medium">Amounts</h6>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <p class="text-sm">Admin Dashboard</p>
                      </td>
                      <td>
                        <p class="text-sm">
                          Design and Development Service
                        </p>
                      </td>
                      <td>
                        <p class="text-sm">3</p>
                      </td>
                      <td>
                        <p class="text-sm">$700</p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="text-sm">Landing Page</p>
                      </td>
                      <td>
                        <p class="text-sm">
                          Landing Page Ui kit design and Development
                        </p>
                      </td>
                      <td>
                        <p class="text-sm">1</p>
                      </td>
                      <td>
                        <p class="text-sm">$1000</p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="text-sm">Web design</p>
                      </td>
                      <td>
                        <p class="text-sm">
                          Web Design and Development and Seo
                        </p>
                      </td>
                      <td>
                        <p class="text-sm">2</p>
                      </td>
                      <td>
                        <p class="text-sm">$4000</p>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>
                        <h6 class="text-sm text-medium">Subtotal</h6>
                      </td>
                      <td>
                        <h6 class="text-sm text-bold">$5700</h6>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>
                        <h6 class="text-sm text-medium">Discount</h6>
                      </td>
                      <td>
                        <h6 class="text-sm text-bold">45%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>
                        <h6 class="text-sm text-medium">Shipping Charge</h6>
                      </td>
                      <td>
                        <h6 class="text-sm text-bold">Free</h6>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>
                        <h4>Total</h4>
                      </td>
                      <td>
                        <h4>$3135</h4>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
          
              <div class="col-12">
                <div class="button-groupd-flexjustify-content-centerflex-wrap">
                  <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
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
