@csrf
<div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label>(*) Razón Social</label>
        <input type="text" name="name" value="{{ $customer->name ?? old('name') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>Doc Identidad / RUC</label>
        <input type="text" name="doc_id" value="{{ $customer->doc_id ?? old('doc_id') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>Teléfono</label>
        <input type="text" name="phone" value="{{ $customer->phone ?? old('phone') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-6">
      <div class="input-style-1">
        <label>Email</label>
        <input type="text" name="email" value="{{ $customer->email ?? old('email') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>(*) Rubro</label>
        <div class="select-position">
          <select name="category[]" class="select2-multiple_1" multiple="multiple">
            @foreach ($categories as $item)
              <option value="{{ $item->id }}" @if(!empty($customerCategories)) {{ in_array($item->id,$customerCategories)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>(*) Equipos Potenciales</label>
        <div class="select-position">
          <select name="potential_products[]" class="select2-multiple_2" multiple="multiple">
            @foreach ($potential_products as $item)
              <option value="{{ $item->id }}" @if(!empty($customerPotentialProducts)) {{ in_array($item->id,$customerPotentialProducts)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <!-- end col -->
    <div class="col-2">
      <div class="input-style-1">
        <label>Cantidad de Unidades</label>
        <input type="number" min="0" name="unit_quantity" value="{{ $customer->unit_quantity ?? old('unit_quantity') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label>Resultado de la Visita</label>
        <textarea type="text" name="result_of_the_visit" value="{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}" class="bg-transparent">{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label>Objetivos</label>
        <textarea type="text" name="objective" value="{{ $customer->objective ?? old('objective') }}" class="bg-transparent">{{ $customer->objective ?? old('objective') }}</textarea>
      </div>
    </div>
    <!-- end col -->
    <div class="col-4">
      <div class="input-style-1">
        <label>Fecha Próxima Visita</label>
        <input type="date" name="next_visit_date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer->next_visit_date ?? old('next_visit_date') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-4">
      <div class="input-style-1">
        <label>Hora Próxima Visita</label>
          <input type="time" name="next_visit_hour" value="{{ $customer->next_visit_hour ?? old('next_visit_hour') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-4">
      <div class="form-check checkbox-style mb-30" style="margin-top: 40px;">
        <input name="is_vigia" @if(!empty($customer->is_vigia)) {{ $customer->is_vigia = 'on'  ? 'checked' : '' }} @endif class="form-check-input" type="checkbox" id="checkbox-not-robot">
        <label class="form-check-label" for="checkbox-not-robot" >¿Es Cliente Vigia?</label>
      </div>
    </div>
    <!-- end col -->
    <div class="col-6">
      <div class="input-style-1">
        <label>Ciudad</label>
        <input name="city" value="{{ $customer->city ?? old('city') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-6">
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
    <!-- end col -->
    <div class="col-12">
      <div class="input-style-1">
        <label>Dirección</label>
        <input type="text" name="address" value="{{ $customer->address ?? old('address') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/customers') }}">Atrás</a>
      </div>
    </div>
</div>