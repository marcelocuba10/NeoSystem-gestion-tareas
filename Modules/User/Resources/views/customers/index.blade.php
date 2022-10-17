@extends('user::layouts.adminLTE.app')
@section('content')

<section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Listado de Clientes</h2>
              @can('customer-create')
                <a href="{{ url('/user/customers/create') }}" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
              @endcan  
            </div>
          </div>
          <!-- end col -->
          <div class="col-md-4">
            <div class="right">
              <div class="table-search d-flex st-input-search">
                <form action="{{ url('/user/customers/search') }}">
                  <input style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar cliente..">
                  <button type="submit"><i class="lni lni-search-alt"></i></button>
                </form>
              </div>
            </div>
          </div>
          <!-- end col -->
        </div>
        <!-- end row -->
      </div>

      <!-- ========== title-wrapper end ========== -->

      <!-- ========== tables-wrapper start ========== -->
      <div class="tables-wrapper">
        <div class="row">
            <div class="col-lg-12">
              <div class="card-style mb-30">
                <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="left">
                {{-- <div class="dataTable-dropdown">
                  <label>
                      <select class="dataTable-selector">
                          <option value="5">5</option>
                          <option value="10" selected="">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option>
                          <option value="25">25</option>
                      </select> entries per page
                  </label>
                </div> --}}
              </div>
              <div class="right">
              </div>
            </div>
                <div class="table-wrapper table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><h6>#</h6></th>
                        <th><h6>Razón Social</h6></th>
                        <th><h6>Rubro</h6></th>
                        <th><h6>Teléfono</h6></th>
                        <th><h6>Email</h6></th>
                        <th><h6>Localidad</h6></th>
                        <th><h6>Acciones</h6></th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                        <tr>
                            <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                            <td class="min-width"><h5 class="text-bold text-dark"><a href="{{ url('/user/customers/show/'.$customer->id ) }}">{{ $customer->name }} {{ $customer->last_name ?? old('last_name') }}</a></h5></td>
                            <td class="text-sm" style="width: 180px;">
                              @foreach ($categories as $item) 
                                <span class="{{ in_array($item->id, json_decode($customer->category) )  ? 'show-span' : 'hide-span' }} ">
                                  {{ $item->name }}
                                </span>
                              @endforeach 
                            </td>
                            <td class="min-width"><p>{{ $customer->phone }}</p></td>
                            <td class="min-width"><p>{{ $customer->email }}</p></td>
                            <td class="min-width"><p>{{ $customer->estate }}</p></td>
                            <td class="text-right">
                                <div class="btn-group">
                                    <div class="action">
                                      <a href="{{ url('/user/customers/show/'.$customer->id) }}">
                                          <button class="text-active"><i class="lni lni-eye"></i></button>
                                      </a>
                                    </div>
                                    @can('customer-edit')
                                    <div class="action">
                                        <a href="{{ url('/user/customers/edit/'.$customer->id) }}">
                                            <button class="text-info"><i class="lni lni-pencil"></i></button>
                                        </a>
                                    </div>
                                    @endcan
                                    @can('customer-delete')
                                    <form method="POST" action="{{ url('/user/customers/delete/'.$customer->id) }}">
                                        @csrf
                                        <div class="action">
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button type="submit" class="text-danger"><i class="lni lni-trash-can"></i></button>
                                        </div>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                      <!-- end table row -->
                    </tbody>
                  </table>
                  <!-- end table -->
                  @if (isset($search))
                      {!! $customers-> appends($search)->links() !!} <!-- appends envia variable en la paginacion-->
                  @else
                      {!! $customers-> links() !!}    
                  @endif
                </div>
              </div>
              <!-- end card -->
            </div>
            <!-- end col -->
          </div>
        <!-- end row -->
      </div>
      <!-- ========== tables-wrapper end ========== -->
    </div>
    <!-- end container -->
  </section>

@endsection