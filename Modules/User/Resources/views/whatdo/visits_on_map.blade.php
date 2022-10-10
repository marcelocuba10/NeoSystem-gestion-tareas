@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">¿Que Puedo Hacer Hoy?</h2>
              <a href="{{ url('/user/whatdo') }}" class="main-btn success-btn btn-hover btn-sm"><i class="lni lni-map-marker mr-5"></i>Visualizar Lista</a>
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
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
                    <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/whatdo') }}">Atrás</a>
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

        for (i = 1; i < locations.length; i++) { 
            addMarker(locations[i]);
          

            // var marker = new google.maps.LatLng(lat, lng);
            // // var marker = new google.maps.LatLng(parseFloat(marker.lat),parseFloat(marker.lng));
            // var mark = new google.maps.Marker({
            //     map: map,
            //     position: marker,
            // });
              
            // google.maps.event.addListener(marker, 'click', (function(marker, i) {
            //   return function() {
            //     infowindow.setContent(locations[i]['customer_name'],locations[i]['longitude']);
            //     infowindow.open(map, marker);
            //   }
            // })(marker, i));

          }


          function addMarker(marker){

                var customer_name = marker.customer_name;
                var city = marker.city;
                var estate = marker.estate;
                var visit_date = marker.visit_date;

                // alert(customer_name + city + estate + visit_date);

                var html = "<b>" + customer_name + "</b> <br/>" + city +",<br/>"+estate+" "+visit_date+",<br/>";

                var markerLatlng = new google.maps.LatLng(parseFloat(marker.latitude),parseFloat(marker.longitude));
                //alert(markerLatlng);

                var mark = new google.maps.Marker({
                    map: map,
                    position: markerLatlng,
                });

                // var infoWindow = new google.maps.InfoWindow;

                // google.maps.event.addListener(mark, 'click', function(){
                //     infoWindow.setContent(html);
                //     infoWindow.open(map, mark);
                // });

                google.maps.event.addListener(mark, 'click', (function() {
                    return function() {
                      //infowindow.setContent(marker.customer_name  + "</b> <br/>" + city);
                      infowindow.setContent(html);
                      infowindow.open(map, mark);
                    }
                  })(mark , i));

                return mark;
          }


          // @foreach($customer_visits as $customers)
          //   var lat = <?php 
          //            $data = $customer_visits[0]->latitude;
          //            $data = str_replace('"', '', $data);
          //            echo str_replace('"', '', $data);
          //          ?>;
          //   var lng = {{ $customers->longitude }}

          //   console.log(lat + lng);

          //   marker = new google.maps.Marker({
          //           position: new google.maps.LatLng(lat, lng),
          //           map: map,
                 
          //         });

          // @endforeach

          


    }

    window.initMap = initMap;

</script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyA8kdDnbu_w0TBIJPPk5JTwgQMXwsLuNBY&callback=initMap"></script>

@endsection