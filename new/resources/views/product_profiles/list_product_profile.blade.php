@extends('layouts.master')
@section('main-content')

@section('page-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
@endsection

<div class="breadcrumb">
  <h1>Product Profiles</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_product_list">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="text-end mb-3">
          @can('products_add')
          <a href="#" class=" btn btn-outline-primary btn-md m-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="i-Add me-2 font-weight-bold"></i>
            {{ __('translate.Create') }}</a>
          @endcan
        </div>

        <div class="table-responsive">
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                      </tr>
                </thead>
                <tbody>
                    @foreach ($product_profiles as $data)
                        <tr>
                            <td>{{ $data->id }}</td>
                            <td>
                                <img src="{{ $data->image }}" alt="" style="width:50px;">
                            </td>
                            <td>{{ $data->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Create Product Profile</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('product.profile.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-12">
                        <label for="name">Profile Name <span class="field_required">*</span></label>
                        <input type="text" name="name" placeholder="Enter Profile Name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="image">Profile Image</label>
                        <input name="image" type="file" class="form-control" required>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" style="float:right!Important;"><i class="i-Yes me-2 font-weight-bold"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
  </div>
</div>


@endsection

@section('page-js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script>
    $(document).ready( function () {
        $('#myTable').DataTable();
    } );
    @if(session('successMessage'))
        toastr.success(session('successMessage'));
    @endif
</script>
@endsection
