@extends('user::layouts.adminLTE.app')
@section('content')

<section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Relatorio de Precios</h2>
            </div>
          </div>
          <div class="col-md-4"></div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="invoice-wrapper">
        <div class="row">
          <div class="col-12">
            <div class="invoice-card card-style mb-30">
              <div class="invoice-header">
                <div class="invoice-for">
                  <form action="#">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="input-style-1">
                          <label>Fecha Desde</label>
                            <input type="date" name="date" id="date" value="" readonly class="bg-gray">  
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-style-1">
                          <label>Fecha Hasta</label>
                            <input type="date" name="date" id="date" value="" readonly class="bg-gray">  
                        </div>
                      </div>
                      <div class="col-md-3" style="margin-top: 35px;">
                        <div class="input-style-1">
                          <a href="#" class="btn btn-lg info-btn rounded-md btn-hover disabled" role="button" aria-disabled="true"><i class="lni lni-search"></i></a>
                          <a href="/user/reports/price-list-products?download=pdf" class="btn btn-lg success-btn rounded-md btn-hover" target="_blank"><i class="lni lni-printer"></i></a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #8dbba4;">
                    <tr>
                      <th>
                        <h6 class="text-sm text-medium">#</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Código</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Nombre</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Precio</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Stock</h6>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="text-sm"><p>{{ $product->code }}</p></td>
                        <td class="text-sm"><p>{{ $product->name }}</p></td>
                        <td class="text-sm"><p>{{ $product->sale_price }}</p></td>
                        <td class="text-sm"><p>{{ $product->quantity }}</p></td>
                    </tr>
                    @endforeach
                  <!-- end table row -->
                </tbody>
                </table>
                @if (isset($filter))
                {{-- {{ $machines->appends(['sort' =>$filter])->links() }}  --}}
                {{-- {!! $machines->appends(Request::except('page'))->render() !!} --}}
                  {!! $products-> appends($filter)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                  {!! $products-> links() !!}    
                @endif
              </div>
            </div>
            <!-- End Card -->
          </div>
          <!-- ENd Col -->
        </div>
        <!-- End Row -->
      </div>

    </div>
    <!-- end container -->
  </section>
@endsection