@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Cables @stop

<!-- Head Styles -->
@section('styles')
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Wizard CSS Files -->
    <link href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Edit Cable @stop

<!-- Page Description -->
@section('desc')Edit Cable Details @stop

<!-- Active Link -->
@section('active')Cables @stop

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
                <form action="{{ route('cables.update', array($cable->id)) }}" method="POST" accept-charset="UTF-8">
                    <input type="hidden" name="_method" value="PUT">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            Edit Cable Details
                        </h3>
                        <h5>Please fill the information accurately.</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#details" data-toggle="tab">Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <div class="tab-pane" id="details">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Cable Details</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">perm_identity</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Client Name</label>
                                                <select class="form-control select2" name="client">      
                                                    <option disabled="" selected=""></option>
                                                    @foreach($clients as $client)
                                                        <option value="{{ $client->id }}" {{ $cable->client->id == $client->id ? 'selected' : '' }}> {{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">timeline</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Reference ID</label>
                                            <input name="reference" value="{{ $cable->reference_id }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">date_range</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Starting Date</label>
                                            <input name="starting_date" value="{{ $cable->starting_date }}" type="date" class="form-control">
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
                                                        <option value="{{ $type->id }}" {{ $cable->type->id == $type->id ? 'selected' : '' }}> {{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>

                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish' />
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
    <!--   Material Wizard JS Files   -->
    <script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/jquery.bootstrap.js') }}" type="text/javascript"></script>
    <!--  Plugin for the Wizard -->
    <script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/material-bootstrap-wizard.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        })
    </script>
@stop