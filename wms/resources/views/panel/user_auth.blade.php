@extends('layouts.page_detail')

@define $user = \App\user::find($id);

@push('style')
<style type="text/css">
	.auth_item[auth='1'] {
		background-color: pink;
	}
</style>
@endpush

@section('panel-body')
<div class="row">
    <div class="col-sm-2">工号：</div>
    <div class="col-sm-2">{{$user->code}}</div>
    <div class="col-sm-2">姓名：</div>
    <div class="col-sm-2">{{$user->name}}</div>
</div>
<div class="row">
	<div class="col-sm-2">权限：</div>
    <div class="col-sm-10">{{$user->auth}}</div>
</div>
<div class="row">

	<div class="col-sm-12"><strong>权限设置</strong></div>

	<div class="col-sm-2">焊接：</div>
    <div class="col-sm-10">
    	<span id="weld_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'[weld_syn]')!==false?1:0}}">焊接综合</span>
    </div>

	<div class="col-sm-2">材料：</div>
    <div class="col-sm-10">
    	<span id="m_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'[m_syn]')!==false?1:0}}">焊材综合</span>
    	<span id="m_loc" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'[m_loc]')!==false?1:0}}">现场焊材库</span>
    	<span id="m_pre" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'[m_pre]')!==false?1:0}}">准备区焊材库</span>
    </div>

	<div class="col-sm-2">检验：</div>
    <div class="col-sm-10">
    	<span id="exam_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'[exam_syn]')!==false?1:0}}">检验综合</span>
    </div>

    <div class="col-sm-12" style="text-align: center;">
    	<button class="btn btn-success" onclick="confirm_auth()">确认</button>
    </div>
	
</div>
@endsection

@push('scripts')
<script type="text/javascript">

	$(".auth_item").on("click",function(){
		if ($(this).attr("auth") == 1) {
			$(this).attr("auth",0);
		} else {
			$(this).attr("auth",1);
		}
	});

	function confirm_auth(){
		var auth = "";
		$(".auth_item[auth='1']").each(function(){
			auth += "["+$(this).attr("id")+"]";
		});
		ajax_post("/panel/user_auth_post",{"auth":auth,"id":{{$id}}},function(data){
			if (data.suc == 1) {
				location.reload();
			} else {
				alert_flavr(data.msg);
			}
		});
	}
</script>
@endpush