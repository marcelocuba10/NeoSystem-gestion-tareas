@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Gráficos</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="invoice-wrapper">
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
    </div>
  </section>

  
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
    
  var visits_cancel_count = <?php echo json_encode($visits_cancel_count); ?>;
  var visits_process_count = <?php echo json_encode($visits_process_count); ?>;
  var visits_no_process_count = <?php echo json_encode($visits_no_process_count); ?>;
  var visits_pending_count = <?php echo json_encode($visits_pending_count); ?>;

  console.log(visits_cancel_count);

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

  var salesCountByMonth = <?php echo json_encode($salesCountByMonth); ?>;
  var salesCancelCountByMonth = <?php echo json_encode($salesCancelCountByMonth); ?>;
  var ordersCountByMonth = <?php echo json_encode($ordersCountByMonth); ?>;
  var ordersCancelCountByMonth = <?php echo json_encode($ordersCancelCountByMonth); ?>;

  var salesPeriods = <?php echo json_encode($salesPeriods); ?>;

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