@extends('layouts.panel_table')

@push('style')
<style type="text/css">
	#tsk-filter > div {
		text-align: right;
	}
</style>
@endpush

@section('panel-body')
	<div id="tsk-filter" class="row">	
		<div class="col-sm-2"><button class="btn btn-success" onclick="print_selected()">批量打印</button></div>	
		<div class="col-sm-1"><label class="control-label">年</label></div>
		<div class="col-sm-2"><input type="text" name="year" class="form_date form-control input-sm" readonly="true" data-date-format="yyyy" minView="4" startView="4"></div>
		<div class="col-sm-1"><label class="control-label">月</label></div>
		<div class="col-sm-2"><input type="text" name="month" class="form_date form-control input-sm" readonly="true" data-date-format="mm" minView="3" startView="3"></div>
		<div class="col-sm-1"><label class="control-label">日</label></div>
		<div class="col-sm-2"><input type="text" name="day" class="form_date form-control input-sm" readonly="true" data-date-format="dd" minView="2" startView="2"></div>
	</div>
@endsection


@push('scripts')
<script type="text/javascript">
	$(".form_date").on("change",function(){
		$(".form_date").each(function(){
			$("#example").DataTable().settings()[0].ajax.data[$(this).attr("name")] = $(this).val();
		});
		$("#example").DataTable().draw();
	});
	function print_selected(){
		if ($(".tsk_id:checked").length == 0) {
			alert_flavr("没有选择任何任务");
		} else {
			var postdata = {};
			postdata["tsk_ids"] = new Array;
			$(".tsk_id:checked").each(function(){
				postdata["tsk_ids"].push($(this).val());
			});
			new_flavr("/tsk/sheets?ids="+array_to_multiple(postdata["tsk_ids"]));
			/*
			ajax_post("/tsk/get_sheets",postdata,function(data){
				if(data.suc == 1){
					$("body").append(data.html);
					print_object(".welding_record");
					//$(".welding_record").remove();
				} else {
					alert_flavr("获取记录单失败");
				}
			})
			*/
		}
	}
</script>
@endpush