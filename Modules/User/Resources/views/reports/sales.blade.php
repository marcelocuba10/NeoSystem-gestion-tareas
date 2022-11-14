@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Resumen Ventas</h2>
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <input style="background-color: #fff;" type="text" id="search" value="{{ $search ?? '' }}" placeholder="Buscar Cliente..">
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
                        <div class="select-style-1">
                          <label>Estado</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="status" style="width: 122px;">
                              @foreach ($status as $item)
                                <option value="{{ $item }}"> {{ $item}} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Tipo</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="types">>
                              @foreach ($types as $item)
                                <option value="{{ $item }}"> {{ $item }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <div class="select-style-1">
                        <label>Fecha</label>
                        <div class="select-position select-sm">
                          <select class="light-bg" id="visit_date" style="width: 166px;">
                            @foreach ($visits_labels as $item)
                              <option value="{{ $item }}"> {{ $item}} </option>
                            @endforeach 
                          </select>
                        </div>
                      </div>
                    </ul>
                  </div>
                </div>
                <div class="right">
                  <ul class="legend3 d-flex align-items-center mb-30">
                    <li>
                      <div class="d-flex">
                        <div class="text">
                          <button class="btn-group-status" name="filter" value="" id="clear"><p class="text-sm text-dark"><i class="lni lni-close"></i>&nbsp; Quitar Filtros</p></button>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="table-wrapper table-responsive">
                <div id="datatable"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
  <script type="text/javascript">
    $('.show_confirm').click(function(event) {
      var form =  $(this).closest("form");
      var name = $(this).data("name");
      event.preventDefault();
      swal({
          title: '¿Está seguro que desea cancelar este registro?',
          // text: "Si eliminas esto, desaparecerá para siempre.",
          icon: "warning",
          buttons: true,
          dangerMode: true,
          buttons: ["No", "Sí"],
      })
      .then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).ready(function(){

      document.getElementById('clear').style.display = 'none';

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/reports/filter_sales') }}",
        data: { 
          filter : '',
          "_token": "{{ csrf_token() }}",
        },
          success:function(datatable)
          {
            $("#datatable").html(datatable);
          }
      });

    });

    $( "#clear" ).click(function() {
      console.log('clear');

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/reports/filter_sales') }}",
        data: { 
          filter : '',
          "_token": "{{ csrf_token() }}",
        },
          success:function(datatable)
          {
            $("#datatable").html(datatable);
          }
      });

      //hide
      document.getElementById('clear').style.display = 'none';
    });

    $("#search").keyup(function(event) {
      //if press key Enter in input
      if (event.keyCode === 13) {
        var search = $(this).val();
        console.log('search');

        $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/reports/search_sales') }}",
            data: { 
              search : search,
              "_token": "{{ csrf_token() }}",
            },
              success:function(datatable)
              {
                $("#datatable").html(datatable);
              }
          });
      }
    });

    $("#status").change(function () {
      var optionSelected = $(this).val();
      var type = 'status';

      console.log(type + ' : ' + optionSelected);

      document.getElementById('clear').style.display = 'initial';

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/reports/filter_sales') }}",
        data: { 
          filter : optionSelected,
          type: type,
          "_token": "{{ csrf_token() }}",
        },
          success:function(datatable)
          {
            $("#datatable").html(datatable);
          }
      });
    });

    $("#types").change(function () {
      var optionSelected = $(this).val();
      var type = 'types';

      console.log(type + ' : ' + optionSelected);

      document.getElementById('clear').style.display = 'initial';

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/reports/filter_sales') }}",
        data: { 
          filter : optionSelected,
          type: type,
          "_token": "{{ csrf_token() }}",
        },
          success:function(datatable)
          {
            $("#datatable").html(datatable);
          }
      });
    });

    $("#visit_date").change(function () {
      var optionSelected = $(this).val();
      var type = 'visit_date';

      console.log(type + ' : ' + optionSelected);

      document.getElementById('clear').style.display = 'initial';

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/reports/filter_sales') }}",
        data: { 
          filter : optionSelected,
          type: type,
          "_token": "{{ csrf_token() }}",
        },
          success:function(datatable)
          {
            $("#datatable").html(datatable);
          }
      });
    });

  </script>

@endsection