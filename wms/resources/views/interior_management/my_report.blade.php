@extends('layouts.panel_table')


@push('style')
<style type="text/css">
	.container {
		padding: 2px;
	}
	.col-xs-6 {
		text-align: center;
	}
</style>
@endpush


@section('panel-body')
	<div class="col-xs-6">
		<a href="/interior_management/current_report" class="btn btn-info btn-small">汇报表单</a>
	</div>
	<div class="col-xs-6">
		<a href="/interior_management/my_report" class="btn btn-success btn-small">我的汇报</a>
	</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$(".datatable_container").before("<div style='text-align:center'><button class=\"btn btn-warning btn-small\" onclick=\"table_flavr('/console/dt_add?model=work_report')\">㈩ 新增</button></div>");
</script>
@endpush