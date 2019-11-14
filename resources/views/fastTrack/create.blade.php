@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Fast Track @stop

<!-- Head Styles -->
@section('styles')
<!-- Creative Tim -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
<link rel="stylesheet" href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/select2/select2.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Fast Track @stop

<!-- Page Description -->
@section('desc')Cable Alert Fast Track @stop

<!-- Active Link -->
@section('active')Fast Track @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-12"> 
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <p><strong>Whoops!</strong> There were some problems with your input.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!--      Wizard container        -->   
        <div class="wizard-container">
            <div class="card wizard-card" data-color="blue" id="wizard">
                <form action="{{ route('fastTrack.store') }}" method="POST" accept-charset="UTF-8">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            New Client & Cable Registration
                        </h3>
                        <h5>Please fill the information accurately.</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li class="active"><a href="#client" data-toggle="tab">Clients Details</a></li>
                            <li><a href="#cable" data-toggle="tab">Cable Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <div class="tab-pane active" id="client">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Client Details.</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">First Name</label>
                                            <input name="first_name" value="{{ old('first_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">perm_identity</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Middle Name (Optional)</label>
                                            <input name="middle_name" value="{{ old('middle_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin_circle</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Last Name</label>
                                            <input name="last_name" value="{{ old('last_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">contact_phone</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Phone Number (Required)</label>
                                            <input name="phone_number" value="{{ old('phone_number') }}" type="number" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">contact_phone</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Phone Number (Optional)</label>
                                            <input name="phone_number_2" value="{{ old('phone_number_2') }}" type="number" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">email</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Email (Optional)</label>
                                            <input name="email" value="{{ old('email') }}" type="email" class="form-control">
                                        </div>
                                    </div>                                    

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">all_out</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Regions</label>
                                            <select class="form-control" name="region">
                                                <option disabled="" selected=""></option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}" {{ old('region') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="cable">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Cable Details</h4>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">timeline</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Reference ID</label>
                                            <input name="reference" value="{{ old('reference') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">swap_vert</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Cable Type</label>
                                                <select class="form-control" name="type">      
                                                    <option disabled="" selected=""></option>
                                                    @foreach($types as $type)
                                                        <option value="{{ $type->id }}"> {{ $type->name }} | Tsh {{ number_format($type->amount,2) }}/- | {{ $type->duration }} {{ str_plural('Month', $type->duration) }} </option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">date_range</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Starting Date</label>
                                            <input name="starting_date" value="{{ old('starting_date') }}" type="date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>

                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Add Another' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish Fast Track' />
                        </div>
                        <div class="pull-left">
                            <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div> <!-- wizard container --> 
    </div>        
</div>
@stop

<!-- Page Scripts -->
@section('scripts')
<!-- Creative Tim -->
<script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/material-bootstrap-wizard.js') }}"></script>
<script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/jquery.bootstrap.js') }}" type="text/javascript"></script>
<!-- Select2 -->
<script src="{{ asset('bower_components/AdminLTE/plugins/select2/select2.full.min.js') }}"></script>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
@stop