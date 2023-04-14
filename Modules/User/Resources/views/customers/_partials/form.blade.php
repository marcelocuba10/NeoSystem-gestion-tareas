  @csrf
  <div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Razón Social</label>
        <input type="text" name="name" value="{{ $customer->name ?? old('name') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Doc Identidad / RUC</label>
        <input type="text" name="doc_id" value="{{ $customer->doc_id ?? old('doc_id') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Teléfono</label>
        <input type="text" name="phone" value="{{ $customer->phone ?? old('phone') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Rubro</label>
        <div class="select-position">
          <select name="category[]" class="select2-multiple_1" multiple="multiple">
            @foreach ($categories as $item)
              <option value="{{ $item->id }}" @if(!empty($customerCategories)) {{ in_array($item->id,$customerCategories)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    @if ($customer)
      <div class="col-6" style="margin-top: -12px;">
        <div class="table-wrapper table-responsive">
          <table class="table top-selling-table mb-30">
            <thead>
              <tr>
                <th><h6><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Equipos Potenciales</h6></th>
                <th><h6><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Cantidad Unidades</h6></th>
                <th><h6>Acción</h6></th>
              </tr>
            </thead>
            <tbody>
              @php
                $c = 0;
              @endphp
              @foreach ($potential_products_selectd as $item_product)
                <tr>
                  <td>
                    <select name="potential_products[]" class="form-control product">
                      <option>Seleccione Producto</option>
                      @foreach($potential_products as $product)  
                        <option value="{{ $product->id }}" name="potential_products[]" {{ ( $product->id == $item_product->id) ? 'selected' : '' }}> {{ $product->name}} </option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="number" min="1" name="qty[]" value="{{ $item_product->quantity }}" class="form-control qty"></td>
                  @if ($c == 0)
                    <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
                  @else
                    <td><button type="button" class="btn btn-danger" id="remove"><i class="lni lni-trash-can"></i></button></td>
                  @endif
                </tr>
                @php
                  $c++;
                @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    @else

      <div class="col-6" style="margin-top: -12px;">
        <div class="table-wrapper table-responsive">
          <table class="table top-selling-table mb-30">
            <thead>
              <tr>
                <th><h6><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Equipos Potenciales</h6></th>
                <th><h6><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Cantidad Unidades</h6></th>
                <th><h6>Acción</h6></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select name="potential_products[]" class="form-control product">
                    <option>Seleccione Producto</option>
                    @foreach($potential_products as $product)  
                      <option name="potential_products[]" value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" min="1" name="qty[]" class="form-control qty"></td>
                <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    @endif
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Posibles Resultados con este Cliente</label>
        <textarea type="text" name="result_of_the_visit" value="{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}" class="bg-transparent">{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Potencial del cliente</label>
        <textarea type="text" name="objective" value="{{ $customer->objective ?? old('objective') }}" class="bg-transparent">{{ $customer->objective ?? old('objective') }}</textarea>
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>Email</label>
        <input type="text" name="email" value="{{ $customer->email ?? old('email') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Fecha Próxima Visita</label>
        <input type="date" name="next_visit_date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer->next_visit_date ?? old('next_visit_date') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Hora Próxima Visita</label>
          <input type="time" name="next_visit_hour" value="{{ $customer->next_visit_hour ?? old('next_visit_hour') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-5">
      <div class="input-style-1">
        <label>Ciudad</label>
        <input name="city" value="{{ $customer->city ?? old('city') }}" type="text" class="bg-transparent">
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
    <div class="col-3">
      <div class="form-check checkbox-style mb-30" style="margin-top: 40px;">
        <input name="is_vigia" @if(!empty($customer->is_vigia)) {{ $customer->is_vigia = 'on'  ? 'checked' : '' }} @endif class="form-check-input" type="checkbox" id="checkbox-not-robot">
        <label class="form-check-label" for="checkbox-not-robot" >¿Es Cliente Vigia?</label>
      </div>
    </div>
    <div class="col-6">
      <div class="input-style-1">
        <label>Dirección</label>
        <input type="text" name="address" value="{{ $customer->address ?? old('address') }}" class="bg-transparent">
      </div>
    </div>

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">{{ ($customer)  ? ' Actualizar' : 'Guardar' }}</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customers') }}">Atrás</a>
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

<script type="text/javascript">
  //When get data from Edit, calculate Total;
  var tr = $(this).parent().parent();
  var qty = tr.find('.qty').val();

  //When select product, focus on input quantity;
  $('tbody').delegate('.product', 'change', function () {
    var  tr = $(this).parent().parent();
    tr.find('.qty').focus();
  });

  //Get product Data and calculate the amount, total;
  $('tbody').delegate('.product', 'change', function () {
    var tr =$(this).parent().parent();
    var qty = 1;
    tr.find('.qty').val(qty);
  });

  $('#add_btn').on('click',function(){
    console.log('add_btn');
    var html = '';
    html += '<tr>';
    html += '<td> <select name="potential_products[]" class="form-control product"> <option>Seleccione Producto</option> @foreach($potential_products as $product) <option name="potential_products[]" value="{{ $product->id }}">{{ $product->name }}</option> @endforeach </select> </td>';
    html += '<td><input type="number" min="1" name="qty[]" class="form-control qty"></td>';
    html += '<td><button type="button" class="btn btn-danger" id="remove"><i class="lni lni-trash-can"></i></button></td>';
    html += '</tr>';
    $('tbody').append(html);
  })

  $(document).on('click', '#remove', function () {
    $(this).closest('tr').remove();
  });

</script> 

@if ($customer != null)
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      var tr =$(this).parent().parent();
      var id = tr.find('.product').val();
      var customer_id = <?php echo json_encode($customer->id); ?>;
      console.log('product id: ' + id + ' customer_id: ' + customer_id );
      $.ajax({
        type: 'DELETE',
        url     :"{{ URL::to('/user/customers/deleteItemOrder') }}",
        dataType: 'json',
        data: {
          "_method" : "DELETE",
          '_token': '{{ csrf_token() }}',
          'id':id,
          'customer_id':customer_id
        },
        success:function (response) {
          console.log('response: '+ response.message);
        }
      });

      //remove file in the table
      $(this).closest('tr').remove();
    });
  </script>
@else
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      //remove file in the table
      $(this).closest('tr').remove();
    });
  </script>
@endif