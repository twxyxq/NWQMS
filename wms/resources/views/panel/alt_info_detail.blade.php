@extends('layouts/page_detail');


@push('scripts')
<script type="text/javascript">
	//$("[change_fn]").attr("change_fn_forbidden",1);
	//$(".ajax_submit").on("")trigger_cal
	$("[change_fn]").each(function(){
		$(this).attr("change_fn","if($('[name="+$(this).attr("name")+"]').attr('cal_permit')==1){"+$(this).attr("change_fn")+";} else {$('[name="+$(this).attr("name")+"]').attr('cal_permit',1);}");
	});
</script>
@endpush