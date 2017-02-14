@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-body">
	            	<!--panel-body-1-->
	                @yield('panel-body-1')
	            </div>
	        </div>
	    </div>
	</div>
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-body">
	            	<!--panel-body-2-->
	                @yield('panel-body-2')
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>
</div>
@endsection