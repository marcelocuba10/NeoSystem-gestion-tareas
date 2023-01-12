@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Relatorio de Productos</h2>
              <a href="{{ url('/user/reports/products?download=pdf') }}" class="btn btn-lg success-btn rounded-md btn-hover" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Imprimir Relatorio de Productos"><i class="lni lni-printer"></i></a>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="invoice-wrapper">
        <div class="row">
          <div class="col-12">
            <div class="invoice-card card-style mb-30">

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #DAEFFE;">
                    <tr>
                      <th><h6 class="text-sm text-medium">Código</h6></th>
                      <th><h6 class="text-sm text-medium">Nombre</h6></th>
                      <th><h6 class="text-sm text-medium">Precio</h6></th>
                      <th><h6 class="text-sm text-medium">Actualizado el</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($products as $product)
                    <tr>
                      <td class="text-sm"><p>{{ $product->custom_code }}</p></td>
                      <td class="min-width"><p class="text-sm"><a href="{{ url('/user/products/show/'.$product->id) }}">{{ Str::limit($product->name, 75) }}</a></p></td>
                      <td class="text-sm"><p>G$ {{number_format($product->sale_price, 0)}}</p></td>
                      <td class="text-sm"><p>{{ $product->updated_at }}</p></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (isset($filter))
                  {!! $products-> appends($filter)->links() !!} <!-- appends envia variable en la paginacion-->
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