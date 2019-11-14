@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Cables @stop

<!-- Head Styles -->
@section('styles')
	<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Cable @stop

<!-- Page Description -->
@section('desc')Cable Details @stop

<!-- Active Link -->
@section('active')Cables @stop

<!-- Page Content -->
@section('content')
<div class="row">
		<div class="col-md-3">

	  	<!-- Profile Image -->
	  	<div class="box box-primary">
	    	<div class="box-body box-profile">
	      		<img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($cable->cover_number) }}" alt="User profile picture">
	      		<h3 class="profile-username text-center">Cable Reference Number:{{ $cable->reference_id }}</h3>
	      		<p class="text-muted text-center"><a href="{{ route('clients.show', array($cable->client->id)) }}">{{ $cable->client->first_name }} {{ $cable->client->middle_name }} {{ $cable->client->last_name }}</a></p>

	      		<ul class="list-group list-group-unbordered">
	        		<li class="list-group-item">
	          			<b>Cable Subscription Type</b>
	          			<p class="text-muted">{{ $cable->type->name }}</p>
	        		</li>
        			<li class="list-group-item">
          				<b>Subscription Date</b> 
          				<p class="text-muted">{{ Carbon::parse($cable->starting_date)->toFormattedDateString() }}</p>
        			</li>
	        		<li class="list-group-item">
	          			<b>Duration</b> 
	          			<p class="text-muted">{{ $cable->type->duration }} Months</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Amount</b> 
	          			<p class="text-muted">Tsh {{ number_format($cable->type->amount, 2) }}</p>
	        		</li>
	      		</ul>

	      		<a href="{{ route('cables.edit', array($cable->id)) }}" class="btn btn-primary btn-block"><b>Edit Cable Details</b></a>
	      		<hr>
		      	<form id="deleteform" action="{{ route('cables.destroy', array($cable->id)) }}" method="POST">
		            <input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <button id="delete" class="btn btn-danger btn-block">Remove Cable</button>
		        </form>
	    	</div><!-- /.box-body -->
	  	</div><!-- /.box -->

		<!-- About Client Box -->
		<div class="box box-primary">
		    <div class="box-header with-border">
		      	<h3 class="box-title">Client Details</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body">
		      	<strong><i class="fa fa-user margin-r-5"></i>Client Name</strong>
		      	<p class="text-muted">{{ $cable->client->first_name }} {{ $cable->client->middle_name }} {{ $cable->client->last_name }}</p>
		      	<hr>
		      	<strong><i class="fa fa-phone margin-r-5"></i>Phone Number</strong>
		      	<p class="text-muted">+{{ $cable->client->phone_number }}</p>
		      	<hr>
		      	@if($cable->client->phone_number_2)
		      		<strong><i class="fa fa-phone margin-r-5"></i>Additional Phone Number</strong>
			      	<p class="text-muted">+{{ $cable->client->phone_number_2 }}</p>
			      	<hr>
			     @endif
		      	<strong><i class="fa fa-tags margin-r-5"></i>Region</strong>
		      	<p class="text-muted">{{ $cable->client->region->name }}</p>
		    </div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- /.col -->
	
	<div class="col-md-9">
	  	<div class="nav-tabs-custom">
	    	<ul class="nav nav-tabs">
	      		<li class="active"><a href="#policies" data-toggle="tab">Subscription Alerts</a></li>
	    	</ul>
	    	<div class="tab-content">
	    		<!-- Policies Tab -->
	    		<div class="tab-pane active" id="policies">
		    	 	<table id="xa" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
			                  	<th>Expiration Date</th>
			                    <th>Alert One</th>
			                    <th>Alert Two</th>
			                    <th>Expires In</th>
		                  	</tr>
	                	</thead>
	                	<tbody>
		                  	<tr>
			                    <td>{{ Carbon::parse($cable->alert->expiration_date)->toFormattedDateString() }}</td>
				                <td>{{ Carbon::parse($cable->alert->alert_one)->toFormattedDateString() }}</td>
				                <td>{{ Carbon::parse($cable->alert->alert_two)->toFormattedDateString() }}</td>
				                <td>{{ Carbon::parse($cable->alert->expiration)->diffForHumans() }}</td>
		                  	</tr>
		                </tbody>
		                <tfoot>
		                  	<tr>
			                    <th>Expiration Date</th>
			                    <th>Alert One</th>
			                    <th>Alert Two</th>
			                    <th>Expires In</th>
		                  	</tr>
		                </tfoot>
	            	</table>
	        	</div>
	    	</div><!-- /.tab-content -->
	  	</div><!-- /.nav-tabs-custom -->
	</div><!-- /.col -->
</div><!-- /.row -->
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

			$('button#delete').on('click', function(e){
				e.preventDefault();
				swal({
				  title: "Are you sure Remove Cable",
				  text: "You will not be able to recover the Cable!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, Remove Cable!',
				  cancelButtonText: 'No, Cancel Please!',
				  confirmButtonClass: 'btn btn-success',
				  cancelButtonClass: 'btn btn-danger',
				  buttonsStyling: false
				  }).then(function () {
					  $("#deleteform").submit();
					  return true;
					}, function (dismiss) {
					  // dismiss can be 'cancel', 'overlay',
					  // 'close', and 'timer'
					  if (dismiss === 'cancel') {
					    swal("Cancelled", "Cable Not Removed :)", "error");
					    e.preventDefault();
					  }
					})
			});

	    });
	</script>
@stop