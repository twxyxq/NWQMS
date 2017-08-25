@extends('layouts.page')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> {!!isset($current_nav)?$current_nav:""!!}
    			</div>
	            <div class="panel-body">
	            	<!--panel-body-->
	            	{!!isset($panel_body)?$panel_body:""!!}
	                @yield('panel-body')
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>
	<!--datatables-->
    @include('conn/datatables')
</div>
@endsection
