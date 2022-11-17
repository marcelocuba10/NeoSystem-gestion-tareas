@csrf
<div class="card-style">
  <div class="row">
    <div class="col-3">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Código</label>
          <input name="custom_code" value="{{ $product->custom_code ?? old('custom_code') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-9">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
        <input name="name" value="{{ $product->name ?? old('name') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Precio Agente</label>
        <input name="purchase_price" id="currency_1" value="{{ $product->purchase_price ?? old('purchase_price') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Precio Venta Público</label>
        <input name="sale_price" id="currency_2" value="{{ $product->sale_price ?? old('sale_price') }}" type="text" class="bg-transparent">
      </div>
    </div>
    {{-- <div class="col-4">
      <div class="input-style-1">
        <label>(*) Inventario</label>
        <input name="inventory" value="{{ $product->inventory ?? old('inventory') }}" type="text" class="bg-transparent">
      </div>
    </div> --}}
    <div class="col-4">
      <div class="input-style-1">
        <label>Marca</label>
        <input name="brand" value="{{ $product->brand ?? old('brand') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label>Modelo</label>
        <input name="model" value="{{ $product->model ?? old('model') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label>Nombre del Proveedor</label>
        <input name="supplier" value="{{ $product->supplier ?? old('supplier') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label>Teléfono del Proveedor</label>
        <input name="phone_supplier" value="{{ $product->phone_supplier ?? old('phone_supplier') }}" type="text" class="bg-transparent">
      </div>
    </div>
  
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/products') }}">Atrás</a>
      </div>
    </div>
  </div>
</div>

<!-- ========= Scripts ======== -->
<!-- ========= disable button after send form ======== -->
<script>
  $(document).ready(function(){
    $('form').submit(function (event) {
      var btn_submit = document.getElementById('btn_submit');
      btn_submit.disabled = true;
      btn_submit.innerText = 'Procesando...'
    });
  })
</script>

{{-- <div class="row">
  <div class="title-wrapper pt-30">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="title d-flex align-items-center flex-wrap mb-30">
          <h2 class="mr-40">Imágenes del Producto</h2>
          @if ($product)
            <a href="{{ url('/admin/products/image-gallery/'.$product->id) }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-pencil mr-5"></i></a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div> --}}

{{-- <div class="row">
  <div class="col-md-12">
    <div class="card-style mb-30">
      @if ($images)
        <div class="row">
          @foreach($images as $image)
          <div class="col-md-4">
            <div class="card">
              <a class="thumbnail fancybox" rel="ligthbox" href="{{ asset('/public/images/products/'.$image->filename) }}">
                <img class="card-img-top"  width="350" height="350" style="max-width: 100%;max-height: 100%;" src="{{ asset('/public/images/products/'.$image->filename) }}" alt="{{ Str::limit($image->filename, 15) }}">
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <h5 class="text-medium">Guarde el producto para luego cargar las imágenes.</h5>
      @endif
    </div>
  </div>
</div> --}}
