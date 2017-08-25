@extends('layouts.page')

@push('style')
<style type="text/css">
	#e_structure{
		text-align: center;
	}

	#e_structure_title {
		font-size: 15px;
		height: 28px;
	}

	#e_structure_introduction {
		font-size: 11px;
		height: 28px;
	}

	[e_status=""] + [e_status=""] {
		display: none;
	}

	[e_status=""] > div {
		opacity: 0.4;
		color: lightgray;
	}

</style>
	
@endpush

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> {!!$current_nav!!}
    			</div>
	            <div class="panel-body">
	            	@define $method = array("RT","UT","PT","MT","SA","HB")
	            	@foreach($method as $method_item)
	            		<a href="?method={{$method_item}}" class="btn btn-default {{isset($_GET['method'])&&$_GET['method']==$method_item?'active':''}}">{{$method_item}}</a>
					@endforeach
	            </div>
	        </div>
	    </div>
	</div>
</div>

@if(isset($_GET["method"]))
<div id="e_structure" class="container">
	<div id="e_structure_title" class="row col-md-12">
		<strong>{{$_GET['method']}}{{$title}}结构</strong>【<a href="###" onclick="new_flavr('/exam/report_detail?method={{$_GET['method']}}')">报告预览</a>】
	</div>
	<div id="e_structure_introduction" class="row col-md-12">
		说明： {{$introduction}}
	</div>
	@for($i = 0; $i < $limit; $i++)
		@define $index = $model_name."_info_".$i
		<div class="row col-md-6" e_status="{{$e_status->$index}}">
			<div class="col-md-2">字段{{$i+1}}</div>
			<div class="col-md-6"><input type="text" name="e_model_{{$i}}" value="{{$e_model->$index}}" class="form-control input-sm"></div>
			<div class="col-md-4">
				<select name="e_status_{{$i}}" class="form-control input-sm" onchange="$(this).parent().parent().attr('e_status',$(this).val())">
					@define $model_text = "\\App\\".$model_name
					@define $info_data = $model_text::where($model_name."_method",$_GET["method"])->whereNotNull($index)->where($index,"<>","")->get()
					@if(sizeof($info_data) == 0)
						<option value="" {{$e_status->$index==null?'selected':''}}>作废</option>
					@endif
					@foreach($status as $status_item)
						<option value="{{$status_item}}" {{$e_status->$index==$status_item?'selected':''}}>{{$status_item}}</option>
					@endforeach
				</select>
			</div>
		</div>
	@endfor
	<div class="row col-md-12">
		<button class="btn btn-info" onclick="change_structure()">修改</button>
	</div>
</div>
@endif

@endsection

@if(isset($_GET["method"]))
	@push('scripts')
	<script type="text/javascript">
		
		function change_structure(){
			var postdata = {};
			postdata["model_name"] = "{{$model_name}}";
			postdata["limit"] = {{$limit}};
			postdata["status_id"] = {{$e_status->id}};
			postdata["model_id"] = {{$e_model->id}};
			@for($i = 0; $i < $limit; $i++)
				postdata["status_info_{{$i}}"] = $("[name='e_status_{{$i}}']").val();
				postdata["model_info_{{$i}}"] = $("[name='e_model_{{$i}}']").val();
			@endfor
			ajax_post("/exam/e_structure",postdata,function(data){
				if(data.suc == 1){
					alert_flavr(data.msg,function(){
						location.reload();
					});
				} else {
					alert_flavr(data.msg);
				}
			});
		}

	</script>

	@endpush
@endif