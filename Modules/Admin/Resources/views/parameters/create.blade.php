@extends('admin::layouts.adminLTE.app')
@section('content')

    <section class="section">
        <div class="container-fluid">
            <!-- ========== title-wrapper start ========== -->
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title mb-30">
                            <h2>Registrar Nuevo Parámetro</h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="breadcrumb-wrapper mb-30">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item" aria-current="page"><a href="{{ url('/admin/parameters') }}">Parámetros</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Nuevo Parámetro</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ========== title-wrapper end ========== -->

            <div class="form-layout-wrapper">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="card-style mb-30">
                        <form method="POST" action="{{ url('/admin/parameters/create') }}">
                            @include('admin::parameters._partials.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection  
