@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/nprogress.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/daterangepicker.css')}}">
@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.ProfitandLoss') }}</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<div id="profit_report">
    <div class="row">
        <div class="col-12">
            <div class="card hide-filter-on-print">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="text-uppercase font-size-sm font-weight-semibold">Filter</span>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="">

                    <form action="{{ route('report_profit.post') }}" method="POST" id="get_form">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" class="form-control" name="start_date" id="start_date">
                            <input type="hidden" class="form-control" name="end_date" id="end_date">
                            <label class="col-form-label col-lg-2 text-mandatory">Date Range</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="date_filter" id="reportrange">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary" style="float:right;">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($data))
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="text-uppercase font-size-sm font-weight-semibold">Profit-Loss</span>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a type="button" href="#" class="btn btn-success" @click="print_profit()"><i class="icon-printer2 mr-2"></i>Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="print_section">
                        <div class="row">
                            <div class="col-6">
                                <h3 class="text-center">Income</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Income Category</th>
                                        <th>Amount</th>
                                    </tr>
                                    @php
                                        $total_deposit = 0;
                                    @endphp
                                    @if(count($deposits) > 0)
                                        @foreach($deposits as $depost)
                                            @php
                                                $category =  \App\Models\DepositCategory::where('id', $depost->deposit_category_id)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $category->title }}</td>
                                                <td>{{ $depost->total_deposit_amount }}</td>
                                            </tr>
                                            @php
                                                $total_deposit += $depost->total_deposit_amount;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <th>Total Income</th>
                                            <th>{{ $total_deposit }}.00</th>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-6">
                                <h3 class="text-center">Expenses</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Expense Category</th>
                                        <th>Amount</th>
                                    </tr>
                                    @if(count($expenses) > 0)
                                        @php
                                            $total_expenses = 0;
                                        @endphp
                                        @foreach($expenses as $expense)
                                            @php
                                                $category =  \App\Models\ExpenseCategory::where('id', $expense->expense_category_id)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $category->title }}</td>
                                                <td>{{ $expense->total_expense_amount }}</td>
                                            </tr>
                                            @php
                                             $total_expenses +=$expense->total_expense_amount;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <th>Total Expenses</th>
                                            <th>{{ $total_expenses }}.00</th>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-6 offset-3">
                                <table class="table table-custom-tw table-sm table-bordered table-bordered-report">
                                    <tr>
                                        <th>Final Balance</th>
                                        <td>
                                            @php
                                                $total_deposit = $total_deposit ?? '0.00';
                                                $total_expenses = $total_expenses ?? '0.00';
                                                $balance = $total_deposit - $total_expenses
                                            @endphp
                                            {{ $balance }}.00
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/nprogress.js')}}"></script>
<script src="{{asset('assets/js/daterangepicker.min.js')}}"></script>


<script type="text/javascript">
    $(function() {
        "use strict";
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                $("#start_date").val(start.format('MMMM D, YYYY'));
                $("#end_date").val(end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    '{{ __('translate.Since_launch') }}' : [moment().subtract(10, 'year'), moment().add(10, 'year')],
                    '{{ __('translate.Today') }}': [moment(), moment()],
                    '{{ __('translate.Yesterday') }}' : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{ __('translate.Last_7_Days') }}' : [moment().subtract(6, 'days'), moment()],
                    '{{ __('translate.Last_30_Days') }}': [moment().subtract(29, 'days') , moment()],
                    '{{ __('translate.This_Month') }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ __('translate.Last_Month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
    });
</script>


<script>
    var app = new Vue({
        el: '#profit_report',
        data: {
            SubmitProcessing:false,
            errors:[],

        },

        methods: {


            //------------------------------ Print -------------------------\\
            print_profit() {
            var divContents = document.getElementById("print_section").innerHTML;
            var a = window.open("", "", "height=500, width=500");
            a.document.write(
                '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"><html>'
            );
            a.document.write("<body >");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();

            setTimeout(() => {
                a.print();
            }, 1000);
            },

            //------------------------------Formetted Numbers -------------------------\\
            formatNumber(number, dec) {
                const value = (typeof number === "string"
                    ? number
                    : number.toString()
                ).split(".");
                if (dec <= 0) return value[0];
                let formated = value[1] || "";
                if (formated.length > dec)
                    return `${value[0]}.${formated.substr(0, dec)}`;
                while (formated.length < dec) formated += "0";
                return `${value[0]}.${formated}`;
            },


        },
        //-----------------------------Autoload function-------------------
        created() {
        }

    })

</script>



@endsection
