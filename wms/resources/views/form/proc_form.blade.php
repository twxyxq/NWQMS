@if($proc != false && $proc->info)
<style type="text/css">
	.flexible_form .form-control,.flexible_form button{
		margin-bottom: 3px;
		margin-top: 3px;
	}
	@media (max-width: 767px) {
		.proc_title {
			display: none;
		}
		.proc_body > div:first-child {
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
	.transparent-input {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.proc_history{
		width:100%;
		border: 1px solid lightgray;
		background-color: #FAF7CE;
	}
	.proc_history td{
		text-align: center;
		border: 1px solid lightgray;
		overflow: hidden;
	}
	#detail {
		background-color: #D6F0F8;
		border: 1px solid lightblue;
		border-radius: 5px;
		padding: 5px;
	}
</style>
<div class="proc_form form-group form-horizontal" proc_id="{{$proc->proc_id}}">
	<div class="row proc_title">
		<div class="col-sm-2">
			<span class="form-control transparent-input"><strong>节点</strong></span>
		</div>
		<div class="col-sm-3">
			<span class="form-control transparent-input"><strong>责任人</strong></span>
		</div>
		<div class="col-sm-3">
			<span class="form-control transparent-input"><strong>审核意见</strong></span>
		</div>
		<div class="col-sm-4">
			<span class="form-control transparent-input"><strong>时间</strong></span>
		</div>
	</div>
	@foreach ($proc->item_info as $item_info)
		<div class="row proc_body">
			<div class="col-sm-2">
				<span class="form-control transparent-input">{{$item_info->pdi_title}}</span>
			</div>
			<div class="col-sm-3">
				@if($item_info->current_version == 0 && strlen($item_info->pdi_action) == 0 && $proc->get_current_proc() != false && $proc->get_current_proc()->owner == Auth::user()->id)
					@if(isset($proc->auth[$item_info->version]))
						@define $auth = ",type:'auth#like#%{".$proc->auth[$item_info->version]."}%'"
					@else
						@define $auth = ""
					@endif
					<input type="text" id="owner{{$item_info->id}}" name="owner{{$item_info->id}}" class="owner form-control" value="{{$item_info->owner}}" bind="{model:'user',col:'id',show:'CONCAT(code,name)'{{$auth}}}" @if($proc->get_next_proc() !== false && $proc->get_next_proc()->id != $item_info->id) nullable="1" @endif>
				@elseif ($c=\App\user::find($item_info->owner))
					<span class="form-control transparent-input">{{$c->code.$c->name}}</span>					
				@endif
			</div>
			<div class="col-sm-3">
				@if($item_info->current_version == 1 && $item_info->owner == Auth::user()->id)
					<input type="text" name="pdi_comment" placeholder="审批意见" class="form-control" value="{{$item_info->pdi_comment}}" nullable="1" refer="1" bind="['通过','同意','重新修改','取消该流程']">
				@elseif($item_info->current_version == 0 && strlen($item_info->pdi_action) > 0)
					<span class="form-control transparent-input" title="{{$item_info->pdi_comment}}">{{$item_info->pdi_comment}}</span>
				@endif
			</div>
			<div class="col-sm-4">
				<span class="form-control transparent-input">{{$item_info->updated_at}}</span>
			</div>
		</div>
	@endforeach
	@if($proc->get_current_proc() != false && $proc->get_current_proc()->owner == Auth::user()->id)
	<div class="row proc_button">
		<div class="col-xs-4 col-xs-offset-2 col-sm-2 col-sm-offset-4">
			<button id="btn-pass" class="btn btn-success" valid="pass_proc()">通过</button>
		</div>
		<div class="col-xs-4 col-sm-2">
			<button id="btn-rollback" class="btn btn-warning">退回</button>
		</div>
	</div>
	@endif
	<div>
		<table class="proc_history">
			<tr>
				<td colspan="5"><strong>历史记录</strong></td>
			</tr>
			<tr>
				<td>
					<strong>节点</strong>
				</td>
				<td>
					<strong>责任人</strong>
				</td>
				<td>
					<strong>处理方式</strong>
				</td>
				<td>
					<strong>审核意见</strong>
				</td>
				<td>
					<strong>时间</strong>
				</td>
			</tr>
		@foreach ($proc->item_history as $item_history)
			<tr>
				<td>
					{{$item_history->pdi_title}}
				</td>
				<td>
					{{$item_history->name}}
				</td>
				<td>
					{{$item_history->action}}
				</td>
				<td>
					{{$item_history->pdi_comment}}
				</td>
				<td>
					{{$item_history->updated_at}}
				</td>
			</tr>
		@endforeach
		</table>
	</div>
	@if(method_exists($proc,"pd_info"))
	<div id="detail">
		{!!$proc->pd_info()!!}
	</div>
	@endif
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
	<div style="text-align: center;">
		<h5>该流程已删除</h5>
	</div>
@endif