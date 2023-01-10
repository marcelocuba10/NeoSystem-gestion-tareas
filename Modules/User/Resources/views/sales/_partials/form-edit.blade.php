  @csrf
  <div class="row">
    <!-- if sale is order and not contain visit_id, show data from sales -- editable -->
    @if ($sale->type == 'Presupuesto' && !$sale->visit_id)
      <div class="col-5">
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
      <div class="col-sm-2">
        <div class="input-style-1">
          <label>Tipo</label>
          <input value="{{ $sale->type }}" type="text" readonly>
        </div>
      </div>
      <div class="col-2">
        <div class="input-style-1">
          <label>Estado</label>
          <input type="text" value="{{ $sale->status }}" readonly>
        </div>
      </div>
    @endif

    <!-- if sale contain not contain visit_id, show data from sale -- readonly -->
    @if ($sale->type == 'Venta' && !$sale->visit_id)
      <div class="col-5">
        <div class="input-style-1">
          <label>Cliente</label>
          <input type="text" value="{{ $sale->customer_name ?? old('customer_name') }}" readonly>
        </div>
      </div>
      <div class="col-3">
        <div class="input-style-1">
          <label>Fecha/Hora</label>
          <input type="text" value="{{ date('d/m/Y - H:i', strtotime($sale->sale_date)) }}" readonly>
        </div>
      </div>
      <div class="col-sm-2">
        <div class="input-style-1">
          <label>Tipo</label>
          <input value="{{ $sale->type }}" type="text" readonly>
        </div>
      </div>
      <div class="col-2">
        <div class="input-style-1">
          <label>Estado</label>
          <input type="text" value="{{ $sale->status }}" readonly>
        </div>
      </div>
    @endif

    <!-- if sale contain visit_id, show data from customer visit -- readonly -->
    @if($sale->visit_id)
      <div class="col-4">
        <div class="input-style-1">
          <label>Cliente</label>
          <input type="text" value="{{ $sale->customer_name ?? old('customer_name') }}" readonly>
        </div>
      </div>
      <div class="col-3">
        <div class="input-style-1">
          <label>Fecha/Hora</label>
          <input type="text" value="{{ date('d/m/Y - H:i', strtotime($sale->visit_date)) }}" readonly>
        </div>
      </div>
      <div class="col-3">
        <div class="input-style-1">
          <label>Fecha Próxima Visita</label>
          <input type="text" value="{{ $sale->next_visit_date ?? old('next_visit_date') }}" readonly>
        </div>
      </div>
      <div class="col-2">
        <div class="input-style-1">
          <label>Hora Próxima Visita</label>
            <input type="text" value="{{ $sale->next_visit_hour ?? old('next_visit_hour') }}" readonly>
        </div>
      </div>
      <div class="col-2">
        <div class="input-style-1">
          <label>Estado</label>
          <input type="text" value="{{ $sale->status ?? old('status') }}" readonly>
        </div>
      </div>
      <div class="col-5">
        <div class="input-style-1">
          <label>Resultado de la Visita</label>
          <textarea type="text" value="{{ $sale->result_of_the_visit }}" readonly>{{ $sale->result_of_the_visit }}</textarea>
        </div>
      </div>
      <div class="col-5">
        <div class="input-style-1">
          <label>Objetivos</label>
          <textarea type="text" value="{{ $sale->objective ?? old('objective') }}" readonly>{{ $sale->objective ?? old('objective') }}</textarea>
        </div>
      </div>
    @endif

    <h5 class="text-medium mb-20" >Detalles {{ $sale->type }}</h5>

    <!-- if is order created with sales page -- editable -->
    @if ($sale->type == 'Presupuesto' && !$sale->visit_id)
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
              @foreach ($order_detail as $item_order)
                <tr>
                  <td style="width: 45%">
                    <select name="product_id[]" class="itemName form-control product">
                      <option>Seleccione Producto</option>
                      @foreach($products as $product)  
                        <option value="{{ $product->id }}" name="product_id[]" {{ ( $product->id == $item_order->product_id) ? 'selected' : '' }}> {{ $product->name}} </option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="text" name="price[]" value="{{number_format($item_order->price, 0)}}" class="form-control price" readonly></td>
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
                <td><b>Total</b></td>
                <td><b class="total" id="total"></b></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    @endif

    <!-- if is order or sale created with customer_visit or sales page -- readonly -->  
    @if ($sale->type == 'Presupuesto' && $sale->visit_id)
      <div class="table-responsive">
        <table class="invoice-table table">
          <thead style="background-color: #DAEFFE;">
            <tr>
              <th>
                <h6 class="text-sm text-medium">Cod</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Producto</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Precio</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Cantidad</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">SubTotal</h6>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order_detail as $item_order)
              <tr>
                <td><p class="text-sm">{{ $item_order->custom_code }}</td>
                <td><p class="text-sm" data-toggle="tooltip" data-placement="bottom" title="{{ $item_order->name }}">{{ Str::limit($item_order->name, 65) }}</p></td>
                <td><p class="text-sm">G$ {{number_format($item_order->price, 0)}}</p></td>
                <td><p class="text-sm">{{ $item_order->quantity }}</p></td>
                <td><p class="text-sm">G$ {{number_format($item_order->amount, 0)}}</p></td>
              </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>
                <h4>Total</h4>
              </td>
              <td>
                <h4>G$ {{number_format($total_order, 0)}}</h4>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif  

    <!-- if sale processed -- readonly -->    
    @if($sale->type == 'Venta')
      <div class="table-responsive">
        <table class="invoice-table table">
          <thead style="background-color: #DAEFFE;">
            <tr>
              <th>
                <h6 class="text-sm text-medium">Cod</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Producto</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Precio</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">Cantidad</h6>
              </th>
              <th>
                <h6 class="text-sm text-medium">SubTotal</h6>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order_detail as $item_order)
              <tr>
                <td><p class="text-sm">{{ $item_order->custom_code }}</td>
                <td><p class="text-sm" data-toggle="tooltip" data-placement="bottom" title="{{ $item_order->name }}">{{ Str::limit($item_order->name, 65) }}</p></td>
                <td><p class="text-sm">G$ {{number_format($item_order->price, 0)}}</p></td>
                <td><p class="text-sm">{{ $item_order->quantity }}</p></td>
                <td><p class="text-sm">G$ {{number_format($item_order->amount, 0)}}</p></td>
              </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>
                <h4>Total</h4>
              </td>
              <td>
                <h4>G$ {{number_format($total_order, 0)}}</h4>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif
    
    <!-- Actions buttons -->    
    @if ($sale->type == 'Presupuesto')
      <div class="col-12">
        <div class="button-group d-flex justify-content-center flex-wrap">
          <input type="hidden" name="orderToSale" id="orderToSale">
          <button type="submit" id="btn_submit" class="main-btn primary-btn btn-hover m-2 btn-orderToSale">Procesar a Venta</button>
          @if (!$sale->visit_id)
            @can('sales-edit')
              <button type="submit" id="btn_submit" class="main-btn primary-btn-outline btn-hover m-2">Actualizar</button>
            @endcan  
          @endif
          @if ($sale->visit_id)
            @can('customer_visit-edit')
              <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/customer_visits/edit/'.$sale->visit_id) }}">Editar</a>
            @endcan 
          @endif
          <div class="button-group d-flex justify-content-center flex-wrap">
            <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/sales') }}">Atrás</a>
          </div>
        </div>
      </div>
    @endif  

    @if($sale->type == 'Venta')
      <div class="col-12">
        <div class="button-group d-flex justify-content-center flex-wrap">
          <input type="hidden" name="sale_id" id="sale_id">
          <input type="hidden" name="invoice_number" id="invoice_number">
          <input type="hidden" name="cancelSale" id="cancelSale">
          @can('sales-delete')
            @if ($sale->status != 'Cancelado')
              <button type="submit" id="btn_submit" data-id="{{$sale->id}}" data-invoice_number="{{$sale->invoice_number}}" class="main-btn danger-btn btn-hover m-2 btn-cancelSale show_confirm">Cancelar Venta</button>
            @endif
          @endcan
          <div class="button-group d-flex justify-content-center flex-wrap">
            <a class="main-btn primary-btn-outline m-2" href="{{ url('/user/sales') }}">Atrás</a>
          </div>
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
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script type="text/javascript">
  $('.show_confirm').click(function(event) {
    var form =  $(this).closest("form");
    var name = $(this).data("name");
    event.preventDefault();
    swal({
        title: '¿Está seguro que desea cancelar esta venta?',
        // text: "Si eliminas esto, desaparecerá para siempre.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ["No", "Sí"],
    })
    .then((willDelete) => {
      if (willDelete) {
        form.submit();
      }
    });
  });
</script>

<script type="text/javascript">

  function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
  }

  $(document).ready(function(){
    
    // Pass parameter to process order to Sale
    $(".btn-orderToSale").click(function() {
      $("#orderToSale").val(true);
    });

    // Pass parameter to process cancel Sale
    $(".btn-cancelSale").click(function() {
      var sale_id = $(this).attr("data-id");
      var invoice_number = $(this).attr("data-invoice_number");

      $("#sale_id").val(sale_id);
      $("#invoice_number").val(invoice_number);
      $("#cancelSale").val(true);
    });
  
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
          //tr.find('.price').val(data.sale_price);
  
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
  
  //hide row when item order is deleted, and recalculate total
  $(document).on('click', '#remove', function () {
    $(this).closest('tr').remove();
    total();
  });
  
</script>

<!-- remove item order detail -- edit mode; else remove in create mode -->
@if ($sale != null)
  <script type="text/javascript">
    $(document).on('click', '#remove', function () {
      var tr =$(this).parent().parent();
      var id = tr.find('.product').val();
      var sale_id = <?php echo json_encode($sale->id); ?>;
      $.ajax({
          type: 'DELETE',
          url     :"{{ URL::to('/user/sales/deleteItemOrder') }}",
          dataType: 'json',
          data: {
            "_method" : "DELETE",
            '_token' : '{{ csrf_token() }}',
            'id' : id,
            'sale_id' : sale_id
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