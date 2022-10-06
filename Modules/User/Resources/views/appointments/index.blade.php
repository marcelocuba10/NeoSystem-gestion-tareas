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
                <a href="#" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
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
                          <span class="bg-color bg-card-secondary"></span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="Visitado" type="submit"><p class="text-sm text-dark">Visitados</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-enabled"></span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="Pendiente" type="submit"><p class="text-sm text-dark">Pendientes</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-attention"> </span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="Cancelado" type="submit"><p class="text-sm text-dark">Cancelados</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-error"> </span>
                          <div class="text">
                            <form action="{{ url('/user/appointments/filter') }}">
                              <button class="btn-group-status" name="filter" value="No Atendido" type="submit"><p class="text-sm text-dark">No Atendidos</p></button>
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
                            @can('appointment-edit')
                            <div class="action">
                              <a href="#">
                                <button class="text-info"><i class="lni lni-pencil"></i></button>
                              </a>
                            </div>
                            @endcan
                            {{-- @can('appointment-delete')
                            <form method="POST" action="#">
                              @csrf
                              <div class="action">
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit" class="text-danger"><i class="lni lni-trash-can"></i></button>
                              </div>
                            </form>
                            @endcan --}}
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