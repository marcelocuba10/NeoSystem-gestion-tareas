@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Resumen de Agentes</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="wrapper">
        <div class="row">
          <div class="col-lg-6 col-xl-6 col-xxl-6">
            <div class="card card-style mb-30">
              <div class="input-style-1">
                <label>Agente</label>
                <select name="seller" class="form-control seller">
                  <option>Seleccione el Agente</option>
                  @foreach($sellers as $seller)  
                    <option value="{{ $seller->id }}" name="seller"> {{ $seller->name}} </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-xl-6 col-xxl-6">
            <div class="invoice-card card-style mb-30" style="padding: 15px 15px;">
              <div class="invoice-address">
                <div class="address-item">
                  <p class="text-sm">
                    <span class="text-sm text-bold">Agente:</span>
                    <span class="text-sm" id="name">--</span>
                  </p>
                  <p class="text-sm">
                    <span class="text-sm text-bold">Código ID:</span>
                    <span class="text-sm" id="idReference">--</span>
                  </p>
                  <p class="text-sm">
                    <span class="text-sm text-bold">Teléfono:</span>
                    <span class="text-sm" id="phone_1">--</span>
                  </p>
                </div>
                <div class="address-item">
                  <p class="text-sm">
                    <span class="text-sm text-bold">Doc Identidad:</span>
                    <span class="text-sm" id="doc_id">--</span>
                  </p>
                  <p class="text-sm">
                    <span class="text-sm text-bold">Localidad:</span>
                    <span class="text-sm" id="estate">--</span>
                  </p>
                  <p class="text-sm">
                    <span class="text-sm text-bold">Estado:</span>
                    <span class="text-sm" id="status">--</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="icon-card mb-30">
              <div class="icon purple"><i class="lni lni-users"></i></div>
              <div class="content">
                <h6 class="mb-10">Visita a clientes</h6>
                <h3 class="text-bold mb-10" id="visited_less_30_days"></h3>
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
                <h3 class="text-bold mb-10" id="visited_more_30_days"></h3>
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
                <h3 class="text-bold mb-10" id="visited_more_90_days"></h3>
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
                <h3 class="text-bold mb-10" id="cant_customers"></h3>
              </div>
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
      @if ($sales)
      <div class="row">
        <!-- Customer visits -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style activity-card clients-table-card mb-30">
            <div class="title d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Últimas Ventas / Presupuestos</h6>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <tbody>
                  @foreach ($sales as $sale)
                    <tr>
                      <td>
                        <div class="employee-image">
                          <img src="{{ asset('/public/images/user-icon-business-man-flat-png-transparent.png') }}" alt="">
                        </div>
                      </td>
                      <td class="employee-info">
                        <h5 class="text-bold {{ ($sale->status == 'Procesado' || $sale->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}"><a href="{{ url('/admin/sales/show/'.$sale->id) }}">{{ $sale->customer_name }}</a></h5>
                        <p class="text-sm">
                          Creado el: {{ date('d/m/Y', strtotime($sale->sale_date)) }} | <i class="lni lni-user"></i> Agente: {{ $sale->seller_name }}
                        </p>
                      </td>
                      <td class="min-width">
                        <span style="float: right;" class="status-btn 
                          @if($sale->status == 'Procesado') primary-btn
                          @elseIf($sale->status == 'No Procesado') danger-btn
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
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <p><a href="{{ url('/admin/reports/customer_visits') }}"><span class="s2mY5ma">Ver informe completo</span></a></p>
            </div>
          </div>
        </div>

        <!-- customer_visits -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div class="title mb-10 d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Últimas Visitas Clientes</h6>
            </div>
            <div class="todo-list-wrapper">
              <ul>
                @if (count($customer_visits) > 0 )
                  @foreach ($customer_visits as $customer_visit)
                    <li class="todo-list-item primary">
                      <div class="todo-content">
                        <p class="text-sm mb-2">
                          <i class="lni lni-calendar"></i> Fecha Prox: {{ $customer_visit->next_visit_date }} | <i class="lni lni-alarm-clock"></i> Hora Prox: {{ $customer_visit->next_visit_hour }}
                        </p>
                        <a href="{{ url('/admin/customer_visits/show/'.$customer_visit->id ) }}"><h6 class="{{ ($customer_visit->status == 'Procesado' || $customer_visit->status == 'Pendiente') ? 'text-dark' : 'text-disabled' }}">{{ $customer_visit->customer_name }}</h6></a>
                        <p class="text-sm">
                          Creado el: {{ date('d/m/Y', strtotime($customer_visit->visit_date)) }} | <i class="lni lni-user"></i> Agente: {{ $customer_visit->seller_name }}
                        </p>
                      </div>
                      <div class="todo-status">
                        <span class="status-btn primary-btn text-bold">
                          {{ $customer_visit->action }}
                        </span>
                        <span class="status-btn 
                          @if($customer_visit->status == 'Procesado') info-btn
                          @elseIf($customer_visit->status == 'No Procesado') danger-btn
                          @elseIf($customer_visit->status == 'Pendiente') primary-btn
                          @elseIf($customer_visit->status == 'Cancelado') light-btn
                          @endif">
                          {{ $customer_visit->status }}
                        </span>
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
              <p><a href="{{ url('/admin/reports/customer_visits') }}"><span class="s2mY5ma">Ver informe completo</span></a></p>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </section>

  <script>
     var sales;
    $(document).ready(function(){

      $('.seller').change(function() {
          var $option = $(this).find('option:selected');

          var value = $option.val();//to get content of "value" attrib
          var text = $option.text();//to get <option>Text</option> content

          $.ajax({
            type    : 'GET',
            url     :"{{ URL::to('/admin/reports/findSeller') }}",
            dataType: 'json',
            data: {
              '_token' : '{{ csrf_token() }}',
              'id' : value //id seller
            },
            success:function (response) {
              var data = response;

              var string_data = JSON.stringify(data); 
              console.log(string_data);

              //info agent
              document.getElementById('idReference').innerHTML = data.seller.idReference;
              document.getElementById('name').innerHTML = data.seller.name;
              document.getElementById('doc_id').innerHTML = data.seller.doc_id;
              document.getElementById('phone_1').innerHTML = data.seller.phone_1;
              document.getElementById('estate').innerHTML = data.seller.estate;
              if (data.seller.status == 1) {
                document.getElementById('status').innerHTML = 'Habilitado';
              }else{
                document.getElementById('status').innerHTML = 'Deshabilitado';
              }

              $sales = data.sales;
              console.log($sales);

              //info date
              var currentMonthName = data.currentMonthName;
              var currentOnlyYear = data.currentOnlyYear;
              var salesPeriods = data.salesPeriods;

              //info data chart 1
              var sales_count = data.sales_count;
              var orders_count = data.orders_count;

              //info data chart 2
              var visits_cancel_count = data.visits_cancel_count;
              var visits_process_count = data.visits_process_count;
              var visits_no_process_count = data.visits_no_process_count;
              var visits_pending_count = data.visits_pending_count;

              //info data cards
              document.getElementById('cant_customers').innerHTML = data.cant_customers;
              document.getElementById('visited_less_30_days').innerHTML = data.visited_less_30_days;
              document.getElementById('visited_more_30_days').innerHTML = data.visited_more_30_days;
              document.getElementById('visited_more_90_days').innerHTML = data.visited_more_90_days;

              //info data column chart
              var salesCountByMonth = data.salesCountByMonth;
              var salesCancelCountByMonth = data.salesCancelCountByMonth;
              var ordersCountByMonth = data.ordersCountByMonth;
              var ordersCancelCountByMonth = data.ordersCancelCountByMonth;

              //var ordersCancelCountByMonth = Number(ordersCancelCountByMonth.replace(/[^0-9.-]+/g,""));
              console.log(salesCountByMonth);

              //calls functions
              highcharts_chart(sales_count, orders_count, currentMonthName);
              highcharts_chart2(visits_cancel_count, visits_process_count, visits_no_process_count, visits_pending_count, currentMonthName);
              highcharts_chart_column(salesCountByMonth, ordersCountByMonth, salesCancelCountByMonth, ordersCancelCountByMonth, currentMonthName, currentOnlyYear, salesPeriods);
            }
          });

      });
    });

    function highcharts_chart(sales_count, orders_count, currentMonthName){
      Highcharts.chart('container_3', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
          text: 'Estado de Ventas mes - ' + currentMonthName
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
                y: sales_count,
                sliced: true,
                selected: true,
                color: '#4a6cf7',
            }, {
                name: 'Presupuestos',
                y: orders_count,
                color: '#c1e4fe',
            }]
          }]
      });
    }

    function highcharts_chart2(visits_cancel_count, visits_process_count, visits_no_process_count, visits_pending_count, currentMonthName){
      Highcharts.chart('container', {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
        },
        title: {
          text: 'Estado de Visitas Clientes mes - ' + currentMonthName
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
    }

    function highcharts_chart_column(salesCountByMonth, ordersCountByMonth, salesCancelCountByMonth, ordersCancelCountByMonth, currentMonthName, currentOnlyYear, salesPeriods){
      Highcharts.chart('container_2', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Ventas y Presupuestos - ' + currentOnlyYear
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
    }

  </script>

@endsection