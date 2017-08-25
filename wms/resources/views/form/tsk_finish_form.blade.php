@if(isset($_GET["id"]))
	@define $data->edit_finished()
@endif

@define $data = \App\tsk::find($id);

@if($data->valid_updating())

	@define $wjs = \App\wj::select(array("id",DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("tsk_id",$id)->get();
	@define $wps = \App\wps::find($data->wps_id);
	@define $material = \App\material_sheet::where("ms_tsk_ids","LIKE","%{".$id."}%")->get();

	<style type="text/css">
		#tsk_form div,#tsk_form label{
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		#tsk_form div:hover,#tsk_form label:hover{
			overflow: visible;
		}
	</style>
	<div id="tsk_form" class="form-group form-horizontal" nullable="except">

		<input type="hidden" name="tsk_id" value="{{$data->id}}">


		<div class="col-sm-12">
			<span class="glyphicon glyphicon-info-sign"></span> 任务信息
		</div>

		<div class="col-sm-4">
			<strong>《{{$data->tsk_title}}》</strong>
		</div>
		<div class="col-sm-4">
			日期：{{$data->tsk_date}}
		</div>
		<div class="col-sm-4">
			{{$data->created_at}}
		</div>


		<div class="col-sm-12">
			<span class="glyphicon glyphicon-info-sign"></span> 焊口信息
		</div>

		@foreach($wjs as $wj)
			<div class="col-sm-4">
				<strong>[{{$wj->wj_code}}]</strong>
			</div>
			<div class="col-sm-4">
				规格：{{$wj->type}}
			</div>
			<div class="col-sm-4">
				检验：{{$wj->rate}}
			</div>
		@endforeach

		<div class="col-sm-12">
			<span class="glyphicon glyphicon-info-sign"></span> 工艺信息
		</div>

		<div class="col-sm-4">
			<strong>《{{$wps->wps_code}}({{$wps->version}})》</strong>
		</div>
		<div class="col-sm-4">
			焊接方法：{{$wps->wps_method}}
		</div>
		<div class="col-sm-4">
			焊材：{{$wps->wps_wire}} {{$wps->wps_rod}}
		</div>

		<div class="col-sm-12">
			<span class="glyphicon glyphicon-info-sign"></span> 焊材信息
		</div>

		@foreach($material as $m)
			<div class="col-sm-5">
				<strong>{{"[".$m->ms_m_type."]".$m->ms_type."φ".$m->ms_diameter." ".$m->ms_s_show}}</strong>
			</div>
			<div class="col-sm-4">
				焊工：
				@if(strlen($m->ms_time)>0)
					<a href="###" onclick="add_ms_pp('{{$m->ms_pp_ids}}')">{{$m->ms_pp_show}}</a>
				@else
					{{$m->ms_pp_show}}
				@endif
			</div>
			<div class="col-sm-3">
				{{strlen($m->ms_time)>0?$m->ms_time:"尚未领取"}}
			</div>
		@endforeach


		<div class="col-sm-12">
			<span class="glyphicon glyphicon-info-sign"></span> 完工录入
		</div>
		
		<label for="tsk_pp" class="col-sm-2 control-label" title="焊工">焊工</label>
		<div class="col-sm-5" id="tsk_pp">
			<input type="text" name="tsk_pp" class="form-control input-sm" bind="{model:'pp',col:'id',show:'CONCAT(pcode,pname)'}" multiples="1" change_fn="change_proportion();" value="{{($data->tsk_pp != null && strlen($data->tsk_pp) != 0)?$data->tsk_pp:''}}">
		</div>
		<label for="tsk_pp_proportion" class="col-sm-2 control-label" title="工作量比例">工作量比例</label>
		<div class="col-sm-3" id="tsk_pp_proportion">
			<span type="text" id="base_tsk_pp_proportion" class="form-control input-sm">
				@if($data->tsk_pp_proportion != null && strlen($data->tsk_pp_proportion) != 0)
					@define $proportion = multiple_to_array($data->tsk_pp_proportion)
					@for($i = 0; $i < sizeof($proportion); $i++)
						@if($i > 0) : @endif
						<input name='tsk_pp_proportion' class='transparent-input' size='2' value='{{$proportion[$i]}}'>
					@endfor
				@endif
			</span>
		</div>


		<label for="tsk_finish_date" class="col-sm-2 control-label" title="完成日期">完成日期</label>
		<div class="col-sm-5" id="tsk_finish_date">
			<input type="text" class="form_date form-control input-sm" id="tsk_finish_date" data-date-format="yyyy-mm-dd" name="tsk_finish_date" readonly="true" endDate="{{$data->tsk_finish_date==null?\Carbon\Carbon::today()->toDateString():$data->tsk_finish_date}}" value="{{$data->tsk_finish_date==null?\Carbon\Carbon::today()->toDateString():$data->tsk_finish_date}}" />
		</div>
		<div class="col-sm-5" id="tsk_finish_submit">
			<button class="btn btn-success" onclick="tsk_finished();">确定</button>
		</div>

	</div>
	<script type="text/javascript">
		function add_ms_pp(pp_ids){
			$("#sp_tsk_pp").val(pp_ids);
			$("#sp_tsk_pp").attr("refresh",1);
			$("#sp_tsk_pp").trigger("dblclick");
		}
		function change_proportion(){
			var num = $("[name='tsk_pp']").parent().find("[type='checkbox']").length;
			if (num == 0) {
				$("#base_tsk_pp_proportion").html("");
			} else {
				var text = new Array();
				var count = 0;
				for (var i = 0; i < num-1; i++) {
					var this_count = Math.ceil((100-count)/(num-i));
					count += this_count;
					text.push(this_count);
				}
				text.push(100-count);
				var html = "";
				for (var i = 0; i < text.length; i++) {
					html += ": <input name='tsk_pp_proportion' class='transparent-input' size='2' value='"+text[i]+"'>";
				}
				$("#base_tsk_pp_proportion").html(html.substr(1));
			}
		}
		function tsk_finished(){
			if(valid_null($("#tsk_form"))){
				var postdata = {};
				postdata["id"] = $("[name='tsk_id']").val();
				postdata["tsk_pp"] = new Array();
				$("[name='tsk_pp']").each(function(){
					postdata["tsk_pp"].push($(this).val());
				});
				postdata["tsk_pp_proportion"] = new Array();
				$("[name='tsk_pp_proportion']").each(function(){
					postdata["tsk_pp_proportion"].push($(this).val());
				});
				postdata["tsk_finish_date"] = $("[name='tsk_finish_date']").val();
				postdata["_method"] = "PUT";
				postdata["_token"] = $("#_token").attr("value");
				ajax_post("/tsk/tsk_finished",postdata,function(rdata){
					if (Number(rdata.suc) == 1) {
						$("#tsk_pp").html("<span class='form-control transparent-input'>"+rdata.tsk_pp_show+"</span>");
						$("#tsk_pp_proportion").html("<span class='form-control transparent-input'>"+rdata.tsk_pp_proportion+"</span>");
						$("#tsk_finish_date").html("<span class='form-control transparent-input'>"+rdata.tsk_finish_date+"</span>");
						$("#tsk_finish_submit").html("<span class='form-control transparent-input'>提交成功</span>");
						$("#example").DataTable().draw(false);
					} else {		
						alert_flavr(rdata.msg);
					}
				});
			}
		}
	</script>
	@else
	无法操作（{{$data->msg}}）
	@endif
