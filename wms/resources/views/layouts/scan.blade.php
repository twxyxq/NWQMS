@extends('layouts.page')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> {!!isset($current_nav)?$current_nav:""!!}
    			</div>
	            <div class="panel-body">
	            	@if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false)
	            		<button class="btn btn-success btn-small" id="mm_scan">点击扫描</button>
	            	@else
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
		            @endif
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

@if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false)
	<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
@endif
<script type="text/javascript">
	@if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false)
		@define $signPackage = $app->GetSignPackage();
		wx.config({
		    beta: true,// 必须这么写，否则在微信插件有些jsapi会有问题
		    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		    appId: '{{$app->getAppId()}}', // 必填，企业微信的cropID
		    timestamp: {{time()}}, // 必填，生成签名的时间戳
		    nonceStr: "{{$signPackage['nonceStr']}}", // 必填，生成签名的随机串
		    signature: "{{$signPackage['signature']}}",// 必填，签名，见[附录1](#11974)
		    jsApiList: ["scanQRCode"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});
		wx.ready(function(){
			//alert("ready");
		});
		$("#mm_scan").on("click",function(){
			wx.scanQRCode({
			    desc: 'scanQRCode desc',
			    needResult: 1, // 默认为0，扫描结果由企业微信处理，1则直接返回扫描结果，
			    scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
			    success: function(res) {
			        code_input(res.resultStr);
			    },
			    error: function(res) {
			        if (res.errMsg.indexOf('function_not_exist') > 0) {
			            alert('版本过低请升级')
			        }
			    }
			});
		});
	@else
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
	@endif
	function code_input(code){
		var input_code = "";
		if (typeof(code) == "undefined" && $("#code_input").length > 0) {
			input_code = $("#code_input").val();
		} else {
			input_code = code;
		}
		if (input_code.length > 0) {
			var postdata = {};
			postdata["code_input"] = input_code;
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
				if ($("#code_input").length() > 0) {
					$("#code_input").val("");
				}
			});
		}
	}

</script>
@endpush