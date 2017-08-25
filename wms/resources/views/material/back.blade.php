@extends('layouts.scan')


@section('scan-info')
<div class="container">
	<div id='sheet_container' class="row">
	   
	</div>
</div>
@endsection



@push('scripts')
<script type="text/javascript">
	function ajax_post_success(data){
		$('#sheet_container').html(data.msg);
	}
	function back(id){
		if ($("[name=ms_back_amount"+id+"]").val().length > 0 || Number($("[name=ms_back_amount"+id+"]").val()) >= 0) {
			var postdata = {};
			postdata["id"] = id;
			postdata["ms_back_amount"] = $("[name=ms_back_amount"+id+"]").val();
			ajax_post("/material/m_back",postdata,function(data){
				$("[name=ms_back_amount"+id+"]").css("display","none");
				$("[name=ms_back_button"+id+"]").css("display","none");
				$("#back_msg"+id).html(data.back_amount);
				$("#back_edit"+id).html(data.msg+" <a href=\"###\" onclick=\"triggle_edit("+id+")\">修改</a>");
				$(".back_suc_msg"+id).css("display","");
			});
		} else {
			alert_flavr("回收数量错误");
		}
	}
	function triggle_edit(id){
		$(".back_suc_msg"+id).css("display","none");
		$("[name=ms_back_amount"+id+"]").css("display","");
		$("[name=ms_back_button"+id+"]").css("display","");
	}
</script>
@endpush