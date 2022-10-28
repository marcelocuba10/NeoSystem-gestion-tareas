@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Resumen Visitas Clientes</h2>
              <a href="{{ url('/admin/whatdo/visit_on_map') }}" class="main-btn success-btn btn-hover btn-sm"><i class="lni lni-map-marker mr-5"></i>Visualizar Mapa</a>
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
                          <label>Rubro / Categoría</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="category">>
                              @foreach ($categories as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Equipos Potenciales</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="potential_products">>
                              @foreach ($potential_products as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Localidad</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="estate">>
                              @foreach ($estates as $item)
                                <option value="{{ $item }}"> {{ $item }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      {{-- <li>
                        <div class="input-style-1">
                          <label>Fecha Última Visita</label>
                          <input class="input-sm" type="date" name="visit_date" id="visit_date" placeholder="DD/MM/YYYY"  class="bg-transparent">
                        </div>
                      </li> --}}
                      <div class="select-style-1">
                        <label>Fecha Última Visita</label>
                        <div class="select-position select-sm">
                          <select class="light-bg" id="visit_date" style="width: 166px;">
                            @foreach ($visits_labels as $item)
                              <option value="{{ $item }}"> {{ $item}} </option>
                            @endforeach 
                          </select>
                        </div>
                      </div>
                      {{-- <li>
                        <div class="input-style-1">
                          <label>Fecha Próxima Visita</label>
                          <input class="input-sm" type="date" name="next_visit_date" id="selectValue5" placeholder="DD/MM/YYYY"  class="bg-transparent">
                        </div>
                      </li> --}}
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
                {{-- <table class="table">
                  <thead>
                    <tr>
                      <th><h6>#</h6></th>
                      <th><h6>Cliente</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Presupuesto?</h6></th>
                      <th><h6>Fecha Visita</h6></th>
                      <th><h6>Fecha Prox Visita</h6></th>
                      <th><h6>Localidad</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($customer_visits as $customer_visit)
                      <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/admin/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
                        <td class="min-width">
                          <span class="status-btn 
                          @if($customer_visit->status == 'Visitado') secondary-btn
                          @elseIf($customer_visit->status == 'No Atendido') close-btn
                          @elseIf($customer_visit->status == 'Cancelado') warning-btn
                          @endif">
                            {{ $customer_visit->status }}
                          </span>
                        </td>
                        @if ($customer_visit->type == 'Order')
                          <td class="min-width"><p>Sí</p></td>
                        @elseIf($customer_visit->type == 'NoOrder')
                          <td class="min-width"><p>No</p></td>
                        @endif
                        <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->visit_date }}</p></td>
                        <td class="min-width"><p><i class="lni lni-calendar mr-10"></i>{{ $customer_visit->next_visit_date }}</p></td>
                        <td class="min-width"><p>{{ $customer_visit->estate }}</p></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <div class="action">
                              <a href="#">
                                <button class="text-active"><i class="lni lni-eye"></i></button>
                              </a>
                            </div>
                            @can('what_can_do-edit')
                            <div class="action">
                              <a href="#">
                                <button class="text-info"><i class="lni lni-pencil"></i></button>
                              </a>
                            </div>
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
                @endif --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    $(document).ready(function(){

      document.getElementById('clear').style.display = 'none';

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/admin/whatdo/filter') }}",
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
        url: "{{ URL::to('/admin/whatdo/filter') }}",
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
            url: "{{ URL::to('/admin/whatdo/search') }}",
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
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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

      $("#category").change(function () {
          var optionSelected = $(this).val();
          var type = 'category';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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

      $("#potential_products").change(function () {
          var optionSelected = $(this).val();
          var type = 'potential_products';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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

      $("#estate").change(function () {
          var optionSelected = $(this).val();
          var type = 'estate';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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

      $("#next_visit_date").change(function () {
          var optionSelected = $(this).val();
          var type = 'next_visit_date';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/admin/whatdo/filter') }}",
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