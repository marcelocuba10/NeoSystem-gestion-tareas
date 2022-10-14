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
              <a href="{{ url('/user/whatdo/visit_on_map') }}" class="main-btn success-btn btn-hover btn-sm"><i class="lni lni-map-marker mr-5"></i>Visualizar Mapa</a>
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
                    {{-- <ul class="legend3 d-flex flex-wrap align-items-center mb-30">
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-secondary"></span>
                          <div class="text">
                            <form action="{{ url('/user/whatdo/filter') }}">
                              <button class="btn-group-status" name="filter" value="Visitado" type="submit"><p class="text-sm text-dark">Visitados</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-enabled"></span>
                          <div class="text">
                            <form action="{{ url('/user/whatdo/filter') }}">
                              <button class="btn-group-status" name="filter" value="Pendiente" type="submit"><p class="text-sm text-dark">Pendientes</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-attention"> </span>
                          <div class="text">
                            <form action="{{ url('/user/whatdo/filter') }}">
                              <button class="btn-group-status" name="filter" value="Cancelado" type="submit"><p class="text-sm text-dark">Cancelados</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="d-flex">
                          <span class="bg-color bg-card-error"> </span>
                          <div class="text">
                            <form action="{{ url('/user/whatdo/filter') }}">
                              <button class="btn-group-status" name="filter" value="No Atendido" type="submit"><p class="text-sm text-dark">No Atendidos</p></button>
                            </form> 
                          </div>
                        </div>
                      </li>
                    </ul> --}}
                    <ul class="legend3 d-flex flex-wrap align-items-center mb-30">
                      <li>
                        <div class="select-style-1">
                          <label>Estado</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="selectValue">
                              @foreach ($status as $item)
                                <option value="{{ $item }}"> {{ $item}} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Rubro</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="selectValue2">>
                              @foreach ($customers_categories as $item)
                                <option value="{{ $item }}"> {{ $item }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Equipos Potenciales</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="selectValue2">>
                              @foreach ($customers_categories as $item)
                                <option value="{{ $item }}"> {{ $item }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="select-style-1">
                          <label>Localidad</label>
                          <div class="select-position select-sm">
                            <select class="light-bg" id="selectValue3">>
                              @foreach ($estates as $item)
                                <option value="{{ $item }}"> {{ $item }} </option>
                              @endforeach 
                            </select>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="input-style-1">
                          <label>Fecha Última Visita</label>
                          <input class="input-sm" type="date" name="next_visit_date" id="selectValue4" placeholder="DD/MM/YYYY"  class="bg-transparent">
                        </div>
                      </li>
                      <li>
                        <div class="input-style-1">
                          <label>Fecha Próxima Visita</label>
                          <input class="input-sm" type="date" name="next_visit_date" id="selectValue5" placeholder="DD/MM/YYYY"  class="bg-transparent">
                        </div>
                      </li>
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
                <div id="permissions">
                </div>
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
                        <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/user/customer_visits/show/'.$customer_visit->id ) }}">{{ $customer_visit->customer_name }}</a></h5></td>
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
        url: "{{ URL::to('/user/whatdo/filter') }}",
        data: { 
          filter : '',
          "_token": "{{ csrf_token() }}",
        },
          success:function(permissions)
          {
            $("#permissions").html(permissions);
          }
      });

    });

    $( "#clear" ).click(function() {
      console.log('clear');

      $.ajax({
        type: "GET",
        url: "{{ URL::to('/user/whatdo/filter') }}",
        data: { 
          filter : '',
          "_token": "{{ csrf_token() }}",
        },
          success:function(permissions)
          {
            $("#permissions").html(permissions);
          }
      });
    });

    $("#search").keyup(function(event) {
      //if press key Enter in input
      if (event.keyCode === 13) {
        var search = $(this).val();
        console.log('search');

        $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/search') }}",
            data: { 
              search : search,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      }
    });

    $("#selectValue").change(function () {
          var optionSelected = $(this).val();
          var type = 'Estado';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/filter') }}",
            data: { 
              filter : optionSelected,
              type: type,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      });

      $("#selectValue2").change(function () {
          var optionSelected = $(this).val();
          var type = 'Rubro';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/filter') }}",
            data: { 
              filter : optionSelected,
              type: type,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      });

      $("#selectValue3").change(function () {
          var optionSelected = $(this).val();
          var type = 'Localidad';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/filter') }}",
            data: { 
              filter : optionSelected,
              type: type,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      });

      $("#selectValue4").change(function () {
          var optionSelected = $(this).val();
          var type = 'Ultima Visita';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/filter') }}",
            data: { 
              filter : optionSelected,
              type: type,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      });

      $("#selectValue5").change(function () {
          var optionSelected = $(this).val();
          var type = 'Próxima Visita';

          console.log(type + ' : ' + optionSelected);

          document.getElementById('clear').style.display = 'initial';

          $.ajax({
            type: "GET",
            url: "{{ URL::to('/user/whatdo/filter') }}",
            data: { 
              filter : optionSelected,
              type: type,
              "_token": "{{ csrf_token() }}",
            },
              success:function(permissions)
              {
                $("#permissions").html(permissions);
              }
          });
      });

  </script>

@endsection