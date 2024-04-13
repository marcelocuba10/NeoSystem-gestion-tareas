@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="section">
    <div class="container-fluid">
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Bienvenido a {{ config('app.name') }}</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon purple">
              <i class="lni lni-bookmark"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Tareas</h6>
              <h3 class="text-bold mb-10">{{ $qty_tasks }}</h3>
              <p class="text-sm text-success">
                <span class="text-gray">(Últimos 30 días)</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon orange"><i class="lni lni-bookmark"></i></div>
            <div class="content">
              <h6 class="mb-10">Tareas Pendientes</h6>
              <h3 class="text-bold mb-10">2</h3>
              <p class="text-sm text-success">
                <span class="text-gray">(más de 30 días)</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon success"><i class="lni lni-bookmark"></i></div>
            <div class="content">
              <h6 class="mb-10">Tareas Atrasadas</h6>
              <h3 class="text-bold mb-10">1</h3>
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
              <h6 class="mb-10">Total Usuarios</h6>
              <h3 class="text-bold mb-10">{{ $qty_users }}</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="row" bis_skin_checked="1">
        <div class="col-lg-6" bis_skin_checked="1">
          <div class="card-style mb-30" bis_skin_checked="1">
            <div class="title mb-10 d-flex justify-content-between align-items-center" bis_skin_checked="1">
              <h6 class="mb-10">Tareas Recientes</h6>
            </div>
            <div class="todo-list-wrapper" bis_skin_checked="1">
              <ul>
                @foreach ($tasks as $task)
                  <li class="todo-list-item success">
                    <div class="todo-content" bis_skin_checked="1">
                      <p class="text-sm mb-2">
                        <i class="lni lni-calendar"></i>
                        {{ date('d/m/Y H:s', strtotime($task->created_at)) }}
                      </p>
                      <h5 class="text-bold mb-10">{{ $task->title }}</h5>
                      <p class="text-sm">
                        <i class="lni lni-alarm-clock"></i>
                        {{ $task->priority }}
                      </p>
                    </div>
                    <div class="todo-status" bis_skin_checked="1">
                      @if($task->status == 2)
                        <span style="float: right;" class="status-btn primary-btn">Terminado</span>
                      @elseIf($task->status == 1)
                        <span style="float: right;" class="status-btn success-btn">Pendiente</span>
                      @elseIf($task->status == 0) 
                        <span style="float: right;" class="status-btn light-btn">Cancelado</span>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-6" bis_skin_checked="1">
          <div class="card-style clients-table-card mb-30" bis_skin_checked="1">
            <div class="title d-flex justify-content-between align-items-center" bis_skin_checked="1">
              <h6 class="mb-10">Usuarios</h6>
            </div>
            <div class="table-wrapper table-responsive" bis_skin_checked="1">
              <table class="table">
                <tbody>
                  @foreach ($users as $user)
                    <tr>
                      <td>
                        <div class="employee-image" bis_skin_checked="1">
                          <img src="{{ asset('/public/images/user-icon-business-man-flat-png-transparent.png') }}" alt="">
                        </div>
                      </td>
                      <td class="employee-info">
                        <h5 class="text-medium">{{ $user->name }}</h5>
                        <p>{{ $user->email }}</p>
                      </td>
                      <td>
                        <div class="d-flex justify-content-end" bis_skin_checked="1">
                          <a href="{{ url('/user/users/profile/'.$user->id) }}"><button class="status-btn primary-btn">Ver Perfil</button></a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection