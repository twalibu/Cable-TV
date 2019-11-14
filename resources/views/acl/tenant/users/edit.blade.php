@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Users @stop

<!-- Head Styles -->
@section('styles')
<!-- Creative Tim -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
<link rel="stylesheet" href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}">
@stop

<!-- Page Header -->
@section('header')Edit User @stop

<!-- Page Description -->
@section('desc')Edit User Details @stop

<!-- Active Link -->
@section('active')Users @stop

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
                <form action="{{ route('users.update', $user->id) }}" method="POST" accept-charset="UTF-8">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input name="_method" value="PUT" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            Edit User Details
                        </h3>
                        <h5>Please fill the information accurately.</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#admin" data-toggle="tab">Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Admin -->
                        <div class="tab-pane" id="admin">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Sytem User Details</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">First Name</label>
                                            <input name="first_name" value="{{ $user->first_name }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin_circle</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Last Name</label>
                                            <input name="last_name" value="{{ $user->last_name }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">email</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Email Address</label>
                                            <input name="email" value="{{ $user->email }}" type="email" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Password</label>
                                            <input name="password" type="password" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Confirm Password</label>
                                            <input name="password_confirmation" type="password" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <br><strong>Please Select User Role(s) Below</strong>
                                        @foreach($user->tenant->roles as $role)
                                            <div class="form-group label-floating checkbox">
                                                <label>
                                                    <input class="control-label" type="checkbox" name="roles[{{ $role->slug }}]" value="{{ $role->id }}" {{ $user->inRole($role) ? 'checked' : '' }}>
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div> 
                            </div>
                        </div>                  
                    </div>

                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Update' />
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
@stop