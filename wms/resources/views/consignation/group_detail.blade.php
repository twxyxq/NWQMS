@extends('layouts.app')

@push('style')
<style type="text/css">
	.vcode {
		color: blue;
	}
</style>
@endpush

@section('content')
<div class="container" id="sheet_detail">
    {!!$sheet!!}
</div>
<div class="container" style="text-align: center;margin-top: 20px">
	<button class="btn btn-default btn-small" onclick="print_object('#sheet_detail')">打印</button>
</div>
@endsection

@push('scripts')
	@if(isset($_GET["id"]))
	<script type="text/javascript">
		function addition_examination(){
			if (confirm("确定复验？")) {
				ajax_post("/consignation/addition_examination",{"ep_id" : {{$_GET["id"]}} },function(data){
					if (data.suc == 1) {
						alert_flavr(data.msg,function(){
							location.reload();
						});
					} else {
						alert_flavr(data.msg);
					}
				});
			}
		}
		function another_examination(){
			if (confirm("确定复验？")) {
				ajax_post("/consignation/another_examination",{"ep_id" : {{$_GET["id"]}} },function(data){
					if (data.suc == 1) {
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
