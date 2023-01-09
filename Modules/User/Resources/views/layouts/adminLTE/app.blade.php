<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="{{ asset('/public/adminLTE/images/favicon.png') }}" type="image/x-icon" />
  <title>{{ config('app.name')}}</title>

  <!-- ========== All CSS files linkup ========= -->
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/lineicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/quill/bubble.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/quill/snow.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/materialdesignicons.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/fullcalendar.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/main.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/morris.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/datatable.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/adminLTE/css/vanilla-dataTables.min.css') }}" />
  <link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">
  <link rel="stylesheet" href="{{ asset('public/css/custom-style.css') }}" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

  <!-- ========= All Javascript files linkup ======== -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>

  <!-- ========= All jQuery files linkup ======== -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

</head>

<body>
  <!-- ========= All HTML content ======== -->
  @include('user::layouts.adminLTE.sidebar')
  @include('user::layouts.includes.alerts')

  <main class="main-wrapper">
    @include('user::layouts.adminLTE.navbar')
    @yield('content')
    @include('user::layouts.adminLTE.footer')
  </main>

  <!-- ========= All Javascript files linkup ======== -->
  <script src="{{ asset('public/adminLTE/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/Chart.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/dynamic-pie-chart.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/moment.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/fullcalendar.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/jvectormap.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/world-merc.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/polyfill.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/main.js') }}"></script>
  <script src="{{ asset('public/js/custom.js') }}"></script>
  <script src="{{ asset('public/js/accounting.min.js') }}"></script>
  <script src="{{ asset('public/js/vanilla-masker.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/apexcharts.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/quill.min.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/datatable.js') }}"></script>
  <script src="{{ asset('public/adminLTE/js/Sortable.min.js') }}"></script>

  <!-- ========= Maskmoney files linkup ======== -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" type="text/javascript"></script>

  <!-- ========= MultiSelect linkup ======== -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- ========= Select2 Multiple ======== -->
  <script>
    $(document).ready(function() {
      $('.select2-multiple_1').select2({
        placeholder: "Seleccione Rubro..",
        allowClear: true,
        width: '100%',
      });
    });
    $(document).ready(function() {
      $('.select2-multiple_2').select2({
        placeholder: "Seleccione Equipos..",
        allowClear: true,
        width: '100%',
      });
    });
  </script>
  <!-- ========= InputMask Currency ======== -->
  <script>
    $(function() {
      $('#currency_1').maskMoney({
        precision: 3,
        thousands: '.',
        decimal: '.'
      });
      $('#currency_2').maskMoney({
        precision: 3,
        thousands: '.',
        decimal: '.'
      });
    })
  </script>
  <!-- ========= Alert hide ======== -->
  <script>
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert-success").alert('close');
        }, 3500);
    
        setTimeout(function() {
            $(".alert-danger").alert('close');
        }, 3500);
    });

    /** ========= InputMask ======== **/
    document.addEventListener("DOMContentLoaded", readyInputMask);
    function readyInputMask() {
      VMasker(document.getElementById("phone")).maskPattern('(999) 999 999');
      VMasker(document.getElementById("date")).maskPattern('99/99/9999');
      VMasker(document.getElementById("date_2")).maskPattern('99/99/9999');
    }

    /** ========= Tooltip ======== **/
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });

    /** ========= Calendar ======== **/
    document.addEventListener("DOMContentLoaded", function () {
      var calendarMiniEl = document.getElementById("calendar-mini");
      var calendarMini = new FullCalendar.Calendar(calendarMiniEl, {
        initialView: "dayGridMonth",
        headerToolbar: {
          end: "today prev,next",
        },
      });
      calendarMini.render();
    });
  </script>
  
</body>

</html>