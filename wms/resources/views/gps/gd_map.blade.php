@extends('layouts.app')

@define $naviTransform = new naviTransform()

@push('style')
    <style type="text/css">
        body,html,#container{
            height: 100%;
            margin: 0px;
        }
        #func {
            position: absolute;
            left: 5px;
            top: 5px;
            width: 20px;
            z-index: 99999;
            font-size: 20px;
        }
        #menu {
            position: absolute;
            left: 6px;
            top: 36px;
            width: 100px;
            z-index: 99999;
            display: none;
        }
        #control {
            position: absolute;
            left: 27px;
            top: 5px;
            z-index: 99999;
        }
        #lines {
            display: block;
            padding-top: 4px;
        }
        #datetime > span {
            display: inline-block;
            width: 133px;
        }
    </style>
@endpush

@section('content')
    <div id="func">
        <a href="###" onclick="triggle_func()"><span class="glyphicon glyphicon-th"></span></a>
    </div>
    <div id="menu">
        <button class="btn btn-info btn-small" onclick="show('lines')">全部轨迹线</button>
        <button class="btn btn-info btn-small" onclick="show('datetime')">时间筛选</button>
    </div>
    <div id="control">
        <div id="lines"></div>
        <div id="datetime" style="display: none">
            <span><input type="text" name="start" minView="0" class="form_date form-control input-sm" readonly="true"></span>
            ~
            <span><input type="text" name="end" minView="0" class="form_date form-control input-sm" readonly="true"></span>
        </div>
    </div>
    
    <div id="container" tabindex="0"></div>
@endsection

@push('scripts')
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.4.0&key=b4bda2640ad19c12d50ea9e0f8c26eb8"></script>
<script src="//webapi.amap.com/ui/1.0/main.js"></script>
<script type="text/javascript"> 
    var map = new AMap.Map('container',{
        resizeEnable: true,
        zoom: 10,
        @if(sizeof($position) == 0)
          center: [116.480983, 40.0958]
        @else
          center: [{{$position[0]["gps_lon"]}}, {{$position[0]["gps_lat"]}}]
        @endif
    });
    var position_group = new Array;
    var position_all = new Array;
    var line_index = 0;

    @define $time = false
    @foreach($position as $p)

        @define $current_time = \Carbon\Carbon::parse($p["created_at"])
        @if($time !== false && $time->diffInMinutes($current_time) > 30)
            position_group.push({
                name: position_all[0].date,
                index: line_index,
                path: position_all,
                date: "{{$time->toDateString()}}"
            });
            position_all = new Array;
            line_index++;
        @endif
        @define $time = $current_time

        //var position = [{{$p["gps_lon"]}}, {{$p["gps_lat"]}}];


        @if($p['gps_jz']==1)
            @define $pos_transform = array($p["gps_lat"],$p["gps_lon"]);
            var marker = new AMap.Marker({
                position : [{{$pos_transform[1]}}, {{$pos_transform[0]}}],
                offset : new AMap.Pixel(-7,-20),
                content : "<span class=\"gps"+line_index+"\">[基]</span>",
                title : "[基站信号]",
                map : map
            });
        @else
            @define $pos_transform = $naviTransform->transform($p["gps_lat"],$p["gps_lon"]);
        @endif

       

        var position = {
            name : "{{$p['created_at'].'; 电量'.$p['gps_Batt']}}",
            lnglat : [{{$pos_transform[1]}}, {{$pos_transform[0]}}],
            date : "{{$time->month.'-'.$time->day}}",
            time : "{{$p['created_at']}}"
        };

        position_all.push(position);

        /*
        var marker = new AMap.Marker({
            position : position,
            offset : new AMap.Pixel(-9,-30),
            title : "{{$p['created_at'].($p['gps_jz']==1?'[基站]':'')}}",
            map : map
        });
        var marker = new AMap.Marker({
            position : [{{$p["gps_lon"]}}, {{$p["gps_lat"]}}],
            offset : new AMap.Pixel(-7,-30),
            content : "{{$p['gps_Batt'].($p['gps_jz']==1?'[基]':'')}}",
            title : "{{$p['created_at'].($p['gps_jz']==1?'[基站]':'')}}",
            map : map
        });
        */
    @endforeach

    position_group.push({
        name: position_all[0].date,
        index: line_index,
        path: position_all,
        date: "{{$time->toDateString()}}"
    });

    AMapUI.load(['ui/misc/PathSimplifier'], function(PathSimplifier) {

        if (!PathSimplifier.supportCanvas) {
            alert('当前环境不支持 Canvas！');
            return;
        }

        //启动页面
        initPage(PathSimplifier);
    });

    function initPage(PathSimplifier) {
        var colors = [
            "#3366cc", "#dc3912", "#ff9900", "#109618", "#990099", "#0099c6", "#dd4477", "#66aa00",
            "#b82e2e", "#316395", "#994499", "#22aa99", "#aaaa11", "#6633cc", "#e67300", "#8b0707",
            "#651067", "#329262", "#5574a6", "#3b3eac"
        ];
        window.colors = colors;
        //创建组件实例
        var pathSimplifierIns = new PathSimplifier({
            zIndex: 100,
            map: map, //所属的地图实例
            getPath: function(pathData, pathIndex) {
                //返回轨迹数据中的节点坐标信息，[AMap.LngLat, AMap.LngLat...] 或者 [[lng|number,lat|number],...]
                //return pathData.path;
                var path = pathData.path;
                var lnglatList = [];
                var len = path.length;
                for (var i = 0; i < len; i++) {
                    lnglatList.push(path[i].lnglat);
                }

                return lnglatList;
            },
            getHoverTitle: function(pathData, pathIndex, pointIndex) {
                //返回鼠标悬停时显示的信息
                //if (pointIndex >= 0) {
                    //鼠标悬停在某个轨迹节点上
                    //return pathData.name + '，点:' + pointIndex + '/' + pathData.path.length;
                //}
                //鼠标悬停在节点之间的连线上
                //return pathData.name + '，点数量' + pathData.path.length;

                if (pointIndex >= 0) {
                    //point 
                    return pathData.name + '，' + pathData.path[pointIndex].name;
                }

                return pathData.name + '，点数量' + pathData.path.length;
            },
            renderOptions: {
                renderAllPointsIfNumberBelow: 500, //绘制路线节点，如不需要可设置为-1
                //轨迹线的样式
                pathLineStyle: {
                    dirArrowStyle: true
                },
                getPathStyle: function(pathItem, zoom) {
                    var color = colors[pathItem.pathData.index],
                        lineWidth = Math.round(4 * Math.pow(1.1, zoom - 3));

                    return {
                        pathLineStyle: {
                            strokeStyle: color,
                            lineWidth: lineWidth
                        },
                        pathLineSelectedStyle: {
                            lineWidth: lineWidth + 2
                        },
                        pathNavigatorStyle: {
                            fillStyle: color
                        }
                    };
                }
            }
        });


        //这里构建两条简单的轨迹，仅作示例
        pathSimplifierIns.setData(position_group);


        window.pathSimplifierIns = pathSimplifierIns;
        window.position_group = position_group;

        for (var i = 0; i < position_group.length; i++) {
            $("#lines").append("<button class=\"btn btn-default btn-small\" style=\"color:"+window.colors[position_group[i].index]+"\"><input class=\"gpsline\" value=\""+i+"\" type=\"checkbox\" onchange=\"get_current()\" checked>"+position_group[i].name+"</button>");
        }

        //创建一个巡航器
        /*
        var navg0 = pathSimplifierIns.createPathNavigator(0, //关联第1条轨迹
            {
                loop: true, //循环播放
                speed: 100000
            });

        navg0.start();
        */
    }

    function get_current(){
        var data = new Array;
        $(".gpsline:checked").each(function(){
            data.push(window.position_group[$(this).val()]);
            $(".gps"+$(this).val()).css("display","");
        });
        $(".gpsline:not(:checked)").each(function(){
            $(".gps"+$(this).val()).css("display","none");
        });
        window.pathSimplifierIns.setData(data);
    }

    function triggle_func(){
        if ($("#menu").css("display") == "none") {
            $("#menu").css("display","block");
        } else {
            $("#menu").css("display","none");
        }
    }

    function show(id){
        $("#control > div,#menu").css("display","none");
        $("#control #"+id).css("display","");
    }

    $("[name='start'],[name='end']").on("change",function(){
        if ($("[name='start']").val().length > 0) {
            var start_time = new Date($("[name='start']").val());
        } else {
            var start_time = new Date("1970-01-01");
        }
        if ($("[name='end']").val().length > 0) {
            var end_time = new Date($("[name='end']").val());
        } else {
            var end_time = new Date("2017-12-31");
        }
        var filt_data = window.position_group;
        var datetime;
        for (var i = 0; i < filt_data.length; i++) {
            for (var j = 0; j < filt_data[i].path.length; j++) {
                datetime = new Date(filt_data[i].path[j].time);
                if (datetime.getTime() < start_time.getTime() || datetime.getTime() > end_time.getTime()) {
                    delete filt_data[i].path[j];
                }
            }
            if (filt_data[i].path.length == 0) {
                delete filt_data[i];
            }
            
        }
        console.log(filt_data);
        window.pathSimplifierIns.setData(filt_data);
    });
</script>  

@endpush