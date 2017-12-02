@define $sheet_height = 62+18+76
@define $sheet_first_height = 62+18+33+33+33+33+76
@define $current_height = 0
@define $sheet_max = 980

@include('sheet.report_sheet_top')
@define $current_height = $sheet_first_height

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
	@define $current_height += 33
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
		<td height="33">检验日期</td>
		<td>{!!$report->exam_date!!}</td>
@for($i = 0; $i < sizeof($info); $i++)
		<td height="33">{{$info[$i][0]}}</td>
		<td>{!!$info[$i][1]!!}</td>
	@if($i % 2 == 0)
		@define $current_height += 33
		</tr>
		<tr>
	@endif
@endfor
	@if($i % 2 == 0)
		@define $current_height += 33
		<td></td>
		<td></td>
	@endif
	</tr>
  </table>
  <table width="672" id="middle_table" class="main_report" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
@for($k = 0; $k < sizeof($result); $k++)
	@define $current_height += 38
	@if($current_height > $sheet_max)
		@include('sheet.report_sheet_bottom')
		@include('sheet.report_sheet_top')
		@define $current_height = $sheet_height+38
		<tr height="38">
		@foreach($result[0] as $result_title)
			<td>{!!$result_title!!}</td>
		@endforeach
		</tr>
	@endif
	<tr height="38">
	@foreach($result[$k] as $result_item)
		<td>{!!$result_item!!}</td>
	@endforeach
	</tr>
@endfor

@if(isset($_GET["report_id"]))
	@while($current_height < $sheet_max - 38)
		@define $current_height += 38
		<tr height="38">
			@for($j = 0; $j < sizeof($result[0]); $j++)
				<td></td>
			@endfor
		</tr>
	@endwhile
@endif

@include('sheet.report_sheet_bottom')

<div style="text-align: center; margin-top: 10px">
@if(isset($report->exam_input))
	<button class="btn btn-success" onclick="exam_confirm()">确认结果</button>
@elseif(isset($report->create))
	<button class="btn btn-danger" onclick="report_confirm()">出版确认</button>
@elseif(isset($report->exam_report_code))
	<button class="btn btn-info" onclick="print_object('.exam_report')">打印报告</button>
@endif
</div>