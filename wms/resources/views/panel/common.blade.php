@extends('layouts.only_panel')


@section('panel-body')
	@foreach($panel_nav as $nav)
		<li class='panel_nav_item col-xs-6 col-sm-4 col-md-3 col-lg-2'>
			<a href='/{{$nav[0]}}'>
				<span class='{{$nav[2]==""?"glyphicon glyphicon-th":$nav[2]}}' style='display:block;font-size:30px;'></span>
				<span id='{{$nav[0]}}'>{{$nav[1]}}</span>
			</a>
		</li>
	@endforeach
@endsection

