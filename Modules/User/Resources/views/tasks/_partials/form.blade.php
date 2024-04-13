@csrf
<div class="row">
  <div class="col-md-6">
    <div class="input-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Título</label>
      <input type="text" class="bg-transparent" value="{{ $task->title ?? old('title') }}" name="title">
    </div>
  </div>
  <div class="col-md-4">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Respondable</label>
      <div class="select-position">
        <select name="assigned_to">
          @foreach ($users as $user)
            <option value="{{ $user->id }}" @if ($task) { {{ ($user->id == $task->assigned_to) ? 'selected' : '' }} } @endif> {{ $user->name}} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="select-style-1">
      <label>Status</label>
      <div class="select-position">
        <select name="status">
          @foreach ($status as $key)
            <option value="{{ $key[0] }}" @if ($task) { {{ ($key[0] == $task->status) ? 'selected' : '' }} } @endif> {{ $key[1] }} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="select-style-1">
      <label>Prioridad</label>
      <div class="select-position">
        <select name="priority">
          @foreach ($priority as $key)
            <option value="{{ $key[1] }}" @if ($task) { {{ ($key[1] == $task->priority) ? 'selected' : '' }} } @endif> {{ $key[1] }} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-10">
    <div class="input-style-1">
      <label>Descripción</label>
      <textarea type="text" name="description" value="{{ $task->description ?? old('description') }}" class="bg-transparent">{{ $task->description ?? old('description') }}</textarea>
    </div>
  </div>
</div>


  <div class="col-12">
    <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">{{ ($task)  ? ' Actualizar' : 'Guardar' }}</button>  
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/tasks') }}">Atrás</a>
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