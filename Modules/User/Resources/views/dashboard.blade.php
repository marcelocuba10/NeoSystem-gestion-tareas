@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Bienvenido a {{ config('app.name') }}</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon purple">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Visita a clientes</h6>
              <h3 class="text-bold mb-10">{{ $visited_less_30_days }}</h3>
              <p class="text-sm text-success">
                <span class="text-gray">(menos de 30 días)</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon orange">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Visita a clientes</h6>
              <h3 class="text-bold mb-10">{{ $visited_more_30_days }}</h3>
              <p class="text-sm text-success">
                <span class="text-gray">(más de 30 días)</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon success">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Visita a clientes</h6>
              <h3 class="text-bold mb-10">{{ $visited_more_90_days }}</h3>
              <p class="text-sm text-success">
                <span class="text-gray">(más de 90 días)</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon primary">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Clientes</h6>
              <h3 class="text-bold mb-10">{{ $cant_customers }}</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Customer visits -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style activity-card clients-table-card mb-30">
            <div class="title d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Últimas Visitas Clientes</h6>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <tbody>
                  @foreach ($customer_visits as $customer_visit)
                    <tr>
                      <td>
                        <div class="employee-image">
                          <img src="{{ asset('/public/images/user-icon-business-man-flat-png-transparent.png') }}" alt="">
                        </div>
                      </td>
                      <td class="employee-info">
                        <h5 class="text-medium">{{ $customer_visit->customer_name }}</h5>
                        <p><i class="lni lni-map-marker"></i>&nbsp;{{ $customer_visit->estate }} - &nbsp;{{ date('d/m/Y - H:i', strtotime($customer_visit->visit_date)) }}</p>
                      </td>
                        <td class="min-width">
                          <span style="float: right;" class="status-btn 
                          @if($customer_visit->status == 'Procesado') primary-btn
                          @elseIf($customer_visit->status == 'No procesado') danger-btn
                          @elseIf($customer_visit->status == 'Pendiente') primary-btn
                          @elseIf($customer_visit->status == 'Cancelado') light-btn
                          @endif">
                            {{ $customer_visit->status }}
                          </span>
                        </td>
                      <td>
                        <div class="d-flex justify-content-end">
                          <a href="{{ url('/user/customer_visits/show/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                            <button class="status-btn primary-btn border-0 m-1">
                              Ver
                            </button>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Appointments -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div class="title mb-10 d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Agenda de Visitas y Llamadas</h6>
            </div>
            <div class="todo-list-wrapper">
              <ul>
                @if (count($appointments) > 0 )
                  @foreach ($appointments as $appointment)
                    <li class="todo-list-item primary">
                      <div class="todo-content">
                        <p class="text-sm mb-2">
                          <i class="lni lni-calendar"></i>
                          Fecha: {{ date('d/m/Y', strtotime($appointment->date)) }}
                        </p>
                        <a href="{{ url('/user/appointments/show/'.$appointment->id ) }}"><h5 class="text-bold mb-10">{{ $appointment->customer_name }}</h5></a>
                        <p class="text-sm">
                          <i class="lni lni-alarm-clock"></i>
                          Hora: {{ $appointment->hour }}
                        </p>
                      </div>
                      <div class="todo-status">
                        <span class="status-btn primary-btn text-bold">
                          {{ $appointment->action }}
                        </span>
                        @if(date('d/m/Y', strtotime($appointment->date)) < $currentDate )
                          <span class="status-btn danger-btn">
                            No procesado
                          </span>
                        @else
                          <span class="status-btn orange-btn">
                            Pendiente
                          </span>
                        @endif
                      </div>
                    </li>
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
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
@endsection