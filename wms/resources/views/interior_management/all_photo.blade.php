@extends('layouts.app')

@push('style')
<style type="text/css">
	.single_photo {
		padding: 2px 2px;
		margin: 2px 0px;
	}
	.content > div > a {
		height:0;  
    	padding-bottom: 100%; 
    	overflow: hidden; 
		margin: 0px 0px;
	}
</style>
@endpush


@section('content')
<div class="content">
	@foreach($photos as $photo)
	<div class="col-xs-4 col-sm-3 col-md-2 col-lg-1 single_photo">
		<a href="###" onclick="new_flavr('/console/detail?type=img&content=http://cme-csd.oss-cn-shenzhen.aliyuncs.com/{{$photo->getKey()}}')" class="thumbnail">
			<img src='http://cme-csd.oss-cn-shenzhen.aliyuncs.com/{{$photo->getKey()}}'>
		</a>
	</div>
	@endforeach
</div>
@endsection