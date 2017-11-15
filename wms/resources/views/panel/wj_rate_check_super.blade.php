@extends('layouts.panel_table')



@section('panel-body')
<a id="process_btn" class="btn btn-default btn-small" href="###" onclick="recal()">重新计算</a> 
<span id="cal_data"> 最后计算时间：{{\App\sys_check_list::where("check_name","wj_rate_check")->get()[0]->updated_at}} </span> 
<span id="process"></span>
<a id="process_btn" class="btn btn-warning btn-small" href="?lower=1">显示检验比例不符合的焊口</a> 
@endsection


@push('scripts')
<script type="text/javascript">
	window.interval = 1000;
	window.max_num = 70000;
	function recal(){
		if (confirm("重新计算？")) {
			$("#process_btn").attr("disabled",true);
			$("#process_btn").attr("onclick","");
			alert_flavr("<span id=\"process_alert\"></span>");
			ajax_post("/panel/wj_rate_check_super_post",{"init":1,"min":0,"max":window.interval},function(data){
				if (data.suc == 1) {
					processing(data.current);
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}

	function processing(current){
		if (current < window.max_num) {
			$("#process,#process_alert").html("当前进度:"+(Number(current)*100/Number(max_num)).toFixed(2)+"%");
			ajax_post("/panel/wj_rate_check_super_post",{"min":current,"max":Number(current)+Number(window.interval)},function(data){
				if (data.suc == 1) {
					processing(data.current);
				} else {
					alert_flavr(data.msg);
				}
			});
		} else {
			$("#process,#process_alert").html("计算完成");
			setTimeout("location.reload()",3000);
		}
	}
</script>
@endpush
