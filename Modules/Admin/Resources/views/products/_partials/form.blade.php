@csrf
<div class="row">
  <div class="col-2">
    <div class="input-style-1">
      <label>Código</label>
      @if ($product)
        <input name="code" value="{{ $product->code ?? old('code') }}" type="text" readonly>
      @else
        <input name="code" value="{{ $code_product }}" type="text" readonly>
      @endif
    </div>
  </div>
  <!-- end col -->
  <div class="col-5">
    <div class="input-style-1">
      <label>(*) Nombre</label>
      <input name="name" value="{{ $product->name ?? old('name') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-5">
    <div class="input-style-1">
      <label>Descripción</label>
      <input name="description" value="{{ $product->description ?? old('description') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-4">
    <div class="input-style-1">
      <label>(*) Precio Compra</label>
      <input name="purchase_price" id="currency_1" value="{{ $product->purchase_price ?? old('purchase_price') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-4">
    <div class="input-style-1">
      <label>(*) Precio Venta</label>
      <input name="sale_price" id="currency_2" value="{{ $product->sale_price ?? old('sale_price') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-4">
    <div class="input-style-1">
      <label>(*) Stock</label>
      <input name="quantity" value="{{ $product->quantity ?? old('quantity') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-6">
    <div class="input-style-1">
      <label>Nombre del Proveedor</label>
      <input name="supplier" value="{{ $product->supplier ?? old('supplier') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-6">
    <div class="input-style-1">
      <label>Teléfono del Proveedor</label>
      <input name="phone_supplier" value="{{ $product->phone_supplier ?? old('phone_supplier') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-6">
    <div class="input-style-1">
      <label>Marca</label>
      <input name="brand" value="{{ $product->brand ?? old('brand') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  <div class="col-6">
    <div class="input-style-1">
      <label>Modelo</label>
      <input name="model" value="{{ $product->model ?? old('model') }}" type="text" class="bg-transparent">
    </div>
  </div>
  <!-- end col -->
  
  <div class="col-12">
    <div class="button-group d-flex justify-content-center flex-wrap">
      <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
      <a class="main-btn danger-btn-outline m-2" href="/admin/products">Atrás</a>
    </div>
  </div>
</div>
