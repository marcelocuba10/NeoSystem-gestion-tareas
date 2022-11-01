@csrf
<div class="row">
  <div class="col-4">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Cliente</label>
      <div class="select-position">
        <select name="customer_id">
          @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @if ($appointment) { {{ ($customer->id == $appointment->customer_id) ? 'selected' : '' }} } @endif> {{ $customer->name}} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-3">
    <div class="input-style-1">
    <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Fecha</label>
    <input type="date" name="date" id="date" placeholder="DD/MM/YYYY" value="{{ $appointment->date ?? old('date') }}" class="bg-transparent">
    </div>
  </div>
  <div class="col-3">
    <div class="input-style-1">
    <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Hora</label>
      <input type="time" name="hour" value="{{ $appointment->hour ?? old('hour') }}" class="bg-transparent">
    </div>
  </div>
  <div class="col-sm-3">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Acciones</label>
      <div class="select-position">
        @if ($appointment)
          <select name="action">
            @foreach ($actions as $item)
            <option value="{{ $item }}" {{ ( $item === $appointment->action) ? 'selected' : '' }}> {{ $item}} </option>
            @endforeach 
          </select> 
        @else
        <select name="action">
          @foreach ($actions as $item)
          <option value="{{ $item }}"> {{ $item}} </option>
          @endforeach 
        </select> 
        @endif
      </div>
    </div>
  </div>
  <div class="col-9">
    <div class="input-style-1">
    <label>Nota/Observaciones (Opcional)</label>
    <textarea type="text" name="observation" value="{{ $appointment->observation ?? old('observation') }}" class="bg-transparent">{{ $appointment->observation ?? old('observation') }}</textarea>
    </div>
  </div>

  <div class="col-12">
    <div class="button-group d-flex justify-content-center flex-wrap">
      @if ($appointment)
        @if ($appointment->status == 'Pendiente')
          <input type="hidden" name="pendingToProcess" id="pendingToProcess">
          <button type="submit" class="main-btn primary-btn btn-hover m-2 btn-pendingToProcess">Marcar como Procesado</button>
          <button type="submit" class="main-btn primary-btn-outline btn-hover m-2">Actualizar</button>
        @endif
      @else
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>  
      @endif
      <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/appointments') }}">Atrás</a>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    // Pass parameter to controller change status visit customer pending to processed 
    $(".btn-pendingToProcess").click(function() {
      $("#pendingToProcess").val(true);
    });
  })
</script>