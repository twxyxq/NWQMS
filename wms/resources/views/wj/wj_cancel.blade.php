@extends('layouts.panel_table')

@section('panel-body')
	<button class="btn btn-success btn-small" onclick="create_cancel()">批量作废</button>
@endsection

@push('scripts')
<script type="text/javascript">
	function create_cancel(){
		if ($("[name='wj_id']:checked").length == 0) {
			alert_flavr("没有选择任何焊口");
		} else {
			var ids = new Array;
			var id_string = "";
			$("[name='wj_id']:checked").each(function(){
				ids.push($(this).val());
				id_string += "{"+$(this).val()+"}";
			});
			if (confirm("确定启动流程？")) {
				ajax_post("/console/procedure_create",{"model":"wj","id":ids,"pd_name":"焊口作废流程","cancel":1},function(data){
					if (data.suc == 1) {
						dt_proc("cancel_procedure",data.proc_id,"wj",id_string);
					} else {
						alert_flavr(data.msg);
					}
				});
			}
		}
	}
</script>
@endpush