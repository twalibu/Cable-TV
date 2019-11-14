<!-- Blank Boilplate -->
@extends('app')

<!-- Page Title -->
@section('title')Communication @stop

<!-- Head Styles -->
@section('styles')
<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
@stop

<!-- Page Header -->
@section('header')Communication Reports @stop

<!-- Page Description -->
@section('desc')SMS Reports @stop

<!-- Active Link -->
@section('active')Communication @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of SMS Reports</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Message ID</th>
                            <th>Sent Date</th>
                            <th>Recepient</th>
                            <th>Status</th>
                            <th>SMS Count</th>
                            <th>Text</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($results as $result)
                            <tr>
                                <td>{{ $result->getMessageId() }}</td>
                                <td>{{ $result->getSentAt()->format('Y-m-d H:i:s') }}</td>
                                <td>+{{ $result->getTo() }}</td>
                                <td>{{ $result->getStatus()->getName() }}</td>
                                <td>{{ $result->getSmsCount() }}</td>
                                <td>{{ $result->getText() }}</td>
                            </tr>                
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Message ID</th>
                            <th>Sent Date</th>
                            <th>Recepient</th>
                            <th>Status</th>
                            <th>SMS Count</th>
                            <th>Text</th>
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
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
    $(function () {
        $('#xa').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "aaSorting": [[0, 'asc']],
            "info": true,
            "autoWidth": true
        });
    });
</script>
@stop