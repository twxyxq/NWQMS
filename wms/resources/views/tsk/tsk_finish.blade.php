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
	            	<input type="text" id="code_input" name="code_input" class="form-control input-sm" placeholder="请输入或扫描二维码，按ENTER确认。或者在下方表格上选择">
	            </div>
	            <div id="tsk_detail" class="panel-body">
	            	<!--panel-body-->
	            	{!!isset($panel_body)?$panel_body:""!!}
	                @yield('panel-body')
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
<script type="text/javascript">
	$('#example').on( 'draw.dt', function () {
	    $("[for="+$("#example").attr("select_id")+"]").parent("td").parent("tr").find("td").addClass("row-select");
	});
	//扫描相关
	$(document).on("keydown",function(){
		if ($("input:focus").length == 0) {
			$("#code_input").val("");
			$("#code_input").focus();
		}
	});
	$(document).on("keyup",function(e){
		var keycode = e.which;
		if (keycode == 13) {
			if (a_flavr == null && $("#code_input:focus").length > 0 && $("#code_input:focus").val().length > 0) {
				if (Math.floor(Number($("#code_input:focus").val())/1000000) == 10000+{{PJCODE}}) {
					add_finish_form(Number($("#code_input:focus").val())%1000000);
					$("#code_input:focus").val("");
				} else if (Math.floor(Number($("#code_input:focus").val())/1000000) == 30000+{{PJCODE}}) {
					add_finish_form(-Number($("#code_input:focus").val())%1000000);
					$("#code_input:focus").val("");
				} else if($("#base_tsk_pp").length == 1 && Math.floor(Number($("#code_input:focus").val())/1000000) == 20000+{{PJCODE}}){
					$("#sp_tsk_pp").val(Number($("#code_input:focus").val())%1000000);
					$("#sp_tsk_pp").attr("refresh",1);
					$("#sp_tsk_pp").trigger("dblclick");
					$("#sp_tsk_pp").val("");
					$("#code_input").val("");
				} else {
					alert_flavr("输入数据无效");
					$("#code_input:focus").val("");
				}
			} else {
				if (a_flavr != null) {
					a_flavr.close();
				}
				a_flavr = null;
			}
		}
	});

	function add_finish_form(id){
		$("#tsk_detail").html("");//先清空
		var postdata = {};
		postdata["id"] = id;
		postdata["_method"] = "PUT";
		postdata["_token"] = $("#_token").attr("value");
		ajax_post("/tsk/tsk_finish_form",postdata,function(rdata){
			if (Number(rdata.suc) == 1) {
				$("#tsk_detail").html(rdata.form);
				form_init();
				$("#example tr td").removeClass("row-select");
				$("#example").attr("select_id",id);
				$("[for="+id+"]").parent("td").parent("tr").find("td").addClass("row-select");
			} else {		
				alert_flavr(rdata.msg);
			}
		});
	}
</script>
@endpush