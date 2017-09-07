<div id="report" class="exam_report">
<style type="text/css">
	.main_report td {
		border: 1px solid black;
	}
	#middle_table tr:first-child td {
		border-top: 0px;
	}
	#middle_table tr:last-child td {
		border-bottom: 0px;
	}
	#middle_table tr td:first-child {
		width: 38px;
	}
</style>
<table border="0" width="672" id="title01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
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

<table width="672" class="main_report" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">