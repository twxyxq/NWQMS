@extends('layouts.panel_table')


@push('scripts')
<script type="text/javascript">
	$('#example').on( 'draw.dt', function () {
	    $("[for="+$("#example").attr("select_id")+"]").parent("td").parent("tr").find("td").addClass("row-select");
	});
	function add_out_form(id,batch,weight,in_date){
		var html = "<div id=\"out_form\" class=\"form-group form-horizontal\" nullable=\"except\">";
		html += "<div><span class=\"glyphicon glyphicon-info-sign\"></span><strong>焊材信息：</strong></div>";
		html += "<input type=\"hidden\" name=\"ss_id\" value=\""+id+"\">";
		html += "<div class=\"col-sm-4\">批号："+batch+"</div>";
		html += "<div class=\"col-sm-4\">重量："+weight+"kg</div>";
		html += "<div class=\"col-sm-4\">入库日期："+in_date+"</div>";
		html += "<div><span class=\"glyphicon glyphicon-info-sign\"></span><strong>退库数据：</strong></div>";
		html += "<lable for=\"ss_out_date\" class=\"col-sm-2\" class=\"control-label\">退库日期</lable>";
		html += "<div id=\"ss_out_date_show\" class=\"col-sm-4\"><input type=\"text\" name=\"ss_out_date\" class=\"form_date form-control input-sm\" data-date-format=\"yyyy-mm-dd\" name=\"tsk_finish_date\" readonly=\"true\" value=\"{{\Carbon\Carbon::today()->toDateString()}}\"></div>";
		html += "<lable for=\"ss_out_weight\" class=\"col-sm-2\" class=\"control-label\">退库重量</lable>";
		html += "<div id=\"ss_out_weight_show\" class=\"col-sm-4\"><input type=\"text\" name=\"ss_out_weight\" class=\"form-control input-sm\" model=\"secondary_store\" for_id=\""+id+"\" blur_valid=\"1\" blurfn=\"1\" refer=\"1\" bind=\"['"+weight+"','0']\" value=\"0\" tip=\"kg\"></div>";
		html += "<lable for=\"ss_out_reason\" class=\"col-sm-2\" class=\"control-label\">退库原因</lable>";
		html += "<div id=\"ss_out_reason_show\" class=\"col-sm-4\"><input type=\"text\" name=\"ss_out_reason\" class=\"form-control input-sm\" refer=\"1\" bind=\"['焊材使用完','超过有效期','受潮不能使用','破碎或损坏']\"></div>";
		html += "<div id=\"ss_out_submit\" class=\"col-sm-6\"><button class=\"btn btn-success\" onclick=\"wm_out();\">退库</button></div>";
		html += "</div>";
		$(".panel-body").html(html);
		form_init();
		$("#example tr td").removeClass("row-select");
		$("#example").attr("select_id",id);
		$("[for="+id+"]").parent("td").parent("tr").find("td").addClass("row-select");
	}
	function wm_out(){
		if(valid_null($("#out_form"))){
			var postdata = {};
			postdata["id"] = $("[name='ss_id']").val();
			postdata["ss_out_date"] = $("[name='ss_out_date']").val();
			postdata["ss_out_weight"] = $("[name='ss_out_weight']").val();
			postdata["ss_out_reason"] = $("[name='ss_out_reason']").val();
			postdata["_method"] = "PUT";
			postdata["_token"] = $("#_token").attr("value");
			ajax_post("/console/ss_out",postdata,function(rdata){
				if (Number(rdata.suc) == 1) {
					$("#ss_out_date_show").html("<span class='form-control transparent-input'>"+rdata.ss_out_date+"</span>");
					$("#ss_out_weight_show").html("<span class='form-control transparent-input'>"+rdata.ss_out_weight+"</span>");
					$("#ss_out_reason_show").html("<span class='form-control transparent-input'>"+rdata.ss_out_reason+"</span>");
					$("#ss_out_submit").html("<span class='form-control transparent-input'>退库成功</span>");
					$("#example").DataTable().draw(false);
				} else {		
					alert_flavr(rdata.msg);
				}
			});
		}
	}
</script>
@endpush