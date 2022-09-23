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

  <h3>Laravel - Image Gallery CRUD Example</h3>
  {{-- <form action="/admin/products/image-gallery" class="form-image-upload" method="POST" enctype="multipart/form-data">
      {!! csrf_field() !!}
      @if (count($errors) > 0)
          <div class="alert alert-danger">
              <strong>Whoops!</strong> There were some problems with your input.<br><br>
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif

      @if ($message = Session::get('success'))
      <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
      </div>
      @endif

      <div class="row">
          <div class="col-md-5">
              <strong>Image:</strong>
              <input type="file" name="image" class="form-control">
          </div>
          <div class="col-md-2">
              <br/>
              <button type="submit" class="btn btn-success">Upload</button>
          </div>
      </div>
  </form>  --}}

  {{-- <form method="post" action="/admin/products/image-gallery" enctype="multipart/form-data"> --}}
    {{-- {{csrf_field()}} --}}
    <div class="input-group control-group increment" >
      <input type="file" name="image[]" class="form-control">
      <div class="input-group-btn"> 
        <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
      </div>
    </div>
    <div class="clone hide">
      <div class="control-group input-group" style="margin-top:10px">
        <input type="file" name="image[]" class="form-control">
        <div class="input-group-btn"> 
          <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
        </div>
      </div>
    </div>
    {{-- <button type="submit" class="btn btn-primary" style="margin-top:10px">Submit</button> --}}
  {{-- </form>     --}}

  {{-- <div class="row">
    <div class='list-group gallery'>
        @if($images->count())
            @foreach($images as $image)
            <div class='col-sm-4 col-xs-6 col-md-3 col-lg-3'>
                <a class="thumbnail fancybox" rel="ligthbox" href="/images/{{ $image->image }}">
                    <img class="img-responsive" alt="" src="/images/products/{{ $image->image }}" />
                    <div class='text-center'>
                        <small class='text-muted'>{{ $image->title }}</small>
                    </div> 
                </a>
                <form action="/admin/products/image-gallery/{{ $image->id }}" method="POST">
                <input type="hidden" name="_method" value="delete">
                {!! csrf_field() !!}
                <button type="submit" class="close-icon btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
                </form>
            </div> 
            @endforeach
        @endif
    </div> 
  </div>  --}}
  
  <div class="col-12">
    <div class="button-group d-flex justify-content-center flex-wrap">
      <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
      <a class="main-btn danger-btn-outline m-2" href="/admin/products">Atrás</a>
    </div>
  </div>
</div>

<script type="text/javascript">


  $(document).ready(function() {

    $(".btn-success").click(function(){ 
        var html = $(".clone").html();
        $(".increment").after(html);
    });

    $("body").on("click",".btn-danger",function(){ 
        $(this).parents(".control-group").remove();
    });

  });

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
