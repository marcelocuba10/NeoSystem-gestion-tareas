@extends('user::layouts.adminLTE.app')
@section('content')

    <section class="section">
        <div class="container-fluid">
            <!-- ========== title-wrapper start ========== -->
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="titlemb-30">
                            <h2>Editar Usuario</h2>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-md-6">
                        <div class="breadcrumb-wrapper mb-30">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/user/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item" aria-current="page"><a href="/user/users/">Usuarios</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Editar Usuario</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- ========== title-wrapper end ========== -->
            <div class="form-layout-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-style mb-30">
                            <form method="POST" action="/user/users/update/{{ $user->id }}">
                                @csrf
                                @method('PUT') <!-- menciono el metodo PUT, ya que en mi route utilzo Route::put(); -->
                                @include('user::users._partials.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection  
