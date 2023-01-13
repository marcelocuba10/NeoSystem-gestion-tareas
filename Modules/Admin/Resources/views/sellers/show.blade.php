@extends('admin::layouts.adminLTE.app')
@section('content')

<section class="section">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="titlemb-30">
            <h2>Detalle Vendedor</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/admin/sellers') }}">Agentes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle Agentes</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="form-layout-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <form method="POST">
              @csrf
              <div class="row">
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Razón Social</label>
                    <input value="{{ $user->name ?? old('name') }}" readonly type="text">
                  </div>
                </div>
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Nombre del Encargado</label>
                    <input value="{{ $user->seller_contact_1 ?? old('seller_contact_1') }}" type="text" readonly>
                  </div>
                </div>
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Email</label>
                    <input value="{{ $user->email ?? old('email') }}" type="email" readonly>
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Status</label>
                    @foreach ($status as $value)
                      @if ($value[0] == $user->status)
                        <input value="{{ $value[1] ?? old('status') }}" type="text"  readonly >
                      @endif
                    @endforeach 
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Doc Identidad</label>
                    <input value="{{ $user->doc_id ?? old('doc_id') }}" type="text" readonly>
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Teléfono 1</label>
                    <input value="{{ $user->phone_1 ?? old('phone_1') }}" type="text" readonly>
                  </div>
                </div>
                <div class="col-3">
                  <div class="input-style-1">
                    <label>Teléfono 2</label>
                    <input value="{{ $user->phone_2 ?? old('phone_2') }}" type="text" readonly>
                  </div>
                </div>
                <div class="col-6">
                  <div class="input-style-1">
                    <label>Ciudad</label>
                    <input value="{{ $user->city ?? old('city') }}" type="text" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="select-style-1">
                    <label>(*) Departamento</label>
                    <div class="select-position bg-gray">
                      <select name="estate" disabled="true">
                        @foreach ($estates as $key)
                          <option {{ ( $key[1] == $userEstate) ? 'selected' : '' }}> {{ $key[1] }} </option>
                        @endforeach 
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-8">
                  <div class="input-style-1">
                    <label>Dirección</label>
                    <input value="{{ $user->address ?? old('address') }}" type="text" readonly>
                  </div>
                </div>

                <div class="col-12">
                  <div class="button-group d-flex justify-content-center flex-wrap">
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/sellers') }}">Atrás</a>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Histórico de Operaciones</h2>
            </div>
          </div>
          <div class="col-md-6">
          </div>
        </div>
      </div>
    </div>

    @if ($customer_visits)
      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <div class="table-wrapper table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th><h6>Número</h6></th>
                    <th><h6>Cliente</h6></th>
                    <th><h6>Estado</h6></th>
                    <th><h6>Tipo</h6></th>
                    <th><h6>Creada el</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($customer_visits as $customer_visit)
                    <tr>
                      <td class="text-sm"><h6 class="{{ ($customer_visit->status == 'Procesado' || $customer_visit->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}">{{ $customer_visit->visit_number }}</h6></td>
                      <td class="min-width"><h5 class="text-bold {{ ($customer_visit->status == 'Procesado' || $customer_visit->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}"><a href="{{ url('/admin/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
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
                      <td class="min-width">
                        <span class="status-btn 
                        @if($customer_visit->action == 'Realizar Llamada') info-btn
                        @elseIf($customer_visit->action == 'Realizar Visita') orange-btn
                        @elseIf($customer_visit->action == 'Enviar Presupuesto') active-btn
                        @endif">
                          {{ $customer_visit->action }}
                        </span>
                      </td>
                      <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ date('d/m/Y - H:i', strtotime($customer_visit->visit_date)) }}</p></td>
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
                          @can('customer_visit-edit')
                            @if ($customer_visit->status == 'Pendiente')
                              <div class="action">
                                <a href="{{ url('/admin/customer_visits/edit/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                  <button class="text-info"><i class="lni lni-pencil"></i></button>
                                </a>
                              </div>
                            @endif
                          @endcan
                          @can('customer_visit-delete')
                            @if ($customer_visit->status == 'Procesado' || $customer_visit->status == 'Pendiente')
                              <form method="POST" action="{{ url('/admin/customer_visits/delete/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Cancelar">
                                @csrf
                                <div class="action">
                                  <input name="_method" type="hidden" value="DELETE">
                                  <button type="submit" class="text-danger"><i class="lni lni-trash-can"></i></button>
                                </div>
                              </form>
                            @endif
                          @endcan
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              {!! $customer_visits-> links() !!}
            </div>
          </div>
        </div>
      </div>

    @else

      <div class="row">
        <div class="col-lg-12">
          <div class="card-style mb-30">
            <div class="table-wrapper table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th><h6>Número</h6></th>
                    <th><h6>Cliente</h6></th>
                    <th><h6>Estado</h6></th>
                    <th><h6>Tipo</h6></th>
                    <th><h6>Creada el</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="min-width"></td>
                    <td class="min-width"></td>
                    <td class="min-width">Sin resultados encontrados</td>
                    <td class="min-width"></td>
                    <td class="min-width"></td>
                    <td class="min-width"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    @endif

  </div>
</section>
@endsection  
