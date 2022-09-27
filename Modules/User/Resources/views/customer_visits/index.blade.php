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
              <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
                <div class="left"></div>
                <div class="right"></div>
              </div>
              <div class="table-wrapper table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th><h6>#</h6></th>
                      <th><h6>Cliente</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Fecha de Visita</h6></th>
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
                          @if($customer_visit->status == 'Pendiente') btn-custom-attention
                          @elseIf($customer_visit->status == 'Visitado') btn-custom-enabled
                          @elseIf($customer_visit->status == 'No Atendido') btn-custom-error
                          @elseIf($customer_visit->status == 'Cancelado') btn-custom-disabled
                          @endif">
                            {{ $customer_visit->status }}
                          </span>
                        </td>
                        <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->visit_date }}</p></td>
                        <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->next_visit_date }}</p></td>
                        <td class="min-width"><p>{{ $customer_visit->estate }}</p></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <div class="action">
                              <a href="{{ url('/user/customer_visits/show/'.$customer_visit->id) }}">
                                <button class="text-active"><i class="lni lni-eye"></i></button>
                              </a>
                            </div>
                            @can('customer_visit-edit')
                            <div class="action">
                              <a href="{{ url('/user/customer_visits/edit/'.$customer_visit->id) }}">
                                <button class="text-info"><i class="lni lni-pencil"></i></button>
                              </a>
                            </div>
                            @endcan
                            @can('customer_visit-delete')
                            <form method="POST" action="{{ url('/user/customer_visits/delete/'.$customer_visit->id) }}">
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