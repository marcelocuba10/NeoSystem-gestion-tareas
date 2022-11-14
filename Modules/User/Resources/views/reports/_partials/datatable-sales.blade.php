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
