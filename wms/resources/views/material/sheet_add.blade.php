@extends('layouts.page')

<style type="text/css">
	#sheet_info .row > div:first-child,.row_title {
		text-align: center;
		font-weight: bold;
	}
	#sheet_info .row {
		border-bottom: 1px solid lightgray; 
	}
	#material_sent .row > div{
		vertical-align: middle;
	}
	#material_sent .row[m_checked='0'] {
		opacity: 0.4;
	}
	#material_sent .row:not([m_checked='0']) > div:first-child > span:first-child {
		display: none;
	}
	#material_sent .row[m_checked='0'] > div:first-child > span:not(:first-child) {
		display: none;
	}
</style>

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
		            		<div class="col-md-2">部门信息</div>
		            		<div class="col-md-4" id="tsk_dept">
		            			<select id="dept" class="form-control input-sm">
		            				<option value="热机">热机</option>
		            				<option value="机械化">机械化</option>
		            				<option value="电仪">电仪</option>
		            			</select>
		            		</div>
		            		<div class="col-md-2 row_title">是否点口单</div>
		            		<div class="col-md-4" id="tsk_spot">
		            			<select id="spot" class="form-control input-sm">
		            				<option value="0">否</option>
		            				<option value="1">是</option>
		            			</select>
		            		</div>
	            		</div>
		            	<div class="row">
		            		<div class="col-md-12">
		            			->焊材发放<-
		            			<input type="hidden" name="tsk_wire" value="">
		            			<input type="hidden" name="tsk_rod" value="">
		            		</div>
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
	$("#code_input").on("focus",function(){
		$("#code_input").val("");
	});
	function checked_triggle(obj){
		if (obj.parent().attr("m_checked") == "0") {
			obj.parent().attr("m_checked","1");
		} else {
			obj.parent().attr("m_checked","0");
		}
	}

	function code_input(){
		if ($("#code_input").val().length > 0) {
			ajax_post("/material/material_sheet_add",{"code_input":$("#code_input").val()},function(data){
				if (data.suc == 1) {
					if (data.tsk_id != undefined) {
						if ($("[name=tsk_ids][value="+data.tsk_id+"]").length == 0) {
							var exist_wm = 0;
							if ($(".store").length == 0) {
								$("[name='tsk_wire']").val(data.tsk_wire);
								$("[name='tsk_rod']").val(data.tsk_rod);
								var html = "";
								var has_store = 0;
								for (var key in data.m_info) {
									has_store = 1;
									html += "<div class=\"row store\" m_checked=\"0\" m_type=\""+data.m_info[key]["type"]+"\" diameter=\""+data.m_info[key]["diameter"]+"\" name=\""+data.m_info[key]["name"]+"\">";
									html += "<div class=\"col-md-1\" onclick=\"checked_triggle($(this))\"><span style=\"font-size:18px;\" class=\"glyphicon glyphicon-unchecked\"></span><span style=\"font-size:18px;\" class=\"glyphicon glyphicon-ok-circle\"></span></div>";
									html += "<div class=\"col-md-1\">"+data.m_info[key]["type"]+"</div>";
									html += "<div class=\"col-md-2\">"+data.m_info[key]["title"]+"</div>";
									html += "<div class=\"col-md-6\">"+data.m_info[key]["store"]+"</div>";
									html += "<div class=\"col-md-2\">";
									html += "<input type=\"text\" class=\"form-control input-sm amount\" title=\""+data.m_info[key]["title"]+"\" name=\""+data.m_info[key]["name"]+"_amount\" placeholder=\"发放数量\">";
									html += "</div>";
									html += "</div>";
								}
								/*
								if (data.tsk_rod != undefined && data.tsk_rod != "N/A") {
									html += "<div class=\"row\">";
									html += "<div class=\"col-md-2\">焊条</div>";
									html += "<div class=\"col-md-2\">"+data.tsk_rod+"</div>";
									html += "<div class=\"col-md-6\">库存：";
									for (var item in data.rod_store) {
										html += "<br>◇["+data.rod_store[item]["ss_warehouse"]+"] &nbsp; "+data.rod_store[item]["ss_trademark"]+" φ"+data.rod_store[item]["ss_diameter"]+" &nbsp; 批号："+data.rod_store[item]["ss_batch"]+" &nbsp; "+data.rod_store[item]["ss_weight"]+"kg";
									}
									html += "</div>";
									html += "<div class=\"col-md-2\">";
									if (data.rod_store.length > 0) {
										html += "<select name=\"tsk_rod_diameter\">";
										html += "<option value=\"1.6\">1.6</option>";
										html += "<option value=\"2.0\">2.0</option>";
										html += "<option value=\"2.4\">2.4</option>";
										html += "<option value=\"3.2\">3.2</option>";
										html += "<option value=\"4.0\">4.0</option>";
										html += "</select>";
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
										html += "<br>◇["+data.wire_store[item]["ss_warehouse"]+"] &nbsp; "+data.wire_store[item]["ss_trademark"]+" φ"+data.wire_store[item]["ss_diameter"]+" &nbsp; 批号："+data.wire_store[item]["ss_batch"]+" &nbsp; "+data.wire_store[item]["ss_weight"]+"kg";
									}
									html += "</div>";
									html += "<div class=\"col-md-2\">";
									if (data.wire_store.length > 0) {
										html += "<select name=\"tsk_wire_diameter\">";
										html += "<option value=\"1.6\">1.6</option>";
										html += "<option value=\"2.0\">2.0</option>";
										html += "<option value=\"2.4\">2.4</option>";
										html += "<option value=\"3.2\">3.2</option>";
										html += "<option value=\"4.0\">4.0</option>";
										html += "</select>";
										html += "<input type=\"text\" class=\"form-control input-sm amount\" name=\"tsk_wire\" title=\""+data.tsk_wire+"\" placeholder=\"发放数量\">";
									} else {
										html += "无库存";
									}
									html += "</div>";
									html += "</div>";
								}
								*/
								if (has_store == 1) {
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
								html += "<input type=\"checkbox\" name=\"tsk_ids\" value=\""+data.tsk_id+"\" style=\"display:none;\" checked>";
								html += "<a href=\"###\" onclick=\"new_flavr('/tsk/tsk_detail?id="+data.tsk_id+"')\">"+data.tsk_title+"</a>";
								html += " <span class=\"glyphicon glyphicon-info-sign\" role=\"button\" data-toggle=\"popover\" data-trigger=\"focus\" data-content=\""+data.tsk_info.tsk_wj_spec+" "+data.tsk_info.tsk_wmethod+" "+data.tsk_wps.wps_wire+" "+data.tsk_wps.wps_rod+"\" onmouseover=\"$(this).popover('show')\" onmouseout=\"$(this).popover('hide')\"></span> ";
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
			$("[name='tsk_wire']").val("");
			$("[name='tsk_rod']").val("");
		}
	}

	function m_sent(){
		if ($("[name=pp_ids]").length == 0) {
			alert_flavr("没有输入领用焊工");
		} else if ($(".row[m_checked='1']").length == 0){
			alert_flavr("没有选择任何焊材");
		} else {
			var m_error = 0;
			var postdata = {};
			postdata["tsk_ids"] = new Array;
			$("[name='tsk_ids']").each(function(){
				postdata["tsk_ids"].push($(this).val());
			});
			postdata["tsk_dept"] = $("#dept").val();
			postdata["tsk_spot"] = $("#spot").val();
			postdata["pp_ids"] = new Array;
			$("[name='pp_ids']").each(function(){
				postdata["pp_ids"].push($(this).val());
			});
			postdata["tsk_material"] = new Array;
			$(".row[m_checked='1']").each(function(){
				var temp = new Array;
				temp.push($(this).attr("name"));
				temp.push($(this).attr("m_type"));
				temp.push($(this).attr("diameter"));
				temp.push($(this).find(".amount").val());
				console.log($(this).find(".amount").attr("name"));
				if (temp[3] == undefined || temp[3].length == 0) {
					m_error = 1;
				}
				postdata["tsk_material"].push(temp);
			});
			/*
			if ($("[name='tsk_rod']").length > 0 && $("[name='tsk_rod']").val().length > 0) {
				postdata["tsk_rod"] = $("[name='tsk_rod']").attr("title");
				postdata["tsk_rod_diameter"] = $("[name='tsk_rod_diameter']").val();
				postdata["tsk_rod_amount"] = $("[name='tsk_rod']").val();
			}
			if ($("[name='tsk_wire']").length > 0 && $("[name='tsk_wire']").val().length > 0) {
				postdata["tsk_wire"] = $("[name='tsk_wire']").attr("title");
				postdata["tsk_wire_diameter"] = $("[name='tsk_wire_diameter']").val();
				postdata["tsk_wire_amount"] = $("[name='tsk_wire']").val();
			if(postdata["tsk_rod_amount"] == undefined && postdata["tsk_wire_amount"] == undefined){
				alert_flavr("没有输入合适的数量");
			}
			}*/
			if (m_error == 1) {
				alert_flavr("输入用量错误");
			} else {
				ajax_post("/material/m_sheet",postdata,function(data){
					if (data.suc == 1) {
						alert_flavr("操作成功",function(){
							location.reload();
						});
					} else {
						alert_flavr(data);
					}
				});
			}
		}
	}
</script>
@endpush