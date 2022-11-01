@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Agenda de Visitas y Llamadas</h2>
              @can('appointment-create')
                <a href="{{ url('/user/appointments/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/user/appointments/search') }}">
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
                <div class="left">
                  <div id="legend3">
                    <ul class="legend3 d-flex flex-wrap align-items-center mb-30">
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-success"> </span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="Realizar Llamada" type="submit"><p class="text-sm text-dark">Realizar Llamadas</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-orange"> </span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="Visitar Personalmente" type="submit"><p class="text-sm text-dark">Visitar Personalmente</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="right">
                  @if (isset($filter))
                  <ul class="legend3 d-flex align-items-center mb-30">
                    <li>
                      <div class="d-flex">
                        <div class="text">
                          <form action="{{ url('/user/appointments/filter') }}">
                            <button class="btn-group-status" name="filter" value="" type="submit"><p class="text-sm text-dark"><i class="lni lni-close"></i>&nbsp; Quitar Filtros</p></button>
                          </form> 
                        </div>
                      </div>
                    </li>
                  </ul>
                  @endif
                </div>
              </div>

              <div class="table-wrapper table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th><h6>Número</h6></th>
                      <th><h6>Cliente</h6></th>
                      <th><h6>Fecha</h6></th>
                      <th><h6>Hora</h6></th>
                      <th><h6>Acción</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (count($appointments) > 0 )
                      @foreach ($appointments as $appointment)
                        <tr>
                          <td class="text-sm"><h6 class="{{ ($appointment->status == 'Procesado' || $appointment->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}">{{ $appointment->visit_number }}</h6></td>
                          @if ($appointment->visit_id)
                            <td class="min-width"><h5 class="text-bold {{ ($appointment->status == 'Procesado' || $appointment->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}"><a href="{{ url('/user/customer_visits/show/'.$appointment->visit_id ) }}">{{ $appointment->customer_name }}</a></h5></td>
                          @else
                            <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/user/appointments/show/'.$appointment->id ) }}">{{ $appointment->customer_name }}</a></h5></td>
                          @endif
                          <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ date('d/m/Y', strtotime($appointment->date)) }}</p></td>
                          <td class="min-width"><p><i class="lni lni-timer mr-10"></i>{{ $appointment->hour }}</p></td>
                          <td class="min-width">
                            <span class="status-btn 
                              @if($appointment->action == 'Realizar Llamada') info-btn
                              @elseIf($appointment->action == 'Visitar Personalmente') orange-btn
                              @elseIf($appointment->action == 'Enviar Presupuesto') active-btn
                              @endif">
                              {{ $appointment->action }}
                            </span>
                          </td>
                          <td class="min-width">
                            <span class="status-btn 
                              @if($appointment->status == 'Procesado') primary-btn
                              @elseIf($appointment->status == 'No Procesado') danger-btn
                              @elseIf($appointment->status == 'Pendiente') primary-btn
                              @elseIf($appointment->status == 'Cancelado') light-btn
                              @endif">
                              {{ $appointment->status }}
                            </span>
                          </td>
                          <td class="text-right">
                            <div class="btn-group">
                              @if ($appointment->visit_id)
                                <div class="action">
                                  <a href="{{ url('/user/customer_visits/show/'.$appointment->visit_id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                                    <button class="text-active"><i class="lni lni-eye"></i></button>
                                  </a>
                                </div>
                                @can('appointment-edit')
                                  @if ($appointment->status == 'Pendiente')
                                    <div class="action">
                                      <a href="{{ url('/user/customer_visits/edit/'.$appointment->visit_id) }}" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                        <button class="text-info"><i class="lni lni-pencil"></i></button>
                                      </a>
                                    </div>
                                  @endif
                                @endcan
                              @else
                                <div class="action">
                                  <a href="{{ url('/user/appointments/show/'.$appointment->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                                    <button class="text-active"><i class="lni lni-eye"></i></button>
                                  </a>
                                </div>
                                @can('appointment-edit')
                                  @if ($appointment->status == 'Pendiente')
                                    <div class="action">
                                      <a href="{{ url('/user/appointments/edit/'.$appointment->id) }}" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                        <button class="text-info"><i class="lni lni-pencil"></i></button>
                                      </a>
                                    </div>
                                  @endif
                                @endcan
                                @can('appointment-delete')
                                  <form method="POST" action="{{ url('/user/appointments/delete/'.$appointment->id) }}" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    @csrf
                                    <div class="action">
                                      <input name="_method" type="hidden" value="DELETE">
                                      <button type="submit" class="text-danger show_confirm"><i class="lni lni-trash-can"></i></button>
                                    </div>
                                  </form>
                                @endcan
                              @endif
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
                    {!! $appointments-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                    {!! $appointments-> links() !!}    
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