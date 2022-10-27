  @csrf
  <div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Razón Social</label>
        <input name="name" value="{{ $user->name ?? old('name') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Nombre del Encargado</label>
        <input name="seller_contact_1" value="{{ $user->seller_contact_1 ?? old('seller_contact_1') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Email</label>
        <input name="email" value="{{ $user->email ?? old('email') }}" type="email" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Contraseña</label>
        <input name="password" type="password" class="bg-transparent">
        @if ($user)
          <span class="form-text m-b-none">Déjelo en blanco si no desea cambiar la contraseña</span>
        @endif
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Confirmar Contraseña</label>
        <input name="confirm_password" type="password" class="bg-transparent">
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
    @if ($currentUserRole == 'SuperAdmin')
      <div class="col-2">
        <div class="select-style-1">
          <label>(*) Status</label>
          <div class="select-position">
            <select name="status">
              @foreach ($status as $key)
                <option value="{{ $key[0] }}" {{ ( $key[0] == $userStatus) ? 'selected' : '' }}> {{ $key[1] }} </option>
              @endforeach 
            </select>
          </div>
        </div>
      </div>
    @else
      <div class="col-2">
        <div class="input-style-1">
          <label>(*) Status</label>
          @foreach ($status as $key)
            @if ($key[0] == $userStatus)
              <input placeholder="{{ $key[1] }}" type="text" readonly>
              <input name="status" value="{{ $key[0] }}" type="text" readonly style="display: none;">
            @endif
          @endforeach 
        </div>
      </div>
    @endif
    <div class="col-3">
      <div class="input-style-1">
        <label>(*) Doc Identidad / RUC</label>
        <input name="doc_id" value="{{ $user->doc_id ?? old('doc_id') }}" type="text" class="bg-transparent">
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
        <label>(*) Departamento</label>
        <div class="select-position">
          <select name="estate">
            @foreach ($estates as $key)
              <option value="{{ $key[1] }}" {{ ( $key[1] == $userEstate) ? 'selected' : '' }}> {{ $key[1] }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <div class="col-8">
      <div class="input-style-1">
        <label>Dirección</label>
        <input name="address" value="{{ $user->address ?? old('address') }}" type="text" class="bg-transparent">
      </div>
    </div>

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/sellers') }}">Atrás</a>
      </div>
    </div>
  </div>