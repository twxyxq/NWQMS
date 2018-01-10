@extends('layouts.scan')


@section('scan-info')
	@include('conn/datatables')
@endsection

@push('scripts')
<script type="text/javascript">
	$("#mm_scan").after(" &nbsp; 请扫描焊工授权证书二维码");
	function ajax_post_success(data){
		//alert_flavr(data.msg);
		$("#example").DataTable().draw(false);
	}
	function cancel_mark(id){
		if (confirm("确认删除？")) {
			ajax_post("/pp/cancel_mark",{"cancel_mark":1, "id":id},function(data){
				if (data.suc == 1) {
					$("#example").DataTable().draw(false);
				} else {
					alert_flavr(data.msg);
				}
			});
		}
	}
</script>
@endpush