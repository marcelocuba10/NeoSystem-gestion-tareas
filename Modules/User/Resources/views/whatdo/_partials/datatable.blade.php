  <table class="table">
    <thead>
      <tr>
        <th><h6>#</h6></th>
        <th><h6>Cliente</h6></th>
        <th><h6>Estado</h6></th>
        <th><h6>Presupuesto?</h6></th>
        <th><h6>Fecha Visita</h6></th>
        <th><h6>Fecha Prox Visita</h6></th>
        <th><h6>Localidad</h6></th>
        <th><h6>Acciones</h6></th>
      </tr>
    </thead>
    <tbody>
      @if (count($customer_visits) > 0 )
        @foreach ($customer_visits as $customer_visit)
          <tr>
            <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
            <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/user/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
            <td class="min-width">
              <span class="status-btn 
              @if($customer_visit->status == 'Visitado') secondary-btn
              @elseIf($customer_visit->status == 'No Atendido') close-btn
              @elseIf($customer_visit->status == 'Cancelado') warning-btn
              @endif">
                {{ $customer_visit->status }}
              </span>
            </td>
            @if ($customer_visit->type == 'Order')
              <td class="min-width"><p>Sí</p></td>
            @elseIf($customer_visit->type == 'NoOrder')
              <td class="min-width"><p>No</p></td>
            @endif
            <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->visit_date }}</p></td>
            <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->next_visit_date }}</p></td>
            <td class="min-width"><p>{{ $customer_visit->estate }}</p></td>
            <td class="text-right">
              <div class="btn-group">
                <div class="action">
                  <a href="#">
                    <button class="text-active"><i class="lni lni-eye"></i></button>
                  </a>
                </div>
                @can('what_can_do-edit')
                <div class="action">
                  <a href="#">
                    <button class="text-info"><i class="lni lni-pencil"></i></button>
                  </a>
                </div>
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
          <td class="min-width"> Sin resultados encontrados</td>
          <td class="min-width"></td>
          <td class="min-width"></td>
          <td class="min-width"></td>
        </tr>
      @endif

    </tbody>
  </table>