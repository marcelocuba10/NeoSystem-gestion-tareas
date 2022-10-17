@extends('user::layouts.adminLTE.app')
@section('content')

<section class="table-components">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-8">
          <div class="title d-flex align-items-center flex-wrap mb-30">
            <h2 class="mr-40">Productos</h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="right">
            <div class="table-search d-flex st-input-search">
              <form action="{{ url('/user/products/search') }}">
                <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar producto..">
                <button type="submit"><i class="lni lni-search-alt"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- ========== tables-wrapper start ========== -->
    <div class="tables-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="left"></div>
              <div class="right"></div>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table top-selling-table table-hover">
                <thead>
                  <tr>
                    <th><h6>#</h6></th>
                    <th><h6>Producto</h6></th>
                    <th><h6>Descripción</h6></th>
                    <th><h6>Precio</h6></th>
                    <th><h6>Inventario</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product)
                  <tr>
                    <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                    <td>
                      <div class="product">
                        <div class="image">
                          @if ($product->filename)
                            <img src="{{ asset('/public/images/products/'.$product->filename) }}" alt="{{ Str::limit($product->filename, 15) }}">
                          @else
                            <img src="{{ asset('/public/adminLTE/images/products/no-image.jpg') }}" alt="{{ Str::limit($product->filename, 15) }}">
                          @endif
                        </div>
                        <p class="text-sm"><a href="{{ url('/user/products/show/'.$product->id) }}">{{ $product->name }}</a></p>
                      </div>
                    </td>
                    <td class="min-width"><p>{{ $product->description }}</p></td>
                    <td class="min-width"><p>G$ {{number_format($product->sale_price, 0)}}</p></td>
                    <td class="min-width">
                      @if ($product->inventory > 5)
                      <p><span class="status-btn info-btn">{{ $product->inventory }}</span></p>
                      @else
                        <p><span class="status-btn orange-btn">{{ $product->inventory }}</span></p>
                      @endif
                    </td>
                    <td class="text-right">
                      <div class="btn-group">
                        <div class="action">
                          <a href="{{ url('/user/products/show/'.$product->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                            <button class="text-active"><i class="lni lni-eye"></i></button>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @if (isset($search))
                  {!! $products-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
              @else
                  {!! $products-> links() !!}    
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection