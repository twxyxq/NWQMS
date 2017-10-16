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
        #menu > button {
            margin-top: 1px;
        }
        #control {
            position: absolute;
            left: 27px;
            top: 5px;
            z-index: 99998;
        }
        #lines {
            display: block;
            padding-top: 4px;
        }
        #datetime > span {
            display: inline-block;
            width: 133px;
        }
        .radar {
            background: -webkit-radial-gradient(center, rgba(32, 255, 77, 0.3) 0%, rgba(32, 255, 77, 0) 75%), -webkit-repeating-radial-gradient(rgba(32, 255, 77, 0) 5.8%, rgba(32, 255, 77, 0) 18%, #20ff4d 18.6%, rgba(32, 255, 77, 0) 18.9%), -webkit-linear-gradient(90deg, rgba(32, 255, 77, 0) 49.5%, #20ff4d 50%, #20ff4d 50%, rgba(32, 255, 77, 0) 50.2%), -webkit-linear-gradient(0deg, rgba(32, 255, 77, 0) 49.5%, #20ff4d 50%, #20ff4d 50%, rgba(32, 255, 77, 0) 50.2%);
            background: radial-gradient(center, rgba(32, 255, 77, 0.3) 0%, rgba(32, 255, 77, 0) 75%), repeating-radial-gradient(rgba(32, 255, 77, 0) 5.8%, rgba(32, 255, 77, 0) 18%, #20ff4d 18.6%, rgba(32, 255, 77, 0) 18.9%), linear-gradient(90deg, rgba(32, 255, 77, 0) 49.5%, #20ff4d 50%, #20ff4d 50%, rgba(32, 255, 77, 0) 50.2%), linear-gradient(0deg, rgba(32, 255, 77, 0) 49.5%, #20ff4d 50%, #20ff4d 50%, rgba(32, 255, 77, 0) 50.2%);
            width: 75vw;
            height: 75vw;
            max-height: 75vh;
            max-width: 75vh;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 0.2rem solid #20ff4d;
            overflow: hidden;
            z-index: 99999;
            opacity: 0.6;
        }
        .radar:before {
            content: ' ';
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            animation: blips 0.8s infinite;
            animation-timing-function: linear;
            animation-delay: 0.2s;
        }
        .radar:after {
            content: ' ';
            display: block;
            background-image: linear-gradient(44deg, rgba(0, 255, 51, 0) 50%, #00ff33 100%);
            width: 50%;
            height: 50%;
            position: absolute;
            top: 0;
            left: 0;
            animation: radar-beam 0.8s infinite;
            animation-timing-function: linear;
            transform-origin: bottom right;
            border-radius: 100% 0 0 0;
        }

        @keyframes radar-beam {
            0% {
            transform: rotate(0deg);
            }
            100% {
            transform: rotate(360deg);
            }
        }
        @keyframes blips {
            14% {
            background: radial-gradient(2vmin circle at 75% 70%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%);
            }
            14.0002% {
            background: radial-gradient(2vmin circle at 75% 70%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 63% 72%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%);
            }
            25% {
            background: radial-gradient(2vmin circle at 75% 70%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 63% 72%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 56% 86%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%);
            }
            26% {
            background: radial-gradient(2vmin circle at 75% 70%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 63% 72%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 56% 86%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%);
            opacity: 1;
            }
            100% {
            background: radial-gradient(2vmin circle at 75% 70%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 63% 72%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%), radial-gradient(2vmin circle at 56% 86%, #ffffff 10%, #20ff4d 30%, rgba(255, 255, 255, 0) 100%);
            opacity: 0;
            }
        }

    </style>
@endpush

@section('content')
    <div id="func">
        <a href="###" onclick="triggle_func()"><span class="glyphicon glyphicon-th"></span></a>
    </div>
    <div id="menu">
        <button class="btn btn-danger btn-small" onclick="location='/radiation_gps/gps'">退出程序</button>
    </div>
    <div class="radar" play="1" count="0"></div>
    <div id="container" tabindex="0"></div>
@endsection

@push('scripts')
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.4.0&key=b4bda2640ad19c12d50ea9e0f8c26eb8"></script>
<script src="//webapi.amap.com/ui/1.0/main.js"></script>
<script type="text/javascript"> 
    var map = new AMap.Map('container',{
        resizeEnable: true,
        zoom: 11,
        center: [121.5, 39.8]
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
            "#C9FFC0","#3366cc", "#dc3912", "#ff9900", "#109618", "#990099", "#0099c6", "#dd4477", "#66aa00",
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
                        lineWidth = Math.round(3.2 * Math.pow(1.1, zoom - 3));

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

        window.pathSimplifierIns = pathSimplifierIns;

        
    }

    function triggle_func(){
        if ($("#menu").css("display") == "none") {
            $("#menu").css("display","block");
        } else {
            $("#menu").css("display","none");
        }
    }

    setInterval(function(){
        if ($(".radar").attr("play") == 1) {
            $(".radar").css("display","");
            ajax_post("/radiation_gps/get_current",{"sn":{{$sn}}},function(data){
                if (data.suc == 1) {
                    if (data.gps.length > 1) {
                        var position_group = new Array();
                        var position_all = new Array;
                        var position = {};
                        for (var i = data.gps.length-1; i >= 0; i--) {


                            position = {
                                name : "电量"+data.gps[i][2]+" 时间："+data.gps[i][3],
                                lnglat : [data.gps[i][0], data.gps[i][1]],
                                time : data.gps[i][3]
                            };
                            position_all.push(position);
                            /*
                            position = {
                                name : "电量："+data.gps[i-1][2]+" 时间："+data.gps[i-1][3],
                                lnglat : [data.gps[i-1][0], data.gps[i-1][1]],
                                time : data.gps[i-1][3]
                            };
                            position_all.push(position);
                            */
                            if (i == 0) {
                                if (window.marker == undefined) {
                                    var marker = new AMap.Marker({
                                        position : [data.gps[i][0], data.gps[i][1]],
                                        //offset : new AMap.Pixel(-7,-20),
                                        //content : "<div class=\"gps_location\" style=\"background-color:red;width:15px;height:15px;\"></div>",
                                        //iconTheme: "default",
                                        animation : "AMAP_ANIMATION_BOUNCE",
                                        map : map
                                    });
                                    window.marker = marker;
                                } else {
                                    window.marker.setPosition([data.gps[i][0], data.gps[i][1]]);
                                }
                                
                            }
                            
                        }

                        position_group.push({
                            name: "实时路径",
                            index: 0,
                            path: position_all
                        });
                        window.pathSimplifierIns.setData(position_group);

                        if (data.play == 0) {
                            $(".radar").attr("count",Number($(".radar").attr("count"))+1);
                        }

                        if ($(".radar").attr("count") < 5) {
                            var navg1 = window.pathSimplifierIns.createPathNavigator(0, {
                                loop: true, //循环播放
                                speed: 3000 //巡航速度，单位千米/小时
                            });

                            navg1.start();
                        } else {
                            $(".radar").attr("play",0);
                            alert_flavr("超过三个小时无新数据，且连续5次未获得数据。<br>获取数据停止，点击确定继续获取。",function(){
                                $(".radar").attr("count",0);
                                $(".radar").attr("play",1);
                            });
                        }
                    }
                    setTimeout(function(){
                        $(".radar").css("display","none");
                    },500);

                } else {
                    alert_flavr(data.msg);
                }
            });
        }
    },5000)
</script>  

@endpush