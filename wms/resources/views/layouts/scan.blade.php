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
	            	<div class="form-group form-horizontal" nullable="except">
		            	<div class="row">
		            		<div class="col-md-10 col-md-offset-1">
			            		<input type="text" id="code_input" name="code_input" class="form-control" placeholder="请输入或扫描，按ENTER或点击确定">
		            		</div>
	            		</div>
	            		<div class="row">
			            	<div class="col-md-10 col-md-offset-1" style="text-align: center;">
			            		<button class="btn btn-default" id="code_input_submit">确定</button>
			            		<button class="btn btn-default" onclick="$('#code_input').val('')">清空</button>
		            		</div>
		            	</div>
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>
	@yield('scan-info')
</div>
<div>
	
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on("keydown",function(){
		if ($("input:focus").length == 0) {
			$("#code_input").focus();
		}
	});
	$(document).on("keyup",function(e){
		var keycode = e.which;
		if (keycode == 13) {
			if ($(".flavr-container").length == 0) {
				code_input();
			} else {
				$(".flavr-container").remove();
			}
		}
	});
	$("#code_input_submit").click(function(){
		code_input();
	});
	function code_input(){
		if ($("#code_input").val().length > 0) {
			var postdata = {};
			postdata["code_input"] = $("#code_input").val();
			@if(isset($post))
				@foreach($post as $key => $value)
					postdata["{{$key}}"] = "{{$value}}";
				@endforeach
			@endif
			ajax_post("{{$url}}",postdata,function(data){
				if (data.suc == 1) {
					ajax_post_success(data);
				} else {
					alert_flavr(data.msg);
				}
				$("#code_input").val("");
			});
		}
	}

</script>
@endpush