@extends('layouts.app')

@section('content')
	<div id="map" style="width:100%;height:550px"></div> 
@endsection

@push('scripts')
<script src="http://api.map.baidu.com/api?v=2.0&ak=LH9HvlSUhgkue1ExBKCQmsHyFqI2kdXW
" type="text/javascript"></script>
<script type="text/javascript"> 
	var points = [new BMap.Point(121.5046650, 39.7104033),
                  new BMap.Point(121.5047650, 39.7105033),
                  new BMap.Point(121.5048650, 39.7107033),
                  new BMap.Point(121.5049650, 39.7109033),
                  new BMap.Point(121.5050650, 39.7111033)
    ];

    //地图初始化
    var bm = new BMap.Map("map");
    bm.centerAndZoom(new BMap.Point(121.5048650, 39.7107033), 17);

    //坐标转换完之后的回调函数
    translateCallback = function (data){
      if(data.status === 0) {
        for (var i = 0; i < data.points.length; i++) {
            bm.addOverlay(new BMap.Marker(data.points[i]));
            bm.setCenter(data.points[i]);
        }
      }
    }
    setTimeout(function(){
        var convertor = new BMap.Convertor();
        convertor.translate(points, 1, 5, translateCallback)
    }, 1000);
    /*
	var map = new BMap.Map("map");          // 创建地图实例  

	var point = new BMap.Point(121.5046650, 39.7104033);  // 创建点坐标  
	map.centerAndZoom(point, 15);              // 初始化地图，设置中心点坐标和地图级别 
	var marker = new BMap.Marker(point);        // 创建标注    
	map.addOverlay(marker);      // 将标注添加到地图中  

	var point1 = new BMap.Point(121.5056650, 39.7114033);  // 创建点坐标  
	var marker1 = new BMap.Marker(point1);        // 创建标注    
	map.addOverlay(marker1);                     // 将标注添加到地图中 
	*/
</script>  

@endpush