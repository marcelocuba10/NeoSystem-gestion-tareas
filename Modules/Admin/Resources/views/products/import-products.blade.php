@extends('admin::layouts.adminLTE.app')
@section('content')

  <section class="table-components">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="title d-flex align-items-center flex-wrap mb-30">
              <h2 class="mr-40">Importar Productos desde Excel</h2>
            </div>
          </div>
          <div class="col-md-4">
            <div class="right"></div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <!-- ========== tables-wrapper start ========== -->
      <div class="tables-wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="card-style mb-30">
              <div class="form-elements-wrapper">
                <div class="alert-box primary-alert">
                  <div class="alert">
                    <span class="text-medium">
                      Por favor, descargue la plantilla ejemplo, para continuar con la importación.
                    </span>&nbsp;<a href="https://www.pyp.com.py/public/docs/plantilla_importar_lista_precios_guaranies.xlsx" target="_blank" style="color: #4a70f7;font-weight: 700;;text-decoration: underline">Descargar plantilla</a>
                  </div>
                </div>
                <form action="{{ url('/admin/products/import') }}" id="import-csv-form" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-12">
                      <div class="input-style-1">
                        <input type="file" name="file" placeholder="Seleccione el archivo" class="bg-transparent">
                        <div data-lastpass-icon-root="true" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="button-group d-flex justify-content-left flex-wrap">
                        <button type="submit" id="submit" class="main-btn info-btn btn-hover m-2">Importar CSV</button>
                        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/products/') }}">Atrás</a>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection