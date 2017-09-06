@define $wjs = \App\wj::select(array(DB::raw("*"),DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("tsk_id",$tsk->id)->get();
@define $info = $wjs[0]
@define $wps = \App\wps::withoutGlobalScopes()->find($tsk->wps_id)
@define $wpq = \App\wpq::withoutGlobalScopes()->whereIn("id",multiple_to_array($wps->wps_wpq))->get()
@define $qp = \App\qp::withoutGlobalScopes()->find($tsk->qp_id)
@define $qp_proc_model = \App\qp_proc_model::whereIn("id",multiple_to_array($qp->qp_proc_model))->get()
@foreach($qp_proc_model as $m)
	@if($m->qpm_condition == "全部" || $m->qpm_condition == $tsk->tsk_ft)
		@define $qp_proc = \App\qp_proc::where("qpp_model_id",$m->id)->get()
		@define break;
	@endif
@endforeach

<div id="tsk_{{$tsk->id}}" class="welding_record" style="display:inline-block">
<style>
	#tb01 {
		border: 1px solid black;
	}
</style>
@if($info->wj_type != "结构")
<table border="1" width="675" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
	<col width="51">
	<col width="66">
	<col width="75">
	<col width="77">
	<col width="43">
	<col width="46">
	<col width="64">
	<col width="74">
	<col width="60">
	<col width="60">
	<col width="59">
	<tr>
		<td rowspan="2" colspan="3" height="56" align="left" valign="middle"><img height="40" src="/images/co_logo.png" /></td>
		<td colspan="7" height="32" align="center" valign="bottom" style="border-left:solid 1px;border-bottom:solid 0px;border-right:solid 1px"><font size="5"><strong>阳江核电站{{$qp->qp_pipe_type}}{{$tsk->tsk_ft}}焊接控制单</strong></font></td>
		<td rowspan="2" colspan="1">{!! QrCode::size(58)->generate(10000000000+PJCODE*1000000+$tsk->id); !!}</td>
	</tr>
	<tr>
		<td colspan="7" height="24" align="right" style="border-left:solid 1px;border-top:solid 0px;border-bottom:solid 1px;border-right:solid 1px">{{10000000000+PJCODE*1000000+$tsk->id}}</td>
	</tr>
	<tr align="left">
		<td colspan="11" height="22" align="left" style="border-top:solid 1px;border-bottom:solid 0px;">工程名称：阳江核电厂5、6号机组常规岛及BOP建安工程（第I标段）</td>
	</tr>
	<tr>
		<td colspan="4" height="22" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-right:solid 0px;">质量计划：{{$qp->qp_code}}</td>
		<td colspan="2" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;border-right:solid 0px;">版次：{{$qp->version}}</td>
		<td align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;border-right:solid 0px;"></td>
		<td colspan="4" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;">图纸编号：{{$info->drawing}}</td>
	</tr>
	<tr>
		<td colspan="2" height="22" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-right:solid 0px;">系统代号：{{$info->sys}}</td>
		<td colspan="2" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;border-right:solid 0px;">管线号：{{$info->pipeline}}</td>
		<td colspan="3" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;border-right:solid 0px;">接头编号：{{$info->vnum}}</td>
		<td colspan="4" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;">焊缝号：{{$info->vcode}}</td>
	</tr>
	<tr>
		<td colspan="3" height="22" align="left" style="border-bottom:solid 1px;border-top:solid 0px;border-right:solid 0px;">型式：{{$info->jtype}}</td>
		<td colspan="4" align="left" style="border-bottom:solid 1px;border-top:solid 0px;border-left:solid 0px;border-right:solid 0px;">上游：{{$info->upstream}}</td>
		<td colspan="4" align="left" style="border-bottom:solid 1px;border-top:solid 0px;border-left:solid 0px;">下游：{{$info->downstream}}</td>
	</tr>
	<tr>
		<td rowspan="4">适用于<br>安装</td>
		<td colspan="2" align="left" valign="top" height="44">计量器具编号/规格：</td>
		<td colspan="3" align="left" valign="top">错边尺：</td>
		<td colspan="2" align="left" valign="top">卷尺：</td>
		<td colspan="3" align="left" valign="top">角尺：</td>
	</tr>
	<tr>
		<td rowspan="2" colspan="2">检查内容</td>
		<td colspan="2" height="27">{{$qp->qp_pipe_type=="仪表"?"仪表及管道":"管道内"}}清洁度</td>
		<td colspan="2">{{$qp->qp_pipe_type=="仪表"?"仪表与管道对口":"插套间隙"}}</td>
		<td colspan="2">{{$qp->qp_pipe_type=="仪表"?"仪表插入深度":"管道平直度"}}</td>
		<td>{{$qp->qp_pipe_type=="仪表"?"结论":"错口值"}}</td>
		<td>{{$qp->qp_pipe_type=="仪表"?"备注":"结论"}}</td>
	</tr>
	<tr>
		<td colspan="2" height="27"></td>
		<td colspan="2"></td>
		<td colspan="2"></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="10" height="{{$qp->qp_pipe_type=='油系统'?50:38}}" align="left">
		{{$qp->qp_pipe_type=='油系统'?"质量要求：1、对接管平直度：DN＜100mm:≤1mm/200mm；DN≥100mm:≤2mm/200mm。2、组对错口值：不宜超过管道壁厚的10％，且≤1mm。3、承插焊接头，承口与插口的轴向不宜留有间隙。4、对口前应用丙酮（或无水乙醇）和白绸布对油管道内部进行擦拭清理，然后用白绸布擦拭，直至白绸布无污迹。5、管口封堵良好":$qp->qp_pipe_type=="仪表"?"质量要求：1、仪表与被测金属表面光滑无毛刺。2、仪表与管道对口安装凡是符合设计要求。3、仪表插入深度符合设计要求。":"质量要求：1、对接管平直度：当公称直径：DN＜100mm时，应≤1mm/200mm；当公称直径：DN≥100mm时，应≤2mm/200mm。2、组对错口值：不宜超过管道壁厚的10％，且≤1mm"}}
		</td>
	</tr>
	<tr>
		<td rowspan="3">材质<br>信息</td>
		<td colspan="2" height="27">位置</td>
		<td colspan="3">母材材质</td>
		<td colspan="2">外径（mm）</td>
		<td colspan="3">厚度（mm）</td>
	</tr>
	<tr>
		<td colspan="2" height="27">A侧</td>
		<td colspan="3">{{$info->ac}}</td>
		<td colspan="2">{{$info->at}}</td>
		<td colspan="3">{{$info->ath}}</td>
	</tr>
	<tr>
		<td colspan="2" height="27">B侧</td>
		<td colspan="3">{{$info->bc}}</td>
		<td colspan="2">{{$info->bt}}</td>
		<td colspan="3">{{$info->bth}}</td>
	</tr>
	<tr>
		<td colspan="11" height="27" align="center">焊接工艺</td>
	</tr>
	<tr>
		<td colspan="2" height="38">NDE<br>检验比例</td>
		<td>工艺评定<br>编号</td>
		<td>工艺卡<br>编号</td>
		<td colspan="2">焊接<br>方法</td>
		<td>填充材料<br>型号</td>
		<td colspan="2">填充材料<br>批号/规格</td>
		<td colspan="2">焊材<br>管理员</td>
	</tr>
	<tr>
		<td colspan="2" height="60">
			RT:{{$info->RT==0?"N/A":($info->RT."%")}} 
			UT:{{$info->UT==0?"N/A":($info->UT."%")}}<br>
			PT:{{$info->PT==0?"N/A":($info->PT."%")}} 
			MT:{{$info->MT==0?"N/A":($info->MT."%")}}
		</td>
		<td>
		@define $n = 0;
		@foreach($wpq as $w)
			@if($n > 0) <br> @endif
			{{$w->wpq_code}}
			@define $n++;
		@endforeach
		</td>
		<td>{{$wps->wps_code}}</td>
		<td colspan="2">{{$wps->wps_method}}</td>
		<td>
		@if($wps->wps_wire == "N/A")
			{{$wps->wps_rod}}
		@elseif($wps->wps_rod == "N/A")
			{{$wps->wps_wire}}
		@else
			{{$wps->wps_wire}}<br>{{$wps->wps_rod}}
		@endif
		</td>
		<td colspan="2"></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="6" height="27" align="left">焊接位置：</td>
		<td colspan="5" align="left">焊工号/姓名：</td>
	</tr>
	<tr>
		<td rowspan="2" height="27">工序号</td>
		<td rowspan="2" colspan="2">操作工序</td>
		<td rowspan="2">工作程序</td>
		<td colspan="2">通知点</td>
		<td colspan="5">报告/签名</td>
	</tr>
	<tr>
		<td>QC2</td>
		<td>CNPEC</td>
		<td>操作者</td>
		<td>QC1/报告号</td>
		<td>QC2</td>
		<td>CNPEC</td>
		<td>备注</td>
	</tr>
	@define $height = 0
	@foreach($qp_proc as $proc)
	<tr>
		@define $h = $proc->qpp_height*30/100
		@define $height += $h
		<td height="{{$h}}">{{$proc->qpp_num}}</td>
		<td colspan="2">{{$proc->qpp_name}}</td>
		<td>{{$proc->qpp_procedure}}</td>
		<td>{{$proc->qpp_qc2=="N/A"?"":$proc->qpp_qc2}}</td>
		<td>{{$proc->qpp_qc3=="N/A"?"":$proc->qpp_qc3}}</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
	@while($height < 360)
	<tr>
		@define $height += 30
		<td height="30"></td>
		<td colspan="2"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endwhile
	<tr>
		<td align="left" colspan="11" valign="top" height="54.8">备注:<br>单位mm</td>
	</tr>

</table>


@else


<div id="tsk_{{$tsk->id}}" class="welding_record">
<table border="1" width="665" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;">
	<col width="51">
	<col width="66">
	<col width="74">
	<col width="76">
	<col width="43">
	<col width="45">
	<col width="63">
	<col width="73">
	<col width="58">
	<col width="58">
	<col width="58">

	@define $qr_code_id = $tsk->tsk_special_id>0?$tsk->tsk_special_id:$tsk->id

	<tr>
		<td rowspan="2" colspan="3" height="56" align="left" valign="middle"><img height="40" src="/images/co_logo.png" /></td>
		<td colspan="7" height="32" align="center" valign="bottom" style="border-left:solid 1px;border-bottom:solid 0px;border-right:solid 1px"><font size="5"><strong>阳江核电站焊接控制单</strong></font></td>
		<td rowspan="2" colspan="1">{!! QrCode::size(58)->generate(30000000000+PJCODE*1000000+$qr_code_id); !!}</td>
	</tr>
	<tr>
		<td colspan="7" height="24" align="right" style="border-left:solid 1px;border-top:solid 0px;border-bottom:solid 1px;border-right:solid 1px">{{30000000000+PJCODE*1000000+$qr_code_id}}</td>
	</tr>
	<tr align="left">
		<td colspan="7" height="35" align="left" style="border-top:solid 1px;border-bottom:solid 0px;border-right:solid 0px;">工程名称：阳江核电厂5、6号机组常规岛及BOP建安工程（第I标段）</td>
		<td colspan="2" align="left" style="border-top:solid 1px;border-bottom:solid 0px;border-left:solid 0px;border-right:solid 0px;">机组：{{$info->ild}}</td>
		<td colspan="2" align="left" style="border-top:solid 1px;border-bottom:solid 0px;border-left:solid 0px;">系统代号：{{$info->sys}}</td>
	</tr>
	<tr>
		<td colspan="4" height="35" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-right:solid 0px;">质量计划：{{$qp->qp_code}}</td>
		<td colspan="3" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-right:solid 0px;border-left:solid 0px;">版次：{{$qp->version}}</td>
		<td colspan="4" align="left" style="border-top:solid 0px;border-bottom:solid 0px;border-left:solid 0px;">图纸编号：{{$info->drawing}}</td>
	</tr>
  <tr>
      <td colspan="6" height="35" align="left" style="border-bottom:solid 1px;border-top:solid 0px;border-right:solid 0px;">焊缝号：{{$info->vcode}}</td>
	  <td colspan="5" align="left" style="border-bottom:solid 1px;border-top:solid 0px;border-left:solid 0px;">型式：{{$info->jtype}}</td>
  </tr>
  <tr>
      <td rowspan="3">材质<br>信息</td>
      <td colspan="2" height="35">位置</td>
      <td colspan="3">母材材质</td>
      <td colspan="2">外径（mm）</td>
      <td colspan="3">厚度（mm）</td>
  </tr>
  <tr>
      <td colspan="2" height="35">A侧</td>
      <td colspan="3">{{$info->ac}}</td>
      <td colspan="2">{{$info->at==0?"N/A":$info->at}}</td>
      <td colspan="3">{{$info->ath}}</td>
  </tr>
  <tr>
      <td colspan="2" height="35">B侧</td>
      <td colspan="3">{{$info->bc}}</td>
      <td colspan="2">{{$info->bt==0?"N/A":$info->bt}}</td>
      <td colspan="3">{{$info->bth}}</td>
  </tr>
  <tr>
      <td colspan="11" height="37" align="center">焊接工艺</td>
  </tr>
  <tr>
      <td colspan="2" height="42">NDE<br>检验比例</td>
      <td>工艺评定<br>编号</td>
      <td>工艺卡<br>编号</td>
      <td colspan="2">焊接<br>方法</td>
      <td>填充材料<br>型号</td>
      <td colspan="2">填充材料<br>批号/规格</td>
      <td colspan="2">焊材<br>管理员</td>
  </tr>
  <tr>
      <td colspan="2" height="65">
      		RT:{{$info->RT==0?"N/A":($info->RT."%")}} 
			UT:{{$info->UT==0?"N/A":($info->UT."%")}}<br>
			PT:{{$info->PT==0?"N/A":($info->PT."%")}} 
			MT:{{$info->MT==0?"N/A":($info->MT."%")}}
      </td>
      <td>
			@define $n = 0;
			@foreach($wpq as $w)
				@if($n > 0) <br> @endif
				{{$w->wpq_code}}
				@define $n++;
			@endforeach
      </td>
      <td>{{$wps->wps_code}}</td>
		<td colspan="2">{{$wps->wps_method}}</td>
		<td>
		@if($wps->wps_wire == "N/A")
			{{$wps->wps_rod}}
		@elseif($wps->wps_rod == "N/A")
			{{$wps->wps_wire}}
		@else
			{{$wps->wps_wire}}<br>{{$wps->wps_rod}}
		@endif
		</td>
      <td colspan="2"></td>
      <td colspan="2"></td>
  </tr>
  <tr>
      <td colspan="6" height="35" align="left">焊接位置：</td>
      <td colspan="5" align="left">焊工号/姓名：</td>
  </tr>
  <tr>
      <td rowspan="2" height="38">工序号</td>
      <td rowspan="2" colspan="2">操作工序</td>
      <td rowspan="2">工作程序</td>
      <td colspan="2">通知点</td>
      <td colspan="5">报告/签名</td>
  </tr>
  <tr>
      <td>QC2</td>
      <td>CNPEC</td>
      <td>操作者</td>
      <td>QC1/报告号</td>
      <td>QC2</td>
      <td>CNPEC</td>
      <td>备注</td>
  </tr>
  @define $height = 0
	@foreach($qp_proc as $proc)
	<tr>
		@define $h = $proc->qpp_height*30/100
		@define $height += $h
		<td height="{{$h}}">{{$proc->qpp_num}}</td>
		<td colspan="2">{{$proc->qpp_name}}</td>
		<td>{{$proc->qpp_procedure}}</td>
		<td>{{$proc->qpp_qc2=="N/A"?"":$proc->qpp_qc2}}</td>
		<td>{{$proc->qpp_qc3=="N/A"?"":$proc->qpp_qc3}}</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
	@while($height < 360)
	<tr>
		@define $height += 30
		<td height="30"></td>
		<td colspan="2"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endwhile
	<tr>
		<td align="left" colspan="11" valign="top" height="54.8">备注:<br>单位mm</td>
	</tr>
  </table>
  </div>

@endif
</div>