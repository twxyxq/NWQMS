@extends('layouts.panel_table')


@section('panel-body')
	<button class="btn btn-success" onclick="show_qrcode()">打印</button>

	<button class="btn btn-success" onclick="new_flavr('/pp/pp_qrcode_detail?n=10')">打印10个</button>

	<button class="btn btn-success" onclick="new_flavr('/pp/pp_qrcode_detail?n=20')">打印20个</button>

	<button class="btn btn-success" onclick="new_flavr('/pp/pp_qrcode_detail')">打印全部</button>
@endsection

@push('scripts')
<script type="text/javascript">
	function show_qrcode(){
		//alert_flavr(data.msg);
		if ($(".pid:checked").length == 0) {
			alert_flavr("没选择焊工");
		} else {
			var id_string = "";
			$(".pid:checked").each(function(){
				id_string += "{"+$(this).val()+"}";
			});
			new_flavr("/pp/pp_qrcode_detail?ids="+id_string);
		}
	}
</script>
@endpush