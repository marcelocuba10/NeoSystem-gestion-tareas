@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Parámetros</h2>
              @can('parameter-sa-create')
                <a href="{{ url('/admin/parameters/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/admin/parameters/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar Parámetro..">
                  <button type="submit"><i class="lni lni-search-alt"></i></button>
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
          <div class="col-lg-6 col-xl-6 col-xxl-6">
            <div class="card-style mb-30">
              <div class="table-wrapper table-responsive">
                <table class="table striped-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><h6>Nombre</h6></th>
                      <th><h6>Tipo</h6></th>
                      <th><h6>Descripción</h6></th>
                      <th><h6>Acciones</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (count($parameters) > 0 )
                      @foreach ($parameters as $parameter)
                      <tr>
                        <td class="min-width"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="min-width"><p>{{ $parameter->name }}</p></td>
                        <td class="min-width"><p>{{ $parameter->type }}</p></td>
                        <td class="min-width"><p>{{ $parameter->description }}</p></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <div class="action">
                              <a href="{{ url('/admin/parameters/show/'.$parameter->id) }}">
                                <button class="text-active">
                                  <i class="lni lni-eye"></i>
                                </button>
                              </a>
                            </div>
                            @can('parameter-sa-edit')
                            <div class="action">
                              <a href="{{ url('/admin/parameters/edit/'.$parameter->id) }}">
                                <button class="text-info">
                                  <i class="lni lni-pencil"></i>
                                </button>
                              </a>
                            </div>
                            @endcan
                            @can('parameter-sa-delete')
                            <form method="POST" action="{{'/admin/parameters/delete/'.$parameter->id}}">
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
                    @else
                      <tr>
                        <td class="text-sm"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width">Sin resultados encontrados</td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                        <td class="min-width"></td>
                      </tr>
                    @endif  
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-xl-6 col-xxl-6">
            <div class="card-style mb-30">
              <div class="title mb-10 d-flex justify-content-between align-items-center">
                <h6 class="mb-10">Notificaciones Email</h6>
              </div>
              <div class="col-12">
                <div class="input-style-1">
                  <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Email Predeterminado</label>
                  <input type="text" placeholder="Ingrese un email" value="{{ $emailDefault }}" readonly>
                  <span class="form-text m-b-none">En este email llegarán las notificaciones cuando el agente registra eventos en el sistema.</span>
                </div>
                <a href="{{ url('/admin/parameters/email/edit/') }}">
                  <button type="button" class="btn btn-block btn-secondary">Cambiar Email</button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection