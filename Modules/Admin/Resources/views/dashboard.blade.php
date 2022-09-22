@extends('admin::layouts.adminLTE.app')
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
              <h6 class="mb-10">Total Vendedores</h6>
              <h3 class="text-bold mb-10">{{ $cant_sellers }}</h3>
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
              <h3 class="text-bold mb-10">{{$cant_products}}</h3>
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
              <h6 class="mb-10">Teste teste</h6>
              <h3 class="text-bold mb-10">121212</h3>
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
              <h6 class="mb-10">Total Máquinas Pool</h6>
              <h3 class="text-bold mb-10">$24,567</h3>
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
                <h6 class="text-medium mb-2">Agentes Registrados</h6>
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
                      <h6 class="text-sm fw-500">ID referencia</h6>
                    </th>
                    <th class="text-end">
                      <h6 class="text-sm fw-500">Status</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sellers as $seller)
                    <tr>
                        <td><p class="text-sm fw-500 text-gray"><a href="/admin/sellers/show/{{$seller->id}}">{{ $seller->name ?? old('name') }} {{ $seller->last_name ?? old('last_name') }}</a></p></td>
                        <td><p class="text-sm fw-500 text-gray">{{ $seller->idReference }}</p></td>
                        <td><p class="text-sm fw-500 text-gray text-end">{{ $seller->status }}</p></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <a href="/admin/sellers"><p class="text-sm mb-20">Ver más..</p></a>
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
                      <td><p class="text-sm fw-500 text-gray"><a href="/admin/products/show/{{$product->id}}">{{ $product->name ?? old('name') }}</a></p></td>
                      <td><p class="text-sm fw-500 text-gray">{{ $product->quantity }}</p></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <a href="/admin/products"><p class="text-sm mb-20">Ver más..</p></a>
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