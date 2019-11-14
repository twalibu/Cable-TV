@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Cable Alert | New Account!',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
        	Hello, Welcome to  Cable Alert!
        </p>

        <p>
        	<h2>Account: {{ $email }}</h2>
        </p>

        <p>
        	To activate your account, <a href="{{ route('auth.activation.attempt', urlencode($code)) }}">Click Here.</a>
        </p>

        <p>
            Or point your browser to this address: <br /> {!! route('auth.activation.attempt', urlencode($code)) !!}
        </p>

        <p>
            Thank you! <br>
            Cable Alert Team!
        </p>

    @include('beautymail::templates.sunny.contentEnd')

@stop
