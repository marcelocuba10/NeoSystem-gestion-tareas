  @csrf
  <div class="row">
    <div class="col-4">
      <div class="select-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Cliente</label>
        <div class="select-position">
          <select name="customer_id">
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}" @if($customer_visit) {{ ($customer->id == $customer_visit->customer_id) ? 'selected' : '' }} @endif> {{ $customer->name}} </option>
            @endforeach 
          </select>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Fecha/Hora</label>
        <input type="text" name="visit_date" placeholder="DD/MM/YYYY" value="@if($customer_visit) {{ date('d/m/Y - H:i', strtotime($customer_visit->visit_date)) }} @else {{ $currentDate }} @endif" readonly>
      </div>
    </div>
    <div class="col-3">
      <div class="input-style-1">
        <label>Fecha Próxima Paso</label>
        <input onchange="showFieldObjectives(this);" type="date" name="next_visit_date" id="date" placeholder="DD/MM/YYYY" value="{{ $customer_visit->next_visit_date ?? old('next_visit_date') }}" class="bg-transparent">
        <span id="msg1" style="display: none" class="form-text m-b-none">Es necesario agregar la <b>Hora</b> y <b>Objetivos</b></span>
      </div>
    </div>
    <div class="col-2">
      <div class="input-style-1">
        <label>Hora Próxima Paso</label>
          <input type="time" name="next_visit_hour" value="{{ $customer_visit->next_visit_hour ?? old('next_visit_hour') }}" class="bg-transparent">
      </div>
    </div>
    <div class="col-sm-3">
      <div class="select-style-1">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Acciones</label>
        <div class="select-position">
          @if ($customer_visit)
            @if ($customer_visit->action == 'Enviar Presupuesto')
              <input type="hidden" name="action" value="{{ $customer_visit->action }}">
              <select name="action" id="action" {{ ($customer_visit->action == 'Enviar Presupuesto') ? 'disabled class=bg-gray' : '' }}>
                @foreach ($actions as $item)
                  <option value="{{ $item }}" @if($customer_visit) {{ ($item == $customer_visit->action) ? 'selected' : '' }} @endif> {{ $item}} </option>
                @endforeach 
              </select> 
            @else
            <select name="action" id="action">
              @foreach ($actions as $item)
                <option value="{{ $item }}" @if($customer_visit) {{ ($item == $customer_visit->action) ? 'selected' : '' }} @endif> {{ $item}} </option>
              @endforeach 
            </select> 
            @endif
          @else
            <select name="action" id="action">
              @foreach ($actions as $item)
                <option value="{{ $item }}"> {{ $item}} </option>
              @endforeach 
            </select> 
          @endif
        </div>
      </div>
    </div>
    <div class="col-4">
      <div class="input-style-1">
        @if ($customer_visit)
          <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Resultados de la Visita/Llamada</label>
        @else
          <label>Resultados de la Visita/Llamada</label>
        @endif
        <textarea type="text" name="result_of_the_visit" value="{{ $customer_visit->result_of_the_visit ?? old('result_of_the_visit') }}" class="bg-transparent">{{ $customer_visit->result_of_the_visit ?? old('result_of_the_visit') }}</textarea>
      </div>
    </div>
    <div class="col-5">
      <div class="input-style-1" id="objective" style="display: none">
        <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Objetivos Visita/Llamada</label>
        <textarea type="text" name="objective" value="{{ $customer_visit->objective ?? old('objective') }}" class="bg-transparent" placeholder="Qué quiero lograr y en cuanto tiempo">{{ $customer_visit->objective ?? old('objective') }}</textarea>
      </div>
    </div>
    
    {{-- <div class="col-12">
      <div class="form-check checkbox-style mb-30">
        <input name="setOrder" class="form-check-input" type="checkbox" id="chkbox_setOrder" @if(!empty($customer_visit)) {{ ($customer_visit->type == 'Order') ? 'checked' : '' }} @endif>
        <label class="form-check-label" for="checkbox-setOrder">¿Crear Presupuesto?</label>
      </div>
    </div> --}}

    {{-- Update visit customer --}}
    @if ($customer_visit)
      @if ($customer_visit->type == 'Presupuesto')
        <div class="col-12" id="setOrder">
          <div class="table-wrapper table-responsive">
            <table class="table top-selling-table mb-50">
              <thead style="background-color: #DAEFFE;">
                <tr>
                  <th><h6>Producto</h6></th>
                  <th><h6>Precio</h6></th>
                  <th><h6>Cantidad</h6></th>
                  <th><h6>Subtotal</h6></th>
                  <th><h6>Acción</h6></th>
                </tr>
              </thead>
              <tbody>
                @php
                  $c = 0;
                @endphp
                @foreach ($order_details as $item_order)
                  <tr>
                    <td>
                      <select name="product_id[]" class="form-control product">
                        <option>Seleccione Producto</option>
                        @foreach($products as $product)  
                          <option value="{{ $product->id }}" name="product_id[]" {{ ( $product->id == $item_order->product_id) ? 'selected' : '' }}> {{ $product->name}} </option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="text" name="price[]" value="{{ $item_order->price }}" class="form-control price" readonly></td>
                    <td><input type="number" min="1" name="qty[]" value="{{ $item_order->quantity }}" class="form-control qty"></td>
                    <td><input type="text" name="amount[]" class="form-control amount" value="{{ $item_order->amount }}" readonly></td>
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
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><h4>Total</h4></td>
                  <td><h4 class="total" id="total"></h4></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="col-12">
          <div class="button-group d-flex justify-content-center flex-wrap">
            <input type="hidden" name="isSetOrder" id="isSetOrder">
            <button type="submit" class="main-btn primary-btn btn-hover m-2">Actualizar</button>
            <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
          </div>
        </div>

      @elseif($customer_visit->type == 'Sin Presupuesto')

        <div class="col-12" id="setOrder" style="display: none">
          <div class="table-wrapper table-responsive">
            <table class="table top-selling-table mb-50">
              <thead style="background-color: #DAEFFE;">
                <tr>
                  <th><h6>Producto</h6></th>
                  <th><h6>Precio</h6></th>
                  <th><h6>Cantidad</h6></th>
                  <th><h6>Subtotal</h6></th>
                  <th><h6>Acción</h6></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select name="product_id[]" class="form-control product">
                      <option>Seleccione Producto</option>
                      @foreach($products as $product)  
                        <option name="product_id[]" value="{{ $product->id }}">{{ $product->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="text" name="price[]" class="form-control price" readonly></td>
                  <td><input type="number" min="1" name="qty[]" class="form-control qty"></td>
                  <td><input type="text" name="amount[]" class="form-control amount" readonly></td>
                  <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><h4>Total</h4></td>
                  <td><h4 class="total"></h4></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="col-12">
          <div class="button-group d-flex justify-content-center flex-wrap">
            <input type="hidden" name="isSetOrder" id="isSetOrder">
            @if ($customer_visit->status == 'Pendiente')
              <input type="hidden" name="pendingToProcess" id="pendingToProcess">
              <button type="submit" class="main-btn primary-btn btn-hover m-2 btn-pendingToProcess">Marcar como Procesado</button>
              <button type="submit" class="main-btn primary-btn-outline btn-hover m-2">Actualizar</button>
            @endif
            <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
          </div>
        </div>
      @endif

    {{-- New customer visit --}}
    @else

      <div class="col-12" id="setOrder" style="display: none">
        <div class="table-wrapper table-responsive">
          <table class="table top-selling-table mb-50">
            <thead style="background-color: #DAEFFE;">
              <tr>
                <th><h6>Producto</h6></th>
                <th><h6>Precio</h6></th>
                <th><h6>Cantidad</h6></th>
                <th><h6>Subtotal</h6></th>
                <th><h6>Acción</h6></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select name="product_id[]" class="form-control product">
                    <option>Seleccione Producto</option>
                    @foreach($products as $product)  
                      <option name="product_id[]" value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="text" name="price[]" class="form-control price" readonly></td>
                <td><input type="number" min="1" name="qty[]" class="form-control qty"></td>
                <td><input type="text" name="amount[]" class="form-control amount" readonly></td>
                <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><h4>Total</h4></td>
                <td><h4 class="total"></h4></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-12">
        <div class="button-group d-flex justify-content-center flex-wrap">
          <input type="hidden" name="isSetOrder" id="isSetOrder">
          <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
          <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
        </div>
      </div>

    @endif

  </div>
</div>

<script type="text/javascript">

  //if setOrder is selected, show box item detail order.
  $('#action').on( 'change', function(){ 
    action = document.getElementById("action").value;
    if (action == 'Enviar Presupuesto') {
      document.getElementById('setOrder').style.display = 'initial';
      $("#isSetOrder").val(true);
    }else{
      document.getElementById('setOrder').style.display = 'none';
      $("#isSetOrder").val(false);
    }
  });

  function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
  }

  //if SetOrder is checked, show order item table;
  // var checkbox = document.getElementById('chkbox_setOrder')
  // checkbox.addEventListener('change', (event) => {
  //   if (event.currentTarget.checked) {
  //     document.getElementById('setOrder').style.display = 'initial';
  //   } else {
  //     document.getElementById('setOrder').style.display = 'none';
  //   }
  // })
  
  function showFieldObjectives(object){
    //show field objectives and message span
    document.getElementById('objective').style.display = 'initial';
    document.getElementById('msg1').style.display = 'initial';
    console.log('show field objective');

    //if clear calendar click hide field objectives and message span
    if (object.value == '') {
      document.getElementById('objective').style.display = 'none';
      document.getElementById('msg1').style.display = 'none';
      console.log('hide field objective');
    }
  }

  $(document).ready(function(){

    // Pass parameter to controller change status visit customer pending to processed 
    $(".btn-pendingToProcess").click(function() {
      $("#pendingToProcess").val(true);
    });

    //check if customer_visit contain orders details and next_visit_date
    var action = document.getElementById("action").value;
    var next_visit_date = document.getElementById('date');

    if (action == 'Enviar Presupuesto') {
      document.getElementById('setOrder').style.display = 'initial';
      $("#isSetOrder").val(true);
    }else{
      document.getElementById('setOrder').style.display = 'none';
      $("#isSetOrder").val(false);
    }

    //check if have next visit, show field objectives and message span
    if (next_visit_date.value) {
      document.getElementById('objective').style.display = 'initial';
      document.getElementById('msg1').style.display = 'initial';
      console.log('show field objective');
    }

    //When get data from Edit, calculate Total;
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

      $.ajax({
        type    : 'GET',
        url     :"{{ URL::to('/user/products/findPrice') }}",
        dataType: 'json',
        data: {
          '_token' : '{{ csrf_token() }}',
          'id' : id
        },
        success:function (response) {
          var data = response;
          var string_data = JSON.stringify(data); 

          //convert number to currency format to show
          var price = data.sale_price;
          price_currency = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          //price_currency = accounting.formatMoney(price, "", 0, ".", ".");
          tr.find('.price').val(price_currency);

          var qty = 1;
          var amount = (qty * data.sale_price);
          tr.find('.qty').val(qty);

          //convert number to currency format to show
          //amount = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          //amount = accounting.formatMoney(amount, "", 0, ".", ".");
          tr.find('.amount').val(amount);

          total();
        }
      });
    });

    //when you type the quantity, it is calculated again subtotal and total
    $('tbody').delegate('.qty', 'keyup', function () {
      var tr = $(this).parent().parent();
      var qty = tr.find('.qty').val();
      var price = tr.find('.price').val();

      //convert currency format to number
      var price = Number(price.replace(/[^0-9.-]+/g,""));
      
      var amount = (qty * price);

      //convert number to currency format to show
      //amount = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      //amount = accounting.formatMoney(amount, "", 0, ".", ".");
      tr.find('.amount').val(amount);

      total();
    });
  });

  function total(){
    var total = 0;
    $('.amount').each(function (index, element) {
      var amount =$(this).val()-0;
      // var amount2 = parseFloat($(this).val() || 0) ;
      // var amount3 = parseFloat($(this).val()).toFixed(3);
      // var amount4 = parseFloat(parseFloat($(this).val()).toFixed(3));
      // console.log('amount format 1: ' + amount);
      // console.log('amount format 2: ' + amount2);
      // console.log('amount format 3: ' + amount3);
      // console.log('amount format 4: ' + amount4);

      total += amount;
    })
    var total = formatNumber(total);
    var total = total.replaceAll(",", ".");
    $('.total').html(total);
  }

  $('#add_btn').on('click',function(){
    var html = '';
    html += '<tr>';
    html += '<td> <select name="product_id[]" class="form-control product"> <option>Seleccione Producto</option> @foreach($products as $product) <option name="product_id[]" data-price="{{ $product->sale_price }}" value="{{ $product->id }}">{{ $product->name }}</option> @endforeach </select> </td>';
    html += '<td><input type="text" name="price[]" class="form-control price" readonly></td>';
    html += '<td><input type="number" min="1" name="qty[]" class="form-control qty"></td>';
    html += '<td><input type="text" name="amount[]" class="form-control amount" readonly></td>';
    html += '<td><button type="button" class="btn btn-danger" id="remove"><i class="lni lni-trash-can"></i></button></td>';
    html += '</tr>';
    $('tbody').append(html);
  })
</script>   

@if ($customer_visit != null)
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      var tr =$(this).parent().parent();
      var id = tr.find('.product').val();
      var visit_id = <?php echo json_encode($customer_visit->id); ?>;
      $.ajax({
          type: 'DELETE',
          url     : "{{ URL::to('/user/customer_visits/deleteItemOrder') }}",
          dataType: 'json',
          data: {
            "_method" : "DELETE",
            '_token' : '{{ csrf_token() }}',
            'id' : id,
            'visit_id' : visit_id
          },
          success:function (response) {
            console.log('response: '+ response);
          }
      });

      //remove file in the table
      $(this).closest('tr').remove();
      total();
    });
  </script>
@else
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      //remove file in the table
      $(this).closest('tr').remove();
      total();
    });
  </script>
@endif


  