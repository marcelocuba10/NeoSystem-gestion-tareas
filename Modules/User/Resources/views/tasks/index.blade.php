@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Tareas</h2>
              <a href="{{ url('/user/tasks/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/user/tasks/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar..">
                  <button type="submit"><i class="lni lni-search-alt"></i></button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tables-wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="card-style mb-30">
              <div class="table-wrapper table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th><h6>Título</h6></th>
                      <th><h6>Responsable</h6></th>
                      <th><h6>Prioridad</h6></th>
                      <th><h6>Estado</h6></th>
                      <th><h6>Registro</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (count($tasks) > 0 )
                      @foreach ($tasks as $task)
                        <tr>
                          <td class="text-sm"><h6 class="text-dark">{{ $task->title }}</h6></td>
                          <td class="min-width"><p>{{ $task->user_assigned_name }}</p></td>
                          <td class="min-width"><p>{{ $task->priority }}</p></td>
                          @if ($task->status == 2)
                            <td class="min-width"><span class="status-btn primary-btn">Completado</span></td>
                          @elseif($task->status == 1)
                            <td class="min-width"><span class="status-btn light-btn">Pendiente</span></td>
                          @else
                            <td class="min-width"><span class="status-btn light-btn">Cancelado</span></td>
                          @endif

                          <td class="min-width"><p>{{ date('d/m/Y H:s', strtotime($task->created_at)) }}</p></td>

                          <td class="text-right">
                            <div class="btn-group">
                                @if ($task->user_id == Auth::user()->id)
                                  <div class="action">
                                    <a href="{{ url('/user/tasks/edit/'.$task->id) }}" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                      <button class="text-info"><i class="lni lni-pencil"></i></button>
                                    </a>
                                  </div>
                                  <form method="POST" action="{{ url('/user/tasks/delete/'.$task->id) }}" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    @csrf
                                    <div class="action">
                                      <input name="_method" type="hidden" value="DELETE">
                                      <button type="submit" class="text-danger show_confirm"><i class="lni lni-trash-can"></i></button>
                                    </div>
                                  </form>
                                @endif
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    @else
                      <tr>
                        <td class="text-sm"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width">Sin información</td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                      </tr>
                    @endif
                  </tbody>
                </table>
                @if (isset($search))
                    {!! $tasks-> appends($search)->links() !!}
                @else
                    {!! $tasks-> links() !!}    
                @endif
              </div>
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
              title: '¿Está seguro que desea eliminar este registro?',
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
  </script>
@endsection