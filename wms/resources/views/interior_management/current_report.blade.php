@extends('layouts.only_panel')


@push('style')
<style type="text/css">
	.container {
		padding: 2px;
	}
	.col-xs-6 {
		text-align: center;
	}
	#tb01 td {
		border: 1px solid #000000;
		font-size: 12px;
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

@section('panel-back')
<div class="container">
	
	@define $i = 0
	@define $t = ""
	@foreach($report as $r)
		@if($t != "" && $t != $r->wr_type)
			@define $i = 0
			</table>
		@endif
		@if($i == 0)
			@define $t = $r->wr_type
			<strong style="font-size: 14px">{{$t}}</strong>：
			<table width="100%" id="tb01" style="border:1px solid #000000;font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;margin: 0 auto;">
				<tr>
					<td height="35" width="26" align="center">
						
					</td>
					<td width="110" align="center">
						标题
					</td>
					<td align="center">
						进展
					</td>
					<td width="40" align="center">
						重要性
					</td>
				</tr>
		@endif
			<tr>
				<td height="35" align="center">
					{{$i+1}}
				</td>
				<td align="center">
					{{$r->wr_title}}
				</td>
				<td align="center">
					{{$r->wr_content}}
				</td>
				<td align="center">
					{{$r->wr_level}}
				</td>
			</tr>
		@define $i++
	@endforeach
	</table>
</div>
@endsection