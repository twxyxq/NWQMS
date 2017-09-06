@extends('layouts.app')


@section('content')
<div class="container">
    {!!$sheet!!}
</div>
@endsection

@push('scripts')
	@if(isset($_GET["exam_id"]))
	<script type="text/javascript">
		@if(!isset($_GET["report_create"]))
		function eps_select(){
			var select = $("#current_iframe").contents().find("[name='exam_eps_id']:checked");
			if (select.length > 0) {
				var postdata = {};
				postdata["exam_id"] = {{$_GET["exam_id"]}};
				postdata["exam_eps_id"] = select.val();
				ajax_post("/exam/eps_select_post",postdata,function(data){
					if (data.suc == 1) {
						return true;
					} else {
						alert_flavr("操作失败");
						return false;
					}
				});
			} else {
				alert_flavr("请选择一个工艺");
				return false;
			}
		}
		function exam_confirm(){
			if ($("[name='exam_date']").val().length == 0) {
				alert_flavr("请输入检验日期");
			} else {
				if (confirm("确认该检验结果？")) {
					ajax_post("/exam/exam_confirm_post",{exam_id:{{$_GET["exam_id"]}},exam_date:$("[name='exam_date']").val()},function(data){
						if (data.suc == 1) {
							alert_flavr("结果已确认",function(){
								location.reload();
							});
						} else {
							alert_flavr(data.msg);
						}
					});
				}
			}
			
		}
		@endif
		function report_confirm(){
			if (confirm("确认出版报告？")) {
				var postdata = {};
				postdata["exam_id_text"] = '{{$_GET["exam_id"]}}';
				postdata["exam_report_code"] = $("[name='exam_report_code']").val();
				postdata["exam_report_date"] = $("[name='exam_report_date']").val();
				ajax_post("/exam/report_confirm_post",postdata,function(data){
					if (data.suc == 1) {
						alert_flavr("报告已确认",function(){
							location.href="/exam/report_detail/?report_id="+data.report_id;
						});
					} if (data.suc == 2) {
						alert_flavr(data.msg,function(){
							location.reload();
						});
					} else {
						alert_flavr(data.msg);
					}
				});
			}
		}
	</script>
	@endif
@endpush
