<div id="report" class="exam_report">
<table border="0" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
	<col width="110">
	<col width="226">
	<col width="110">
	<col width="226">
	<tr>
		<td colspan="4" height="62" align="center" valign="top" style="border:solid 0px;">
			<p><strong><font style="font-size:19px">山 东 电 力 建 设 第 二 工 程 公 司</font></strong></p>
			<strong><font style="font-size:27px">{{e_method_translation($report->exam_report_method)}}检测报告</font></strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="18" align="left" valign="bottom">
			报告编号：{!!isset($report->exam_report_code)?$report->exam_report_code:""!!}
		</td>
		<td colspan="2" align="right" valign="bottom">
			报告日期：{!!isset($report->exam_report_date)?$report->exam_report_date:""!!}
		</td>
	</tr>
</table>
<table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
	<tr>
		<td height="33">工程名称</td>
		<td colspan="3">阳江核电厂5、6号机组常规岛及BOP建安工程（第I标段）</td>
	</tr>
	<tr>
		<td height="33">委托单位</td>
		<td>焊接工程处</td>
		<td>委托单号</td>
		<td>{{isset($report->es_code)?$report->es_code:""}}</td>
	</tr>
	<tr>
		<td height="33">机组系统</td>
		<td>{{isset($report->ild_sys)?$report->ild_sys:""}}</td>
		<td>检测部位</td>
		<td>焊接接头</td>
	</tr>
@if($report->exam_report_method == "RT")
	<tr>
		<td height="33">规格（mm）</td>
		<td>{{isset($report->type)?$report->type:""}}</td>
		<td>焊接方法</td>
		<td>{{isset($report->weld_method)?$report->weld_method:""}}</td>
	</tr>
@endif
	<tr>
		<td height="33">材  质</td>
		<td>{{isset($report->c)?$report->c:""}}</td>
		<td>接头型式</td>
		<td>{{isset($report->jtype)?$report->jtype:""}}</td>
	</tr>
	<tr>
@for($i = 0; $i < sizeof($info); $i++)
		<td height="33">{{$info[$i][0]}}</td>
		<td>{!!$info[$i][1]!!}</td>
	@if($i % 2 == 1)
	</tr>
	<tr>
	@endif
@endfor
	@if($i % 2 == 1)
		<td></td>
		<td></td>
	@endif
	</tr>
  </table>
  <table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow=hidden;TABLE-LAYOUT:fixed;word-break:break-all; word-wrap:break-all;">
@foreach($result as $result_item)
	<tr>
	@foreach($result_item as $result_item_item)
		<td>{!!$result_item_item!!}</td>
	@endforeach
	</tr>
@endforeach
  </table>
  <table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow=hidden;TABLE-LAYOUT:fixed;word-break:break-all; word-wrap:break-all;">
	<tr>
	@if($report->exam_report_method == "RT")
		<td align="left" valign="top">CNPEC见证：</td>
	@endif
		<td height="76" align="left" valign="top">批准：</td>
		<td align="left" valign="top">审核：</td>
		<td align="left" valign="top">评定：</td>
		<td align="left" valign="top">检测单位（章）</td>
	</tr>
   </table>
</div>

<div style="text-align: center; margin-top: 10px">
@if(isset($report->exam_input))
	<button class="btn btn-success" onclick="exam_confirm()">确认结果</button>
@elseif(isset($report->create))
	<button class="btn btn-danger" onclick="report_confirm()">出版确认</button>
@elseif(isset($report->exam_report_code))
	<button class="btn btn-info" onclick="print_object('.exam_report')">打印报告</button>
@endif
</div>