<div id="exam_sheet_{{isset($info->id)?$info->id:0}}" class="consignation_sheet">
<table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow=hidden;TABLE-LAYOUT:fixed;word-break:break-all; word-wrap:break-all;" bordercolor="#000000">
	<col width="70">
	<col width="152">
	<col width="70">
	<col width="120">
	<col width="90">
	<col width="170">
	<tr>
		<td colspan="6" height="56" align="center" valign="middle"><strong><font style="font-size:22px">金 属 试 验 委 托 单</font></strong></td>
	</tr>
	<tr>
		<td height="37">编 号</td>
		<td colspan="3">{!!$info->es_code!!}</td>
		<td>委托日期</td>
		<td>{{$info->created_at->toDateString()}}</td>
	</tr>
	<tr>
		<td height="37">委托单位</td>
		<td colspan="3">焊接工程处</td>
		<td>机组系统</td>
		<td>{{$info->es_ild_sys}}</td>
	</tr>
	<tr>
		<td height="37">状 态</td>
		<td colspan="3">□供货 □焊后 □返修 □热处理</td>
		<td>检验方法</td>
		<td>{{$info->es_method}}</td>
	</tr>
	<tr>
		<td height="37">委托人</td>
		<td colspan="3">{{\App\user::find($info->created_by)->name}}</td>
		<td>要求完成时间</td>
		<td>{!!$info->es_demand_date!!}</td>
	</tr>
	<tr>
		<td height="37">序号</td>
		<td>焊 口 号</td>
		<td>接头型式</td>
		<td colspan="2">材质规格</td>
		<td>焊 工</td>
	</tr>
	@define $i=1
	@foreach($wjs as $wj)
		<tr>
			<td height="37">{{$i++}}</td>
			<td>{{$wj->vcode}}</td>
			<td>{{$wj->jtype}}</td>
			<td colspan="2">{{$wj->base}}</td>
			<td>{{\App\tsk::find($wj->tsk_id)->tsk_pp_show}}</td>
		</tr>
	@endforeach
</table>
</div>
<div style="text-align: center; margin-top: 10px">
@if(!isset($info->id))
	<input type="hidden" name="es_wj_type" value="{{$info->es_wj_type}}">
	<input type="hidden" name="es_ild_sys" value="{{$info->es_ild_sys}}">
  	<button class="btn btn-success" onclick="generate_finish()">生成委托单</button>
@else
	<button class="btn btn-info btn-small" onclick="print_object($('#exam_sheet_{{isset($info->id)?$info->id:0}}'))">打印委托单</button>
@endif
</div>