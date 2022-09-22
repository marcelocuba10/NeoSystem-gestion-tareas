@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Bienvenido a {{ config('app.name') }}</h2>
            </div>
          </div>
          <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- ========== title-wrapper end ========== -->
      <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon purple">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Clientes</h6>
              <h3 class="text-bold mb-10">{{ $cant_customers }}</h3>
            </div>
          </div>
          <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon orange">
              <i class="lni lni-user"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Productos</h6>
              <h3 class="text-bold mb-10">{{ $cant_products }}</h3>
            </div>
          </div>
          <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon success">
              <i class="lni lni-graph"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Ventas</h6>
              <h3 class="text-bold mb-10">121.212</h3>
            </div>
          </div>
          <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon primary">
              <i class="lni lni-credit-cards"></i>
            </div>
            <div class="content">
              {{-- @php
                use Modules\User\Entities\Machines;
                $cant_machines = Machines::count(); 
              @endphp --}}
              <h6 class="mb-10">Visitas Pendientes</h6>
              <h3 class="text-bold mb-10">4</h3>
            </div>
          </div>
          <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
      </div>
      <!-- End Row -->
      <div class="row">
        <div class="col-md-6 col-lg-3 col-xl-6 col-xxl-3">
          <div class="card-style mb-30">
            <div class="title d-flex flex-wrap align-items-center justify-content-between mb-10">
              <div class="left">
                <h6 class="text-medium mb-2">Clientes Registrados</h6>
              </div>
              <div class="right mb-2">
              </div>
            </div>
            <!-- End Title -->

            <div class="table-responsive">
              <table class="table sell-order-table">
                <thead>
                  <tr>
                    <th>
                      <h6 class="text-sm fw-500">Nombre</h6>
                    </th>
                    <th>
                      <h6 class="text-sm fw-500">Doc Identidad</h6>
                    </th>
                    <th class="text-end">
                      <h6 class="text-sm fw-500">Teléfono</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($customers as $customer)
                    <tr>
                        <td><p class="text-sm fw-500 text-gray"><a href="/user/customers/show/{{$customer->id}}">{{ $customer->name ?? old('name') }} {{ $customer->last_name ?? old('last_name') }}</a></p></td>
                        <td><p class="text-sm fw-500 text-gray">{{ $customer->doc_id }}</p></td>
                        <td><p class="text-sm fw-500 text-gray text-end">{{ $customer->phone }}</p></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <a href="/user/customers"><p class="text-sm mb-20">Ver más..</p></a>
            </div>
          </div>
        </div>
        <!-- End Col -->
        <div class="col-md-6 col-lg-3 col-xl-6 col-xxl-3">
          <div class="card-style mb-30">
            <div class="title d-flex flex-wrap align-items-center justify-content-between mb-10">
              <div class="left">
                <h6 class="text-medium mb-2">Productos Registrados</h6>
              </div>
              <div class="right mb-2">
              </div>
            </div>
            <!-- End Title -->

            <div class="table-responsive">
              <table class="table sell-order-table">
                <thead>
                  <tr>
                    <th>
                      <h6 class="text-sm fw-500">Nombre</h6>
                    </th>
                    <th>
                      <h6 class="text-sm fw-500">Stock</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product)
                    <tr>
                      <td><p class="text-sm fw-500 text-gray"><a href="/user/products/show/{{$product->id}}">{{ $product->name ?? old('name') }}</a></p></td>
                      <td><p class="text-sm fw-500 text-gray">{{ $product->quantity }}</p></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <a href="/user/products"><p class="text-sm mb-20">Ver más..</p></a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-xl-12 col-xxl-6">
          <div class="card-style calendar-card mb-30">
            <div id="calendar-mini"></div>
          </div>
        </div>
        <!-- End Col -->
      </div>
      <!-- End Row -->

    </div>
    <!-- end container -->
</section>
@endsection