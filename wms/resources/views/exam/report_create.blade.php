@extends('layouts.panel_table')


@section('panel-body')
    <button class="btn btn-success" onclick="report_create()">出版</button> &nbsp; 请选择检验结果出版报告，如需合并出版，则必须工艺相同、额外参数相同。
@endsection

@push('scripts')
	<script type="text/javascript">
		function report_create(){
			if ($("[name='exam_id']:checked").length > 0) {
				var postdata = {};
				postdata["exam_ids"] = new Array();
				$("[name='exam_id']:checked").each(function(){
					postdata["exam_ids"].push($(this).val());
				});
				ajax_post("/exam/report_create_post",postdata,function(data){
					if (data.suc == 1) {
						new_flavr("/exam/report_detail?report_create=1&exam_id="+postdata["exam_ids"],"",function(){
							$("#example").DataTable().draw();
						});
					} else {
						alert_flavr("操作失败");
						return false;
					}
				});
			} else {
				alert_flavr("请选择一个结果项");
			}
		}
	</script>
@endpush
