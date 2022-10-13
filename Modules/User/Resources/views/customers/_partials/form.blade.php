@csrf
<div class="row">
    <div class="col-6">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Razón Social</label>
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
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Teléfono</label>
        <input type="text" name="phone" value="{{ $customer->phone ?? old('phone') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
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
                <th><h6>Equipos Potenciales</h6></th>
                <th><h6>Cantidad Unidades</h6></th>
                <th><h6>Acción</h6></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($order_details as $item_order)
                <tr>
                  <td>
                    <select name="potential_products[]" class="form-control product">
                      <option>Seleccione Producto</option>
                      @foreach($potential_products as $product)  
                        <option value="{{ $product->id }}" name="potential_products[]" {{ ( $product->id == $item_order->product_id) ? 'selected' : '' }}> {{ $product->name}} </option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="number" min="1" name="qty[]" value="{{ $item_order->quantity }}" class="form-control qty"></td>
                  <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
                </tr>
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
                <th><h6>Equipos Potenciales</h6></th>
                <th><h6>Cantidad Unidades</h6></th>
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
    <!-- end col -->
    {{-- <div class="col-3">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Equipos Potenciales</label>
        <div class="select-position">
          <select name="potential_products[]" class="select2-multiple_2" multiple="multiple">
            @foreach ($potential_products as $item)
              <option value="{{ $item->id }}" @if(!empty($customerPotentialProducts)) {{ in_array($item->id,$customerPotentialProducts)  ? 'selected' : '' }} @endif> {{ $item->name }} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div> --}}
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Resultado de la Visita</label>
        <textarea type="text" name="result_of_the_visit" value="{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}" class="bg-transparent">{{ $customer->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Objetivos</label>
        <textarea type="text" name="objective" value="{{ $customer->objective ?? old('objective') }}" class="bg-transparent">{{ $customer->objective ?? old('objective') }}</textarea>
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
        <label>Fecha Próxima Visita</label>
        <input type="date" name="next_visit_date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer->next_visit_date ?? old('next_visit_date') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-3">
      <div class="input-style-1">
        <label>Hora Próxima Visita</label>
          <input type="time" name="next_visit_hour" value="{{ $customer->next_visit_hour ?? old('next_visit_hour') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
    <div class="col-5">
      <div class="input-style-1">
        <label>Ciudad</label>
        <input name="city" value="{{ $customer->city ?? old('city') }}" type="text" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->
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
    <!-- end col -->
    <div class="col-3">
      <div class="form-check checkbox-style mb-30" style="margin-top: 40px;">
        <input name="is_vigia" @if(!empty($customer->is_vigia)) {{ $customer->is_vigia = 'on'  ? 'checked' : '' }} @endif class="form-check-input" type="checkbox" id="checkbox-not-robot">
        <label class="form-check-label" for="checkbox-not-robot" >¿Es Cliente Vigia?</label>
      </div>
    </div>
    <!-- end col -->
    <div class="col-6">
      <div class="input-style-1">
        <label>Dirección</label>
        <input type="text" name="address" value="{{ $customer->address ?? old('address') }}" class="bg-transparent">
      </div>
    </div>
    <!-- end col -->

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">{{ ($customer)  ? ' Actualizar' : 'Guardar' }}</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customers') }}">Atrás</a>
      </div>
    </div>

</div>

<script type="text/javascript">

  function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
  }

  $(document).ready(function(){

    //check if have next visit, show field objectives and message span
    var next_visit_date = document.getElementById('date');
    if (next_visit_date.value) {
      document.getElementById('objective').style.display = 'initial';
      document.getElementById('msg1').style.display = 'initial';
      console.log('show field objective');
    }

    //When get data from Edit, calculate Total;
    var tr = $(this).parent().parent();
    var qty = tr.find('.qty').val();
    var price = tr.find('.price').val();
    var amount = (qty * price);
    tr.find('.amount').val(amount);
    total();

    //When select product, focus on input quantity;
    $('tbody').delegate('.product', 'change', function () {
      var  tr = $(this).parent().parent();
      tr.find('.qty').focus();
    })

    //Get product Data and calculate the amount, total;
    $('tbody').delegate('.product', 'change', function () {
      var tr =$(this).parent().parent();
      var id = tr.find('.product').val();
      var dataId = {'id':id};
      $.ajax({
        type    : 'GET',
        url     :"{{ URL::to('/user/products/findPrice') }}",
        dataType: 'json',
        data: {"_token": $('meta[name="csrf-token"]').attr('content'), 'id':id},
        success:function (response) {
          var data = response;
          var string_data = JSON.stringify(data); 
          tr.find('.price').val(data.sale_price);
          tr.find('.qty_av').val(data.inventory);

          var qty = 1;
          var amount = (qty * data.sale_price);
          tr.find('.qty').val(qty);
          tr.find('.amount').val(amount);
          total();
        }
      });
    });

    //when write the quantity, calculate amount, total;
    $('tbody').delegate('.qty', 'keyup', function () {
      var tr = $(this).parent().parent();
      var qty = tr.find('.qty').val();
      var price = tr.find('.price').val();
      var amount = (qty * price);
      tr.find('.amount').val(amount);
      total();
    });
  });

  function total(){
    var total = 0;
    $('.amount').each(function (i,e) {
      var amount =$(this).val()-0;
      total += amount;
    })
    var total = formatNumber(total);
    var total = total.replaceAll(",", ".");
    $('.total').html(total);
  }

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
    total();
  });

</script> 