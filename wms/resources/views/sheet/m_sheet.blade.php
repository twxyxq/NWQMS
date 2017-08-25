<div id="m_sheet_{{$data->id}}" class="mr_doc" style="text-align: center;">
	<table border="1" width="672" id="tb01" style="font-size:13px;text-align:center;border-collapse:collapse;overflow:hidden;table-layout:fixed;word-break:break-all; word-wrap:break-all;margin: 0 auto;border-color:#000000">
		<tr>
			<td height="44" align="center" colspan="2" style="border-right:solid 1px;border-bottom:solid 0px;">
				<strong><font size="5px"> &nbsp;  &nbsp; 焊材领用单</font></strong>
			</td>
			<td width="59" rowspan="2" style="border-left:solid 1px;border-bottom:solid 1px;">
				{!! QrCode::size(58)->generate(40000000000+PJCODE*1000000+$data->id); !!}
			</td>
		</tr>
		<tr>
			<td height="15" align="left" style="border-top:solid 0px;border-bottom:solid 1px;border-right:solid 0px;">
				
			</td>
			<td align="right" style="border-top:solid 0px;border-bottom:solid 1px;border-left:solid 0px;border-right:solid 1px;">{{40000000000+PJCODE*1000000+$data->id}}</td>
		</tr>
		<tr>
			<td height="45" align="left" colspan="3" id="mr_task">单据名称：{{$data->ms_title}}</td>
		</tr>
		<tr>
			<td height="45" colspan="3" align="left">焊口信息：
				@foreach($wj as $wj_item)
					{{$wj_item->vcode}}[{{$wj_item->base}}] 
				@endforeach
			</td>
		</tr>
		<tr>
			<td height="45" align="left">领用焊工：{{$data->ms_pp_show}}</td>
			<td align="left" colspan="2">领用类型：{{$data->ms_m_type}}</td>
		</tr>
		<tr>
			<td height="45" align="left">型号规格：{{$data->ms_type}} φ{{$data->ms_diameter}}</td>
			<td align="left" colspan="2">技术员：{{\App\user::find($data->created_by)->name}}</td>
		</tr>
		<tr>
			<td height="45" align="left">领用数量：{{$data->ms_amount}}</td>
			<td align="left" colspan="2">发放批号/签字：
				@if($data->ms_s_id == 0)
					@if(isset($store))
						<div id="sent_msg{{$data->id}}">
						@if(sizeof($store) > 0)
							@foreach($store as $store_item)
								<input type="radio" name="ms_s_id{{$data->id}}" title="{{$store_item->ss_trademark}} φ{{$store_item->ss_diameter}} {{$store_item->ss_batch}}" value="{{$store_item->id}}">{{$store_item->ss_trademark}} φ{{$store_item->ss_diameter}} {{$store_item->ss_batch}}<br>
							@endforeach
							<button class="btn btn-success btn-small" onclick="sent({{$data->id}})">确认发放</button>
							<input type="hidden" name="ms_store" value="{{$warehouse}}">
						@else
							无库存
						@endif
						</div>
						<div id="sent_suc_msg{{$data->id}}" style="display:none">
							
						</div>
					@else
						尚未发放
					@endif
				@else
					{{$data->ms_s_show}}/{{\App\user::find($data->ms_by)->name}} {{$data->ms_time}}
				@endif


				
			</td>
		</tr>
		@if(!isset($store))
			<tr>
				<td height="45" align="left">回收数量：
				@if($data->ms_back_time==null)
					@if(isset($warehouse) && $data->ms_store==$warehouse)
						<input type="text" name="ms_back_amount{{$data->id}}" class="form-control input-sm" name="ms_back_amount" value="0">
					@else
						未回收
					@endif
					<div class="back_suc_msg{{$data->id}}" id="back_msg{{$data->id}}" style="display: none"></div>
				@else
					{{$data->ms_back_amount}}
				@endif
				</td>
				<td align="left" colspan="2">回收签字/日期：
					@if($data->ms_back_time==null)
						@if(isset($warehouse) && $data->ms_store==$warehouse)
							<button onclick="back({{$data->id}})" name="ms_back_button{{$data->id}}" class="btn btn-success btn-small">确认回收</button>
						@elseif(!isset($warehouse))
							尚未回收
						@else
							该单据不在本库发出
						@endif
					@else
						{{\App\user::find($data->ms_back_by)->name}} {{$data->ms_back_time}}
					@endif
					<div class="back_suc_msg{{$data->id}}" id="back_edit{{$data->id}}" style="display: none"></div>
				</td>
			</tr>
		@endif
	</table>
</div>