  @csrf
  <div class="row">
    <div class="col-4">
      <div class="select-style-1">
        <label>(*) Cliente</label>
        <div class="select-position">
          @if($customer_visit)
          <select name="customer_id">
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}" {{ ( $customer->id == $customer_visit->customer_id) ? 'selected' : '' }}> {{ $customer->name}} </option>
            @endforeach 
          </select>
          @else
          <select name="customer_id">
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}"> {{ $customer->name}} </option>
            @endforeach 
          </select>
          @endIf
        </div>
      </div>
    </div>
    <!-- end col --> 
    <div class="col-3">
      <div class="input-style-1">
        <label>Fecha/Hora de Visita</label>
        <input type="text" name="visit_date" value="{{ $currentDate ?? old('currentDate') }}" readonly>
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>Fecha Próxima Visita</label>
        <input type="date" name="next_visit_date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer_visit->next_visit_date ?? old('next_visit_date') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-2">
      <div class="input-style-1">
        <label>Hora Próxima Visita</label>
          <input type="time" name="next_visit_hour" value="{{ $customer_visit->next_visit_hour ?? old('next_visit_hour') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-sm-2">
      <div class="select-style-1">
        <label>(*) Estado</label>
        <div class="select-position">
          @if ($customer_visit)
            <select name="status">
              @foreach ($status as $item)
                <option value="{{ $item }}" {{ ( $item === $customer_visit->status) ? 'selected' : '' }}> {{ $item}} </option>
              @endforeach 
            </select> 
          @else
            <select name="status">
              @foreach ($status as $item)
                <option value="{{ $item }}"> {{ $item}} </option>
              @endforeach 
            </select> 
          @endif
        </div>
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label>Resultado de la Visita</label>
        <textarea type="text" name="result_of_the_visit" value="{{ $customer_visit->result_of_the_visit ?? old('result_of_the_visit') }}" class="bg-transparent">{{ $customer_visit->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label>Objetivos</label>
        <textarea type="text" name="objective" value="{{ $customer_visit->objective ?? old('objective') }}" class="bg-transparent">{{ $customer_visit->objective ?? old('objective') }}</textarea>
      </div>
    </div>
    <!-- end col -->
    
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
      </div>
    </div>
  </div>