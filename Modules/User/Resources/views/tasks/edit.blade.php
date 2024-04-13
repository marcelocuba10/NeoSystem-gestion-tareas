@extends('user::layouts.adminLTE.app')
@section('content')

    <section class="section">
        <div class="container-fluid">
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title mb-30"><h2>Editar Tarea</h2></div>
                    </div>
                </div>
            </div>

            <div class="form-layout-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-style mb-30">
                            <form method="POST" action="{{ url('/user/tasks/update/'.$task->id) }}">
                                @csrf
                                @method('PUT')
                                @include('user::tasks._partials.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection  
