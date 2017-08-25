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
	function sent(id){
		if ($("[name=ms_s_id"+id+"]:checked").length > 0) {
			var postdata = {};
			postdata["id"] = id;
			postdata["ms_store"] = $("[name=ms_store]").val();
			postdata["ms_s_id"] = $("[name=ms_s_id"+id+"]:checked").val();
			postdata["ms_s_show"] = $("[name=ms_s_id"+id+"]:checked").attr("title");
			ajax_post("/material/m_sent",postdata,function(data){
				$("#sent_suc_msg"+id).html(data.msg+" <a href=\"###\" onclick=\"triggle_edit("+id+")\">修改</a>");
				$("#sent_suc_msg"+id).css("display","");
				$("#sent_msg"+id).css("display","none");
			});
		} else {
			alert_flavr("没有选择焊材");
		}
	}
	function triggle_edit(id){
		$("#sent_suc_msg"+id).css("display","none");
		$("#sent_msg"+id).css("display","");
	}
</script>
@endpush