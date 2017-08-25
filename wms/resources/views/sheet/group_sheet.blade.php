<div align="center">
	<div id="ep_doc_2872" class="ep_doc">
		<table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
			<col width="42">
			<col width="180">
			<col width="150">
			<col width="150">
			<col width="150">

			<tr>
				<td height="45" align="center" colspan="5">
					<strong><font size="5px">焊口分组抽批单</font></strong>
				</td>
			</tr>
			<tr>
				<td height="45" align="left" colspan="5">
					分组名称：{{$exam_plan->ep_code}}
				</td>
			</tr>
			<tr>
				<td height="45" align="left" colspan="3">检验方法：{{$exam_plan->ep_method}}</td>
				@define $match = array()
            	@define $vv = preg_match("/[0-9.]+\%/", $exam_plan->ep_code, $match);
				<td align="left" colspan="2">检验比例：{{isset($match[0])?$match[0]:""}}</td>
			</tr>
			<tr>
				<td height="45" align="center">序号</td>
				<td align="center">焊口号</td>
				<td align="center">一次检验</td>
				<td align="center">加倍检验</td>
				<td align="center">全部检验</td>
			</tr>
			@define $i = 0
			@foreach($wjs as $wj)
			<tr>
				<td height="45" align="center">{{++$i}}</td>
				<td align="center">{{$wj->vcode}}</td>
				<td align="center">{{$wj->samples}}</td>
				<td align="center">{!!$wj->addition_samples!!}</td>
				<td align="center">{!!$wj->another_samples!!}</td>
			</tr>
			@endforeach
			<tr>
				<td height="45" colspan="5" align="left">
					当前状态：{{$exam_plan->status}}
				</td>
			</tr>
		</table>
	</div>
</div>
