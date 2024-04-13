
    <header class="header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-6">
            <div class="header-left d-flex align-items-center">
              <div class="menu-toggle-btn mr-20">
                <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                  <i class="lni lni-chevron-left me-2"></i> Menu
                </button>
              </div>
            </div>
          </div>
          <div class="col-lg-7 col-md-7 col-6">
            <div class="header-right">
              <div class="profile-box ml-15">
                <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile" data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="profile-info">
                    <div class="info">
                      <h6>@if(Auth::check()) {{Auth::user()->name}} @endif</h6>
                      <div class="image">
                        <img src="{{ asset('public/adminLTE/images/profile/profile-2.png') }}" alt="" />
                        <span class="status"></span>
                      </div>
                    </div>
                  </div>
                  <i class="lni lni-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(0px, 48px, 0px);" data-popper-placement="bottom-end">
                  <li>
                    <a href="{{ url('/user/users/profile/'.Auth::user()->id) }} "><i class="lni lni-user"></i> Mi Perfil</a>
                  </li>
                  <li>
                    <a href="{{ url('/user/logout/') }} "> <i class="lni lni-exit"></i> Cerrar Sesión </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>