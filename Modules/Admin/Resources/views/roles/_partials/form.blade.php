@csrf
<div class="row">
  <div class="col-4" style="display: none">
    <div class="input-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>ID</label>
      <input type="text" name="id" id="id" value="{{ $role->id ?? old('id') }}" class="bg-transparent">
    </div>
  </div>
  <div class="col-4">
    <div class="input-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Nombre</label>
      <input type="text" name="name" value="{{ $role->name ?? old('name') }}" class="bg-transparent" placeholder="Ingrese Nombre">
    </div>
  </div>
  <div class="col-4">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Rol de Sistema</label>
      <div class="select-position">
        <select name="system_role">
          @foreach ($keys as $key)
            <option value="{{ $key[0] }}" {{ ( $key[0] == $system_role) ? 'selected' : '' }}> {{ $key[1] }} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-4">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Guard</label>
      <div class="select-position">
        <select name="guard_name" id="guard_name">
          @foreach ($guard_names as $guard_name)
            <option value="{{ $guard_name }}" {{ ( $guard_name == $roleGuard) ? 'selected' : '' }}> {{ $guard_name}} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div> 
  <div class="col-12" id="toshow">
    <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Permisos</label>
    </div>
    <!-- show content javascript -->
    <div id="permissions">
    </div>
  </div>
  <div class="col-12">
    <div class="button-group d-flex justify-content-center flex-wrap">
      <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
      <a class="main-btn primary-btn-outline m-2" href="{{ url('/admin/ACL/roles') }}">Atrás</a>
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

<script>
$(document).ready(function(){
  // when starting, it captures the value of the select, to then perform the query
  var guard_name = document.getElementById("guard_name").value;
  var id = document.getElementById("id").value;

  if(id){
    $.ajax({
      type: "POST",
      url: "{{ URL::to('/admin/ACL/permissions/get') }}",
      data: { 
        id : id,
        guard_name : guard_name,
        "_token": "{{ csrf_token() }}",
      },
        success:function(permissions)
        {
          $("#permissions").html(permissions);
        }
    });

    // call the function when an event is generated in the select
    $('#guard_name').on( 'change', function(){ 
      guard_name = document.getElementById("guard_name").value;
      $.ajax({
        type: "POST",
        url: "{{ URL::to('/admin/ACL/permissions/get') }}",
        data: { 
          id : id,
          guard_name : guard_name,
          "_token": "{{ csrf_token() }}",
        },
          success:function(permissions)
          {
            $("#permissions").html(permissions);
          }
      });
  });
  }else{
    $.ajax({
      type: "POST",
      url: "{{ URL::to('/admin/ACL/permissions/get') }}",
      data: { 
        guard_name : guard_name,
        "_token": "{{ csrf_token() }}",
      },
        success:function(permissions)
        {
          $("#permissions").html(permissions);
        }
    });

    // call the function when an event is generated in the select
    $('#guard_name').on( 'change', function(){ 
    guard_name = document.getElementById("guard_name").value;
    $.ajax({
      type: "POST",
      url: "{{ URL::to('/admin/ACL/permissions/get') }}",
      data: { 
        guard_name : guard_name,
        "_token": "{{ csrf_token() }}",
      },
        success:function(permissions)
        {
          $("#permissions").html(permissions);
        }
    });
  });
  }
});

</script>