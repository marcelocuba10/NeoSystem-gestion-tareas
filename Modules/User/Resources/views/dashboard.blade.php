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
            <div class="icon orange"><i class="lni lni-users"></i></div>
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
            <div class="icon success"><i class="lni lni-users"></i></div>
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
            <div class="icon primary"><i class="lni lni-users"></i></div>
            <div class="content">
              <h6 class="mb-10">Total Clientes</h6>
              <h3 class="text-bold mb-10">{{ $cant_customers }}</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
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
                        @elseIf($customer_visit->status == 'No Procesado') danger-btn
                        @elseIf($customer_visit->status == 'Pendiente') primary-btn
                        @elseIf($customer_visit->status == 'Cancelado') light-btn
                        @endif">
                          {{ $customer_visit->status }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex justify-content-end">
                          <a href="{{ url('/user/customer_visits/show/'.$customer_visit->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                            <button class="status-btn primary-btn border-0 m-1">Ver</button>
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

        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div class="title mb-10 d-flex justify-content-between align-items-center"><h6 class="mb-10">Agenda de Visitas y Llamadas</h6></div>
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
                            No Procesado
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

  <section class="section">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div id="container"></div>
          </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div id="container_3"></div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-xl-12 col-xxl-12">
          <div class="card-style mb-30">
            <div id="container_2"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
    
  var visits_cancel_count = <?php echo json_encode($visits_cancel_count); ?>;
  var visits_process_count = <?php echo json_encode($visits_process_count); ?>;
  var visits_no_process_count = <?php echo json_encode($visits_no_process_count); ?>;
  var visits_pending_count = <?php echo json_encode($visits_pending_count); ?>;

  Highcharts.chart('container', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'Estado de Visitas Clientes durante 30 días'
      },
      tooltip: {
          pointFormat: "{series.name}({point.y}): <b>{point.percentage:.1f}%</b>"
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f} %'
              }
          }
      },
      series: [{
          name: 'Total',
          colorByPoint: true,
          data: [{
              name: 'Visitados',
              y: visits_process_count,
              sliced: true,
              selected: true,
              color: '#4caf50e3',
          }, {
              name: 'No Visitados',
              y: visits_no_process_count,
              color: '#d50100c7',
          }, {
              name: 'Pendiente',
              y: visits_pending_count,
              color: '#fb7d33',
          }, {
              name: 'Cancelados',
              y: visits_cancel_count,
              color: '#ffc107',
          }]
      }]
  });

  var salesPeriods = <?php echo json_encode($salesPeriods); ?>;

  var salesCountByMonth = <?php 
    $data = json_encode($salesCountByMonth);
    echo str_replace('"', '', $data);
  ?>;

  var salesCancelCountByMonth = <?php 
    $data = json_encode($salesCancelCountByMonth);
    echo str_replace('"', '', $data);
  ?>;

  var ordersCountByMonth = <?php 
    $data = json_encode($ordersCountByMonth);
    echo str_replace('"', '', $data);
  ?>;

  var ordersCancelCountByMonth = <?php 
    $data = json_encode($ordersCancelCountByMonth);
    echo str_replace('"', '', $data);
  ?>;

  Highcharts.chart('container_2', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Ventas y Presupuestos - 2022'
    },
    xAxis: {
      categories: salesPeriods,
        crosshair: true
    },
    yAxis: {
        title: {
            useHTML: true,
            text: 'Ventas y Presupuestos'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },  
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Ventas Concretadas',
        data: salesCountByMonth

      }, {
        name: 'Ventas Canceladas',
        data: salesCancelCountByMonth

    }, {
        name: 'Presupuestos',
        data: ordersCountByMonth

    }, {
        name: 'Presupuestos Cancelados',
        data: ordersCancelCountByMonth

    }]
  });


  var salesCountByMonth = <?php echo json_encode($sales_count); ?>;
  var ordersCountByMonth = <?php echo json_encode($orders_count); ?>;

  Highcharts.chart('container_3', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'Estado de Ventas durante 30 días'
      },
      tooltip: {
          pointFormat: "{series.name}({point.y}): <b>{point.percentage:.1f}%</b>"
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f} %'
              }
          }
      },
      series: [{
          name: 'Total',
          colorByPoint: true,
          data: [{
              name: 'Ventas',
              y: salesCountByMonth,
              sliced: true,
              selected: true,
              color: '#4a6cf7',
          }, {
              name: 'Presupuestos',
              y: ordersCountByMonth,
              color: '#c1e4fe',
          }]
      }]
  });

</script>
@endsection