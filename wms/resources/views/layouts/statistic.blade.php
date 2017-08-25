@extends('layouts.page')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> {!!$current_nav!!}
    			</div>
	            <div class="panel-body">
	            	<!--panel-body-->
	            	{!!isset($panel_body)?$panel_body:""!!}
	                <div id="statistic_para" class="ajax_input form-group form-horizontal">
	                	@yield('statistic_para')
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div style="text-align: center;">
	<!--datatables-->
    @include('conn/datatables')

    <div id="wms_graph" style="width:100%;height:400px;"></div>
</div>
@endsection

@push('pre_scripts')
<script type="text/javascript">
	$("#statistic_para select,#statistic_para .form_date").on("change",function(){
		statistic_para();
	})
	$("#statistic_para [bind]").attr("change_fn","statistic_para()");
</script>
@endpush

@push('scripts')
<script type="text/javascript" src="/js/echarts.min.js"></script>
<script type="text/javascript">
	function statistic_para(){
		$("#statistic_para select[data!=0],#statistic_para input[data!=0]").each(function(){
			$("#example").DataTable().settings()[0].ajax.data[$(this).attr("name")] = $(this).val();
		});
		$("#example").DataTable().draw();
	}

	var myChart = echarts.init(document.getElementById('wms_graph'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '{{$title}}'
            },
            tooltip: {},
            legend: {
            	width:'100%',
                data:[
                @for($i=0;$i<sizeof($sdata);$i++)
                	@if($i>0) , @endif
                	'{{$sdata[$i]["title"]}}'
                @endfor
                ]
            },
            xAxis: {
                data: []
            },
            yAxis: {}
        };

		// 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
        window.onresize = myChart.resize;
			// 异步加载数据
		function refresh_echarts(){
			var title = new Array();
			$("span[id^='period']").each(function(){
				var temp = $(this).html();
				if ($("span[id$='addition_"+$(this).attr("id")+"']").length > 0) {
					$("span[id$='addition_"+$(this).attr("id")+"']").each(function(){
						temp += $(this).html();
					});
				}
				title.push(temp);
			});
			@foreach($sdata as $s)
				var {{$s["name"]}} = new Array();
				$("span[id^='{{$s["name"]}}']").each(function(){
					{{$s["name"]}}.push($(this).html());
				});
			@endforeach
			
			myChart.setOption({
				title: {
					text: generate_title()
				},
				xAxis: {
					data: title
				},
				series: [
				@for($i=0;$i<sizeof($sdata);$i++)
					@if($i>0) , @endif
					{
						// 根据名字对应到相应的系列
						name: '{{$sdata[$i]["title"]}}',
						data: {{$sdata[$i]["name"]}},
						type: '{{$sdata[$i]["type"]}}'
						@if(isset($sdata[$i]["stack"]))
							,stack: '{{$sdata[$i]["stack"]}}'
						@endif
						@if(isset($sdata[$i]["format"]))
							,label: {
								normal: {
									show: true,
									position: '{{isset($sdata[$i]["format_position"])?$sdata[$i]["format_position"]:"bottom"}}',
									formatter: '{c}{{$sdata[$i]["format"]}}' 
								}
							}
						@endif
					}
				@endfor
				]
				@if(isset($yAxis))
					,yAxis: {
						show: true
	            		@if(isset($yAxis["format"]))
		            	,axisLabel: {
							formatter: '{value}{{$yAxis["format"]}}' 
						}
		            	@endif
	            		@if(isset($yAxis["max"]))
		            	,max: {!!$yAxis["max"]!!}
		            	@endif
	            		@if(isset($yAxis["min"]))
		            	,min: {!!$yAxis["min"]!!}
		            	@endif
		            }
				@endif
			});
		}

		$('#example').on( 'draw.dt', function () {
			refresh_echarts();
		} );
</script>
@endpush