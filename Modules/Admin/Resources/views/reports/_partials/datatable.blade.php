  <table class="table table-hover">
    <thead>
      <tr>
        <th><h6>Número</h6></th>
        <th><h6>Cliente</h6></th>
        <th><h6>Estado</h6></th>
        <th><h6>Rubro</h6></th>
        <th><h6>Localidad</h6></th>
        <th><h6>Creada el</h6></th>
        <th><h6>Acciones</h6></th>
      </tr>
    </thead>
    <tbody>
      @if (count($customer_visits) > 0 )
        @foreach ($customer_visits as $customer_visit)
          <tr>
            <td class="text-sm"><h6 class="{{ ($customer_visit->status == 'Procesado' || $customer_visit->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}">{{ $customer_visit->visit_number }}</h6></td>
            <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/admin/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
            <td class="min-width">
              <span class="status-btn 
                @if($customer_visit->status == 'Procesado') primary-btn
                @elseIf($customer_visit->status == 'No Procesado') danger-btn
                @elseIf($customer_visit->status == 'Pendiente') primary-btn
                @elseIf($customer_visit->status == 'Cancelado') light-btn
                @endif">
                  {{ $customer_visit->status }}
              </span>
            </td>
            <td class="text-sm" style="width: 180px;">
                  @foreach ($categories as $item) 
                    <span class="{{ in_array($item->id, json_decode($customer_visit->category) )  ? 'show-span' : 'hide-span' }} ">
                      {{ $item->name }}
                    </span>
                  @endforeach 
            </td>
            <td class="min-width"><p>{{ $customer_visit->estate }}</p></td>
            <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ date('d/m/Y', strtotime($customer_visit->visit_date)) }}</p></td>
            <td class="text-right">
              <div class="btn-group">
                <div class="action">
                  @if ($customer_visit->type == 'Presupuesto')
                    <a href="{{ url('/admin/customer_visits/generateInvoicePDF/?download=pdf&visit_id='.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Imprimir" target="_blank">
                      <button class="text-secondary"><i class="lni lni-printer"></i></button>
                    </a>
                  @endif
                </div>
                <div class="action">
                  <a href="{{ url('/admin/customer_visits/show/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                    <button class="text-active"><i class="lni lni-eye"></i></button>
                  </a>
                </div>
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
          <td class="min-width"> Sin resultados encontrados</td>
          <td class="min-width"></td>
          <td class="min-width"></td>
          <td class="min-width"></td>
        </tr>
      @endif

    </tbody>
  </table>