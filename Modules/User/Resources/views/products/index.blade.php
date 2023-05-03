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
            <a style="margin-left: 17px;" href="{{ url('/user/multimedia') }}"><i class="hthtg lni lni-library" data-toggle="tooltip" data-placement="bottom" title="Descargar Lista de Precios"></i></a>
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
            <div class="table-wrapper table-responsive">
              <table class="table top-selling-table table-hover">
                <thead>
                  <tr>
                    <th><h6>Código</h6></th>
                    <th><h6>Producto</h6></th>
                    <th><h6>Precio Agente</h6></th>
                    <th><h6>Precio Público</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product)
                  <tr>
                    <td class="min-width"><h6 class="text-sm"><a href="{{ url('/user/products/show/'.$product->id) }}">{{ $product->custom_code }}</a></h6></td>
                    <td class="min-width"><p class="text-sm"><a href="{{ url('/user/products/show/'.$product->id) }}">{{ Str::limit($product->name, 75) }}</a></p></td>
                    <td class="min-width"><p>G$ {{number_format($product->purchase_price, 0,",",".")}}</p></td>
                    <td class="min-width"><p>G$ {{number_format($product->sale_price, 0,",",".")}}</p></td>
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