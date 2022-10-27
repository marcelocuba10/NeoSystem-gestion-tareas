@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Agentes</h2>
              @can('seller-sa-create')
                <a href="{{ url('/admin/sellers/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex" style="margin-top: -35px;float: right;">
                <form action="{{ url('/admin/sellers/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar vendedor..">
                  {{-- <button type="submit"><i class="lni lni-search-alt"></i></button> --}}
                </form>
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
              <div class="table-wrapper table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="md"><h6>Código</h6></th>
                      <th class="md"><h6>Razón Social</h6></th>
                      <th class="md"><h6>Ciudad</h6></th>
                      <th class="md"><h6>Localidad</h6></th>
                      <th class="md"><h6>Teléfono</h6></th>
                      <th class="md"><h6>Status</h6></th>
                      <th class="md"><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $user)
                      <tr>
                        <td class="min-width"><p>{{ $user->idReference }}</p></td>
                        <td class="min-width"><h5 class="text-bold {{ ($user->status == 1) ? 'text-dark' : 'text-disabled' }}"><a href="{{ url('/admin/sellers/show/'.$user->id ) }}">{{ $user->name }}</a></h5></td>
                        <td class="min-width"><p>{{ $user->city }}</p></td>
                        <td class="min-width"><p>{{ $user->estate }}</p></td>
                        <td class="min-width"><p><i class="lni lni-phone mr-10"></i>{{ $user->phone_1 }}</p></td>
                        <td class="min-width">
                          @if ($user->status == 1)
                            <p><span class="status-btn primary-btn">Activado</span></p>
                          @else
                            <p><span class="status-btn light-btn">Desactivado</span></p>
                          @endif
                        </td>
                        <td class="text-right">
                          <div class="btn-group">
                            <div class="action">
                              <a href="{{ url('/admin/sellers/show/'.$user->id) }}">
                                <button class="text-active">
                                  <i class="lni lni-eye"></i>
                                </button>
                              </a>
                            </div>
                            @can('seller-sa-edit')
                              <div class="action">
                                <a href="{{ url('/admin/sellers/edit/'.$user->id) }}">
                                  <button class="text-info">
                                    <i class="lni lni-pencil"></i>
                                  </button>
                                </a>
                              </div>
                            @endcan
                            {{-- @can('seller-sa-delete')
                              <form method="POST" action="{{ url('/admin/sellers/delete/'.$user->id) }}">
                                @csrf
                                <div class="action">
                                  <input name="_method" type="hidden" value="DELETE">
                                  <button type="submit" class="text-danger">
                                    <i class="lni lni-trash-can"></i>
                                  </button>
                                </div>
                              </form>
                            @endcan --}}
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (isset($search))
                  {!! $users-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
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