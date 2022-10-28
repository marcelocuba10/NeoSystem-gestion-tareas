@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Visitas Clientes Por Zona Geográfica</h2>
              <a href="{{ url('/admin/whatdo') }}" class="main-btn success-btn btn-hover btn-sm"><i class="lni lni-map-marker mr-5"></i>Visualizar Lista</a>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <!-- ========== tables-wrapper start ========== -->
      <div class="form-layout-wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="card-style mb-30">
              <div class="row">
                <div id="map"></div>
                <div class="col-12">
                  <div class="button-group d-flex justify-content-center flex-wrap">
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/whatdo') }}">Atrás</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script type="text/javascript">

    var locations = {!! json_encode($customer_visits) !!};
    var marker, i;
    var map;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: { lat: -25.48313710, lng: -54.62047370 },
          zoom: 13
        });   

        var infowindow = new google.maps.InfoWindow();

        for (i = 0; i < locations.length; i++) { 
          console.log(i);
          addMarker(locations[i]);
        }

      function addMarker(marker){
        var customer_id = marker.customer_id;
        var customer_name = marker.customer_name;
        var visit_date = marker.visit_date;
        var next_visit_date = marker.next_visit_date;
        var next_visit_hour = marker.next_visit_hour;
        var estate = marker.estate;
        var action = marker.action;

        var html = "<b style='overflow: hidden;font-weight: 500;font-size: 14px;color:#333'>" + customer_name + "</b> <br/>Visitado: " + visit_date +",<br/>Localidad: "+ estate +",<br/>Próxima Visita: "+ next_visit_date +",<br/>Hora: "+ next_visit_hour + ",<br/>Acción: "+ action + "<br/><a href='/facundo/admin/customers/show/"+customer_id+"'>Ver Ficha Cliente</a>";

        const then = new Date(visit_date); //visit_date
        const now = new Date(); //current date
        //Subtract visit date with current date, result in milliseconds.
        const msBetweenDates = Math.abs(then.getTime() - now.getTime());

        // convert ms to days                 hour   min  sec   ms
        const daysBetweenDates = msBetweenDates / (24 * 60 * 60 * 1000);

        if (daysBetweenDates < 30) {
          console.log('visitado hace menos de 30 días');
          let a=(daysBetweenDates)/(1000*60*60*24);
          console.log('days:' + a);
          var imageColor = "{!! asset('public/images/markers/marker-icon-green-20x32.png') !!}";
        } 
        
        if(daysBetweenDates > 30 && daysBetweenDates < 90) {
          console.log('visitado entre 30 y 90 días');
          var imageColor ="{!! asset('public/images/markers/marker-icon-yellow-20x32.png') !!}";
        }

        if (daysBetweenDates > 90) {
          console.log('visitado hace más de 90 días');
          var imageColor = "{!! asset('public/images/markers/marker-icon-red-20x32.png') !!}";
        } 

        var markerLatlng = new google.maps.LatLng(parseFloat(marker.latitude),parseFloat(marker.longitude));

        var mark = new google.maps.Marker({
            map: map,
            position: markerLatlng,
            icon: imageColor,
        });

        google.maps.event.addListener(mark, 'click', (function() {
            return function() {
              infowindow.setContent(html);
              infowindow.open(map, mark);
            }
          })(mark , i));

        return mark;
      }
    }

    window.initMap = initMap;

</script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyA8kdDnbu_w0TBIJPPk5JTwgQMXwsLuNBY&callback=initMap"></script>

@endsection