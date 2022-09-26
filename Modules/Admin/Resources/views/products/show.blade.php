@extends('admin::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title mb-30">
            <h2>Detalle Producto</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/admin/products') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Producto</li>
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
              <div class="row">
                <div class="col-2">
                  <div class="input-style-1">
                    <label>Código</label>
                    <input value="{{ $product->code ?? old('code') }}" type="text" readonly>
                  </div>
                </div>
                <!-- end col -->
                <div class="col-5">
                  <div class="input-style-1">
                    <label>(*) Nombre</label>
                    <input value="{{ $product->name ?? old('name') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-5">
                  <div class="input-style-1">
                    <label>Descripción</label>
                    <input value="{{ $product->description ?? old('description') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-4">
                  <div class="input-style-1">
                    <label>(*) Precio Compra</label>
                    <input id="currency_1" value="{{number_format($product->purchase_price, 0)}}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-4">
                  <div class="input-style-1">
                    <label>(*) Precio Venta</label>
                    <input id="currency_2" value="{{number_format($product->sale_price, 0)}}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-4">
                  <div class="input-style-1">
                    <label>(*) Stock</label>
                    <input value="{{ $product->quantity ?? old('quantity') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Nombre del Proveedor</label>
                    <input value="{{ $product->supplier ?? old('supplier') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Teléfono del Proveedor</label>
                    <input value="{{ $product->phone_supplier ?? old('phone_supplier') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Marca</label>
                    <input value="{{ $product->brand ?? old('brand') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Modelo</label>
                    <input value="{{ $product->model ?? old('model') }}" type="text">
                  </div>
                </div>
                <!-- end col -->
          
              <div class="col-12">
                <div class="button-groupd-flexjustify-content-centerflex-wrap">
                  <a class="main-btn danger-btn-outline m-2" href="{{ url('/admin/products') }}">Atrás</a>
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
