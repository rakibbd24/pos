<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Posly - Ultimate Inventory Management System with POS</title>

  <!-- Favicon icon -->
  <link rel=icon href={{ asset('images/logo.svg') }}>
  <!-- Base Styling  -->

  <link rel="stylesheet" href="{{ asset('assets/pos/main/css/fonts.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/pos/main/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/styles/css/themes/lite-purple.min.css') }}">
  <link  rel="stylesheet" href="{{ asset('assets/fonts/iconsmind/iconsmind.css') }}">

  <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('assets/styles/vendor/bootstrap-vue.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/toastr.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/vue-select.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/nprogress.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/autocomplete.css')}}">
  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
  integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>

  <script src="{{ asset('assets/js/axios.js') }}"></script>
  <script src="{{ asset('assets/js/vue-select.js') }}"></script>
  <script src="{{ asset('assets/pos/plugins/jquery/jquery.min.js') }}"></script>
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/flatpickr.min.css')}}">

    {{-- Alpine Js --}}
    <script defer src="{{ asset('js/plugin-core/alpine-collapse.js') }}"></script>
    <script defer src="{{ asset('js/plugin-core/alpine.js') }}"></script>
    <script src="{{ asset('js/plugin-script/alpine-data.js') }}"></script>
    <script src="{{ asset('js/plugin-script/alpine-store.js') }}"></script>

</head>

<body class="sidebar-toggled sidebar-fixed-page pos-body">

  <!-- Pre Loader Strat  -->
  <div class='loadscreen' id="preloader">
      <div class="loader spinner-border spinner-border-lg">
      </div>
  </div>

  <div class="compact-layout pos-layout">
    <div data-compact-width="100" class="layout-sidebar pos-sidebar">
      @include('layouts.new-sidebar.sidebar')
    </div>

    <div class="layout-content">
      @include('layouts.new-sidebar.header')

      <div class="content-section" id="main-pos">
        <form action="{{ route('pos.create') }}" method="POST">
            @csrf
            <section class="pos-content">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Customer</label>
                            <select name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="card m-0 card-list-products">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold m-0">{{ __('translate.Cart') }}</h6>
                            </div>

                            <div class="card-items">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Item</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>

                                    <tbody id="cart-data-new">
                                        @if(session()->has('sale_cart'))
                                            @php
                                                $g_total = 0;
                                            @endphp
                                            @foreach (session()->get('sale_cart') as $cart)
                                                <tr id="row{{ $cart['item_id'] }}">
                                                    <td style="width:15%;">
                                                        <input type="hidden" name="item_id[]" value="{{ $cart['item_id'] }}">
                                                        <span class="increment-decrement btn btn-danger rounded-circle" onclick="removeTR({{ $cart['item_id'] }})">X</span>
                                                        <img  src="{{ $cart['item_image'] }}" style="width:50px;">
                                                    </td>
                                                    <td style="width:20%;"> {{ $cart['item_name'] }} </td>
                                                    <td style="width:20%;">
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text" id="basic-addon1">{{ $currency }}</span>
                                                            <input type="text" class="form-control" placeholder="Price"  id="unit_price{{ $cart['item_id'] }}" name="unit_price[]" aria-describedby="basic-addon1" oninput="updatePrice(this.value, {{ $cart['item_id'] }});" value="{{ $cart['item_price'] }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                @php
                                                    $g_total += $cart['item_price'];
                                                @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="cart-summery">
                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <label class="form-label">Payment Date <span class="text-warning">*</span></label>
                                            <input type="text" class="form-control" name="date" id="datetimepicker" value="Select Payment Date" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <label class="form-label">Payment Amount <span class="text-warning">*</span></label>

                                            @if(session()->has('sale_cart'))
                                                <input type="text" class="form-control" name="montant" id="montant" placeholder="0.00" required value="{{ $g_total }}">
                                            @else
                                                <input type="text" class="form-control" name="montant" id="montant" placeholder="0.00" required>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label for="exampleFormControlInput1" class="form-label">Payment Choice <span class="text-warning">*</span></label>
                                        <select name="payment_method_id" required>
                                            <option value="">Choose Payment Choice</option>
                                            @foreach ($payment_methods as $payment_method)
                                                <option value="{{ $payment_method->id }}">{{ $payment_method->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label for="exampleFormControlInput1" class="form-label">Account <span class="text-warning">*</span></label>
                                        <select name="account_id" required>
                                            <option value="">Choose Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" @if($account->id=='3') selected="selected" @endif>{{ $account->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="pt-3 border-top border-gray-300 summery-total">
                                            @if(session()->has('sale_cart'))
                                                <input type="hidden" name="GrandTotal" id="input_GrandTotal" value="{{ $g_total }}">
                                            @else
                                                <input type="hidden" name="GrandTotal" id="input_GrandTotal" value="0">
                                            @endif
                                            <h5 class="summery-item m-0">
                                                <span>Grand Total</span>
                                                <span id="GrandTotal">{{$currency}}
                                                    @if(session()->has('sale_cart'))
                                                        {{ $g_total }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </span>
                                            </h5>
                                        </div>
                                        <button class="cart-btn btn btn-primary mt-2">
                                            Order Now
                                          </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="products">
                            <div class="row">
                                @foreach ($products as $product)
                                    <div class="col-4" onclick="addToCart({{$product->product->id}});">
                                        <div class="card product-card cursor-pointer">
                                            @if($product->product->image == 'no_image.png')
                                                <img src="{{ asset('images/products/no_image.png') }}" alt="">
                                            @else
                                                <img src="{{ $product->product->image }}" alt="">
                                            @endif
                                            <div class="card-body pos-card-product">
                                                <p class="text-gray-600">{{ $product->product->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-center">
                                    {!! $products->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
      </div>
    </div>
  </div>
  {{-- -----------------------------------------------------------------------------------------------}}

  <script type="text/javascript">
        $(window).on('load', function(){
        jQuery("#loader").fadeOut(); // will fade out the whole DIV that covers the website.
        jQuery("#preloader").delay(800).fadeOut("slow");
        jQuery("pos-layout").show(); // will fade out the whole DIV that covers the website.

    });
    </script>

  {{-- vue js --}}
  <script src="{{asset('assets/js/bootstrap-vue.min.js')}}"></script>
  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
  <script src="{{asset('/assets/js/moment.min.js')}}"></script>

  {{-- sweetalert2 --}}
  <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>


  {{-- common js --}}
  <script src="{{ asset('assets/js/common-bundle-script.js') }}"></script>
  {{-- page specific javascript --}}
  @yield('page-js')

  <script src="{{ asset('assets/js/script.js') }}"></script>

  <script src="{{asset('assets/js/vendor/toastr.min.js')}}"></script>
  <script src="{{asset('assets/js/toastr.script.js')}}"></script>

  <script src="{{ asset('assets/js/customizer.script.js') }}"></script>
  <script src="{{asset('assets/js/nprogress.js')}}"></script>


  <script src="{{ asset('assets/js/tooltip.script.js') }}"></script>
  <script src="{{ asset('assets/js/script_2.js') }}"></script>
  <script src="{{ asset('assets/js/vendor/feather.min.js') }}"></script>
  <script src="{{asset('assets/js/flatpickr.min.js')}}"></script>


  <script src="{{ asset('assets/js/compact-layout.js') }}"></script>

  <script type="text/javascript">
    $(function () {
        "use strict";

        $(document).ready(function () {

          flatpickr("#datetimepicker", {
            defaultDate: "today"
          });

        });

      });
  </script>
  <script
  src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
  integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
></script>

<script>
    $(function () {
      $("select").selectize({ });
    });
  </script>

<script>
    function grand_total()
    {
        var arr = [];
        $("#cart-data-new tr").each(function() {
            arr.push(this.id);
        });

        var total_price = 0;
        arr.forEach(function(data){
            var uid = data.replace('row','');
            var unit_price = parseFloat($("#unit_price"+uid).val());
            total_price += unit_price;
        });
        var currency = "{{ $currency }}";
        var GrandTotal = currency+" "+total_price;
        $("#montant").val(total_price);
        $("#GrandTotal").text(GrandTotal);
        $("#input_GrandTotal").val(total_price);
    }
    function addToCart(product_id)
    {
        var arr = [];
        $("#cart-data-new tr").each(function() {
            arr.push(this.id);
        });

        var arr_data = "row"+product_id;
        if(arr.includes(arr_data))
        {
            toastr.error('Selected item already added to cart!');
            return false;
        }

        var url = "{{ route('get.ajax.product') }}";
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                product_id: product_id,
            },
            dataType: 'json',
            success:function(response){
                var audio = new Audio("/assets/audio/Beep.wav")
                audio.play();

                if(response.product.image == 'no_image.png')
                {
                    var img = "{{ asset('images/products/no_image.png') }}";
                }else{
                    var img = response.product.image;
                }
                var html = `
                        <tr id="row${response.product.id}">
                            <td style="width:15%;">
                                <input type="hidden" name="item_id[]" value="${response.product.id}">
                                <span class="increment-decrement btn btn-danger rounded-circle" onclick="removeTR(${response.product.id})">X</span>
                                <img  src="${img}" style="width:50px;">
                            </td>
                            <td style="width:20%;"> ${response.product.name}</td>
                            <td style="width:20%;">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">{{ $currency }}</span>
                                    <input type="text" class="form-control" placeholder="Price" id="unit_price${response.product.id}" name="unit_price[]" aria-describedby="basic-addon1" oninput="updatePrice(this.value, ${response.product.id});" value="0">
                                </div>
                            </td>
                        </tr>
                `;

                $("#cart-data-new").append(html);
                var url = "{{ route('session.add.to.cart') }}";
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        item_id: response.product.id,
                        item_name: response.product.name,
                        item_image: img,
                        item_price: '0'
                    },
                    dataType: 'json',
                    success:function(response){
                        //console.log(response.status);
                    }
                });
            }
        });
        grand_total();
    }

    function removeTR(uid)
    {
        $(`#cart-data-new #row${uid}`).remove();
        var url = "{{ route('session.remove.from.cart') }}";
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                item_id: uid
            },
            dataType: 'json',
            success:function(response){
                //console.log(response.status);
            }
        });
        grand_total();
    }

    function updatePrice(price, uid)
    {
        var url = "{{ route('session.update.price.cart') }}";
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                item_id: uid,
                item_price: price
            },
            dataType: 'json',
            success:function(response){
                //console.log(response.status);
            }
        });
        grand_total();
    }
</script>
</body>

</html>
