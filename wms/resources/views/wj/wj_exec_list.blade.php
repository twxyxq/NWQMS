@extends('layouts.panel_table')

@push('style')
<style type="text/css">
	#processing {
		width: 100%;
		height: 100%;
		background-color: rgba(209,195,195,0.75);
		z-index: 99998;
		position: absolute;
	}
	.progress,#progress_text {
		position: absolute;
		z-index: 99999;
		top:50%;
		left: 10%;
		width: 80%;
	}
</style>
@endpush

@section('layout')
	<div id="processing">
		<div id="progress_text" style="top:40%; text-align: center; font-weight: bold; font-size: 15px">正在更新检验结果</div>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
		</div>
	</div>
@endsection

@push('scripts')
<script type="text/javascript">
	ajax_post("/panel/wj_exec_cal",{"plan_cal":1},function(data){
		if (data.suc == 1) {
			processing_null(data);
		} else {
			alert_flavr(data.msg);
		}
	});
	function processing_null(data){
		if (data.processing > data.total) {
			$("#progress_text").html("正在同步修改结果");
			$(".progress-bar").css("width","0%");
			$(".progress-bar").html("0%");
			ajax_post("/panel/wj_exec_cal",{"plan_cal_update":1},function(data){
				if (data.suc == 1) {
					processing_update(data);
				} else {
					alert_flavr(data.msg);
				}
			});
		} else {
			ajax_post("/panel/wj_exec_cal",{"plan_cal":1, "total":data.total, "processing":data.processing},function(data){
				if (data.suc == 1) {
					var ppp = data.processing*100/data.total;
					ppp = ppp.toFixed(2);
					$(".progress-bar").css("width",ppp+"%");
					$(".progress-bar").html(ppp+"%");
					processing_null(data);
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}
	function processing_update(data){
		if (data.processing > data.total) {
			$("#progress_text").html("正在同步焊口");
			$(".progress-bar").css("width","0%");
			$(".progress-bar").html("0%");
			ajax_post("/panel/wj_exec_cal",{"wj_cal":1},function(data){
				if (data.suc == 1) {
					processing_wj(data);
				} else {
					alert_flavr(data.msg);
				}
			});
		} else {
			ajax_post("/panel/wj_exec_cal",{"plan_cal_update":1, "total":data.total, "processing":data.processing},function(data){
				if (data.suc == 1) {
					var ppp = data.processing*100/data.total;
					ppp = ppp.toFixed(2);
					$(".progress-bar").css("width",ppp+"%");
					$(".progress-bar").html(ppp+"%");
					processing_update(data);
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}
	function processing_wj(data){
		if (data.processing > data.total) {
			$("#processing").css("display","none");
		} else {
			ajax_post("/panel/wj_exec_cal",{"wj_cal":1, "total":data.total, "processing":data.processing},function(data){
				if (data.suc == 1) {
					var ppp = data.processing*100/data.total;
					ppp = ppp.toFixed(2);
					$(".progress-bar").css("width",ppp+"%");
					$(".progress-bar").html(ppp+"%");
					processing_wj(data);
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}
</script>
@endpush