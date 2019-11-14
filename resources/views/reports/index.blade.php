@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Executive Summary @stop

<!-- Head Styles -->
@section('styles')
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@stop

<!-- Page Header -->
    @section('header')Executive Summary @stop

<!-- Page Description -->
@section('desc')Summary from Cable Alert @stop

<!-- Active Link -->
@section('active')Executive Summary @stop

<!-- Page Content -->
@section('content')
    <!-- First Row -->
    <div class="row">
        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $total_clients }} Clients</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
                <a href="{{ url('clients') }}" class="small-box-footer">Manage Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $total_cables }} Cables</h3>
                    <p>Total Cables</p>
                </div>
                <div class="icon">
                    <i class="ion ion-umbrella"></i>
                </div>
                <a href="{{ url('cables') }}" class="small-box-footer">Manage Cables <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $total_notifications }} Notifications</h3>
                    <p>Total Notifications Today</p>
                </div>
                <div class="icon">
                    <i class="ion ion-email-unread"></i>
                </div>
                <a href="{{ url('alerts')  }}" class="small-box-footer">See Alerts <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->

    <!-- Second Row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-lg-5 col-xs-12">
            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Summary for {{ $monthName }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <strong><i class="fa fa-square margin-r-5"></i>Total Active Subscriptions</strong>
                        <p class="text-muted">{{ number_format($total_active) }} {{ str_plural('Subscription', $total_active) }}</p>
                    <hr>
                    <strong><i class="fa fa-square-o margin-r-5"></i>Total Subscription Types</strong>
                        <p class="text-muted">{{ number_format($total_types) }} {{ str_plural('Subscription Type', $total_types) }}</p>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <!-- Right Col -->
        <div class="col-lg-7 col-xs-12">
            <!-- Info Boxes Style 2 -->
            <div class="info-box bg-navy">
                <span class="info-box-icon"><i class="ion ion-person-add"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">New Clients</span>
                    <span class="info-box-number">{{ $new_clients }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: @if($total_clients > 0)
                                {{ ($new_clients/$total_clients)*100 }}%
                            @else
                                0%
                            @endif
                            "></div>
                    </div>
                    <span class="progress-description">
                        @if($total_clients > 0)
                                {{ ($new_clients/$total_clients)*100 }}%
                            @else
                                0%
                            @endif
                             Increase in 30 Days
                    </span>
                </div>
            </div>
            
            <div class="info-box bg-purple">
                <span class="info-box-icon"><i class="ion ion-umbrella"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">New Cables Subscriptions</span>
                    <span class="info-box-number">{{ $new_cables }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: @if($total_cables > 0)
                                {{ ($new_cables/$total_cables)*100 }}%
                            @else
                                0%
                            @endif"></div>
                    </div>
                    <span class="progress-description">
                        @if($total_cables > 0)
                                {{ ($new_cables/$total_cables)*100 }}%
                            @else
                                0%
                            @endif Increase in 30 Days
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Third row -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>TZS {{ number_format($sms->balance,2) }}/-</h3>
                    <p>SMS Balance</p>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <a href="{{ url('communications') }}" class="small-box-footer">Send Messages To Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-6 col-xs-12">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ number_format($sms->balance/$sms->price) }} SMS Left</h3>
                    <p>SMS Balance (In Numbers)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <a href="{{ url('communications') }}" class="small-box-footer">Send Messages To Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- End of right col -->
    </div>

    <!-- Four Row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-lg-7 col-xs-12">
            <div id="chart-div"></div>
            {!! $lava->render('PieChart', 'CableTypes', 'chart-div') !!}
        </div>

        <div class="col-lg-5 col-xs-12">
            <!-- General Report -->
            <form action="{{ url('report/generate') }}" method="POST" accept-charset="UTF-8">
                <div class="box box-info">
                    <div class="box-header">
                        <i class="fa fa-clone"></i>
                        <h3 class="box-title">Print Report</h3>
                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <i class="fa fa-bolt"></i> Export Report
                        </div><!-- /. tools -->
                    </div>
                    <div class="box-body">                    
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input type="hidden" name="report_start" id="report_start" value="">
                        <input type="hidden" name="report_end" id="report_end" value="">
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" class="btn btn-default pull-right" name="daterange" id="daterange">
                                    <span>
                                        <i class="fa fa-calendar"></i> Please Select Start and End Dates
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>                   
                    </div>
                    <div class="box-footer clearfix">
                        <button class="pull-right btn btn-default" type="submit">Generate Report <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </form>
        </div><!-- end of right col -->
    </div><!-- /.row -->
    
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function () {
            $('#daterange').daterangepicker(
                {
                    ranges: {
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()]                    
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#report_start').val(start.format('YYYY-MM-DD'));
                    $('#report_end').val(end.format('YYYY-MM-DD'));
                }
            );
        });
    </script>
@stop