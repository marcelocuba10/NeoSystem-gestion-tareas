@csrf
<div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Nombre</label>
        <input type="text" placeholder="Ingrese Nombre" class="bg-transparent" value="{{ $permission->name ?? old('name') }}" name="name">
        <span class="form-text m-b-none">Exemplo: role-sa-list, role-sa-create, role-sa-edit, role-sa-delete</span>
      </div>
    </div>
    <!-- end col -->
    <div class="col-6">
      <div class="select-style-1">
        <label>(*) Guard</label>
        <div class="select-position">
          <select name="guard_name">
            @foreach ($guard_names as $guard_name)
              <option value="{{ $guard_name }}" {{ ( $guard_name == $permissionGuard) ? 'selected' : '' }}> {{ $guard_name}} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <!-- end col -->
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn danger-btn-outline m-2" href="/admin/ACL/permissions">Atrás</a>
      </div>
    </div>
</div>