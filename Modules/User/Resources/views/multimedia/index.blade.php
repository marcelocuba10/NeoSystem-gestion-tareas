@extends('user::layouts.adminLTE.app')
@section('content')

<section class="table-components">
  <div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="titlemb-30">
            <h2>Archivos compartidos</h2>
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('user/dashboard') }}"> Dashboard </a></li>
                <li class="breadcrumb-item active" aria-current="page">Archivos Compartidos</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- ========== tables-wrapper start ========== -->
    <div class="file-manager-cards-wrapper">
      <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30" id="price_list" style="cursor: pointer;">
            <div class="icon purple">
              <i class="lni lni-coin text-bold"></i>
            </div>
            <div class="content">
              <div>
                <h6 class="text-semibold text-purple">Lista de Precios</h6>
                <p class="text-sm text-gray">{{ $count_price_list }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">459 MB</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30" id="images" style="cursor: pointer;">
            <div class="icon success">
              <i class="lni lni-image text-bold"></i>
            </div>
            <div class="content">
              <div>
                <h6 class="text-semibold text-success">Imágenes</h6>
                <p class="text-sm text-gray">{{ $count_images }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">120 MB</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30" id="manuals" style="cursor: pointer;">
            <div class="icon primary">
              <i class="lni lni-library text-bold"></i>
            </div>
            <div class="content">
              <div>
                <h6 class="text-semibold text-primary">Manuales de Uso</h6>
                <p class="text-sm text-gray">{{ $count_manuals }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">374 MB</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30" id="docs" style="cursor: pointer;">
            <div class="icon orange">
              <i class="lni lni-files text-bold"></i>
            </div>
            <div class="content">
              <div>
                <h6 class="text-semibold text-orange">Documentos</h6>
                <p class="text-sm text-gray">{{ $count_docs }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">237 MB</h6>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tables-wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="card-style mb-30">
              <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
                <div class="left">
                </div>
                <div class="right">
                  <ul class="legend3 d-flex align-items-center mb-30">
                    <li>
                      <div class="d-flex">
                        <div class="text">
                          <button class="btn-group-status" name="filter" value="" id="clear"><p class="text-sm text-dark"><i class="lni lni-close"></i>&nbsp; Quitar Filtros</p></button>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="table-wrapper table-responsive">
                <div id="permissions"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
  $(document).ready(function(){

    document.getElementById('clear').style.display = 'none';

    $.ajax({
      type: "GET",
      url: "{{ URL::to('/user/multimedia/filter') }}",
      data: { 
        filter : '',
        "_token": "{{ csrf_token() }}",
      },
        success:function(permissions)
        {
          $("#permissions").html(permissions);
        }
    });

  });

  $( "#clear" ).click(function() {
    console.log('clear');

    $.ajax({
      type: "GET",
      url: "{{ URL::to('/user/multimedia/filter') }}",
      data: { 
        filter : '',
        "_token": "{{ csrf_token() }}",
      },
        success:function(permissions)
        {
          $("#permissions").html(permissions);
        }
    });

    //hide
    document.getElementById('clear').style.display = 'none';
  });

    $("#price_list").click(function () {
        var type = 'price_list';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/user/multimedia/filter') }}",
          data: { 
            filter : type,
            "_token": "{{ csrf_token() }}",
          },
            success:function(permissions)
            {
              $("#permissions").html(permissions);
            }
        });
    });

    $("#images").change(function () {
        var type = 'images';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/user/multimedia/filter') }}",
          data: { 
            filter : type,
            "_token": "{{ csrf_token() }}",
          },
            success:function(permissions)
            {
              $("#permissions").html(permissions);
            }
        });
    });

    $("#manuals").change(function () {
        var type = 'manuals';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/user/multimedia/filter') }}",
          data: { 
            filter : type,
            "_token": "{{ csrf_token() }}",
          },
            success:function(permissions)
            {
              $("#permissions").html(permissions);
            }
        });
    });

    $("#docs").change(function () {
        var type = 'docs';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/user/multimedia/filter') }}",
          data: { 
            filter : type,
            "_token": "{{ csrf_token() }}",
          },
            success:function(permissions)
            {
              $("#permissions").html(permissions);
            }
        });
    });

</script>
@endsection