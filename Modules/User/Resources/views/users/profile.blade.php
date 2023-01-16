@extends('user::layouts.adminLTE.app')
@section('content')

  <section class="section">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Perfil</h2>
            </div>
          </div>
          <div class="col-md-6">
            <div class="breadcrumb-wrapper mb-30">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->
      <div class="row">
        <div class="col-xxl-9 col-lg-8">
          <div class="profile-wrapper mb-30">
            <div class="profile-cover">
              <img src="{{ asset('/public/adminLTE/images/profile/profile-cover-2.png') }}" alt="cover-image">
            </div>
            <div class="d-md-flex">
              <div class="profile-photo">
                <div class="image">
                  <img src="{{ asset('/public/adminLTE/images/profile/profile-2.png') }}" alt="profile">
                  <div class="update-image">
                    <input>
                    <label for=""><i class="lni lni-camera"></i></label>
                  </div>
                </div>
                <div class="profile-meta pt-25">
                  <h5 class="text-bold mb-10">{{ $user->name }}</h5>
                </div>
              </div>
              <div class="profiles-activities w-100 pt-30">
                <ul class="d-flex align-items-center">
                  <li class="ms-auto">
                    <a href="{{ url('/user/users/edit/profile/'.$user->id) }}" class="main-btn btn-sm primary-btn btn-hover mb-20">
                      <i class="lni lni-pencil-alt mr-10"></i>Actualizar Perfil
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="profile-info">
              <form action="#">
                <div class="row">
                  <div class="col-6">
                    <div class="input-style-1">
                      <label>Razón Social</label>
                      <input placeholder="{{ $user->name }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-style-1">
                      <label>Nombre del Encargado</label>
                      <input value="{{ $user->seller_contact_1 ?? old('seller_contact_1') }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="input-style-1">
                      <label>Teléfono</label>
                      <input placeholder="{{ $user->phone_1 }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="input-style-1">
                      <label>Doc Identidad</label>
                      <input placeholder="{{ $user->doc_id }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="input-style-1">
                      <label>Ciudad</label>
                      <input placeholder="{{ $user->city }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-style-1">
                      <label>Departamento</label>
                      <input placeholder="{{ $user->estate }}" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-style-1">
                      <label>Dirección</label>
                      <input placeholder="{{ $user->address }}" type="text" readonly>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection