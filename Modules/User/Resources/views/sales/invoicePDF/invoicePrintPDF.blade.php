<!DOCTYPE html>
<html>
<head>
    <title>Imprimir {{ $sale->type }}</title>
</head>
<style type="text/css">
    body{
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 14px;
    }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:120px;
        height:100px;
        padding-top:0px;
        margin-top: -15px;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:0px 5px;
        height: 20px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:12px;
    }
    table tr td{
        font-size:12px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .float-right{
        float:right;
    }
    .total-part{
        font-size:14px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
</style>
<body>
<div class="head-title">
    <h1 class="text-center m-0 p-0">Nota de {{ $sale->type }}</h1>
</div>
<div class="add-detail mt-10">
    <div class="w-50 float-left mt-10">
        <p class="m-0 pt-5 text-bold w-100">Nº Pedido u Orden: <span class="gray-color">{{ $sale->invoice_number }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Emitida el: <span class="gray-color">{{ ( $sale->type == 'Venta') ? date('d/m/Y - H:i', strtotime($sale->sale_date)) : date('d/m/Y - H:i', strtotime($sale->order_date)) }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Estado: <span class="gray-color">{{ $sale->status }}</span></p>
    </div>
    <div class="float-right logo">
        {{-- <img src="{{ asset('public/adminLTE/images/logo/logo-pyp.png') }}">   --}}
        <h3 style="margin-top:30px">{{ $user->name }}</h3>
    </div>
    <div style="clear: both;"></div>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50">De</th>
            <th class="w-50">Para</th>
        </tr>
        <tr>
            <td>
                <div class="box-text">
                    <p>Nombre: {{ $user->name }}</p>
                    <p>Doc / RUC: {{ $user->doc_id }}</p>
                    <p>Teléfono: {{ $user->phone_1 }}</p>
                    <p>Email: {{ $user->email }}</p>
                    <p>Dirección: {{ $user->address }} - {{ $user->estate }}</p>
                </div>
            </td>
            <td>
                <div class="box-text">
                    <p>Nombre: {{ $sale->customer_name }}</p>
                    <p>Doc / RUC: {{ $sale->doc_id }}</p>
                    <p>Teléfono: {{ $sale->phone }}</p>
                    <p>Email: {{ $sale->email }}</p>
                    <p>Dirección: {{ $sale->address }} - {{ $sale->estate }}</p>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
        @foreach ($order_details as $item_order)
            <tr align="center" style="height: 10px;">
            <td><p class="text-sm">{{ $item_order->custom_code }}</td>
            <td><p class="text-sm" data-toggle="tooltip" data-placement="bottom" title="{{ $item_order->name }}">{{ Str::limit($item_order->name, 55) }}</p></td>
            <td><p class="text-sm">{{number_format($item_order->price, 0)}}</p></td>
            <td><p class="text-sm">{{ $item_order->quantity }}</p></td>
            <td><p class="text-sm">{{number_format($item_order->amount, 0)}}</p></td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7">
                <div class="total-part">
                    <div class="total-left w-85 float-left" align="right">
                        <p>SubTotal</p>
                        <p>Descuento</p>
                        <p>Precio Total</p>
                    </div>
                    <div class="total-right w-15 float-left text-bold" align="right">
                        <p>{{number_format($total_order, 0)}}</p>
                        <p>0.00</p>
                        <p>{{number_format($total_order, 0)}}</p>
                    </div>
                    <div style="clear: both;"></div>
                </div> 
            </td>
        </tr>
    </table>
</div>
</html>