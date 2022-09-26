@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Permisos</h2>
              @can('permission-sa-create')
                <a href="{{ url('/admin/ACL/permissions/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex" style="margin-top: -35px;float: right;">
                <form action="{{ url('/admin/ACL/permissions/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar Permiso..">
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
              <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="left"></div>
              <div class="right"></div>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th><h6>#</h6></th>
                    <th><h6>Nombre</h6></th>
                    <th><h6>Guard</h6></th>
                    <th><h6>Acciones</h6></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($permissions as $permission)
                  <tr>
                    <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                    <td class="min-width"><p>{{ $permission->name }}</p></td>
                    <td class="min-width"><p>{{ $permission->guard_name }}</p></td>
                    <td class="text-right">
                      <div class="btn-group">
                        @can('permission-sa-edit')
                        <div class="action">
                          <a href="{{ url('/admin/ACL/permissions/edit/'.$permission->id) }}">
                            <button class="text-info">
                              <i class="lni lni-pencil"></i>
                            </button>
                          </a>
                        </div>
                        @endcan
                        @can('permission-sa-delete')
                        <form method="POST" action="{{ url('/admin/ACL/permissions/delete/'.$permission->id) }}">
                          @csrf
                          <div class="action">
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit" class="text-danger">
                              <i class="lni lni-trash-can"></i>
                            </button>
                          </div>
                        </form>
                        @endcan
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @if (isset($search))
                {!! $permissions-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
              @else
                {!! $permissions-> links() !!}    
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection