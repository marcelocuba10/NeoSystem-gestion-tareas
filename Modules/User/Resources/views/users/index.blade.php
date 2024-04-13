@extends('user::layouts.adminLTE.app')
@section('content')

<section class="table-components">
  <div class="container-fluid">
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-8">
          <div class="title d-flex align-items-center flex-wrap mb-30">
            <h2 class="mr-40">Usuarios</h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="right">
            <div class="table-search d-flex" style="margin-top: -35px;float: right;">
              <form action="{{ url('/user/users/search') }}">
                <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar..">
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
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="left"></div>
              <div class="right"></div>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th class="sm"><h6 class="text-sm text-medium">#</h6></th>
                    <th class="md"><h6>Nombre</h6></th>
                    <th class="md"><h6>Apellidos</h6></th>
                    <th class="md"><h6>Teléfono</h6></th>
                    <th class="md"><h6>Email</h6></th>
                    <th class="md"><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $user)
                  <tr>
                    <td class="min-width"><h6 class="text-sm">{{ ++$i }}</h6></td>
                    <td class="min-width"><p>{{ $user->name }}</p></td>
                    <td class="min-width"><p>{{ $user->last_name }}</p></td>
                    <td class="min-width"><p><i class="lni lni-phone mr-10"></i>{{ $user->phone }}</p></td>
                    <td class="min-width"><p><i class="lni lni-envelope mr-10"></i>{{ $user->email }}</p></td>
                    <td class="text-right">
                      <div class="btn-group">
                        <div class="action">
                          <a href="{{ url('/user/users/show/'.$user->id) }}">
                            <button class="text-active">
                              <i class="lni lni-eye"></i>
                            </button>
                          </a>
                        </div> 
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @if (isset($search))
                {!! $users-> appends($search)->links() !!}
              @else
                {!! $users-> links() !!}    
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection