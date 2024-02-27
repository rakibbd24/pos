<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice</title>

    <link rel="stylesheet" href="{{asset('assets/styles/vendor/pdf_style.css')}}">
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            }

            td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            }
    </style>
  </head>
  <body>
    <div class="container">
        <div style="width:100%; margin:0px auto;padding-20px;">

            <table>
                <tr>
                    <td>
                        <b>Date: {{$sale->date ?? ''}}</b>
                        <br><b>Order Reference: {{$sale->Ref ?? ''}}</b>
                        <br><b>Seller representative: {{$sale->user->name ?? ''}}</b>
                        <br><b>Price: {{$sale_item->price ?? ''}}</b>
                    </td>
                    <td>
                        <b>Product Name: {{$sale_item->product->name ?? ''}}</b>
                        <br><b>Customer Mail: {{$sale->client->email ?? ''}}</b>
                        <br><b>Customer Phone: {{$sale->client->phone ?? ''}}</b>
                    </td>
                </tr>
            </table>

            <div class="col-12" style="margin-top:20px;">
                <table class="table table-bordered">
                    <tr style="background-color: #dddddd;">
                        <th colspan="2" style="text-align:center;">Email Access Details</th>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{$sale_item->product->email ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Email Password</th>
                        <td>{{$sale_item->product->email_password ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Recovery Email</th>
                        <td>{{$sale_item->product->recovery_email ?? ''}}</td>
                    </tr>
                </table>
            </div>

            <div class="col-12" style="margin-top:20px;">
                <table class="table table-bordered">
                    <tr style="background-color: #dddddd;">
                        <th colspan="2" style="text-align:center;">Account Access Details</th>
                    </tr>

                    <tr>
                        <th>Account Email</th>
                        <td>{{$sale_item->product->account_email ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Account Password</th>
                        <td>{{$sale_item->product->account_password ?? ''}}</td>
                    </tr>
                    <tr>
                        <th>Passcode Or Pin</th>
                        <td>{{$sale_item->product->passcode_pin ?? ''}}</td>
                    </tr>
                </table>
            </div>

            <div class="col-12" style="margin-top:20px;">
                <table class="table table-bordered">
                    <tr style="background-color: #dddddd;">
                        <th colspan="2" style="text-align:center;">Number Access Details</th>
                    </tr>
                    <tr>
                        <th>Number Company</th>
                        <td>{{$sale_item->product->number_company ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Username Or Email</th>
                        <td>{{$sale_item->product->number_email_username ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Password For Number</th>
                        <td>{{$sale_item->product->number_password ?? ''}}</td>
                    </tr>
                </table>
            </div>

            <div class="col-12" style="margin-top:20px;">
                <table class="table table-bordered">
                    <tr style="background-color: #dddddd;">
                        <th colspan="2" style="text-align:center;">Proxy Access Details</th>
                    </tr>
                    <tr>
                        <th>Proxy Website</th>
                        <td>{{$sale_item->product->proxy_website ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Proxy IP Or Host</th>
                        <td>{{$sale_item->product->proxy_ip_host ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Proxy Port</th>
                        <td>{{$sale_item->product->port ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Proxy Username</th>
                        <td>{{$sale_item->product->proxy_username ?? ''}}</td>
                    </tr>

                    <tr>
                        <th>Proxy Password</th>
                        <td>{{$sale_item->product->proxy_password ?? ''}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-12" style="margin-top:20px;">
                <table class="table table-bordered">
                    <tr style="background-color: #dddddd;">
                        <th colspan="2" style="text-align:center;">Addisional Details</th>
                    </tr>
                    <tr>
                        <td>Download Attachment</td>
                        <td>
                            <a href="{{ $sale_item->product->secure_url }}" download>Click here</a>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:center;">{{$sale_item->product->note ?? ''}}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>
