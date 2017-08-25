@if($proc->info)
<style type="text/css">
	.flexible_form .form-control,.flexible_form button{
		margin-bottom: 3px;
		margin-top: 3px;
	}
	@media (max-width: 767px) {
		.proc_title {
			display: none;
		}
		.proc_body div:first-child {
			background-color: #F1F1F1;
		}
	}
	.proc_title {
		text-align: center;
		border: 1px solid #E5E5E5;
		background-color: #F1F1F1;
	}
	.proc_body,.proc_button {
		text-align: center;
		border: 1px solid #E5E5E5;
	}
</style>
<div class="flexible_form form-group form-horizontal">
	<div class="row proc_title">
		<div class="col-sm-2">
			<span class="form-control transparent-input"><strong>节点</strong></span>
		</div>
		<div class="col-sm-5">
			<span class="form-control transparent-input"><strong>责任人</strong></span>
		</div>
		<div class="col-sm-5">
			<span class="form-control transparent-input"><strong>审核意见</strong></span>
		</div>
	</div>
	@foreach ($proc->item_info as $item_info)
		<div class="row proc_body">
			<div class="col-sm-2">
				<span class="form-control transparent-input">{{$item_info->pdi_title}}</span>
			</div>
			<div class="col-sm-5">
				@if($item_info->current_version == 0 && strlen($item_info->pdi_action) == 0)
					<input type="text" id="owner{{$item_info->id}}" name="owner{{$item_info->id}}" model="procedure_item" id_in_model="{{$item_info->id}}" col="owner" class="owner form-control" value="{{$item_info->owner}}" bind="{model:'user',col:'id',show:'CONCAT(code,name)'}" @if($proc->get_next_proc()->id != $item_info->id) nullable="1" @endif>
				@elseif ($c=\App\user::find($item_info->owner))
					<span class="form-control transparent-input">{{$c->code.$c->name}}</span>					
				@endif
				@if($item_info->current_version == 1)
				<input type="hidden" name="current_version_old" value="0" model="procedure_item" id_in_model="{{$item_info->id}}" col="current_version">
				<input type="hidden" name="pdi_action" value="1" model="procedure_item" id_in_model="{{$item_info->id}}" col="pdi_action">
				@endif
				@if($proc->get_next_proc()->id == $item_info->id)
				<input type="hidden" name="current_version_new" value="1" model="procedure_item" id_in_model="{{$item_info->id}}" col="current_version">
				@endif
			</div>
			<div class="col-sm-5">
				@if($item_info->current_version == 1 && $item_info->owner == Auth::user()->id)
					<input type="text" name="pdi_comment" model="procedure_item" id_in_model="{{$item_info->id}}" col="pdi_comment" placeholder="审批意见" class="form-control" value="{{$item_info->pdi_comment}}" nullable="1" refer="1" bind="['通过','同意','重新修改','取消该流程']">
				@elseif($item_info->current_version == 0 && strlen($item_info->pdi_action) > 0)
					<span class="form-control transparent-input">{{$item_info->pdi_comment}}</span>
				@endif
			</div>
		</div>
	@endforeach
	<div class="row proc_button">
		@if($proc->get_next_proc() === false)
			<input type="hidden" name="unlock_model" value="" model="{{$proc->model_name}}" id_in_model="{{array_to_multiple($proc->ids)}}" col="procedure">
		@endif
		<div class="col-xs-4 col-xs-offset-2 col-sm-2 col-sm-offset-4">
			<button id="btn-pass" class="btn btn-success" valid="pass_proc()">通过</button>
		</div>
		<div class="col-xs-4 col-sm-2">
			<button class="btn btn-warning">退回</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	//$(".owner:first").parent().click(function(){
		//$(this).removeClass("form_null");
	//});
	
	function pass_proc(){
		$("#base_"+$(".owner:first").attr("for")).removeClass("form_null");
		if ($(".owner:first").parent().find("#"+$(".owner:first").attr("for")) != undefined &&　$(".owner:first").parent().find("#"+$(".owner:first").attr("for")).val().length == 0) {
			$("#base_"+$(".owner:first").attr("for")).addClass("form_null");
			alert_flavr("必须设置下一级责任人");
			return false;
		}
		return true;
	}
	function fail_proc(){

	}
</script>
@else
<div class="form-group form-horizontal">
	<div class="col-sm-6" style="text-align: center;">
		<h5>该流程尚未启动，请启动流程</h5>
	</div>
	<div class="col-sm-6" style="text-align: center;">
		<a class="btn btn-success" href="/console/view_procedure?proc=status_avail_procedure&model={{$proc->model_name}}&id={{array_to_multiple($proc->ids)}}&proc_id=&new=1">
			启动流程
		</a>
	</div>
</div>

@endif