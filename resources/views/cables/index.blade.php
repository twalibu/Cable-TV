@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Cables @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Cables @stop

<!-- Page Description -->
@section('desc')Cables Dashboard @stop

<!-- Active Link -->
@section('active')Cables @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Cables Subscriptions</h3>              
                <a href="{{ url('cables/create') }} " class="btn btn-primary btn-sm pull-right">Add New Cable Subscription</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Subscription Type</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($cables as $cable)
                            <tr>
                                <td><a href="{{ route('clients.show', array($cable->client->id)) }}">{{ $cable->client->first_name }} {{ $cable->client->last_name }}</a></td>
                                <td><strong>Type:</strong> {{ $cable->type->name }}<br><strong>Amount:</strong> Tsh {{ number_format($cable->type->amount,2) }}/-</td>
                                <td><strong>Subscription Date: </strong>{{ Carbon::parse($cable->starting_date)->toFormattedDateString() }}<br>
                                <strong>Renewal:</strong> {{ Carbon::parse($cable->alert->expiration)->diffForHumans() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('cables.show', array($cable->id)) }}">View Details</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ route('cables.edit', array($cable->id)) }}">Edit Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Client</th>
                            <th>Subscription Type</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- DataTables -->
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(function () {
            $('#xa').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            });
        });
    </script>
@stop