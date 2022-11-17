  @csrf
  <div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
        <input type="text" class="bg-transparent" value="{{ $user->name ?? old('name') }}" name="name">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Apellidos</label>
        <input type="text" class="bg-transparent" value="{{ $user->last_name ?? old('last_name') }}" name="last_name">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Email</label>
        <input type="email" class="bg-transparent" value="{{ $user->email ?? old('email') }}" name="email">
      </div>
    </div>
    @if ($currentUserRole == 'SuperAdmin')
      <div class="col-6">
        <div class="select-style-1">
          <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Rol</label>
          <div class="select-position">
            <select name="roles">
              @foreach ($roles as $role)
                <option value="{{ $role }}" {{ ( $role == $userRole) ? 'selected' : '' }}> {{ $role}} </option>
              @endforeach 
            </select>
          </div>
        </div>
      </div>
    @else
      <div class="col-6">
        <div class="input-style-1">
          <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Rol</label>
          <input type="text" value="{{ $userRole ?? old('userRole') }}" name="roles" readonly >
        </div>
      </div>
    @endif
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Contraseña</label>
        <input type="password" name="password" class="bg-transparent">
        @if ($user)
          <span class="form-text m-b-none">Déjelo en blanco si no desea cambiar la contraseña</span>
        @endif
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Confirmar Contraseña</label>
        <input type="password" name="confirm_password" class="bg-transparent">
        @if ($user)
          <span class="form-text m-b-none">Déjelo en blanco si no desea cambiar la contraseña</span>
        @endif
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>Teléfono</label>
        <input type="text" name="phone" value="{{ $user->phone ?? old('phone') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Doc Identidad</label>
        <input type="text" name="doc_id" value="{{ $user->doc_id ?? old('doc_id') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-12">
      <div class="input-style-1">
        <label>Dirección</label>
        <input type="text" name="address" value="{{ $user->address ?? old('address') }}" class="bg-transparent">
      </div>
    </div>

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/users') }}">Atrás</a>
      </div>
    </div>
  </div>

<!-- ========= Scripts ======== -->
<!-- ========= disable button after send form ======== -->
<script>
  $(document).ready(function(){
    $('form').submit(function (event) {
      var btn_submit = document.getElementById('btn_submit');
      btn_submit.disabled = true;
      btn_submit.innerText = 'Procesando...'
    });
  })
</script>