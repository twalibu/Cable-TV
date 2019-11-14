@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Dashboard @stop

<!-- Head Styles -->
@section('styles')
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Dashboard @stop

<!-- Page Description -->
@section('desc')Bima Alert Dashboard @stop

<!-- Active Link -->
@section('active')Dashboard @stop

<!-- Page Content -->
@section('content')
    <!-- Small boxes Section -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $cables }} Cables</h3>
                    <p>Cable Management</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-analytics-outline"></i>
                </div>
                <a href="{{ url('/cables') }}" class="small-box-footer">Manage Cable <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-6 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $clients }} Clients</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
                <a href="{{ url('clients')  }}" class="small-box-footer">Manage Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->
    
    <!-- Second row -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <!-- quick email widget -->
            <form action="{{ url('send') }}" method="POST" accept-charset="UTF-8">
                <div class="box box-info">
                    <div class="box-header">
                        <i class="fa fa-envelope"></i>
                        <h3 class="box-title">Quick SMS</h3>
                        <!-- tools box -->
                        <div class="pull-right box-tools" id="sms-counter">
                            <i class="fa fa-spinner"></i> Remaining Characters <span class="label label-warning remaining">:</span>
                        </div><!-- /. tools -->
                    </div>
                    <div class="box-body">                    
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input name="tenant" value="{{ Sentinel::getUser()->tenant->id }}" type="hidden">
                        <div class="form-group">
                            <select name="receiver[]" class="form-control select2" multiple="multiple" data-placeholder="Select Client...">
                                @if($members->count() > 0)
                                    <option value="all">Select All</option>
                                @endif
                                @foreach($members as $client)
                                    <option value="{{ $client->phone_number }}">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="message" id="message" class="form-control" placeholder="Enter Messege Here" style="width: 100%; height: 110px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                        </div>                        
                    </div>
                    <div class="box-footer clearfix">
                        <button class="pull-right btn btn-default" type="submit">Send <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </form>           
        </div><!-- end of left col -->

        <div class="col-lg-6 col-xs-12">
            <!-- Calendar -->
                <div class="box box-solid bg-green-gradient">
                    <div class="box-header">
                        <i class="fa fa-calendar"></i>
                        <h3 class="box-title">Calendar</h3>                    
                    </div><!-- /.box-header -->
                    <div class="box-body no-padding">
                        <!--The calendar -->
                        <div id="calendar" style="width: 100%"></div>
                    </div><!-- /.box-body -->                
                </div><!-- /.box -->
        </div><!-- End of right col -->
    </div>
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    {{-- SMS Counter --}}
    <script src="{{ asset('bower_components/sms-counter/sms_counter.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- datepicker -->
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        // SMS Counter
        $('#message').countSms('#sms-counter');
        //The Calender
        $("#calendar").datepicker();
    });
</script>
@stop