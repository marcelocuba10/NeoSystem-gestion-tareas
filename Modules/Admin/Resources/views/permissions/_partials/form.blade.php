  @csrf
  <div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
        <input type="text" placeholder="Ingrese Nombre" class="bg-transparent" value="{{ $permission->name ?? old('name') }}" name="name">
        <span class="form-text m-b-none">Exemplo: role-sa-list, role-sa-create, role-sa-edit, role-sa-delete</span>
      </div>
    </div>
    <div class="col-6">
      <div class="select-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Guard</label>
        <div class="select-position">
          <select name="guard_name">
            @foreach ($guard_names as $guard_name)
              <option value="{{ $guard_name }}" {{ ( $guard_name == $permissionGuard) ? 'selected' : '' }}> {{ $guard_name}} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/ACL/permissions') }}">Atrás</a>
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