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
      <div class="form-check checkbox-style mb-30">
        <input name="is_vigia" class="form-check-input" type="checkbox" id="checkbox-not-robot">
        <label class="form-check-label" for="checkbox-not-robot" >¿Crear Presupuesto?</label>
      </div>
    </div>
    <!-- end col -->

    {{-- <form  method="POST" action="">
      @csrf      
      <div class="table-wrapper table-responsive">
        <table class="table top-selling-table">
          <thead>
            <tr>
              <th><h6>Producto</h6></th>
              <th><h6>Stock</h6></th>
              <th><h6>Precio</h6></th>
              <th><h6>Cantidad</h6></th>
              <th><h6>Subtotal</h6></th>
              <th scope="col"><a class="addRow btn btn-success"><i class="lni lni-plus"></i></a></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <select name="product_id[]" class="form-control productname">
                  <option>Select Product</option>
                  @foreach($products as $product)
                    <option name="product_id[]" data-qty_av="{{ $product->quantity }}" data-price="{{ $product->sale_price }}" value="{{ $product->id }}">{{ $product->name }}</option>
                  @endforeach
                </select>
              </td>
              <td><input type="text" name="qty_av[]" id="qty_av" class="form-control qty_av" readonly></td>
              <td><input type="text" name="price[]" id="price" class="form-control price" readonly></td>
              <td><input type="text" name="qty[]" class="form-control qty"></td>
              <td><input type="text" name="amount[]" class="form-control amount" readonly></td>
              <td><a class="btn btn-danger remove"><i class="lni lni-trash-can"></i></a></td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td><b>Total</b></td>
              <td><b class="total"></b></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div >
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form> --}}

    <div class="col-12">
      <div class="button-group d-flex justify-content-center flex-wrap">
        <button type="submit" class="main-btn primary-btn btn-hover m-2">Guardar</button>
        <a class="main-btn danger-btn-outline m-2" href="{{ url('/user/customer_visits') }}">Atrás</a>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){

        $('tbody').delegate('.productname', 'change', function () {
            var  tr = $(this).parent().parent();
            tr.find('.qty').focus();
        })

        $('tbody').delegate('.productname', 'change', function () {
            var tr =$(this).parent().parent();
            var id = tr.find('.productname').val();
            var dataId = {'id':id};
            var price = $('.productname option:selected').attr('data-price');
            var qty_av = $('.productname option:selected').attr('data-qty_av');
            $("#price").val(price); 
            $("#qty_av").val(qty_av);  
            $.ajax({
                type    : 'GET',
                url: '/user/products/getItemProduct',
                dataType: 'json',
                data: {
                  "_token": $('meta[name="csrf-token"]').attr('content'),
                  'id':id
                },
                success:function (data) {
                    tr.find('.price').val(data.price);
                }
            });
        });

        $('tbody').delegate('.qty,.price', 'keyup', function () {
            var tr = $(this).parent().parent();
            var qty = tr.find('.qty').val();
            var price = tr.find('.price').val();
            var amount = (qty * price);
            tr.find('.amount').val(amount);
            total();
        });

        function total(){
            var total = 0;
            $('.amount').each(function (i,e) {
                var amount =$(this).val()-0;
                total += amount;
            })
            $('.total').html(total);
        }

        $('.addRow').on('click', function () {
            addRow();
        });

        function addRow() {
            var addRow = '<tr>\n' +
                '         <td><select name="product_id[]" class="form-control productname">\n' +
                '         <option value="0" selected="true" disabled="true">Select Product</option>\n' +
'                                        @foreach($products as $product)\n' +
'                                            <option name="product_id[]" data-qty_av="{{ $product->quantity }}" data-price="{{ $product->sale_price }}" value="{{ $product->id }}">{{ $product->name }}</option>\n' +
'                                        @endforeach\n' +
                '               </select></td>\n' +
'                                <td><input type="text" name="qty_av[]" id="qty_av" class="form-control qty_av" readonly></td>\n' +
'                                <td><input type="text" name="price[]" id="price" value='+price.value+' class="form-control price" readonly></td>\n' +
'                                <td><input type="text" name="qty[]" class="form-control qty" ></td>\n' +
'                                <td><input type="text" name="amount[]" class="form-control amount" readonly></td>\n' +
'                                <td><a class="btn btn-danger remove"><i class="lni lni-trash-can"></i></a></td>\n' +
'                             </tr>';
            $('tbody').append(addRow);
        };

        $('.remove').on('click', function () {
            var l =$('tbody tr').length;
            if(l==1){
                alert('you cant delete last one')
            }else{
                $(this).parent().parent().remove();
            }
        });

    });
</script>
  