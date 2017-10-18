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
	            	<!--panel-body-->
	            	{!!isset($panel_body)?$panel_body:""!!}
	                @yield('panel-body')
	                <div id="up_cqcn_img" class="col-sm-12" style="text-align: center;">
	                	
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>
	<!--datatables-->
    @include('conn/datatables')
</div>
@endsection

@push('scripts')
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
	$(".ajax_submit").parent("div").before("<div class=\"col-sm-3\"><button class=\"btn btn-default btn-small\" onclick=\"upload_img()\">上传图片</botton></div>");
	@define $app = new \JSSDK("ww87531fd7a21b0b82","mjbYfBlZk4jQg2GBMLIcIL_m0Jhc2e3cCNwW_pJIER8",1000002);
	@define $signPackage = $app->GetSignPackage();
	wx.config({
	    beta: true,// 必须这么写，否则在微信插件有些jsapi会有问题
	    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	    appId: 'ww87531fd7a21b0b82', // 必填，企业微信的cropID
	    timestamp: {{time()}}, // 必填，生成签名的时间戳
	    nonceStr: "{{$signPackage['nonceStr']}}", // 必填，生成签名的随机串
	    signature: "{{$signPackage['signature']}}",// 必填，签名，见[附录1](#11974)
	    jsApiList: ["chooseImage","uploadImage","downloadImage"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	});
	wx.ready(function(){
		//alert("ready");
	});


	function upload_img(){
		wx.chooseImage({
		    count: 1, // 默认9
		    sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
		    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
		    success: function (res) {
		        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
		        //alert(localIds);
		        //$("#img_show").attr("src",localIds);
		        wx.uploadImage({
				    localId: localIds.toString(), // 需要上传的图片的本地ID，由chooseImage接口获得
				    isShowProgressTips: 1, // 默认为1，显示进度提示
				    success: function (res) {
				        var serverId = res.serverId; // 返回图片的服务器端ID
				        //alert(serverId);
				        up_img(serverId,set_img);
				    }
				});
		    }
		});
	}

	function up_img(serverId,fn){
		var img_name = {{time().rand(0,9)}};
		$.get("/wechat/download_wechat_img?path=cqcn&mediaid="+serverId+"&AgentID=1000002&file_name="+img_name,function(data){
			if (data == "success") {
				fn(img_name);
			} else {
				alert_flavr("上传失败");
			}
		});
		/*
		ajax_post("/console/up_remote.console.php", {serverId: ""+serverId+"" }, function(data){
			//alert(data);
			if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){alert(data);}
			eval('var rdata = '+data);
			//alert(rdata.suc); 
			if (Number(rdata.suc) == 1) {
			    fn(rdata.content);
			} else {					
			    alert("fail");			
			}
		});
		*/				
	}
	function set_img(img_name){
		$("[name='cqcn_img']").val(img_name+".jpg");
		$("#up_cqcn_img").html("<img src=\"/uploads/cqcn/"+img_name+".jpg\" style=\"width:100px\">");
		alert_flavr("上传成功");
	}
</script>
@endpush
