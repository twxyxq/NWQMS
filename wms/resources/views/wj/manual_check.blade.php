@extends('layouts.page')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<span class="glyphicon glyphicon-home"></span> {!!$current_nav!!}
    			</div>
	            <div class="panel-body">
	            	<button class="btn btn-success btn-small" onclick="manual_check_proc()">开启审核流程</button>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>
	<!--datatables-->
    @include('conn/datatables')
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function manual_check_proc(){
		if ($("[name='wj_id']:checked").length > 0) {
			if (confirm("确定启动流程？")) {
				var ids = new Array;
				$("[name='wj_id']:checked").each(function(){
					ids.push($(this).val());
				});
				ajax_post("/console/procedure_create",{"model":"wj","id":ids,"pd_name":"焊口生效流程"},function(data){
					if (data.suc == 1) {
						new_flavr("/console/view_procedure?proc=status_avail_procedure&proc_id="+data.proc_id,"审批流程",function(){
							$("#example").DataTable().draw(false);
						});
					} else {
						alert_flavr(data.msg);
					}
				});
			}
		} else {
			alert_flavr("请至少选择一个焊口");
		}
	}
</script>
@endpush