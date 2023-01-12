@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Informes de Clientes</h2> <a href="{{ url('/user/reports/customers?download=pdf') }}" class="btn btn-lg success-btn rounded-md btn-hover" target="_blank"><i class="lni lni-printer"></i></a>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="invoice-wrapper">
        <div class="row">
          <div class="col-12">
            <div class="invoice-card card-style mb-30">

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #DAEFFE;">
                    <tr>
                      <th><h6 class="text-sm text-medium">#</h6></th>
                      <th><h6 class="text-sm text-medium">Razón Social</h6></th>
                      <th><h6 class="text-sm text-medium">Teléfono</h6></th>
                      <th><h6 class="text-sm text-medium">¿Es Vigia?</h6></th>
                      <th><h6 class="text-sm text-medium">Ciudad</h6></th>
                      <th><h6 class="text-sm text-medium">Localidad</h6></th>
                      <th><h6 class="text-sm text-medium">Próxima Visita</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($customers as $customer)
                      <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="text-sm"><p>{{ $customer->name }}</p></td>
                        <td class="text-sm"><p><i class="lni lni-phone mr-10"></i>{{ $customer->phone }}</p></td>
                        <td class="text-sm">
                          @if ($customer->is_vigia == "on")
                            <p>Sí</p>
                          @else
                            <p>No</p>
                          @endif
                        </td>
                        <td class="text-sm"><p>{{ $customer->city }}</p></td>
                        <td class="text-sm"><p>{{ $customer->estate }}</p></td>
                        <td class="text-sm"><p>{{ $customer->next_visit_date }}</p></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (isset($filter))
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