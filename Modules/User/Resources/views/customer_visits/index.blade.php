@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Visita Clientes</h2>
              @can('customer_visit-create')
                <a href="{{ url('/user/customer_visits/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/user/customer_visits/search') }}">
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
              <div class="table-wrapper table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th><h6>Número</h6></th>
                      <th><h6>Cliente</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Presupuesto?</h6></th>
                      <th><h6>Creada el</h6></th>
                      <th><h6>Acción</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($customer_visits as $customer_visit)
                      <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ $customer_visit->visit_number }}</h6></td>
                        <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/user/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
                        <td class="min-width"><span class="status-btn primary-btn">{{ $customer_visit->status }}</span></td>
                        @if ($customer_visit->type == 'Presupuesto')
                          <td class="min-width"><p>Sí</p></td>
                        @elseIf($customer_visit->type == 'Sin Presupuesto')
                          <td class="min-width"><p>No</p></td>
                        @endif
                        <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ date('d/m/Y - H:i', strtotime($customer_visit->visit_date)) }}</p></td>
                        <td class="min-width">
                          <span class="status-btn 
                          @if($customer_visit->action == 'Realizar Llamada') success-btn
                          @elseIf($customer_visit->action == 'Visitar Personalmente') orange-btn
                          @elseIf($customer_visit->action == 'Enviar Presupuesto') info-btn
                          @endif">
                            {{ $customer_visit->action }}
                          </span>
                        </td>
                        <td class="text-right">
                          <div class="btn-group">
                            <div class="action">
                              @if ($customer_visit->type == 'Presupuesto')
                                <a href="{{ url('/user/customer_visits/generateInvoicePDF/?download=pdf&customer_visit='.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Imprimir" target="_blank">
                                  <button class="text-secondary"><i class="lni lni-printer"></i></button>
                                </a>
                              @endif
                            </div>
                            <div class="action">
                              <a href="{{ url('/user/customer_visits/show/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                                <button class="text-active"><i class="lni lni-eye"></i></button>
                              </a>
                            </div>
                            @can('customer_visit-edit')
                            <div class="action">
                              <a href="{{ url('/user/customer_visits/edit/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                <button class="text-info"><i class="lni lni-pencil"></i></button>
                              </a>
                            </div>
                            @endcan
                            @can('customer_visit-delete')
                            <form method="POST" action="{{ url('/user/customer_visits/delete/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
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
                  </tbody>
                </table>
                @if (isset($search))
                    {!! $customer_visits-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                    {!! $customer_visits-> links() !!}    
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection