@csrf
<div class="row">
  <div class="col-4">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Cliente</label>
      <div class="select-position">
        <select name="customer_id">
          @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @if($sale) {{ ( $customer->id == $sale->customer_id) ? 'selected' : '' }} @endif> {{ $customer->name}} </option>
          @endforeach 
        </select>
      </div>
    </div>
  </div>
  <div class="col-3">
    <div class="input-style-1">
      <label>Fecha/Hora de Venta</label>
      <input type="text" name="visit_date" value="{{ date('d/m/Y - H:i', strtotime($currentDate)) }}" readonly>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="select-style-1">
      <label><span class="c_red" data-toggle="tooltip" data-placement="bottom" title="Campo Obligatorio">(*)&nbsp;</span>Tipo</label>
      <div class="select-position">
        <select name="type">
          @foreach ($actions as $item)
            <option value="{{ $item }}" @if($sale) {{ ( $item === $sale->type) ? 'selected' : '' }} @endif> {{ $item}} </option>
          @endforeach 
        </select> 
      </div>
    </div>
  </div>

  {{-- @if ($sale)
    <div class="col-12" id="setOrder">
      <div class="table-wrapper table-responsive">
        <table class="table top-selling-table mb-50">
          <thead style="background-color: #DAEFFE;">
            <tr>
              <th><h6>Producto</h6></th>
              <th><h6>Precio</h6></th>
              <th><h6>Cantidad</h6></th>
              <th><h6>Subtotal</h6></th>
              <th><h6>gfdgfdgfdg</h6></th>
            </tr>
          </thead>
          <tbody>
            @php
              $c = 0;
            @endphp
            @foreach ($order_visits as $item_order)
              <tr>
                <td>
                  <select name="product_id[]" class="itemName form-control product"></select>
                </td>
                <td><input type="text" name="price[]" value="{{number_format($item_order->price, 0)}}" class="form-control price" readonly></td>
                <td><input type="number" min="1" name="qty[]" value="{{ $item_order->quantity }}" class="form-control qty"></td>
                <td><input type="text" name="amount[]" class="form-control amount" value="{{ $item_order->amount }}" readonly></td>
                <td><button type="button" class="btn btn-success" id="add_btn"><i class="lni lni-plus"></i></button></td>
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
              <td><b>Total</b></td>
              <td><b class="total" id="total"></b></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/sales') }}">Atrás</a>
      </div>
    </div>
  @endif --}}

  <!-- create Sale -->
  @if (!$sale)
    <div class="col-12">
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
              <td style="width: 45%">
                {{-- <select name="product_id[]" class="form-control product">
                  <option>Seleccione Producto</option>
                  @foreach($products as $product)  
                    <option name="product_id[]" value="{{ $product->id }}">{{ $product->name }}</option>
                  @endforeach
                </select> --}}
                <select name="product_id[]" class="itemName form-control product"></select>
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
        <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/sales') }}">Atrás</a>
      </div>
    </div>
  @endif
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
  function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
  }

  $(document).ready(function(){
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
        data: {
          "_token": $('meta[name="csrf-token"]').attr('content'),
          'id': id
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

      //convert currency format to number
      var price = Number(price.replace(/[^0-9.-]+/g,""));

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
    html += '<td><select name="product_id[]" class="itemName form-control product"></select></td>';
    html += '<td><input type="text" name="price[]" class="form-control price" readonly></td>';
    html += '<td><input type="number" min="1" name="qty[]" class="form-control qty"></td>';
    html += '<td><input type="text" name="amount[]" class="form-control amount" readonly></td>';
    html += '<td><button type="button" class="btn btn-danger" id="remove"><i class="lni lni-trash-can"></i></button></td>';
    html += '</tr>';
    $('tbody').append(html);
    renderSelect2(); // call to render select2
  })
</script>   

@if ($sale != null)
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      var tr =$(this).parent().parent();
      var id = tr.find('.product').val();
      var sale_id = <?php echo json_encode($sale->id); ?>;
      console.log('product id: ' + id + ' visit_id: ' + sale_id );
      $.ajax({
          type: 'DELETE',
          url     :"{{ URL::to('/user/sales/deleteItemOrder') }}",
          dataType: 'json',
          data: {
            "_method" : "DELETE",
            '_token': '{{ csrf_token() }}',
            'id':id,
            'sale_id':sale_id
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript">
  $('.itemName').select2({
    placeholder: 'Seleccione un producto',
    ajax: {
      url     :"{{ URL::to('/user/sales/select2-autocomplete-ajax') }}",
      dataType: 'json',
      delay: 250,
      processResults: function (data) {
        return {
          results:  $.map(data, function (item) {
                return {
                    text: item.name,
                    id: item.id
                }
            })
        };
      },
      cache: true
    }}).on('select2:select', function (e) {
      var data = e.params.data;
      console.log(data);
    });

  function renderSelect2(){
    $('.itemName').select2({
    placeholder: 'Seleccione un producto',
    ajax: {
      url     :"{{ URL::to('/user/sales/select2-autocomplete-ajax') }}",
      dataType: 'json',
      delay: 250,
      processResults: function (data) {
        return {
          results:  $.map(data, function (item) {
                return {
                    text: item.name,
                    id: item.id
                }
            })
        };
      },
      cache: true
    }}).on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
    });
  }

</script>
