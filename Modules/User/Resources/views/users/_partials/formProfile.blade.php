  @csrf
  <div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Razón Social</label>
        <input type="text" class="bg-transparent" value="{{ $user->name ?? old('name') }}" name="name">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre del Encargado</label>
        <input name="seller_contact_1" value="{{ $user->seller_contact_1 ?? old('seller_contact_1') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
          <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Email</label>
          <input type="email" class="bg-transparent" value="{{ $user->email ?? old('email') }}" name="email">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
          <label>Contraseña</label>
          <input type="password" name="password" class="bg-transparent">
          @if ($user)
            <span class="form-text m-b-none">Déjelo en blanco si no desea cambiar la contraseña</span>
          @endif
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>Confirmar Contraseña</label>
        <input type="password" name="confirm_password" class="bg-transparent">
        @if ($user)
          <span class="form-text m-b-none">Déjelo en blanco si no desea cambiar la contraseña</span>
        @endif
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Teléfono 1</label>
        <input name="phone_1" value="{{ $user->phone_1 ?? old('phone_1') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Teléfono 2</label>
        <input name="phone_2" value="{{ $user->phone_2 ?? old('phone_2') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Doc Identidad / RUC</label>
        <input type="text" name="doc_id" value="{{ $user->doc_id ?? old('doc_id') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        <label>Ciudad</label>
        <input name="city" value="{{ $user->city ?? old('city') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-4">
      <div class="select-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Departamento</label>
        <div class="select-position">
          <select name="estate">
            @foreach ($estates as $key)
              <option value="{{ $key[1] }}" {{ ( $key[1] == $userEstate) ? 'selected' : '' }}> {{ $key[1] }} </option>
            @endforeach 
          </select>
        </div>
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
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/users/profile/'.$user->id) }}">Atrás</a>
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
