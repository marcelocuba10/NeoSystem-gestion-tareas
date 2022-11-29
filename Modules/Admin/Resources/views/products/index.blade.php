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
              <a href="{{ url('/admin/products/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
            @endcan  
            <a style="margin-left: 17px;" href="{{ url('/admin/products/import-csv') }}"><i class="hthtg lni lni-upload" data-toggle="tooltip" data-placement="bottom" title="Importar datos desde planilla Excel"></i></a>
          </div>
        </div>
        <!-- end col -->
        <div class="col-md-4">
          <div class="right">
            <div class="table-search d-flex st-input-search">
              <form action="{{ url('/admin/products/search') }}">
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
              <table class="table top-selling-table table-hover">
                <thead>
                  <tr>
                    <th><h6>Código</h6></th>
                    <th><h6>Nombre</h6></th>
                    <th><h6>Precio Agente</h6></th>
                    <th><h6>Precio Público</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product)
                  <tr>
                    <td class="text-sm"><h6 class="text-sm">{{ $product->custom_code }}</h6></td>
                    {{-- <td>
                      <div class="product">
                        <div class="image">
                          @if ($product->filename)
                            <img src="{{ asset('/public/images/products/'.$product->filename) }}" alt="{{ Str::limit($product->filename, 15) }}">
                          @else
                            <img src="{{ asset('/public/adminLTE/images/products/no-image.jpg') }}" alt="{{ Str::limit($product->filename, 15) }}">
                          @endif
                        </div>
                        <p class="text-sm"><a href="{{ url('/admin/products/show/'.$product->id) }}">{{ $product->name }}</a></p>
                      </div>
                    </td> --}}
                    <td class="min-width"><p class="text-sm"><a href="{{ url('/admin/products/show/'.$product->id) }}">{{ Str::limit($product->name, 50) }}</a></p></td>
                    <td class="min-width"><p>G$ {{number_format($product->purchase_price, 0,",",".")}}</p></td>
                    <td class="min-width"><p>G$ {{number_format($product->sale_price, 0,",",".")}}</p></td>
                    {{-- <td class="min-width">
                      @if ($product->inventory > 5)
                      <p><span class="status-btn info-btn">{{ $product->inventory }}</span></p>
                      @else
                        <p><span class="status-btn orange-btn">{{ $product->inventory }}</span></p>
                      @endif
                    </td> --}}
                    <td class="text-right">
                      <div class="btn-group">
                        {{-- <div class="action">
                          <a href="{{ url('/admin/products/image-gallery/'.$product->id) }}">
                            <button class="text-success"><i class="lni lni-image"></i></button>
                          </a>
                        </div> --}}
                        <div class="action">
                          <a href="{{ url('/admin/products/show/'.$product->id) }}">
                            <button class="text-active"><i class="lni lni-eye"></i></button>
                          </a>
                        </div>
                        @can('product-sa-edit')
                          <div class="action">
                            <a href="{{ url('/admin/products/edit/'.$product->id) }}">
                              <button class="text-info"><i class="lni lni-pencil"></i></button>
                            </a>
                          </div>
                        @endcan
                        @can('product-sa-delete')
                          <form method="POST" action="{{ url('/admin/products/delete/'.$product->id) }}">
                            @csrf
                            <div class="action">
                              <input name="_method" type="hidden" value="DELETE">
                              <button type="submit" class="text-danger show_confirm"><i class="lni lni-trash-can"></i></button>
                            </div>
                          </form>
                        @endcan
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script type="text/javascript">
  $('.show_confirm').click(function(event) {
        var form =  $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: '¿Está seguro que desea eliminar este registro?',
            // text: "Subtitulo",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["No", "Sí"],
        })
        .then((willDelete) => {
          if (willDelete) {
            form.submit();
          }
        });
    });
</script>

@endsection