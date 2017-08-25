@extends('layouts.app')


@section('content')
<div class="container">
    {!!$sheet!!}
</div>
@endsection

@push('scripts')
	@if(!isset($_GET["sheet_id"]) && !isset($_GET["sheet_code"]))
	<script type="text/javascript">
		function change_specify(){
			if ($("[name='es_code_specify']").is(":checked")) {
				$("[name='es_code']").removeAttr("readonly");
			} else {
				$("[name='es_code']").attr("readonly",true);
				$("[name='es_code']").val($("[name='es_code']").attr("value"));
			}
		}
		function generate_finish(){
			if ($("[name='es_code']").val().length > 0 && $("[name='es_demand_date']").val().length > 0) {
				var postdata = {};
				postdata["es_code"] = $("[name='es_code']").val();
				postdata["es_demand_date"] = $("[name='es_demand_date']").val();
				postdata["es_ild_sys"] = $("[name='es_ild_sys']").val();
				postdata["es_wj_type"] = $("[name='es_wj_type']").val();
				postdata["es_exam_ids_text"] = "{{$_GET['ids']}}";
				postdata["es_method"] = "{{$_GET['exam_method']}}";
				if ($("[name='es_code_specify']").is(":checked")) {
					postdata["es_code_specify"] = 1;
				} else {
					postdata["es_code_specify"] = 0;
				}
				ajax_post("/consignation/generate_sheet",postdata,function(data){
					if (data.suc == 1) {
						alert_flavr(data.msg,function(){
							location.href = "?sheet_id="+data.exam_id;
						});
					} else if (data.suc == 2) {
						alert_flavr(data.msg,function(){
							location.reload();
						});
					}
				});
			} else {
				alert_flavr("请填写完整");
			}
		}
	</script>
	@endif
@endpush
