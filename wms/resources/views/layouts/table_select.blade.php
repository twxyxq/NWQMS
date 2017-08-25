@extends('layouts.app')

@section('content')
	@if(isset($page_info) && $page_info != null)
		<div class="container">
			<div class="row">
			    <div class="col-md-10 col-md-offset-1">
			        <div class="panel panel-default">
			            <div class="panel-body">
			            	{!!$page_info!!}
			            </div>
			        </div>
			    </div>
			</div>
		</div>
	@endif
	@include('conn/datatables')
@endsection