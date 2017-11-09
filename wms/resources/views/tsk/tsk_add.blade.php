@extends('layouts.page')

@push('style')
	<style type="text/css">
		#task .form-control{
			width: 96%;
		}
		#task th,#task td{
			text-align: center;
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
	            	@include('conn/datatables')
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>

<div class="container">
	<table id="task" class="table table-striped table-hover">
		<thead>
			<th>操作</th>
			<th>焊口信息</th>
			<th>质量计划</th>
			<th>方式</th>
		</thead>
		<tbody>
			
		</tbody>
		<tfoot>
			<th colspan="5">
				<button id="tsk_submit_button" class="btn btn-success" onclick="submit_tsk()">确定</button>
			</th>
		</tfoot>
	</table>
</table>
</div>
    
</div>
@endsection

@push('scripts')
	<script type="text/javascript">

		function wj_choose(id){

			//ajax_post("/tsk/tsk_input_form",{"id" : id},function(){

			//});











			if ($("#wj_info_"+id).length == 0) {
				var html = "<tr valign='middle'>";
				html += "<td rowspan='2'><button id='wj_info_"+id+"' for='"+id+"'  class='wj_info btn btn-danger btn-small' onclick='remove_wj_info("+id+")'>删除</button></td>";
				html += "<td rowspan='2'><strong>《<a href='###' onclick='new_flavr(\"/console/dt_edit?model=wj&id="+id+"\")'>"+$("#vcode_"+id).html()+"</a>》</strong><br>"+$("#type_"+id).html()+"<br>"+$("#rate_"+id).html()+"</td>";
				html += "<td><input type='text' class='form-control input-sm' id='wj_qp_"+id+"' name='wj_qp_"+id+"' bind=\"{model:'qp',col:'id',show:'CONCAT(qp_name,\\'(\\',version,\\')\\')',type:'qp_sys#"+$("#sys_"+id).val()+"'}\" value='"+$("#qid_"+id).html()+"'></td>";
				html += "<td>";
				html += "<select class='form-control input-sm' id='wj_ft_"+id+"' name='wj_ft_"+id+"'>";
				html += "<option value='安装'>安装</option>";
				html += "<option value='预制'>预制</option>";
				html += "</select>";
				html += "</td>";
				html += "</tr><tr>";
				html += "<td id='wps_choose_"+id+"' class='wps_choose' colspan='2' align='left' style='text-align:left'></td>";
				html += "</tr>";

				$("#task > tbody").append(html);

				$("#wj_ft_"+id).val($("#ft_"+id).html());

				$("#wj_qp_"+id+",#wj_wps_"+id).intelligent_input({force:1});

				ajax_post("/wps/wj_get_wps",{"wj_id":id},function(data){
					if (data.suc == 1) {
						var wps_html = "";
						for (var key in data.wps) {
							wps_html += "<br><input type='radio' name='wj_wps_"+id+"' value='"+data.wps[key]["id"]+"'>"+data.wps[key]["wps_code"]+"("+data.wps[key]["version"]+")";
							wps_html += " &nbsp; "+(data.wps[key]["wps_base_metal_type_A"]+"/"+data.wps[key]["wps_base_metal_type_B"]).replace(/}{/g,",").replace(/}/g,"").replace(/{/g,"");
							wps_html += " &nbsp; "+data.wps[key]["wps_jtype"];
							wps_html += " &nbsp; "+data.wps[key]["wps_method"];
							wps_html += " &nbsp; 管径："+data.wps[key]["wps_diameter_lower_limit"]+"~"+(data.wps[key]["wps_diameter_upper_limit"]==0?"∞":data.wps[key]["wps_diameter_upper_limit"]);
							wps_html += " &nbsp; 厚度："+data.wps[key]["wps_thickness_lower_limit"]+"~"+(data.wps[key]["wps_thickness_upper_limit"]==0?"∞":data.wps[key]["wps_thickness_upper_limit"]);
							wps_html += " &nbsp; 焊材："+data.wps[key]["wps_wire"]+","+data.wps[key]["wps_rod"];
						}
						$("#wps_choose_"+id).html(wps_html.substr(4));
					} else {
						$("#wps_choose_"+id).html(data.msg);
					}
				});
			}

			refresh_data();

		}

		function remove_wj_info(id){
			$("#wj_info_"+id).parent("td").parent("tr").remove();
			$("#wps_choose_"+id).parent("tr").remove();
			refresh_data();
		}

		function refresh_data(){
			var in_id = "";
			$(".wj_info").each(function(){
				in_id += ","+$(this).attr("for");
			});
			in_id = in_id.substr(1);
			$("#example").DataTable().settings()[0].ajax.data.indexNotIn = in_id;
			$("#example").DataTable().draw(false);
		}

		function submit_tsk(){
			if ($(".wj_info").length == 0) {
				alert_flavr("没有选择焊口");
			} else {
				//暂时禁用按钮
				$("#tsk_submit_button").attr("disabled",true);
				$("#tsk_submit_button").attr("onclick","");
				setTimeout('$("#tsk_submit_button").attr("disabled",false);',3000);
				setTimeout('$("#tsk_submit_button").attr("onclick","submit_tsk()");',3000);


				$("#task input,#task div[type=divtext]").removeClass("form_null");
				var null_count = 0
				$("#task").find("input[name][data!=0],select[name][data!=0]").each(function(){
					if ($(this).val().length == 0) {
						null_count++;
						if ($(this).attr("type") == "hidden") {
							$(this).parent("div[type='divtext']").addClass("form_null");
						} else {
							$(this).addClass("form_null");
						}
					}
				});
				null_count += $(".wj_info").length - $("input:radio[data!=0]:checked").length;
				if (null_count == 0) {
					var postdata = {};
					//postdata["data"] = new Array();
					$(".wj_info").each(function(){
						postdata[$(this).attr("id")] = new Array();
						postdata[$(this).attr("id")].push($(this).attr("for"));
						postdata[$(this).attr("id")].push($("[name='wj_qp_"+$(this).attr("for")+"']").val());
						postdata[$(this).attr("id")].push($("[name='wj_ft_"+$(this).attr("for")+"']").val());
						postdata[$(this).attr("id")].push($("[name='wj_wps_"+$(this).attr("for")+"']:checked").val());
					});
					postdata["_method"] = "PUT";
					postdata["_token"] = $("#_token").attr("value");
					//console.log(postdata);
					$.post("/tsk/tsk_add_exec", postdata, function(data){
						if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
							alert_flavr("操作失败！错误信息："+data);
						} else {
							eval('var rdata = '+data);
							//alert(rdata.suc); 
							if (Number(rdata.suc) == 1) {
								if (confirm(rdata.msg+"\n是否立即打印？")) {
									new_flavr("/tsk/sheets?ids="+array_to_multiple(rdata.tsk_ids));
								}
								$(".wj_info").parent("td").parent("tr").remove();
								$(".wps_choose").parent("tr").remove();
								refresh_data();						
							} else {		
								alert_flavr(rdata.msg);
							}
						}
						
					});	
				} else {
					alert_flavr("有"+null_count+"项数据为空");
				}
			}
		}
	</script>
@endpush
