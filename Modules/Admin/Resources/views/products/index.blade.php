@extends('admin::layouts.adminLTE.app')
@section('content')

<section class="table-components">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-8">
          <div class="title d-flex align-items-center flex-wrap mb-30">
            <h2 class="mr-40">Productos</h2>
            @can('product-sa-create')
              <a href="/admin/products/create" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
            @endcan  
          </div>
        </div>
        <!-- end col -->
        <div class="col-md-4">
          <div class="right">
            <div class="table-search d-flex st-input-search">
              <form action="/admin/products/search">
                <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar producto..">
                <button type="submit"><i class="lni lni-search-alt"></i></button>
              </form>
            </div>
          </div>
        </div>
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- ========== tables-wrapper start ========== -->
    <div class="tables-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="left">
              </div>
              <div class="right">
              </div>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th><h6>#</h6></th>
                    <th><h6>Nombre</h6></th>
                    <th><h6>Descripción</h6></th>
                    <th><h6>Precio Venta</h6></th>
                    <th><h6>Stock</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                  <!-- end table row-->
                </thead>
                <tbody>
                  @foreach ($products as $product)
                  <tr>
                    <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                    <td class="min-width"><h5 class="text-bold text-dark"><a href="/admin/products/show/{{$product->id}}">{{ $product->name }}</a></h5></td>
                    <td class="min-width"><p>{{ $product->description }}</p></td>
                    <td class="min-width"><p>G$ {{number_format($product->sale_price, 0)}}</p></td>
                    <td class="min-width">
                      @if ($product->quantity > 5)
                      <p><span class="status-btn info-btn">{{ $product->quantity }}</span></p>
                      @else
                        <p><span class="status-btn orange-btn">{{ $product->quantity }}</span></p>
                      @endif
                    </td>
                    <td class="text-right">
                      <div class="btn-group">
                        <div class="action">
                          <a href="/admin/products/show/{{$product->id}}">
                            <button class="text-active"><i class="lni lni-eye"></i></button>
                          </a>
                        </div>
                        @can('product-sa-edit')
                          <div class="action">
                            <a href="/admin/products/edit/{{$product->id}}">
                              <button class="text-info"><i class="lni lni-pencil"></i></button>
                            </a>
                          </div>
                        @endcan
                        @can('product-sa-delete')
                          <form method="POST" action="/admin/products/delete/{{$product->id}}">
                            @csrf
                            <div class="action">
                              <input name="_method" type="hidden" value="DELETE">
                              <button type="submit" class="text-danger"><i class="lni lni-trash-can"></i></button>
                            </div>
                          </form>
                        @endcan
                      </div>
                    </td>
                  </tr>
                  @endforeach
                  <!-- end table row -->
                </tbody>
              </table>
              <!-- end table -->
              @if (isset($search))
                  {!! $products-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
              @else
                  {!! $products-> links() !!}    
              @endif
            </div>
          </div>
          <!-- end card -->
        </div>
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
    <!-- ========== tables-wrapper end ========== -->
  </div>
  <!-- end container -->
</section>

@endsection