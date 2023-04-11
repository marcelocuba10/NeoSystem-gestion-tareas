@extends('admin::layouts.adminLTE.app')
@section('content')

<section class="table-components">
  <div class="container-fluid">
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="title d-flex align-items-center flex-wrap mb-30">
            <h2 class="mr-40">Archivos Compartidos</h2>
            @can('multimedia-sa-create')
              <a href="javascript:void(0)" data-toggle="modal" data-target="#imageModal" class="main-btn info-btn btn-hover btn-sm"><i class="lni lni-plus mr-5"></i></a>
            @endcan  
          </div>
        </div>
        <div class="col-md-6">
          <div class="breadcrumb-wrapper mb-30">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Archivos Compartidos</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

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
                <p class="text-sm text-gray">{{ $price_list->count }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">{{ $price_list_total_size }}</h6>
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
                <p class="text-sm text-gray">{{ $images->count }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">{{ $images_total_size }}</h6>
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
                <p class="text-sm text-gray">{{ $manuals->count }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">{{ $manuals_total_size }}</h6>
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
                <p class="text-sm text-gray">{{ $docs->count }} archivos</p>
              </div>
              <div>
                <h6 class="file-size">{{ $docs_total_size }}</h6>
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

      <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <form action="{{ url('/admin/multimedia/upload-file/') }}" class="w-100" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Agregar Archivo</h5>
                <button type="button" class="btn-close-img" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="col-xl-12 m-auto">
                  <input type="hidden" name="fileId" id="fileId">
                  <input type="hidden" name="oldFile" id="oldFile">
                  <div class="form-group file-input">
                    <input type="file" name="filename" class="form-control">
                  </div>
                  <select class="form-select mt-10" aria-label="Default select example" name="category" id="category">
                    <option selected>Seleccione una Categoría</option>
                    <option value="Lista de Precios">Lista de Precios</option>
                    <option value="Imágenes">Imágenes</option>
                    <option value="Manuales">Manuales</option>
                    <option value="Documentos">Documentos</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success">Subir</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script>
  var optionSelected = '';

  $("#category").change(function () {
    var optionSelected = $(this).val();
    console.log(optionSelected);
  });

  $(document).ready(function(){

    $(".add-new-btn").click(function() {
      $("#fileId").val("");
      $("#oldFile").val("");
      $("div .old-img").remove();
    });

    $(".btn-edit").click(function() {
      var id = $(this).attr("data-id");
      var filename = $(this).attr("data-image");

      $("#fileId").val(id);
      $("#oldFile").val(filename);

      if(filename !== undefined) {
        $(".modal-title").text("Subir Archivo");
        $(".file-input").after("<div class='form-group old-img' style='margin-top: 15px;'><img src='/files/"+filename+"' style='height:100px;'></div>");
      }
    });

    document.getElementById('clear').style.display = 'none';

    $.ajax({
      type: "GET",
      url: "{{ URL::to('/admin/multimedia/filter') }}",
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
      url: "{{ URL::to('/admin/multimedia/filter') }}",
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
        var type = 'Lista de Precios';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/admin/multimedia/filter') }}",
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

    $("#images").click(function () {
        var type = 'Imágenes';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/admin/multimedia/filter') }}",
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

    $("#manuals").click(function () {
        var type = 'Manuales';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/admin/multimedia/filter') }}",
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

    $("#docs").click(function () {
        var type = 'Documentos';
        document.getElementById('clear').style.display = 'initial';

        $.ajax({
          type: "GET",
          url: "{{ URL::to('/admin/multimedia/filter') }}",
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