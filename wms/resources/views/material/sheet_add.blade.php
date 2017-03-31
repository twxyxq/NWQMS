@extends('layouts.page')

<style type="text/css">
	#sheet_info .row > div:first-child {
		text-align: center;
		font-weight: bold;
	}
	#sheet_info .row {
		border-bottom: 1px solid lightgray; 
	}
	#material_sent .row > div{
		height: 60px;
		vertical-align: middle;
	}
</style>

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> <!--current_nav-->
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
		            		</div>
		            	</div>
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>
	<div class="row" id="sheet_info">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-body">
	            	<div class="form-group form-horizontal" nullable="except">
		            	<div class="row">
		            		<div class="col-md-2">任务信息</div>
		            		<div class="col-md-8" id="tsk_info"></div>
	            		</div>
		            	<div class="row">
		            		<div class="col-md-2">焊工信息</div>
		            		<div class="col-md-8" id="tsk_pp"></div>
	            		</div>
		            	<div class="row">
		            		<div class="col-md-12">->焊材发放<-</div>
	            		</div>
		            	<div id="material_sent">

	            		</div>
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>
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
			ajax_post("/console/material_sheet_add",{"code_input":$("#code_input").val()},function(data){
				if (data.suc == 1) {
					if (data.tsk_id != undefined) {
						if ($("[name=tsk_ids][value="+data.tsk_id+"]").length == 0) {
							var exist_wm = 0;
							if ($("[name=tsk_rod]").length == 0 && $("[name=tsk_wire]").length == 0) {
								var html = "";
								if (data.tsk_rod != undefined && data.tsk_rod != "N/A") {
									html += "<div class=\"row\">";
									html += "<div class=\"col-md-2\">焊条</div>";
									html += "<div class=\"col-md-2\">"+data.tsk_rod+"</div>";
									html += "<div class=\"col-md-6\">库存：";
									for (var item in data.rod_store) {
										html += "<br>◇["+data.rod_store[item]["ss_warehouse"]+"] &nbsp; "+data.rod_store[item]["ss_trademark"]+" &nbsp; 批号："+data.rod_store[item]["ss_batch"]+" &nbsp; "+data.rod_store[item]["ss_weight"]+"kg";
									}
									html += "</div>";
									html += "<div class=\"col-md-2\">";
									if (data.rod_store.length > 0) {
										html += "<input type=\"text\" class=\"form-control input-sm amount\" title=\""+data.tsk_rod+"\" name=\"tsk_rod\" placeholder=\"发放数量\">";
									} else {
										html += "无库存";
									}
									html += "</div>";
									html += "</div>";
								}
								if (data.tsk_wire != undefined && data.tsk_wire != "N/A") {
									html += "<div class=\"row\">";
									html += "<div class=\"col-md-2\">焊丝</div>";
									html += "<div class=\"col-md-2\">"+data.tsk_wire+"</div>";
									html += "<div class=\"col-md-6\">库存：";
									for (var item in data.wire_store) {
										html += "<br>◇["+data.wire_store[item]["ss_warehouse"]+"] &nbsp; "+data.wire_store[item]["ss_trademark"]+" &nbsp; 批号："+data.wire_store[item]["ss_batch"]+" &nbsp; "+data.wire_store[item]["ss_weight"]+"kg";
									}
									html += "</div>";
									html += "<div class=\"col-md-2\">";
									if (data.wire_store.length > 0) {
										html += "<input type=\"text\" class=\"form-control input-sm amount\" name=\"tsk_wire\" title=\""+data.tsk_wire+"\" placeholder=\"发放数量\">";
									} else {
										html += "无库存";
									}
									html += "</div>";
									html += "</div>";
								}
								if (true) {
									html += "<div class=\"row\"><div>";
									html += "<button class=\"btn btn-success\" onclick=\"m_sent();\">发放</button>";
									html += "</div></div>";
								}
								$("#material_sent").html(html);
							} else {
								exist_wm = 1;
							}
							if (exist_wm == 0 || (data.tsk_wire == $("[name=tsk_wire]").val() && data.tsk_rod == $("[name=tsk_rod]").val())) {
								var html = "<span class=\"flex_no_shrink\" style=\"background-color:#F2DAFA;padding:2px;margin:2px;display:inline-block\">";
								html += "<input type=\"checkbox\" name=\"tsk_ids\" value=\""+data.tsk_id+"\" style=\"display:none;\" checked>"+data.tsk_title;
								html += "<span style=\"color:#B3B3B3;cursor:pointer;\" class=\"glyphicon glyphicon-remove\" onclick=\"$(this).parent('span').remove();refresh_wm();\"></span>";
								html += "</span>";
								$("#tsk_info").append(html);
							} else if (exist_wm == 1) {
								alert_flavr("该任务使用的焊材不同，不能一起发放");
							}
						}
						$("#code_input").val("");
					} else if (data.pp_id != undefined){
						if ($("[name=pp_ids][value="+data.pp_id+"]").length == 0) {
							var html = "<span class=\"flex_no_shrink\" style=\"background-color:#F2DAFA;padding:2px;margin:2px;display:inline-block\">";
							html += "<input type=\"checkbox\" name=\"pp_ids\" value=\""+data.pp_id+"\" style=\"display:none;\" checked>"+data.pcode+data.pname;
							html += "<span style=\"color:#B3B3B3;cursor:pointer;\" class=\"glyphicon glyphicon-remove\" onclick=\"$(this).parent('span').remove();\"></span>";
							html += "</span>";
							$("#tsk_pp").append(html);
						}
						$("#code_input").val("");
					}
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}

	function refresh_wm(){
		if ($("[name=tsk_ids]").length == 0) {
			$("#material_sent").html("");
		}
	}

	function m_sent(){
		if ($("[name=pp_ids]").length == 0) {
			alert_flavr("没有输入领用焊工");
		} else if ($(".amount").length == 0){
			alert_flavr("没有合适的焊材");
		} else {
			var postdata = {};
			postdata["tsk_ids"] = new Array;
			$("[name='tsk_ids']").each(function(){
				postdata["tsk_ids"].push($(this).val());
			});
			postdata["pp_ids"] = new Array;
			$("[name='pp_ids']").each(function(){
				postdata["pp_ids"].push($(this).val());
			});
			if ($("[name='tsk_rod']").length > 0 && $("[name='tsk_rod']").val().length > 0) {
				postdata["tsk_rod"] = $("[name='tsk_rod']").attr("title");
				postdata["tsk_rod_amount"] = $("[name='tsk_rod']").val();
			}
			if ($("[name='tsk_wire']").length > 0 && $("[name='tsk_wire']").val().length > 0) {
				postdata["tsk_wire"] = $("[name='tsk_wire']").attr("title");
				postdata["tsk_wire_amount"] = $("[name='tsk_wire']").val();
			}
			if(postdata["tsk_rod_amount"] == undefined && postdata["tsk_wire_amount"] == undefined){
				alert_flavr("没有输入合适的数量");
			} else {
				ajax_post("/material/m_sent",postdata,function(data){
					alert(data);
				});
			}
		}
	}
</script>
@endpush