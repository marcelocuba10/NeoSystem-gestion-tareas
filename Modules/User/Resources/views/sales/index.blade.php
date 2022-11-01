@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Ventas</h2>
              @can('sales-create')
                <a href="{{ url('/user/sales/create') }}" class="main-btn info-btn btn-hover btn-sm" data-toggle="tooltip" data-placement="bottom" title="Crear Nueva Venta"><i class="lni lni-plus mr-5"></i></a>
              @endcan
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/user/sales/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar cliente..">
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
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th><h6>Número</h6></th>
                      <th><h6>Cliente</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Tipo</h6></th>
                      <th><h6>Total</h6></th>
                      <th><h6>Creada el</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (count($sales) > 0 )
                      @foreach ($sales as $sale)
                        <tr>
                          <td class="text-sm"><h6 class="{{ ($sale->status == 'Procesado' || $sale->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}">{{ $sale->invoice_number }}</h6></td>
                          <td class="min-width"><h5 class="text-bold {{ ($sale->status == 'Procesado' || $sale->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}"><a href="{{ url('/user/sales/show/'.$sale->id) }}">{{ $sale->customer_name }}</a></h5></td>
                          <td class="min-width">
                            <span class="status-btn 
                            @if($sale->status == 'Procesado') primary-btn
                            @elseIf($sale->status == 'Pendiente') primary-btn
                            @elseIf($sale->status == 'Cancelado') light-btn
                            @endif">
                            {{ $sale->status }}
                            </span>
                          </td>
                          <td class="min-width">
                            <span class="status-btn 
                            @if($sale->type == 'Venta') info-btn
                            @elseIf($sale->type == 'Presupuesto') active-btn
                            @endif">
                              {{ $sale->type }}
                            </span>
                          </td>
                          <td class="min-width"><p><b>G$ {{number_format($sale->total, 0)}}</b></p></td>
                          @if ($sale->visit_date)
                            <td class="min-width"><p>{{ date('d/m/Y - H:i', strtotime($sale->visit_date)) }}</p></td>
                          @else
                            <td class="min-width"><p>{{ date('d/m/Y - H:i', strtotime($sale->sale_date)) }}</p></td>
                          @endif
                          <td class="text-right">
                            <div class="btn-group">
                              @can('sales-edit')
                                <div class="action">
                                  <a href="{{ url('/user/sales/generateInvoicePDF/?download=pdf&saleId='.$sale->id) }}" data-toggle="tooltip" data-placement="bottom" title="Imprimir" target="_blank">
                                    <button class="text-secondary"><i class="lni lni-printer"></i></button>
                                  </a>
                                  @if ($sale->type == 'Venta' && $sale->status == 'Cancelado')
                                    <a href="{{ url('/user/sales/show/'.$sale->id) }}" class="main-btn-sm deactive-btn rounded-md btn-hover" data-toggle="tooltip" data-placement="bottom" title="Ver Detalles">Ver</a>
                                  @elseif($sale->type == 'Venta' && $sale->status != 'Cancelado')
                                    <a href="{{ url('/user/sales/edit/'.$sale->id) }}" class="main-btn-sm deactive-btn rounded-md btn-hover" data-toggle="tooltip" data-placement="bottom" title="Ver Detalles">Ver</a>
                                  @elseif($sale->type == 'Presupuesto' && $sale->status == 'Cancelado')
                                    <a href="{{ url('/user/sales/show/'.$sale->id) }}" class="main-btn-sm deactive-btn rounded-md btn-hover" data-toggle="tooltip" data-placement="bottom" title="Ver Detalles">Ver</a>
                                  @elseif($sale->type == 'Presupuesto')
                                    <a href="{{ url('/user/sales/edit/'.$sale->id) }}" class="main-btn-sm success-btn rounded-md btn-hover" data-toggle="tooltip" data-placement="bottom" title="Procesar Presupuesto">Procesar</a>
                                  @endif
                                </div>
                              @endcan
                              @can('sales-delete')
                                @if ($sale->status != 'Cancelado')
                                  <form method="POST" action="{{ url('/user/sales/delete/'.$sale->id) }}" data-toggle="tooltip" data-placement="bottom" title="Cancelar">
                                    @csrf
                                    <div class="action">
                                      <input name="_method" type="hidden" value="DELETE">
                                      <button type="submit" class="text-danger show_confirm"><i class="lni lni-trash-can"></i></button>
                                    </div>
                                  </form>
                                @endif
                              @endcan
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    @else
                      <tr>
                        <td class="text-sm"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width">Sin resultados encontrados</td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                      </tr>
                    @endif  
                  </tbody>
                </table>
                @if (isset($search))
                    {!! $sales-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                    {!! $sales-> links() !!}    
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
              title: '¿Está seguro que desea cancelar este registro?',
              // text: "Si eliminas esto, desaparecerá para siempre.",
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