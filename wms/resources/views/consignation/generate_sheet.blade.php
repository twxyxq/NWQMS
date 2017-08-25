@extends('layouts.panel_table')

@section('panel-body')
<div class="container">
	<div>
		<button class="btn btn-success btn-small" onclick="start_generate()">生成委托单</button>（请先选择焊口，然后点击生成）
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function start_generate(){
		if ($(".wj_no_sheet:checked").length > 0) {
			var sys = "";
			var exam_method = "";
			var ids = "";
			var valid_pass = 1;
			$(".wj_no_sheet:checked").each(function(){
				var id = $(this).val();
				if ((sys != "" && sys != $("#ep_ild_sys_"+id).html()) || (exam_method != "" && exam_method != $("#exam_method_"+id).html())){
					valid_pass == 0;
				}
				sys = $("#ep_ild_sys_"+id).html();
				exam_method = $("#exam_method_"+id).html();
				ids += "/"+id;
			});
			if (valid_pass == 1) {
				if (ids.length > 0){
					ids = ids.substr(1);
					new_flavr("/consignation/sheet_detail?ids="+ids+"&exam_method="+exam_method,"生成委托单",function(){
						$("#example").DataTable().draw(false);
					});
				} else {
					alert("选择的焊口丢失！");
				}
			} else {
				alert_flavr("选择的焊口不属于同一检测方法、机组、系统，不能一起打印");
			}
		} else {
			alert_flavr("没有选择焊口");
		}
	}
	function change_epp_code_readonly(){
		if($("#epp_code_specify").is(":checked")){
			$("#epp_code").attr("readonly",false);
			$("#epp_code").attr("onkeydown","");
			$("#epp_code").css("background-color","");
		} else {
			$("#epp_code").attr("readonly",true);
			$("#epp_code").attr("onkeydown","pingbi(8);");
			$("#epp_code").css("background-color","lightgrey");
		}
	}
</script>
@endpush