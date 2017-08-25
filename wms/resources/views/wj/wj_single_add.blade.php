@extends('layouts.panel_table')

@push('scripts')
<script type="text/javascript">
	$("[name='vcode']").after("<span style=\'position:absolute;top:-3px;right:21px;\'><label class=\'control-label\'><input type=\'checkbox\' id=\'vcode_cal\' checked>计算</label>");
	$("[name='ild'],[name='sys'],[name='pipeline'],[name='vnum']").on("focus",function(){
		disable_submit();
	});
	$("[name='ild'],[name='sys'],[name='pipeline'],[name='vnum']").on("keyup change blur",function(){
		if ($("#vcode_cal").is(":checked")) {
			$("[name='vcode']").val($("[name='ild']").val()+$("[name='sys']").val()+"-"+$("[name='pipeline']").val()+"-"+$("[name='vnum']").val());
		}
		recover_submit();
	});
	$("[name='exam_specify']").on("change",function(){
		if ($("[name='exam_specify']").val() == 1) {
			$("[name='RT'],[name='UT'],[name='PT'],[name='MT'],[name='SA'],[name='HB']").attr("readonly",false);
		} else {
			$("[name='RT'],[name='UT'],[name='PT'],[name='MT'],[name='SA'],[name='HB']").attr("readonly",true);
		}
	});
</script>
@endpush