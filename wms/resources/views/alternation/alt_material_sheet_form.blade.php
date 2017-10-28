@extends('layouts.page_detail')

@push('style')
	<style type="text/css">
		.panel-body span, .button{
			text-align: center;
		}
	</style>
@endpush 

@section('panel-body')

	<div class="col-sm-2"><span class="form-control input-sm transparent-input">焊工：</span></div>
	<div class="col-sm-3">
		<span class="form-control input-sm transparent-input">{{$material_sheet->ms_pp_show}}</span>
		<input type="hidden" name="ms_pp_ids_origin" value="{{$material_sheet->ms_pp_ids}}">
	</div>
	<div class="col-sm-2"><span class="form-control input-sm transparent-input">--></span></div>
	<div class="col-sm-5"><input type="text" name="ms_pp_ids" class="form-control input-sm" bind='{model:"pp",col:"id",show:"CONCAT(pcode,\" \",pname)"}' multiples="1" value="{{$material_sheet->ms_pp_ids}}"></div>

	<div class="col-sm-2"><span class="form-control input-sm transparent-input">领用数量：</span></div>
	<div class="col-sm-3">
		<span class="form-control input-sm transparent-input">{{(int)$material_sheet->ms_amount}}</span>
	</div>
	<div class="col-sm-2"><span class="form-control input-sm transparent-input">--></span></div>
	<div class="col-sm-5"><input type="text" name="ms_amount" class="form-control input-sm" value="{{(int)$material_sheet->ms_amount}}"></div>

	<div class="col-sm-2"><span class="form-control input-sm transparent-input">回收数量：</span></div>
	<div class="col-sm-3">
		<span class="form-control input-sm transparent-input">{{(int)$material_sheet->ms_back_amount}}</span>
	</div>
	<div class="col-sm-2"><span class="form-control input-sm transparent-input">--></span></div>
	<div class="col-sm-5"><input type="text" name="ms_back_amount" class="form-control input-sm" value="{{(int)$material_sheet->ms_back_amount}}"></div>

	<div class="col-sm-12 button"><button class="btn btn-success btn-small" onclick="alt_sheet()">领用单变更</button></div>
@endsection


@push('scripts')
<script type="text/javascript">
	function alt_sheet(){
		if (confirm("确认变更？")) {
			var postdata = {};
			postdata["id"] = {{$_GET["id"]}};
			postdata["ms_title"] = "{{$material_sheet->ms_title}}";
			postdata["ms_pp_ids"] = new Array;
			$("[name='ms_pp_ids']").each(function(){
				postdata["ms_pp_ids"].push($(this).val());
			});
			postdata["ms_amount"] = $("[name='ms_amount']").val();
			postdata["ms_back_amount"] = $("[name='ms_back_amount']").val();
			if (postdata["ms_pp_ids"].length == 0 || postdata["ms_amount"].length == 0 || postdata["ms_back_amount"].length == 0) {
				alert_flavr("输入数据不合法！");
			} else {
				ajax_post("/alternation/alt_material_sheet_exec",postdata,function(data){
					if (data.suc == 1) {
						if (window.parent.flavr != undefined || window.parent.flavr != null) {
							window.parent.flavr.close();
						}
						window.parent.dt_alt_proc(data.proc_id,"material_sheet",{{$_GET["id"]}});
					} else {
						alert_flavr(data.msg);
					}
				});
			}
			
		}
	}
</script>
@endpush