@extends("layouts/page_detail")

@section('panel-body')
	<label class="control-label col-sm-2">行高调整</label>
	<div class="col-sm-3">
		<select class="form-control input-sm" name="qpp_height">
			@define $qp_proc = \App\qp_proc::find($_GET["id"]);
			@foreach($qp_proc->item->qpp_height->restrict as $height)
				<option value="{{$height}}"{{$qp_proc->qpp_height==$height?"selected":""}}>{{$height}}</option>
			@endforeach
		</select>
	</div>
	<div class="col-sm-3">
		<button class="btn btn-success btn-small" onclick="change_height()">确认</button>
	</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function change_height(){
		ajax_post("/qp/qp_proc_height_change_exec",{"id":{{$_GET["id"]}},"height":$("[name='qpp_height']").val()},function(data){
			alert_flavr(data.msg,function(){
				location.reload();
			});
		});
	}
</script>
@endpush