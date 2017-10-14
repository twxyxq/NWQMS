@extends('layouts.app')

@define $naviTransform = new naviTransform()

@push('style')
    <style type="text/css">
        #gd_map{
            height: 430px;
            border: 1px solid gray;
        }
    </style>
@endpush

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-body">
	            	@foreach($equipment as $eq)
	            		<li class='panel_nav_item col-sm-6 col-md-4 col-lg-3'>
							<a href='###'>
								<span class='glyphicon glyphicon-info-sign' style='display:block;font-size:30px;'></span>
								<span id='{{$eq->gps_SN}}' style='display:block;'>{{$eq->gps_SN}}</span>
								<span id='{{$eq->gps_SN}}_menu' style='display:block;'>
									<a href="/radiation_gps/all_path?sn={{$eq->gps_SN}}" class="btn btn-info btn-small">全部路径</a> 
									<a href="/radiation_gps/current_path?sn={{$eq->gps_SN}}" class="btn btn-success btn-small">实时路径</a>
								</span>
							</a>
						</li>
	            	@endforeach
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div id="gd_map" class="col-md-10 col-md-offset-1" tabindex="0"></div>
@endsection

@push('scripts')
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.4.0&key=b4bda2640ad19c12d50ea9e0f8c26eb8"></script>
<script src="//webapi.amap.com/ui/1.0/main.js"></script>
<script type="text/javascript">
	var map = new AMap.Map('gd_map',{
        resizeEnable: true,
        zoom: 12,
        @if(!isset($equipment))
          center: [116.480983, 40.0958]
        @else
          center: [{{$equipment[0]["gps_lon"]}}, {{$equipment[0]["gps_lat"]}}]
        @endif
    });
	@foreach($equipment as $eq)
		@define $pos_transform = $naviTransform->transform($eq["gps_lat"],$eq["gps_lon"]);
	    var marker = new AMap.Marker({
	        position : [{{$pos_transform[1]}}, {{$pos_transform[0]}}],
	        offset : new AMap.Pixel(-7,-20),
	        title : "{{$eq['gps_SN']}} {{$eq['created_at']}}",
	        map : map
	    });
	@endforeach
</script>
@endpush