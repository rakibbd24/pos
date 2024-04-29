@extends('layouts.master')
@section('main-content')
@section('page-css')
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
  integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Edit_Product') }}</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row" id="section_create_product">
    <div class="col-lg-12 mb-3">

        <!--begin::form-->
        <form action="{{ route('products.update.new', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @php
                            $product_profile = \App\Models\productProfile::where('name', $product->name)->first();
                        @endphp
                        @if($product_profile != NULL)
                            <div class="form-group col-md-4">
                                <label for="name">{{ __('translate.Product_Name') }} <span
                                        class="field_required">*</span></label>
                                <select name="name" id="p_profile">
                                    <option value="">Select Profile</option>
                                    @foreach ($profiles as $profile)
                                        <option value="{{ $profile->name }}" @if($profile->name == $product_profile->name) selected="selected" @endif>{{ $profile->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="form-group col-md-4">
                                <label for="name">{{ __('translate.Product_Name') }} <span
                                        class="field_required">*</span></label>
                                <select name="name" id="p_profile">
                                    <option value="{{ $product->name }}" selected="selected">{{ $product->name }}</option>
                                    <option value="">Select Profile</option>
                                    @foreach ($profiles as $profile)
                                        <option value="{{ $profile->name }}">{{ $profile->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group col-md-4">
                            <label for="cost">{{ __('translate.Product_Cost') }} <span class="field_required">*</span></label>
                            <input type="text" class="form-control" id="cost" placeholder="{{ __('translate.Enter_Product_Cost') }}"
                                name="cost" value="{{ $product->cost }}" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="p_account_holder">Account Holder<span
                                    class="field_required">*</span></label>
                           <select name="account_holder" id="p_account_holder">
                                <option value="">Select Account Holder</option>
                                <option value="{{ $product->account_holder }}" selected="selected">{{ $product->account_holder }}</option>
                                @foreach ($products as $pdata)
                                    <option value="{{ $pdata->account_holder }}">{{ $pdata->account_holder }}</option>
                                @endforeach
                           </select>
                        </div>

                        <div class="form-group col-md-4">
                           <label for="email">Email</label>
                            <div class="input-group">
                            <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Email Address" name="email" value="{{ $product->email }}">
                            </div>
                            </div>
                         </div>


                        <div class="form-group col-md-4">
                            <label for="email_password">Email Password</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Email Password" name="email_password"  value="{{ $product->email_password }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="recovery_email">Recovery Email</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Recovery Email" name="recovery_email" value="{{ $product->recovery_email }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="account_email">Account Email</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Account Email Address" name="account_email" value="{{ $product->account_email }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="account_password">Account Password</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Account Password" name="account_password" value="{{ $product->account_password }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="passcode_pin">Passcode or pin</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Passcode or pin" name="passcode_pin" value="{{ $product->passcode_pin }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="number_company">Number Company</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Company Name" name="number_company" value="{{ $product->number_company }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="number_email_username">Username or Email</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Username or Email" name="number_email_username" value="{{ $product->number_email_username }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="number_password">Password for number</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Password for number" name="number_password" value="{{ $product->number_password }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="mobile_number">Mobile Number</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Mobile Number" name="mobile_number" value="{{ $product->mobile_number }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="proxy_website">Proxy Website</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Proxy Website" name="proxy_website" value="{{ $product->proxy_website }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="proxy_ip_host">Proxy IP or Host</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Proxy IP or Host" name="proxy_ip_host" value="{{ $product->proxy_ip_host }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="port">Proxy Port</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Proxy Port" name="port" value="{{ $product->port }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="proxy_username">Proxy Username</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Proxy username" name="proxy_username" value="{{ $product->proxy_username }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="proxy_password">Proxy Password</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Proxy Password" name="proxy_password" value="{{ $product->proxy_password }}">
                                </div>
                            </div>
                        </div>


                        <div class="form-group col-md-4 d-none">
                            <label for="image">{{ __('translate.Image') }} </label>
                            <input name="image" type="file" class="form-control">
                        </div>

                        <div class="form-group col-md-6 d-none">
                            <label for="port">Attachment Folder Name</label>
                            <div class="input-group">
                                <div class="input-group mb-3">
                                <input type="text" class="form-control"
                                placeholder="Folder Name" name="attatchement_folder_name" value="{{ $product->existing_attatchment_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Use existing attatchment</label>
                            <select name="existing_attatchment_id" id="p_att">
                                <option value="">Select existing attatchment</option>
                                @foreach ($folders as $ditem)
                                    <option value="{{ $ditem->existing_attatchment_id }}" @if($product->existing_attatchment_id == $ditem->existing_attatchment_id) selected="selected" @endif>{{ $ditem->existing_attatchment_id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered" id="item_files_table">
                                    <tr>
                                        <th colspan="3" class="text-center bg-light">Files</th>
                                    </tr>
                                    @if(count($product->files)> 0)
                                        @foreach($product->files as $data)
                                            <tr id="file_row{{ $data->id }}">
                                                <td>
                                                    {{ $data->file_name }}
                                                </td>
                                                <td>
                                                    <a target="_new" href="{{ asset('product_upload/'.$product->existing_attatchment_id.'/'.$data->file_name) }}">Download</a>
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="text-denger" onclick="deleteFile({{ $data->id }})">Delete</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center text-warning">No File Found</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-md-12 mb-4">
                            <label for="note">{{ __('translate.Please_provide_any_details') }} </label>
                            <textarea type="text" class="form-control" name="note"
                                placeholder="{{ __('translate.Please_provide_any_details') }}">{{ $product->note }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="dropzone dropzone-previews" id="drzone"></div>
                        </div>
                        <div class="col-12 d-none">
                            <div id="filelist" style=" background: #f4f7fb; width: 100%; min-height: 100px; border: 1px solid #CCCCCC; border-radius: 5px;padding:10px;"></div>
                            <br />
                            <input type="hidden" id="csrf" value="{{ csrf_token() }}">
                            <div id="container">
                                <a id="pickfiles" href="javascript:;" style="position: relative;z-index: 1;display: block;text-align: center;width: 100%;height: 100px;margin-top: -121px;padding-top: 101px;">Choose Files</a>
                                <a id="uploadfiles" href="javascript:;"></a>
                            </div>
                            <br />
                            <pre id="console"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="i-Yes me-2 font-weight-bold"></i> {{ __('translate.submit') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- end::form -->
    </div>

</div>
@endsection

@section('page-js')
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  ></script>
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        // The dropzone method is added to jQuery elements and can
        // be invoked with an (optional) configuration object.
        Dropzone.autoDiscover = false;
        var upload_url = "{{ route('file-upload') }}";
        var myDropzone = new Dropzone("div#drzone", {
                                        url: upload_url,
                                        acceptedFiles: 'image/*,application/pdf',
                                        headers: {
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        }
                                    });
        $(function () {
        $("#p_att").selectize({ });
        $("#p_profile").selectize({
                        create: true,
                        });

                        $("#p_account_holder").selectize({
                        create: true,
                        });
        });

        function deleteFile(id)
        {
            var ok = confirm("Are you sure you want to delete the selected file?");

            if(ok)
            {
                var app_url = "{{ url('') }}";
                $(`#item_files_table #file_row${id}`).remove();
                $.ajax({
                    url: app_url + '/delete-item-file',
                    method: 'GET',
                    data: {
                        id: id,
                    },
                    dataType: 'json',
                    success:function(response){
                        $(`#item_files_table #file_row${id}`).remove();
                    }
                });
            }
        }
    </script>
@endsection
