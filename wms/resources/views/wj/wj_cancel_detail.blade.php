@extends('layouts.page_table_detail')

@section('panel-body')
	<button class="btn btn-success btn-small" onclick="cancel_proc()">启动作废流程</button>
@endsection

@push('scripts')
<script type="text/javascript">
	function cancel_proc(){
		if (confirm("确定启动流程？")) {
			ajax_post("/console/procedure_create",{"model":"wj","id":multiple_to_array("{{$_GET['ids']}}"),"pd_name":"焊口作废流程","cancel":1},function(data){
				if (data.suc == 1) {
					new_flavr("/console/view_procedure?proc=status_avail_procedure&proc_id="+data.proc_id,"审批流程",function(){
						$("#example").DataTable().draw(false);
					});
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}
</script>
@endpush