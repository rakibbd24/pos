@extends('layouts.master')
@section('main-content')
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/css/custom.css')}}">
@endsection

<div class="breadcrumb">
    <h1>Import Product</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row">
    <div class="col-12">
        <div class="text-center">
            <h4 class="text-center">Download Spreadsheet Template</h4>
            <p class="text-center">For best results use our spreadsheet template which will include all records that are already in the system.</p>
            <a target="_new" href="{{ asset('sample/item.xlsx') }}" class="btn btn-success mr-2" download="" title="item"><i class="icon-download mr-2"></i>Template for New Item</a>
        </div>
    </div>
    <div class="col-12 mt-5">
        <div class="loader" id="loader"></div>
        <div style="background-color: rgba(221, 221, 221, 0.32); padding: 20px; filter: blur(0px); pointer-events: auto;" id="blur-area">
            <div class="row">
                <div class="col-6 offset-3">
                    <div class="alert alert-warning text-center" id="ajax_error" style="display:none;"></div>
                    <div class="alert alert-success text-center" id="ajax_success" style="display:none;"></div>
                    <form method="POST" id="import_form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf"/>
                        <div class="form-group col-md-12">
                            <h4 class="text-center font-weight-bolder">Upload File</h4>
                            <p class="text-center" style="margin-top:-10px;">Upload a .xlsx or .csv Items Template with the updated information:</p>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="file" name="file" class="form-control" placeholder="Select file" value="">
                        </div>

                        <div class="form-group row mt-2">
                            <div class="col-lg-12">
                                <button type="submit" id="save_button" class="btn btn-primary btn-block" style="width:100%;">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script type="text/javascript">
    var app_url = "{{ url('') }}";
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('#csrf').val()
        }
    });
    $(document)
        .ajaxStart(function () {
            document.getElementById("ajax_error").style.display = "none" ;
            document.getElementById("ajax_error").style.display = "ajax_success" ;
            document.getElementById("blur-area").style.filter = 'blur(2px)';
            document.getElementById("blur-area").style.pointerEvents = 'none';
            document.getElementById("loader").style.visibility = "visible" ;
            document.getElementById("loader").style.display = "block" ;

        })
        .ajaxStop(function () {
            document.getElementById("blur-area").style.filter = 'blur(0px)';
            document.getElementById("blur-area").style.pointerEvents = 'auto';
            document.getElementById("loader").style.visibility = "hidden" ;
        });
        $('#import_form').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type:'POST',
                    url: app_url + '/products/product-import-store',
                    method: 'POST',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: (response) => {
                        console.log(response);
                        if(response.status == '0')
                        {
                            document.getElementById("ajax_error").style.display = "block" ;
                            $("#ajax_error").text(response.message);
                        }else{
                            document.getElementById("ajax_success").style.display = "block" ;
                            $("#ajax_success").text(response.message);
                        }
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
        });
    </script>
@endsection
