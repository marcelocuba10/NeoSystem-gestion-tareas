  @csrf
  <div class="row">
    <div class="col-4">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
        <input type="text" placeholder="Ingrese Nombre" class="bg-transparent" value="{{ $parameter->name ?? old('name') }}" name="name">
      </div>
    </div>
    <div class="col-3">
      <div class="select-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Tipo de Parámetro</label>
        <div class="select-position">
          <select name="type">
            @foreach ($keys as $key)
              <option value="{{ $key[1] }}" {{ ( $key[1] == $type_parameter) ? 'selected' : '' }}> {{ $key[1] }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <div class="col-5">
      <div class="input-style-1">
        <label>Descripción</label>
        <textarea type="text" name="description" value="{{ $parameter->description ?? old('description') }}" class="bg-transparent">{{ $parameter->description ?? old('description') }}</textarea>
      </div>
    </div>

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/parameters') }}">Atrás</a>
      </div>
    </div>
  </div>