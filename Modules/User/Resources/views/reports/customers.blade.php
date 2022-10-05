@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Relatorio de Clientes</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="invoice-wrapper">
        <div class="row">
          <div class="col-12">
            <div class="invoice-card card-style mb-30">
              <div class="invoice-header">
                <div class="invoice-for">
                  <form action="#">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="input-style-1">
                          <label>Cliente</label>
                          <form action="{{ url('/user/reports/customers/search') }}">
                            <input disabled class="bg-gray" style="background-color: #fff;" id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar cliente..">
                          </form>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-style-1">
                          <label>Desde</label>
                            <input type="date" name="date" id="date" value="{{ $schedule->date ?? old('date') }}" readonly class="bg-gray">  
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-style-1">
                          <label>Hasta</label>
                            <input type="date" name="date" id="date" value="{{ $schedule->date ?? old('date') }}" readonly class="bg-gray">  
                        </div>
                      </div>
                      <div class="col-md-3"style="margin-top: 35px;">
                        <div class="input-style-1">
                          <a href="#" class="btn btn-lg info-btn rounded-md btn-hover disabled" role="button" aria-disabled="true"><i class="lni lni-search"></i></a>
                          <a href="{{ url('/user/reports/customers?download=pdf') }}" class="btn btn-lg success-btn rounded-md btn-hover" target="_blank"><i class="lni lni-printer"></i></a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #8dbba4;">
                    <tr>
                      <th>
                        <h6 class="text-sm text-medium">#</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Razón Social</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Teléfono</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Email</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">¿Es Vigia?</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Localidad</h6>
                      </th>
                      <th>
                        <h6 class="text-sm text-medium">Próxima Visita</h6>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($customers as $customer)
                      <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="text-sm"><p>{{ $customer->name }}</p></td>
                        <td class="text-sm"><p><i class="lni lni-phone mr-10"></i>{{ $customer->phone }}</p></td>
                        <td class="text-sm"><p>{{ $customer->email }}</p></td>
                        <td class="text-sm">
                          @if ($customer->is_vigia == "on")
                            <p>Sí</p>
                          @else
                            <p>No</p>
                          @endif
                        </td>
                        <td class="text-sm"><p>{{ $customer->estate }}</p></td>
                        <td class="text-sm"><p>{{ $customer->next_visit_date }}</p></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (isset($filter))
                {{-- {{ $machines->appends(['sort' =>$filter])->links() }}  --}}
                {{-- {!! $machines->appends(Request::except('page'))->render() !!} --}}
                  {!! $customers-> appends($filter)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                  {!! $customers-> links() !!}    
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection