@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Relatorio de Agentes</h2>
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
                          <label>Agente</label>
                          <form action="{{ url('/admin/reports/sellers/search') }}">
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
                          <a href="{{ url('/admin/reports/sellers?download=pdf') }}" class="btn btn-lg success-btn rounded-md btn-hover" target="_blank"><i class="lni lni-printer"></i></a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="table-responsive">
                <table class="invoice-table table">
                  <thead style="background-color: #DAEFFE;">
                    <tr>
                      <th><h6 class="text-sm text-medium">#</h6></th>
                      <th><h6 class="text-sm text-medium">Código</h6></th>
                      <th><h6 class="text-sm text-medium">Razón Social</h6></th>
                      <th><h6 class="text-sm text-medium">Teléfono</h6></th>
                      <th><h6 class="text-sm text-medium">Contacto</h6></th>
                      <th><h6 class="text-sm text-medium">Ciudad</h6></th>
                      <th><h6 class="text-sm text-medium">Localidad</h6></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($sellers as $seller)
                      <tr>
                        <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
                        <td class="text-sm"><h6 class="text-sm">{{ $seller->idReference }}</h6></td>
                        <td class="text-sm"><p>{{ $seller->name }}</p></td>
                        <td class="text-sm"><p><i class="lni lni-phone mr-10"></i>{{ $seller->phone_1 }}</p></td>
                        <td class="text-sm"><p><i class="lni lni-user mr-10"></i>{{ $seller->seller_contact_1 }}</p></td>
                        <td class="text-sm"><p>{{ $seller->city }}</p></td>
                        <td class="text-sm"><p>{{ $seller->estate }}</p></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (isset($filter))
                  {!! $sellers-> appends($filter)->links() !!} <!-- appends envia variable en la paginacion-->
                @else
                  {!! $sellers-> links() !!}    
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection