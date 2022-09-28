@extends('admin::layouts.adminLTE.app')
@section('content')

<section>
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title d-flex align-items-center flex-wrap mb-30">
                        <h2 class="mr-40">Imágenes Producto</h2>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#imageModal" data-id="{{$product->id}}" class="add-new-btn main-btn info-btn btn-hover btn-sm">
                            <i class="lni lni-plus mr-5"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/admin/products') }}">Productos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Imágenes</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== title-wrapper end ========== -->

        <!-- Project Wrapper Start -->
        <div class="projects-wrapper">
            @if (count($images) > 0)
                <div class="row">
                    @foreach($images as $image)
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <a class="thumbnail fancybox" rel="ligthbox" href="{{ asset('/images/products/'.$image->filename) }}">
                                <img class="card-img-top"  width="350" height="350" style="max-width: 100%;max-height: 100%;" src="{{ asset('/images/products/'.$image->filename) }}" alt="{{ Str::limit($image->filename, 15) }}">
                            </a>
                            <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($image->filename, 15) }}</h5>
                            <div class="btn-group">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#imageModal" data-id="{{$image->id}}" data-image="{{$image->filename}}" class="btn btn-sm success-btn btn-edit">Edit</a>
                            </div>
                            <div class="btn-group">
                                <form action="{{ url('/admin/products/image-delete/'.$image->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{$image->id}}"/>
                                    <button type="submit" class="btn btn-sm danger-btn ml-2">Delete</button>
                                </form>
                            </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ url('/admin/products/upload-image/') }}" class="w-100" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-weight-bold">Upload Image</h5>
                                    <button type="button" class="btn-close-img" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-xl-12 m-auto">
                                        <input type="hidden" name="productId" id="productId">
                                        <input type="hidden" name="imageId" id="imageId">
                                        <input type="hidden" name="oldImage" id="oldImage">
                                        <div class="form-group file-input">
                                            <input type="file" name="filename" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card-style mb-30">
                    <h5 class="text-medium">Sin imágenes en este producto..</h5>
                </div>
            @endif
        </div>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script>
      $(document).ready(function() {

          $(".add-new-btn").click(function() {
                var id = $(this).attr("data-id");
                $("#productId").val(id);
                $("#imageId").val("");
                $("#oldImage").val("");
                $("div .old-img").remove();
          });


          $(".btn-edit").click(function() {
                var id = $(this).attr("data-id");
                var imageName = $(this).attr("data-image");

                $("#imageId").val(id);
                $("#oldImage").val(imageName);

                if(imageName !== undefined) {
                    $(".modal-title").text("Update Image");
                    $(".file-input").after("<div class='form-group old-img' style='margin-top: 15px;'><img src='/images/products/"+imageName+"' style='height:100px;'></div>");
                }
          });

      });

  </script>

  <script type="text/javascript">
      $(document).ready(function(){
          $(".fancybox").fancybox({
              openEffect: "none",
              closeEffect: "none"
          });
      });
  </script>
  @endsection  
